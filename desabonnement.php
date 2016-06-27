<?php

include('fonctions.php');
include('db_connect.php');

db_open();

$adresse_email = htmlspecialchars(stripcslashes($_GET["contact"]));
$id_commentaire = htmlspecialchars(stripcslashes($_GET["id"]));

if(isset($_GET["contact"]) && isset($_GET["id"])){

    $verif = pg_query("SELECT comment_email, comment_id FROM csf_comments WHERE comment_id = '".pg_escape_string($id_commentaire)."' AND comment_email='".pg_escape_string($adresse_email)."' AND comment_followed = '1' LIMIT 1");
	
	if(pg_num_rows($verif) == 0)
    {
        $existe_pas = '<div class="erreur"><p>Erreur! Soit la ressource demandée n\'existe pas, soit vous avez déjà effectué cette action !</p></div>';
    }
	
	else
    {
        $result = pg_query("UPDATE csf_comments SET comment_followed = '0' WHERE comment_id= '".pg_escape_string($id_commentaire)."' AND comment_email = '".pg_escape_string($adresse_email)."' LIMIT 1");
        $ok = '<div class="ok"><p>La modification à été effectué avec succès. Vous ne recevrez plus de réponse concernant ce sujet.</p></div>';
    }
	
	}

	else{
		$erreur = '<div class="erreur"><p>Une erreur s\'est produit concernant le lien de désabonnement que vous avez reçut. Merci de renvoyer le mail dans son intéégralité au webmaster avec le motif de renvoie.</p></div>';
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Désabonnement</title>
	<meta name="Description" content="Désabonnement" />
	<meta name="robots" content="noindex, nofollow" />
	<link rel="canonical" href="http://<?php echo $_SERVER['HTTP_HOST'];?>/desabonnement.php" /> <link href="http://<?php echo $_SERVER['HTTP_HOST'];?>/style.css" rel="stylesheet" type="text/css"/>
</head>
 
<body>
 
<div id="moncadre">

<?php include('menu.php');?>

<div class="cadrecentrale">

<h1>Désabonnement</h1>

<div class="cadre">

	<?php if(isset($existe_pas)) echo $existe_pas;?>
	<?php if(isset($ok)) echo $ok;?>
	<?php if(isset($erreur)) echo $erreur;?>

</div>

<?php include('footer.php');?>

</div>
 
</body>

</html>