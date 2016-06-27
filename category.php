<?php

$catSlug = $_GET['q'];

include('db_connect.php');
include('fonctions.php');

db_open();

$categorie = pg_query("SELECT categorie_id, categorie_name, categorie_description, categorie_parent FROM csf_categories WHERE categorie_slug = '".pg_escape_string($catSlug)."'");

//$categorie_enfant = null;

while($cat = pg_fetch_array($categorie))
{
	$categorie_enfant = pg_query("SELECT categorie_id, categorie_name, categorie_description, categorie_slug FROM csf_categories WHERE categorie_parent = ".$cat['categorie_id']);
	
	$id_categorie = $cat['categorie_id'];
	
	$titre_categorie = $cat['categorie_name'];
}

while($subCat = pg_fetch_array($categorie_enfant))
{
	
	$description_sub_categorie = $subCat['categorie_description'];
	
	$subcatSlug = $subCat['categorie_slug'];
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $titre_categorie;  ?></title>
	<meta name="Description" content="<?php if(isset($description_categorie)) echo $description_categorie; ?>" />
</head>
 
<body>
 
<?php //include('menu.php');?>

<?php //include('formulaire.php');?>

<h1>

<?php 

//echo $slug;

?>

</h1>

<p class="fil-ariane">
	<strong>
		<a title="<?php echo $titre_categorie;?>" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>"></a>
	</strong>
</p>

<?php

if(false)
{
	$page = pg_query("SELECT post_title, post_description, post_slug, post_date FROM csf_posts WHERE post_categorie = ".($id_categorie).
			" AND post_approved = '1' ORDER BY post_id DESC");

	while($affiche = pg_fetch_array($page))
	{
		echo '<div><h2 class="h2"><a title="'.$affiche['post_title'].'" href="http://'.$_SERVER['HTTP_HOST'].'/'.$catSlug.'/'.$subCatSlug .'/'.
			 sansPointPhp($affiche['post_slug']).'">'.$affiche['post_title'].'</a></h2><p>'.tronquer(nl2br($affiche['post_description'])).'<br/><br/><span>'.
			 convertDate($affiche['post_date']).'</span></p></div>';
	}
}

else
{
	
}

?>

</div>
<?php //include('footer.php'); ?>
</div>
 
</body>
</html>