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
<title>Administration</title>
</head>

<body>

<div id="moncadre">

<?php include('menu.php');?>

<div class="cadrecentrale">

<h1>Administration</h1>

<?php
	
$commentaire_en_attente = pg_query("SELECT comment_id FROM csf_comments WHERE comment_approved = '0'");
$total_commentaire_en_attente = pg_num_rows($commentaire_en_attente);

echo '<p>Commentaire en attente : '.$total_commentaire_en_attente.'</p>';

//On ajoute quelques infos
echo '<h1>Informations du fichier <a href="modifier-site.php?fichier=fonctions.php">fonctions.php</a></h1>';
echo '<p>Nombre d\'article affiché par page dans les catégories : ';

if(empty($messagesParPage))
{ 
    echo '<span class="article-attente"></span>Vous devez renseigner un chiffre supérieure à zéro!</p>';
}
 
else
{
    echo ''.$messagesParPage.'</p>';
}
 
echo '<p>Nombre d\'article affiché sur la Home Page : ';

if(is_numeric($nombre_article_csf_home))
{
    echo ''.$nombre_article_csf_home.'</p>';
}

else
{
    echo '<span class="article-attente"></span>Vous devez renseigner un chiffre !</p>';
}
 
echo '<p>Lien vers le formulaire de contact : ';

if($afficher_lien_formulaire_contacte == 1)
{ 
    echo 'Affiché</p>';
} 

else
{ 
    echo 'Non affiché</p>';
}
 
echo '<p>Adresse email : ';

if(!empty($adresse_email))
{ 
    echo ''.$adresse_email.'</p>';
} 

else
{ 
    echo '<span class="article-attente"></span>Aucune adresse email renseigné</p>';
}
 
if($afficher_lien_formulaire_contacte == 1 AND empty($adresse_email))
{
    echo '<p><span class="article-attente"></span>Vous affichez le formulaire de contacte mais aucune adresse email n\'est renseigné !</p>';
}
 
echo '<p>Pseudo utilisé pour répondre aux commentaires : ';

if(!empty($pseudo_admin))
{ 
    echo ''.$pseudo_admin.'</p>';
} 

else
{ 
    echo '<span class="article-attente"></span>Aucun pseudo renseigné</p>';
}

?>
	</div><!--cadrecentrale-->

<?php include('footer.php');?>

</div><!--moncadre-->

</body>

</html>