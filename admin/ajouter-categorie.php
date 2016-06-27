<?php

include '../config.php';
include('../fonctions.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="style.css" rel="stylesheet" type="text/css"/>
	<title>Ajouter une catégorie</title>
</head>
 
<body>
 
<div id="moncadre">

<?php include('menu.php');?>
 
<div class="cadrecentrale">
 
<h1>Ajouter une catégorie</h1>
 
<?php

//On se connecte à la base de données
include('../db_connect.php');
db_open();
 
//initialisation du masquage du formulaire
$masquer_formulaire = 0;
 
//Traitement du formulaire
if(isset($_POST["Valider"]))
{
	$titre = htmlspecialchars(stripcslashes(trim(strtolower($_POST["titre"]))));
	
	//Pour le pseudo url rewriting
	$slug = OptimiseUrl($titre);
	$description = htmlspecialchars(stripcslashes($_POST["description"]));
	
	if(!empty($_POST["cat_parent"])) {
	
		$categorie = pg_query("SELECT categorie_name, categorie_slug FROM csf_categories WHERE categorie_id = '".pg_escape_string($_POST["cat_parent"])."'");
		
		while($affiche_categorie = pg_fetch_array($categorie))
		{
			$categorie_parent = $affiche_categorie['categorie_slug'];
		}
	}
	
	else 
	{
		$categorie_parent = '';
	}
	
	//Vérification du formulaire
	if(empty($titre))
	{
		$alerte0 ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie de titre.</div>';
	}
	
	//On regarde si le titre existe déjà en BDD
	$categorie_exist = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_slug = '".pg_escape_string($slug)."'");
	$total = pg_num_rows($categorie_exist);
	
	if($total >= 1)
	{
		$alerte_categorie_exist ='<div class="erreur"><a name="ok"></a>Il existe déjà une catégorie portant ce titre.</div>';
	}
	
	//On vérifie si le nom du dossier est autorisé (fichier fonctions.php)
	else if(in_array($titre , $dossier_interdit))
	{
		echo '<div class="erreur">Ce titre est interdit car il existe un dossier portant ce nom.</div>';
	}
	
	else if(empty($description))
	{
		$alerte1 ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie de description.</div>';
	}
	
	//Si tout est ok
	else
	{
		if(is_numeric($_POST["cat_parent"]))
		
			// on enregistre les données
			$result = pg_query("INSERT INTO csf_categories VALUES (DEFAULT, '".pg_escape_string($titre)."', '".pg_escape_string($description)."', '".pg_escape_string($slug)."', '".pg_escape_string($_POST["cat_parent"])."' ) ");
		
		else
		
			// on enregistre les données
			$result = pg_query("INSERT INTO csf_categories VALUES (DEFAULT, '".pg_escape_string($titre)."', '".pg_escape_string($description)."', '".pg_escape_string($slug)."' ) ");
					
		//Si il y a une erreur
		if (!$result) {
			die('Requête invalide : ' . pg_last_error());
		}
		
		else{
			
			//Si tout est ok, on informe le webmaster
			$message_ok = '<div class="ok"><a name="ok"></a>Catégorie enregistrée avec succès ! Redirection en cours... <img src="images/loading.gif" alt="Loading"/></div>';
			
			//on masque le formulaire
			$masquer_formulaire = 1;
			
			//----------------- Création des dossiers de(s) catégorie(s) -------------------------------
			
			//
			if(empty($categorie_parent))
			{
				//on crée le nom du dossier
				$dossier_categorie = "../".OptimiseUrl($titre);
				
				//si le dossier n'existe pas, on le créé
				if (!file_exists($dossier_categorie)) 
				{
					//on crée automatiquement un dossier
					mkdir ("$dossier_categorie", 0777);
					
					//On crée un fichier index dans ce même dossier pour lister les articles de la dite catégorie
					$fichier_a_ouvrir = fopen (''.$dossier_categorie.'/index.php', "w+");
					
					//on écrit le code php suivant
					fwrite($fichier_a_ouvrir,"<?php include('../categorie.php');?>");
					
					//on ferme
					fclose ($fichier_a_ouvrir);
				
				}	
			}
			
			else {
			
				//on crée le nom du dossier parent et du dossier enfant
				$dossier_categorie_parent = "../".OptimiseUrl($categorie_parent);
				$dossier_categorie_enfant = $dossier_categorie_parent."/".OptimiseUrl($titre);
				
				//si le dossier parent n'existe pas, on le crée
				if (!file_exists($dossier_categorie_parent)) 
				{
					//on crée automatiquement un dossier
					mkdir ("$dossier_categorie_parent", 0777);
					
					//On crée un fichier index dans ce même dossier pour lister les articles de la dite catégorie
					$fichier_a_ouvrir = fopen (''.$dossier_categorie_parent.'/index.php', "w+");
					
					//on écrit le code php suivant
					fwrite($fichier_a_ouvrir,"<?php include('../categorie.php');?>");
					
					//on ferme
					fclose ($fichier_a_ouvrir);
					
					//si le dossier n'existe pas, on le crée
					if (file_exists($dossier_categorie_parent) && !file_exists($dossier_categorie_enfant)) {
						
						//on crée automatiquement un dossier
						mkdir ("$dossier_categorie_enfant", 0777);
						
						//On crée un fichier index dans ce même dossier pour lister les articles de la dite catégorie
						$fichier_a_ouvrir = fopen (''.$dossier_categorie_enfant.'/index.php', "w+");
						
						//on écrit le code php suivant
						fwrite($fichier_a_ouvrir,"<?php include('../../categorie.php');?>");
						
						//on ferme
						fclose ($fichier_a_ouvrir);
					
					}
				}
				
				else {
					
					//si le dossier enfant n'existe pas, on le crée
					if (!file_exists($dossier_categorie_enfant)) 
					{
						
						//on crée automatiquement un dossier
						mkdir ("$dossier_categorie_enfant", 0777);
						
						//On crée un fichier index dans ce même dossier pour lister les articles de la dite catégorie
						$fichier_a_ouvrir = fopen (''.$dossier_categorie_enfant.'/index.php', "w+");
						
						//on écrit le code php suivant
						fwrite($fichier_a_ouvrir,"<?php include('../../categorie.php');?>");
						
						//on ferme
						fclose ($fichier_a_ouvrir);
					
					}
					
					else {
					
						//On crée un fichier index dans ce même dossier pour lister les articles de la dite catégorie
						$fichier_a_ouvrir = fopen (''.$dossier_categorie_enfant.'/index.php', "w+");
						
						//on écrit le code php suivant
						fwrite($fichier_a_ouvrir,"<?php include('../../categorie.php');?>");
						
						//on ferme
						fclose ($fichier_a_ouvrir);
					
					}
				}
			
			}
			
			//--------------------------------------------------------------------------------
			
			//on redirige vers l'index
			echo '<script type="text/javascript"> window.setTimeout("location=(\'index.php\');",750) </script>';
		}
		
	}//On ferme else
	
}//On ferme if(isset($_POST["Valider"]))

?>

<?php if(isset($message_ok)) echo $message_ok;

//On masque le formulaire
if($masquer_formulaire == 0) { 

?>
 
<form action="#ok" method="post">

	<?php if(isset($alerte0)) echo $alerte0;?>
	<?php if(isset($alerte_categorie_exist)) echo $alerte_categorie_exist;?>

	<p>Titre de la catégorie* :<br/>
		<input name="titre" size="65" value="<?php if (!empty($_POST["titre"])) { echo stripcslashes(htmlspecialchars($_POST["titre"],ENT_QUOTES)); } ?>" type="text"/>
	</p>

	<?php if(isset($alerte1)) echo $alerte1;?>

	<p>Description de la catégorie :<br/>

		<textarea name="description" rows="10" cols="50" >
		
            <?php

            if (!empty($_POST["description"])) {
                echo stripcslashes(htmlspecialchars($_POST["description"],ENT_QUOTES));
            }

            ?>
		
		</textarea>
		
	</p>
	
	<p>Catégorie parent :<br/>
	
		<select name="cat_parent">
		
			<option value="">Sélectionnez une catégorie</option>
			
			<?php
			
			//On affiche les catégories
			$cat_parent = pg_query("SELECT categorie_id, categorie_name FROM csf_categories WHERE categorie_parent IS NULL ORDER BY categorie_id ASC");
			
			while($affiche = pg_fetch_array($cat_parent))
			{
				echo '<option value="'.$affiche['id'].'"';
				if(isset($_POST["cat_parent"]) && $_POST["cat_parent"] == $affiche['id']){echo "selected = 'selected'";}
				echo '>'.$affiche['categorie_name'].'</option>';
			}
			
			?>
		
		</select>
		
    </p>
	 
	<p>
		<input name="Valider" value="Valider" type="submit"/>
		<input name="Effacer" value="Effacer" type="reset"/>
	</p>

</form>

<?php

//Fin masque formulaire

}

?>

</div>
 
<?php include('footer.php');?>
 
</div>
 
</body>
 
</html>