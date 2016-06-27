<?php

include '../config.php';
include('../fonctions.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
 
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>Supprimer un article</title> 
  <link href="style.css" rel="stylesheet" type="text/css"/>
 
</head>
 
<body>
 
<div id="moncadre">
 
<?php include('menu.php');?>
 
<div class="cadrecentrale">
 
<h1>Supprimer un article</h1>
 
<?php

$id = $_GET["id"];
$categorie = $_GET["cat"];
 
$masquer_formulaire = 0;

if(isset($id))
{

	//On se connecte à la base de données
	include('../db_connect.php');
	db_open();

	//on va chercher le titre de l'article
	$req_article = pg_query("SELECT post_title FROM csf_posts WHERE post_id = '".pg_escape_string($id)."'");

	while($article = pg_fetch_array($req_article))
	{
		$titre = $article['post_title'];
	}
	 
	echo '<div class="cadre"><p>Attention : vous êtes sur le point de supprimer l\'article « '.$titre.' » ainsi que le ou les commentaires associés à cet article !</p></div>';
	 
}

if(isset($_POST["Supprimer"]))
{
    //on va chercher le nom du dossier correspondant à l'article
    $categorie_article = pg_query("SELECT categorie_slug, categorie_parent FROM csf_categories WHERE categorie_id = '".pg_escape_string($categorie)."'");
    
	while($cat_article = pg_fetch_array($categorie_article))
	{
    	$dossier_cat = $cat_article['categorie_slug'];
    	
    	$cat_parent = $cat_article['categorie_parent'];
	}
    
	if (is_null($cat_parent))
	{
		$dir = $dossier_cat;
	}
	
	else
	{	
		//On cherche la catégorie parent
		$req_cat_parent = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_id = ".$cat_parent);
		
		while($cat_parent = pg_fetch_array($req_cat_parent))
		{
    		$dossier_cat_parent = $cat_parent['categorie_slug'];
		}
		
		//url du dossier à supprimer
		$dir = $dossier_cat_parent.'/'.$dossier_cat;
	}
    
    //On efface l'article
    $efface_donnees = pg_query("DELETE FROM csf_posts WHERE post_id = $id");
 
    //Si il y a une erreur
    if (!$efface_donnees) {
        die('Requête invalide : ' . pg_last_error());
    }
	
    //On efface les commentaires associés à l'article
    $efface_commentaire = pg_query("DELETE FROM csf_comments WHERE comment_post = $id");
 
    //Si il y a une erreur
    if (!$efface_commentaire) {
        die('Requête invalide : ' . pg_last_error());
    }   
	
    else {
        
		//Tout est ok, on vérifie que le fichier correspondant à l'article est présent sur le serveur
        if (file_exists('../'.$dir.'/'.$_GET["slug"].'')) {
            
			//on supprime le fichier correspondant à l'article
            unlink('../'.$dir.'/'.$_GET["slug"].'');
            
			//on informe que l'article est supprimé
            echo '<div class="ok"><p>L\'article « '.$_GET["slug"].' » en base ainsi que le fichier « /'.$dir.'/'.$_GET["slug"].' » ont été supprimé avec succès.</p></div>';
        }
		
        else{
            echo '<div class="erreur">L\'article a bien été supprimé de la base, mais le fichier « /'.$dir.'/'.$_GET["slug"].' » n\'existait pas sur le serveur.<br /></div>';
        
        }
        
    }//On ferme else
	
    //on masque le formulaire
    $masquer_formulaire = 1;
    
}// On ferme isset($_POST["Supprimer"]))

//on masque le formulaire si tout est ok
if($masquer_formulaire == 0) {

?>
 
<form action="supprimer-article.php?id=<?php echo $_GET["id"];?>&slug=<?php echo ''.$_GET["slug"].'';?>&cat=<?php echo $_GET["cat"];?>" method="post">
  <p><input name="Supprimer" value="Supprimer l'article (id = <?php echo $id;?>)" type="submit" /></p>
</form>
 
<?php

}//masquage du formulaire

?>
 
</div>
 
<?php include('footer.php');?>
 
</div>
 
</body>
 
</html>