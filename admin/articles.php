<?php

include('../fonctions.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
 
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Article(s)</title>
	<link href="style.css" rel="stylesheet" type="text/css"/>
 
</head>
 
<body>
 
<div id="moncadre">
 
<?php include('menu.php');?>
 
<div class="cadrecentrale">
 
 <h1>Éditer les articles</h1>
 
 <p><a title="Ajouter un article" href="ajouter-categorie.php">Ajouter un article</a></p>
 
<?php

//On se connecte à la base de données
include('../db_connect.php');

db_open();
 
//On va chercher le nom de la catégorie
//$dossier_article = pg_query("SELECT categorie_name, slug FROM csf_categories WHERE id='".pg_escape_string($id)."'");

/*while($nom_dossier = pg_fetch_array($dossier_article))
{
	$nom_du_dossier = $nom_dossier['categorie_slug'];
	$titre = $nom_dossier['categorie_name'];
}*/
 
//echo '<h1>'.$titre.'</h1>';
 
//On sélectionne les articles
$result = pg_query("SELECT post_id, post_title, post_slug, post_approved, post_categorie FROM csf_posts ORDER BY post_id ASC");

//Si vide, on informe
if(pg_num_rows($result) == 0)
{
	echo '<div class="cadre"><p>Aucun article.</p></div>';
}

//S'il y a quelque chose, on affiche les données
else {
	
	echo '<table style="width: 100%;" cellpadding="2" cellspacing="2"> <tbody> 	<tr> 	<td class="hauttd">Article</td><td class="hauttd">Modifier</td><td class="hauttd">Supprimer</td></tr>';
	
	while($affiche = pg_fetch_array($result))
	{
		//On vérifie si l'article est en attente ou pas
		if($affiche['valide'] == "0")
		{ 
			$attente = '<span class="article-attente"></span>';
		}
		
		else
		{
			$attente = '<span class="article-ok"></span>';
		}
		
		echo '<tr><td>'.$attente.'<a target="_blank" href="http://'.$_SERVER['HTTP_HOST'].'/'.$nom_du_dossier.'/'.sansPointPhp($affiche['post_slug']).'">'.$affiche['post_title'].'</a></td> 	<td><a href="modifier-article.php?id='.$affiche['post_id'].'&slug='.$affiche['post_slug'].'&cat='.$id.'"><img src="images/modifier.png" alt="Modifier"/></a></td> 	<td><a href="supprimer-article.php?id='.$affiche['post_id'].'&slug='.$affiche['post_slug'].'&cat='.$id.'"><img src="images/supprimer.png" alt="Supprimer"/></a></td></tr>';
	}
	
	echo '</tbody></table>';
	
}//On ferme else

?>
 
</div>
 
<?php include('footer.php');?>
 
 
</div>
 
</body>
 
</html>