<?php

include '../config.php';
include('../fonctions.php');

session_start();
$IsAuthorized = true;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Réponse au commentaire</title>
	<link href="style.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript" src="../ckeditor/ckeditor.js"></script> 

</head>

<body>

<div id="moncadre">

<?php include('menu.php');?>

<div class="cadrecentrale">

<h1>Réponse au commentaire</h1>

<?php

//on récupère les variables qui transites par l'url
$id_transmit = $_GET['id'];
$id_article = $_GET['article'];
$pseudo_transmit = $_GET['pseudo'];

/*$statut permet d'indiquer que c'est la première fois que ce commentaire est validé (même si ce n'est pas vrai) et donc d'envoyer un mail aux abonnés du sujet.
L'admin peut donc répondre autant de fois qu'il veut à un même commentaire tout en prévenant les abonnés du sujet.*/
$statut = "";

//On se connecte à la base de données
include('../db_connect.php');
db_open();

//On va chercher les informations sur l'article à qui appartient le commentaire
$infosArticle = pg_query("SELECT post_title, post_slug, post_categorie FROM csf_posts WHERE post_id = '".pg_escape_string($id_article)."'");

while($afficheInfosArticle = pg_fetch_array($infosArticle))
{
	$titre_article = $afficheInfosArticle['post_title'];
	$url_article = $afficheInfosArticle['post_slug'];
	$id_categorie = $afficheInfosArticle['id_categorie'];
}

//On va chercher la catégorie à qui appartient le commentaire
$infosCategories = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_id = '".pg_escape_string($id_categorie)."'");

while($afficheinfosCategoriess = pg_fetch_array($infosCategories))
{
	$dossier_article=$afficheinfosCategoriess['categorie_slug'];
}

//On construit l'url de l'article et on supprime l'extension
$url_commentaire = 'http://'.$_SERVER['HTTP_HOST'].'/'.$dossier_article.'/'.sansPointPhp($url_article).'#'.$id_transmit.'';

//initialisation du masquage du formulaire
$masquer_formulaire = 0;

//Traitement du formulaire
if(isset($_POST["Valider"]))
{
	$formulaire_pseudo = htmlspecialchars(stripcslashes(trim($_POST["pseudo"])));
	$formulaire_email = stripcslashes($_POST["email"]);
	$formulaire_commentaire = stripcslashes($_POST["commentaire"]);
	$formulaire_valide = $_POST["valide"];
	$ip = getIp();
	$timestamp = time();

	//Vérification du formulaire
	if(empty($formulaire_pseudo)){
		$alerte_pseudo ='<div class="erreur"><a name="ok"></a>Le pseudo est vide. Veuillez remplir la variable adéquate dans le fichier fonctions.php.</div>';
	}
	else if(empty($formulaire_email)){
		$alerte_email ='<div class="erreur"><a name="ok"></a>Votre email est vide. Veuillez remplir la variable adéquate dans le fichier fonctions.php.</div>';
	}	
	else if(empty($formulaire_commentaire)){
		$alerte_commentaire ='<div class="erreur"><a name="ok"></a>Le commentaire est vide.</div>';
	}

	//Si tout est ok
	else
	{
		// on enregistre les données
		$result = pg_query("INSERT INTO csf_comments VALUES
                        (
                        '".pg_escape_string($formulaire_pseudo)."',
                        '".pg_escape_string($formulaire_email)."',
                        '<b>Réponse à @ ".pg_escape_string($pseudo_transmit)." :</b><br/><br/>".pg_escape_string($formulaire_commentaire)."',
                        '".pg_escape_string($ip)."',
                        '".pg_escape_string($timestamp)."',
                        '".pg_escape_string($formulaire_valide)."',
                        '".pg_escape_string("1")."',
                        '".pg_escape_string($id_article)."'
                        )
                    ");

		//Si il y a une erreur
		if (!$result) {
			die('Requête invalide : ' . pg_last_error());
		}
		
		else{
		
			//Si tout est ok, on informe le webmaster
			$message_ok = '<div class="ok"><a name="ok"></a>Commentaire modifiée avec succès ! Redirection automatique en cours...</div>';
			
			//on masque le formulaire
			$masquer_formulaire = 1;
			
			//Si le commentaire est validé pour la première fois 	
			if($formulaire_valide == 1 AND empty($statut)){
			
				//On envoie un mail à ceux qui suivent le sujet tout en excluent le posteur
				$result1 = pg_query("SELECT MAX(comment_id), comment_email, comment_pseudo, comment_followed FROM csf_comments WHERE comment_email != '$formulaire_email' AND comment_followed = '1' AND comment_post = '$id_article' AND comment_approved = '1' GROUP BY comment_email");
				
				//si il y a au moin un résultat
				if(pg_num_rows($result1) >= 1 ){
					
					//on va chercher tout ce qui correspond
					while($envoi_mail = pg_fetch_array($result1))
					{
						$pseudo=$envoi_mail["comment_pseudo"];
						$mail=$envoi_mail["comment_email"];
						$subject1 = "Réponse au commentaire...";
						
						//message   
						$msg1  = "Bonjour $pseudo\n\n";
						$msg1 .= "Un nouveau commentaire concernant l'article « ".$titre_article." » a été déposé et est disponible sur la page $url_commentaire.\n";
						$msg1 .= "Vous pouvez vous désabonner ici http://".ROOT."/desabonnement.php?contact=$mail&id=".$envoi_mail["MAX(comment_id)"]."\n\n";
						$msg1 .= "Cordialement http://".ROOT.".\nCourriel automatique (Ne pas répondre à ce message)";

						//email des posteurs  
						$recipient1 = $mail;
						
						//email de celui qui envoie le mail
						$mailheaders1 = "From: $adresse_email\n";
						$mailheaders1 .= "Reply-To: $adresse_email\n\n";
						
						//on envoie l'email
						mail($recipient1, $subject1, $msg1, $mailheaders1);
						
					}//on ferme la boucle while
					
					//Si il y a une erreur
					if (!$result1) {
						die('Requête invalide : ' . pg_last_error());
					}
				}
				
			}//on ferme if($formulaire_valide==1 AND empty($statut)){
			
		}//on ferme else
		
	}//On ferme else
	
}//On ferme if(isset($_POST["Valider"]))

//Si la variable $message_ok existe et est différente de vide
if(isset($message_ok) && !empty($message_ok)){
	
	echo $message_ok;
	
	//on redirige vers la page d'administration des commentaires
	echo '<script>function redirection(page){ window.location=page; } setTimeout(\'redirection("commentaire.php")\',2000);</script>';
}

//On masque le formulaire si besoin
if($masquer_formulaire == 0) {

	?>
	
	<form action="#ok" method="post">

		<p>Url : <?php echo '<a target="_blank" href="'.$url_commentaire.'">'.$url_commentaire.'</a>';?></p>

		<?php if(isset($alerte_pseudo)) echo $alerte_pseudo;?>
		
		<p>Pseudo :<br/>
		<input name="pseudo" size="65" value="<?php echo $pseudo_admin;?>" type="text"/>
		</p>

		<?php if(isset($alerte_email)) echo $alerte_email;?>
		
		<p>Email :<br/>
		<input name="email" size="65" value="<?php echo $adresse_email;?>" type="text"/>
		</p>

		<?php if(isset($alerte_commentaire)) echo $alerte_commentaire;?>
		
		<p>Commentaire :
            <br/>
            <textarea name="commentaire" rows="10" cols="50" ><?php

            if (!empty($_POST["commentaire"])) {
                echo stripcslashes(htmlspecialchars($_POST["commentaire"],ENT_QUOTES));

            }

            ?>

            </textarea>

            <script type="text/javascript">
                CKEDITOR.replace( 'commentaire',
                {
                    filebrowserBrowseUrl : '/ckfinder/ckfinder.html',
                    filebrowserImageBrowseUrl : '/ckfinder/ckfinder.html?type=Images',
                    filebrowserFlashBrowseUrl : '/ckfinder/ckfinder.html?type=Flash',
                    filebrowserUploadUrl : '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&currentFolder=/archive/',
                    filebrowserImageUploadUrl : '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&currentFolder=/cars/',
                    filebrowserFlashUploadUrl : '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
                }
                );
            </script>
		</p>

		<p>Statut du commentaire :<br/>
            <select name="valide">
                <option value="">Sélectionnez un choix</option>
                <option value="0"<?php if (isset($valide) && $valide == 0) {echo "selected='selected'";}?>>En attente</option>
                <option value="1"<?php if (isset($valide) && $valide == 1) {echo "selected='selected'";}?>>Valider</option>
            </select>
		</p>

		<p>
			<input name="Valider" value="Valider" type="submit"/>
			<input name="Effacer" value="Effacer" type="reset"/>
		</p>
		
	</form>
	
	<?php
	
}//Fin du masquage du formulaire

?>

</div>

<?php include('footer.php');?>

</div>

</body>

</html>