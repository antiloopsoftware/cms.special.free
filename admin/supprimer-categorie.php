<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
 
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>Supprimer une catégorie</title>
  <link href="style.css" rel="stylesheet" type="text/css"/>
 
</head>
 
<body>
 
<div id="moncadre">
 
<?php include('menu.php');?>
 
<div class="cadrecentrale">
 
<h1>Supprimer une catégorie</h1>
 
<?php

if(isset($_GET["id"]))
{
	$id = $_GET["id"];
	
}

$masquer_formulaire = 0;

//On se connecte à la base de données
include('../db_connect.php');

db_open();

include('../fonctions.php');

if(isset($_GET["id"]))
{ 
	//On vérifie que la catégorie ne comporte aucun article
	$result = pg_query("SELECT id_categorie FROM csf_posts WHERE id_categorie = $id");

	if(pg_num_rows($result) != 0)
	{
		echo '<div class="erreur"><p>Cette catégorie comporte actuellement un ou des articles ! Il vous est donc impossible de la supprimer.<br/><a href="javascript:history.back()">Retour page précédente</a></p></div>';
	   
		//on masque le formulaire
		$masquer_formulaire = 1;
	}
	
	//On vérifie que la catégorie ne possède aucune catégorie enfant
	$result2 = pg_query("SELECT categorie_parent FROM csf_categories WHERE categorie_parent = $id");

	if(pg_num_rows($result2) != 0)
	{
		echo '<div class="erreur"><p>Cette catégorie possède actuellement une ou des catégories enfants ! Il vous est donc impossible de la supprimer.<br/><a href="javascript:history.back()">Retour page précédente</a></p></div>';
			
		//on masque le formulaire
		$masquer_formulaire = 1;
	}
}

if(isset($_POST["Supprimer"]))
{
    //On efface la catégorie
    $efface_donnees = pg_query("DELETE FROM csf_categories WHERE categorie_id = $id");
 
    //Si il y a une erreur
    if (!$efface_donnees) {
        die('Requête invalide : ' . pg_last_error());
    }
	
    else {
	
		//On vérifie que la catégorie ne possède aucune catégorie enfant
		$result3 = pg_query("SELECT categorie_parent FROM csf_categories WHERE categorie_id = $id");
		
		if (is_null($result3['categorie_parent']))
		{
			//url du dossier à supprimer
			$dir = '../'.$_GET["slug"].'';
		}
		
		else
		{	
			//On cherche la catégorie parent
			$result4 = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_id = ".$result3['id_parent_categorie']);
			
			//url du dossier à supprimer
			$dir = '../'.$result4['categorie_slug'].'/'.$_GET["slug"].'';
		}
		
        //on supprime le dossier et son contenu
        advRmDir($dir);
 
        //on informe que le message est supprimé
        echo '<div class="ok"><p>La catégorie « '.$_GET["slug"].' » à été supprimé avec succès. <a href=index.php>Retour à l\'administration.</a></p></div>';
       
	   
    }//On ferme else
	
    //on masque le formulaire
    $masquer_formulaire = 1;
   
}// On ferme isset($_POST["Supprimer"]))
 
//on masque le formulaire si tout est ok
if($masquer_formulaire == 0) {

?>
 
<div class="cadre">Attention, vous êtes sur le point de supprimer la catégorie « <?php echo $_GET["slug"];?> »!</div>

<form action="supprimer-categorie.php?id=<?php echo $id;?>&slug=<?php echo $_GET["slug"];?>" method="post">
  <p><input name="Supprimer" value="Supprimer la catégorie N°<?php echo $id;?>" type="submit" /></p>
</form>
 
<?php

}//masquage du formulaire

?>
 
</div>
 
<?php include('footer.php');?>
 
</div>
 
</body>
 
</html>