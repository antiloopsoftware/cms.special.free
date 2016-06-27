<?php

header( 'Content-Type: text/html; charset=UTF-8' );
mb_internal_encoding( 'UTF-8' );

session_start();
$IsAuthorized = true;
 
//On se connecte à la base de données
include('../db_connect.php');
db_open();

include('../fonctions.php');

?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
 
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Ajouter un article</title>
	<link href="style.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript" src="../ckeditor/ckeditor.js" charset="utf-8"></script>
 
</head>
 
<body>
 
<div id="moncadre">
 
<?php include('menu.php');?>
 
<div class="cadrecentrale">
 
<h1>Ajouter un article</h1>

<?php

//Initialisation du masquage du formulaire
$masquer_formulaire = 0;

//Traitement du formulaire
if(isset($_POST["Valider"]))
{
	$titre = stripcslashes(trim($_POST["titre"]));
    $description = stripcslashes(trim($_POST["description"]));
    $contenu = stripcslashes(trim($_POST["contenu"]));
    $categorie = stripcslashes(trim($_POST["cat"]));
    $valide = stripcslashes(trim($_POST["valide"]));
    $commentaire = stripcslashes(trim($_POST["commentaire"]));
	
    //Pour le fichier utilisé par la pseudo url rewriting
    $slug = ''.OptimiseUrl(stripcslashes(trim($titre))).'.php';
 
	//On regarde si le titre existe déjà, sinon ça va écraser le fichier déjà présent
    $article_exist = pg_query("SELECT post_slug FROM csf_posts WHERE post_slug = '".pg_escape_string($slug)."'");
    $total = pg_num_rows($article_exist);
	
	$req_cat = null;
    
	if($total >= 1)
	{
        $alerte_article_exist = '<div class="erreur"><a name="ok"></a>Il existe déjà un article portant ce titre.</div>';
    }
	
	//Vérification du formulaire
    else if(empty($titre))
	{
        $alerte0 = '<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie de titre.</div>';
    }
	
    //else if(empty($description))
	//{
        //$alerte1 = '<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie de description.</div>';
    //}
	
    else if(empty($contenu))
	{
        $alerte2 = '<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie de contenu.</div>';
    }
	
    //else if(empty($categorie))
	//{
        //$alerte3 = '<div class="erreur"><a name="ok"></a>Vous n\'avez pas sélectionné de catégorie.</div>';
    //}
	
    else if(empty($valide))
	{
        $alerte4 = '<div class="erreur"><a name="ok"></a>Vous n\'avez pas sélectionné votre choix.</div>';
    }
	
    else if(empty($commentaire))
	{
        $alerte5 = '<div class="erreur"><a name="ok"></a>Vous n\'avez pas sélectionné votre choix.</div>';
    }
	
	//Si tout est ok
	
	//Pas de catégorie
    else if (empty($categorie))
    {
	echo "INSERT INTO csf_posts VALUES (DEFAULT, '".pg_escape_string($titre)."', '".pg_escape_string($description)."', '".pg_escape_string($contenu)."', 'now()', '".pg_escape_string($slug)."', '".pg_escape_string($valide)."', '".pg_escape_string($commentaire)."') ";
		$result = pg_query("INSERT INTO csf_posts VALUES (DEFAULT, '".pg_escape_string($titre)."', '".pg_escape_string($description)."', '".pg_escape_string($contenu)."', 'now()', '".pg_escape_string($slug)."', '".pg_escape_string($valide)."', '".pg_escape_string($commentaire)."') ");
		
		//Si il y a une erreur
		if (!$result) 
		{
			die('Requête invalide : ' . pg_last_error());
		}
		
		else
		{
			//---------------------- Création du fichier pour le pseudo url rewriting ------------------------------------

			//On vérifie si le fichier existe.
			if(!file_exists('../'.$slug.''))
			{
				//comme il n'existe pas, on le créé
				$fichier_a_ouvrir = fopen ('../'.$slug.'', "w+");
				
				//on écrit le code php suivant
				fwrite($fichier_a_ouvrir,"<?php include('url_rewriting.php');?>");
				
				//on ferme
				fclose ($fichier_a_ouvrir);
			}
			
			//----------------- Le fichier est créé et existe maintenant sur le serveur -------------------------------
			
			//Si tout est ok, on informe le webmaster
			$message_ok = '<div class="ok"><a name="ok"></a>Article (sans catégorie) enregistré avec succès ! Redirection automatique en cours... <img src="images/loading.gif" alt="Loading"/></div>';
			
			//on masque le formulaire
			$masquer_formulaire = 1;
			
			//on redirige vers la catégorie correspondante
			echo '<script type="text/javascript"> window.setTimeout("location=(\'../'.OptimiseUrl($titre).'\');",750) </script>';
		}
    }
	
	else
	{
	    //On va chercher le slug de la catégorie afin d'insérer le fichier au bon endroit
		$req_cat = pg_query("SELECT categorie_parent, categorie_slug FROM csf_categories WHERE categorie_id = '".pg_escape_string($categorie)."'");
        
		$cat_parent = null;
		$nom_dossier_ou_enfant = '';
		
		while($cat = pg_fetch_array($req_cat))
        {
            $cat_parent = $cat['categorie_parent'];
			$nom_dossier_ou_enfant = $cat['categorie_slug'];
        }
		
		if (isset($cat_parent))
		{
			$nom_dossier_parent = null;
			$nom_dossier_enfant = null ;
		
			$req_cat_parent = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_id = '".pg_escape_string($cat_parent)."'");
			
			while($c_parent = pg_fetch_array($req_cat_parent))
			{
				$nom_dossier_parent = $c_parent['categorie_slug'];
			}
			
			// on enregistre les données

			$result = pg_query("INSERT INTO csf_posts VALUES (DEFAULT, '".pg_escape_string($titre)."', '".pg_escape_string($description)."', '".pg_escape_string($contenu)."', 'now()', '".pg_escape_string($slug)."', '".pg_escape_string($valide)."', '".pg_escape_string($commentaire)."', '".pg_escape_string($categorie)."' ) ");
			
			//Si il y a une erreur
			if (!$result) 
			{
				die('Requête invalide : ' . pg_last_error());
			}
			
			else
			{
				//---------------------- Création du fichier pour le pseudo url rewriting ------------------------------------
				
				//On vérifie si le fichier existe.
				if(!file_exists('../'.$nom_dossier_parent.'/'.$nom_dossier_ou_enfant.'/'.$slug.''))
				{
					//comme il n'existe pas, on le crée
					$fichier_a_ouvrir = fopen ('../'.$nom_dossier_parent.'/'.$nom_dossier_ou_enfant.'/'.$slug.'', "w+");
				
					//on écrit le code php suivant
					fwrite($fichier_a_ouvrir,"<?php include('../../url_rewriting.php');?>");
					
					//on ferme
					fclose ($fichier_a_ouvrir);					
				}
				
				//----------------- Le fichier est créé et existe maintenant sur le serveur ------------------------------
				
				//Si tout est ok, on informe le webmaster
				$message_ok = '<div class="ok"><a name="ok"></a>Article enregistré avec succès ! Redirection automatique en cours... <img src="images/loading.gif" alt="Loading"/></div>';
				
				//on masque le formulaire
				$masquer_formulaire = 1;
				
				//on redirige vers la catégorie correspondante
				echo '<script type="text/javascript"> window.setTimeout("location=(\'categorie-articles.php?id='.$categorie.'\');",750) </script>';
			}
		}
		
		else
		{       	
			// on enregistre les données

			$result = pg_query("INSERT INTO csf_posts VALUES (DEFAULT, '".pg_escape_string($titre)."', '".pg_escape_string($description)."', '".pg_escape_string($contenu)."', 'now()', '".pg_escape_string($slug)."', '".pg_escape_string($valide)."', '".pg_escape_string($commentaire)."', '".pg_escape_string($categorie)."' ) ");
			
			//Si il y a une erreur
			if (!$result) 
			{
				die('Requête invalide : ' . pg_last_error());
			}
			
			else
			{
				//---------------------- Création du fichier pour le pseudo url rewriting ------------------------------------

				//On vérifie si le fichier existe.
				if(!file_exists('../'.$nom_dossier_ou_enfant.'/'.$slug.''))
				{
					//comme il n'existe pas, on le créé
					$fichier_a_ouvrir = fopen ('../'.$nom_dossier_ou_enfant.'/'.$slug.'', "w+");
					
					//on écrit le code php suivant
					fwrite($fichier_a_ouvrir,"<?php include('../url_rewriting.php');?>");
					
					//on ferme
					fclose ($fichier_a_ouvrir);
				}
				
				//-----------------Le fichier est créé et existe maintenant sur le serveur-------------------------------
				
				//Si tout est ok, on informe le webmaster
				$message_ok = '<div class="ok"><a name="ok"></a>Article enregistré avec succès ! Redirection automatique en cours... <img src="images/loading.gif" alt="Loading"/></div>';
				
				//on masque le formulaire
				$masquer_formulaire = 1;
				
				//on redirige vers la catégorie correspondante
				echo '<script type="text/javascript"> window.setTimeout("location=(\'categorie-articles.php?id='.$categorie.'\');",750) </script>';
			}
			
		}//isset($cat_parent)
		
	}//On ferme else
    
}//On ferme if(isset($_POST["Valider"]))

?>	

<?php if(isset($message_ok)) echo $message_ok;

//On masque le formulaire
if($masquer_formulaire == 0) {

    ?>
	
    <form action="#ok" method="POST">
 
		<?php if(isset($alerte_article_exist)) echo $alerte_article_exist;?>
		<?php if(isset($alerte0)) echo $alerte0;?>
		
		<p>Titre de l'article :<br/>
			<input name="titre" size="65" value="<?php if (!empty($_POST["titre"])) { echo stripcslashes($_POST["titre"]); } ?>" type="text"/>
		</p>
	 
		<?php if(isset($alerte1)) echo $alerte1;?>
		
		<p>Description de l'article :<br/>
		
			<textarea name="description" rows="10" cols="50" ><?php
			
			if (!empty($_POST["description"])) 
			{
				echo stripcslashes(htmlspecialchars($_POST["description"],ENT_QUOTES));
			}
			
			?>
			
			</textarea>
		
		</p>
	 
		<?php if(isset($alerte2)) echo $alerte2;?>
		<p>Contenu de l'article :<br/>
		
			<textarea name="contenu" rows="10" cols="50" >
			
			<?php
			
			if (!empty($_POST["contenu"])) 
			{
				echo stripcslashes($_POST["contenu"]);
			}
		
			?>
			
			</textarea>
			
			<script type="text/javascript" charset="utf-8">
				CKEDITOR.replace( 'contenu',
				{
					filebrowserBrowseUrl : '/ckfinder/ckfinder.html',
					filebrowserImageBrowseUrl : '/ckfinder/ckfinder.html?type=Images',
					filebrowserFlashBrowseUrl : '/ckfinder/ckfinder.html?type=Flash',
					filebrowserUploadUrl : '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&currentFolder=/archive/',
					filebrowserImageUploadUrl : '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&currentFolder=/cars/',
					filebrowserFlashUploadUrl : '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
				}
				);
			</script>
		</p>
	 
		<?php //if(isset($alerte3)) echo $alerte3;?>
		
		<p>Catégorie :<br/>
		
			<select name="cat">
			
				<option value="">Sélectionnez une catégorie</option>
				
				<?php
				
				//On affiche les catégories
				$categorie = pg_query("SELECT categorie_id, categorie_name FROM csf_categories ORDER BY categorie_id ASC");
				
				while($affiche = pg_fetch_array($categorie))
				{
					echo '<option value="'.$affiche['categorie_id'].'"';
					if(isset($_POST["cat"]) && $_POST["cat"] == $affiche['categorie_id']){echo "selected = 'selected'";}
					echo '>'.$affiche['categorie_name'].'</option>';
				}
				
				?>
				
				</select>
			
		</p>
		
		<?php if(isset($alerte4)) echo $alerte4;?>
		
		<p>Afficher l'article ? :<br/>
		
			<select name="valide">
				<option value="">Sélectionnez un choix</option>
				<option value="1" <?php if(isset($_POST["valide"]) && $_POST["valide"] == "1"){echo "selected='selected'";}?>>Oui</option>
				<option value="0" <?php if(isset($_POST["valide"]) && $_POST["valide"] == "0"){echo "selected='selected'";}?>>Non</option>
			</select>
			
	</p>

	<?php if(isset($alerte5)) echo $alerte5;?>

	<p>Afficher le système de commentaire ? :<br/>
		<select name="commentaire">
			<option value="">Sélectionnez un choix</option>
			<option value="1" <?php if(isset($_POST["commentaire"]) && $_POST["commentaire"] == "1"){echo "selected='selected'";}?>>Oui</option>
			<option value="0" <?php if(isset($_POST["commentaire"]) && $_POST["commentaire"] == "0"){echo "selected='selected'";}?>>Non</option>
		</select>
	</p>

	<p>
		<input name="Valider" value="Valider" type="submit"/>
		<input name="Effacer" value="Effacer" type="reset"/>
	</p>

</form>

<?php

}//Fin masque formulaire

?>
 
</div>
 
<?php include('footer.php');?>
 
</div>
 
</body>
 
</html>