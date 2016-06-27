<?php

include 'config.php';

// On indique que c'est du xml
header("Content-type: application/xml");

//Ouverture du document
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?> <urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">";       
 
include('fonctions.php');

//On se connecte à la base de données
include('db_connect.php');

db_open();

//On sélectionne les données des pages
$result = pg_query("SELECT post_title, post_date, post_categorie FROM csf_posts WHERE post_approved = '1' ORDER BY post_id ASC LIMIT 20");
 
while($affiche = pg_fetch_array($result))
{
    //On sélectionne les catégories correspondent aux pages
    $result1 = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_id = ".$affiche['post_categorie']);
	
    while($affiche1 = pg_fetch_array($result1))
    {
        echo '<url> <loc>'.$url.'/'.$affiche1['categorie_slug'].'/'.sansPointPhp($affiche['post_slug']).'</loc> <lastmod>'.convertit_date_anglais($affiche['post_date']).'</lastmod> <changefreq>monthly</changefreq> <priority>0.5</priority> </url>';
    }
}

// Fermeture de la connexion à la base de données
db_close();

//Fermeture du document
echo '</urlset>';