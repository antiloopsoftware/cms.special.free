<?php

include '../config.php';
include('../fonctions.php');

//On se connecte à la base de données
include('../db_connect.php');
db_open();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">

<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css"/>
<title>Éditer les catégories</title>
</head>

<body>

<div id="moncadre">

<?php include('menu.php');?>

<div class="cadrecentrale">

<h1>Éditer les catégories</h1>

<p><a title="Ajouter une catégorie" href="ajouter-categorie.php">Ajouter une catégorie</a></p>

<?php

//On sélectionne les données des catégories
$result = pg_query("SELECT categorie_id, categorie_name, categorie_slug FROM csf_categories ORDER BY categorie_id ASC");

//Si rien, on informe le webmaster
if(pg_num_rows($result) == 0)
{
    echo '<div class="cadre"><p>Aucune catégorie.</p></div>';
}
	
//Il y a quelque chose, on affiche
else {

    echo '<table style="width: 100%;" cellpadding="2" cellspacing="2"><tbody><tr><td class="hauttd">Catégories</td><td class="hauttd">Modifier</td><td class="hauttd">Supprimer</td></tr>';
    
	while($affiche = pg_fetch_array($result))
    {
        //On calcul le nombre d'article dans chaque catégorie
        $result1 = pg_query("SELECT post_categorie FROM csf_posts WHERE post_categorie = ".$affiche['categorie_id']."");
        $total = pg_num_rows($result1);
		
        //On vérifie si la catégorie contient des articles en attentes
        $result2 = pg_query("SELECT post_id FROM csf_posts WHERE post_categorie = ".$affiche['categorie_id']." AND post_approved = '0'");
        $attente = pg_num_rows($result2);
		
        if($attente != 0)
        { 
            $attente = '<span class="article-attente"></span>';
        }
		
        else
        {
            $attente = '<span class="article-ok"></span>';
        }
		
        //Fin du calcul
        //On affiche les données
        echo '<tr><td>'.$attente.'<a href="categorie-articles.php?id='.$affiche['categorie_id'].'">'.$affiche['categorie_name'].'</a><span style="float:right;">('.$total.')</span></td><td><a href="modifier-categorie.php?id='.$affiche['categorie_id'].'&slug='.$affiche['categorie_slug'].'"><img src="images/modifier.png" alt="Modifier"/></a></td><td><a href="supprimer-categorie.php?id='.$affiche['categorie_id'].'&slug='.$affiche['categorie_slug'].'"><img src="images/supprimer.png" alt="Supprimer"/></a></td></tr>';
    }
	
    echo '</tbody></table>';
    
}//On ferme else

?>
	</div><!--cadrecentrale-->

<?php include('footer.php');?>

</div><!--moncadre-->

</body>

</html>