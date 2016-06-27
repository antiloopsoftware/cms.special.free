<?php

include '../config.php';
include('../fonctions.php');

//On se connecte à la base de données
include('../db_connect.php');

db_open();
 
//on récupère ce qui est transmit par l'url
$id_transmit = $_GET["id"];
$slug = $_GET["slug"];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
 
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Modifier une catégorie</title>  
	<link href="style.css" rel="stylesheet" type="text/css"/>
 
</head>
 
<body>
 
<div id="moncadre">
 
<?php include('menu.php');?>
 
<div class="cadrecentrale">
 
<h1>Modifier catégorie</h1>
 
<?php

if(isset($_POST["Modifier"]))
{
	$titre = htmlspecialchars(stripcslashes(trim($_POST["titre"])));
	$description = htmlspecialchars(stripcslashes($_POST["description"]));
	
	//on vérifie si le titre ou la description sont vides
	if(empty($titre) || empty($description)){
		echo '<div class="erreur">Le titre et/ou la description de la catégorie doit être renseigné!</div>';
	}
	
	else{
		//on ré écrit le nom du dossier
		$slug = OptimiseUrl($_POST["titre"]);
		
		//On regarde si le titre existe déjà. Si le titre na pas changé, on risque une erreur puisque la requête ci-dessous va chercher un titre qui correspond
        // c'est pour ça que l'on ajoute une recherche sur un identifiant différent
		$categorie_exist = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_slug = '".pg_escape_string($slug)."' AND categorie_id != '".pg_escape_string($id_transmit)."'");
		$total = pg_num_rows($categorie_exist);
		
		if($total >= 1){
			echo '<div class="erreur">Il existe déjà une catégories portant ce titre.</div>';
		}	
		
		//On vérifie si le nom du dossier est autorisé (fichier fonctions.php)
		else if(in_array($slug , $dossier_interdit)){
			echo '<div class="erreur">Ce titre est interdit car il existe un dossier portant ce nom.</div>';
		}
		
		else{
			// on enregistre les données
			$result = pg_query(" UPDATE csf_categories SET categorie_name = '".pg_escape_string($titre)."', categorie_description = '".pg_escape_string($description)."', categorie_slug = '".pg_escape_string($slug)."' WHERE categorie_id = '$id_transmit'");
 
			//Si il y a une erreur
			if (!$result) {
				die('Requête invalide : ' . pg_last_error());
			}
			
			else {
				
				//Cas 1 - Le nom du dossier passé en parametre dans l'url existe sur le serveur
				if (file_exists("../".$slug)) {
					
					// on compare les 2 noms de dossier et si ils sont différents, c'est que le titre à changé
					if($slug != $slug){
						//on renome le dossier
						rename ("../".$slug, "../".$slug);
					}	
					
					//on informe le webmaster
					echo '<div class="ok"><p>La modification à été éffectué avec succès. Redirection automatique en cours... </p></div>';
					
					//on redirige
					echo '<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",750) </script>';	
				}
				
				//Cas 2 - Le nom du dossier dossier passé en parametre dans l'url n'existe pas sur le serveur
				else if(!file_exists("../".$slug)){
					
					//On informe le webmaster
					echo '<div class="erreur">1-Pour une raison indéterminée, le dossier "'.$slug.'" n\'existe pas sur le serveur!</div>';
					
					//on créé automatiquement un dossier en prenant le titre ré écrit de la catégorie au cas ou le webmaster change le titre
					mkdir ("../".$slug, 0777);
					
					//On créé un fichier index dans ce même dossier pour lister les articles de la dite catégorie
					$fichier_a_ouvrir = fopen ("../".$slug."/index.php", "w+");
					
					//on écrite le code php suivant
					fwrite($fichier_a_ouvrir,"<?php include('../categorie.php');?>");
					
					//On informe que le dossier à été re créé
					echo '<div class="ok">2-Le dossier "'.$slug.'" a été créé!</div>';
					
					//on va chercher tout les articles de la catégorie pour re créer les fichiers manquants
					$article = pg_query("SELECT post_slug FROM csf_posts WHERE post_categorie = '".pg_escape_string($id_transmit)."'");
					
					//On compte le nombre d'article
					$total = pg_num_rows($article);
					
					//Si il y a quelque chose
					if(!empty($total)){
						
						//On boucle
						while($affiche = pg_fetch_array($article)){
							
							//On re créer les fichiers
							$fichier_a_ouvrir = fopen ("../".$slug."/".$affiche['slug']."", "w+");
							fwrite($fichier_a_ouvrir,"<?php include('../url_rewriting.php');?>");
						}
						
						//Si erreur, on crie
						if (!$article) {
							die('Requête invalide : ' . pg_last_error());
						} 
						
						//Tout c'est bien passé
						else{
							
							//On informe le webmaster
							echo '<div class="ok">3-L\'ensemble des fichiers ont été re créés ! <br/>Redirection automatique en cours... </div>';
							
							//on redirige
							echo '<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",750) </script>';	
						}
					}
					
					else{
						
						//Aucun fichier à re créer, on informe le webmaster
						echo '<div class="ok">3-Il n\'y a aucun article dans cette catégorie ! <br/>Redirection automatique en cours...</div>';
						
						//on redirige
						echo '<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",750) </script>';	
					}
					
					//on ferme
					fclose ($fichier_a_ouvrir);
				}
			}	
		}	
	}

}//on ferme if(isset($_POST["Modifier"]))	

?>
 
<form action="modifier-categorie.php?id=<?php echo $id_transmit;?>&slug=<?php echo $slug;?>" method="post">
<fieldset>
 
<?php

//On va chercher les info de la catégorie
$result = pg_query("SELECT categorie_name, categorie_description FROM csf_categories WHERE categorie_id = $id_transmit");
 
while($affiche = pg_fetch_array($result))
{
	?>
	
	<p>Titre de la catégorie :<br/>
	<input name="titre" size="65" value="<?php echo $affiche['categorie_name'];?>" type="text"/>
	</p>
 
	<p>Description de la catégorie :<br/>
	<textarea name="description" rows="10" cols="50" ><?php echo $affiche['categorie_description'];?></textarea>
	</p>
 
	<input name="Modifier" value="Modifier" type="submit"/>
	<input name="Effacer" value="Effacer" type="reset"/>
 
	</fieldset>
 
	</form>
	
	<?php
	
}//On ferme la boucle while

?>
 
</div>
 
<?php include('footer.php');?>
 
</div>
 
</body>
 
</html>