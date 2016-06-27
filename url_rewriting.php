<?php

include 'config.php';
include('fonctions.php');

//fichier avec extension pour la requète SQL
$path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']);
$nom_du_fichier = $path_parts['basename'];

//fichier sans l'extension utilisé dans le fil d'Ariane
$nom_du_fichier_sans_extension = sansPointPhp($nom_du_fichier);

//On se connecte à la base de données
include('db_connect.php');
db_open();

//On va chercher tout ce qui correspond au nom du fichier
$article = pg_query("SELECT * FROM csf_posts WHERE post_slug = '".pg_escape_string($nom_du_fichier)."'");

//on voit si il y a quelque chose
if(pg_num_rows($article) == 0)
{
    //S'il n'y a rien, on redirige vers l'index
    header("Status : 301 Moved Permanently");
    header('location:http://'.ROOT.'');
    exit();
}

else
{
    //il y a quelque chose
    while($affiche = pg_fetch_array($article))
    {  
        //$id sert pour le système de commentaire
        $id = $affiche['post_id'];
        $titre = $affiche['post_title'];
        $description = $affiche['post_description'];
        $contenu = $affiche['post_content'];
        if(isset($affiche['post_date'])) $date = convertDate($affiche['post_date']);
        $commentaire = $affiche['post_comment'];
		$id_categorie = $affiche['post_categorie'];
    }
	
	$categorie = pg_query("SELECT categorie_name, categorie_slug FROM csf_categories WHERE categorie_id = '".pg_escape_string($id_categorie)."'");
    
	while($affiche_categorie = pg_fetch_array($categorie))
    {
        $titre_categorie = $affiche_categorie['categorie_name'];
        $categorie_slug = $affiche_categorie['categorie_slug'];
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $titre;?></title>
	<meta name="Description" content="<?php echo $description;?>" />

	<link rel="canonical" href="http://<?php echo ROOT;?>/<?php echo $categorie_slug;?>/<?php echo $nom_du_fichier_sans_extension;?>" />
	<link href="http://<?php echo ROOT;?>/style.css" rel="stylesheet" type="text/css"/>
</head>
 
<body>
 
<div id="moncadre">

<?php include('menu.php');?>

<div class="cadrecentrale">

<?php include('formulaire.php');?>

<h1><?php echo $titre;?></h1><span class="date"><?php if(isset($date)) echo $date;?></span>

<p class="fil-ariane"><a title="home" href="http://<?php echo ROOT;?>">home</a> »
	<strong><a title="<?php echo $titre_categorie;?>" href="http://<?php echo ROOT;?>/<?php echo $categorie_slug;?>/"><?php echo $titre_categorie;?></a></strong> »
	<strong><a title="<?php echo $titre;?>" href="http://<?php echo ROOT;?>/<?php echo $categorie_slug;?>/<?php echo $nom_du_fichier_sans_extension;?>"><?php echo $titre;?></a></strong>
</p>

<?php

echo $contenu;

//On va chercher les X articles similaires
$article_similaire = pg_query("SELECT post_title, post_slug FROM csf_posts WHERE post_slug != '".pg_escape_string($nom_du_fichier)."' AND post_approved = '1' AND post_categorie = '".pg_escape_string($id_categorie)."' ORDER BY RAND() LIMIT 5");
 
//Si il y a quelque chose
if(pg_num_rows($article_similaire) != 0)
{
	echo '<span class="commentaire-thematique">Dans la même thématique</span><ul class="articles-similaires">';
		
	//il y a quelque chose
	while($affiche_similaire = pg_fetch_array($article_similaire))
	{
	  echo '<li><strong><a title="'.$affiche_similaire['post_title'].'" href="http://'.ROOT.'/'.$categorie_slug.'/'.sansPointPhp($affiche_similaire['post_slug']).'">'.$affiche_similaire['post_title'].'</a></strong></li>';
	}
		
	echo '</ul>';
}

//On affiche ou pas le système de commentaires
if($commentaire == "1" AND !empty($adresse_email)){
	include('commentaire.php');
}

?>

</div>
<?php include('footer.php');?>
</div>
 

</body>
</html>