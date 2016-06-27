<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
 
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="style.css" rel="stylesheet" type="text/css"/>
	<title>Commentaire</title>
 
</head>
 
<body>
 
<div id="moncadre">

<?php include('menu.php');?>
 
<div class="cadrecentrale">
 
<h1>Commentaire</h1>

<span style="border:1px solid black;margin:5px;padding:5px;" class="attentetd">En attente</span>
<span style="border:1px solid black;margin:5px;padding:5px;" class="validertd">Valider</span>
<span style="border:1px solid black;margin:5px;padding:5px;" class="bannitd">Banni</span>

<?php

include '../config.php';
include('../fonctions.php');

//On se connecte à la base de données
include('../db_connect.php');
db_open();

//On sélectionne les données des catégories
$result = pg_query("SELECT comment_id, comment_pseudo, comment_date, comment_approved, comment_post FROM csf_comments ORDER BY comment_id DESC LIMIT 25");

//Si rien, on informe le webmaster
if(pg_num_rows($result) == 0)
{
    echo '<div class="cadre"><p>Aucun commentaire.</p></div>';
}

//S'il y a quelque chose, on affiche
else {
 
    echo '<table style="width: 100%;" cellpadding="2" cellspacing="2"><tbody><tr><td class="hauttd">Pseudo/Date</td><td class="hauttd">Modifier/valider</td><td class="hauttd">Répondre</td></tr>';
    
	while($affiche = pg_fetch_array($result))
    {
        //On attribue une couleur différente selon le statut
        if($affiche['comment_approved'] == 0){$td='<td class="attentetd">';}
        if($affiche['comment_approved'] == 1){$td='<td class="validertd">';}
        if($affiche['comment_approved'] == 2){$td='<td class="bannitd">';}
        
		//On affiche les données
        echo '<tr>'.$td.'Commentaire de '.$affiche['pseudo'].' déposé le '.convertDate($affiche['date']).'</td>'.$td.'<a href="modifier-commentaire.php?id='.$affiche['id'].'&article='.$affiche['id_article'].'&statut='.$affiche['validation'].'"><img src="images/modifier.png" alt="Modifier"/></a></td>'.$td.'';
       
	    //Si c'est l'admin, il ne peut se répondre à lui même donc on bloque l'image
        //Si le commentaire n'a pas été validé, on ne peut pas répondre également
        if($affiche['comment_pseudo']!=$pseudo_admin AND $affiche['comment_approved'] == 1){
            echo '<a href="repondre-commentaire.php?id='.$affiche['comment_id'].'&article='.$affiche['comment_post'].'&pseudo='.$affiche['comment_pseudo'].'"><img src="images/repondre.png" alt="Répondre"/></a>';
        }
		
        echo '</td></tr>';
    }
	
    echo '</tbody></table>';
    
}//On ferme else

?>
  
</div>
 
<?php include('footer.php');?>
 
</div>
 
</body>
 
</html>