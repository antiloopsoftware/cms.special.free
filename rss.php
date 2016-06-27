<?php

include 'config.php';

// On indique que c'est du xml
header("Content-type: application/xml");

include('fonctions.php');

//On se connecte à la base de données
include('db_connect.php');

db_open();

//On sélectionne les données de la Home Page
$index = pg_query("SELECT home_title, home_description FROM csf_home");
 
while($csf_home = pg_fetch_array($index))
{
	$titre=$csf_home['home_title'];
	$description=$csf_home['home_description'];
}
 
//Entête du flux rss
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n <rss version=\"2.0\">\n <channel>\n <title>$titre</title>\n <link>$url</link>\n <description>$description</description>\n <language>fr</language>\n\n";

//On sélectionne les données des pages
$result = pg_query("SELECT post_title, post_description, post_slug, post_date, post_categorie FROM csf_posts WHERE post_approved = '1' ORDER BY post_id ASC LIMIT 20");
 
while($affiche = pg_fetch_array($result))
{
	//On sélectionne les catégories qui correspondent aux pages
	$result1 = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_id = ".$affiche['post_categorie']."");
	
	while($affiche1 = pg_fetch_array($result1))
	{
		//On affiche les flux
		echo '<item> <title>'.$affiche['titre'].'</title> <link>'.$url.'/'.$affiche1['categorie_slug'].'/'.sansPointPhp($affiche['post_slug']).'</link> <description><![CDATA['.tronquer(nl2br($affiche['post_description'])).']]></description> <pubDate>'.date ("D, d M Y H:i:s +0000", $affiche['post_date']).'</pubDate> </item>';
	}
}

// Fermeture de la connexion à la base de données
db_close();

//On ferme le flux rss
echo "</channel>\n</rss>";

?>