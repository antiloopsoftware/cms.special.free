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
	<title>Modifier un commentaire</title>
	<link href="style.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
 
</head>
 
<body>
 
<div id="moncadre">
 
<?php include('menu.php');?>
 
<div class="cadrecentrale">
 
<h1>Modifier un commentaire</h1>
 
<?php

//on récupère les variables qui transites par l'url
$id_transmit=$_GET['id'];
$id_article=$_GET['article'];
$statut=$_GET['statut'];
 
//On se connecte à la base de données
include('../db_connect.php');
db_open();

//On sélectionne les données pour les afficher dans le formulaire
$article = pg_query("SELECT * FROM csf_comments WHERE comment_id = '".pg_escape_string($id_transmit)."'");

while($affiche = pg_fetch_array($article))
{
    $pseudo = $affiche['comment_pseudo'];
    $commentaire = stripcslashes($affiche['comment_content']);
    $valide = $affiche['comment_approved'];
    $date = convertDate($affiche['comment_date']);
    $email = $affiche['comment_email'];
}

 
 //On va chercher les informations sur l'article à qui appartient le commentaire
$infosArticle = pg_query("SELECT post_title, post_slug, post_categorie FROM csf_posts WHERE post_id = '".pg_escape_string($id_article)."'");

while($afficheInfosArticle = pg_fetch_array($infosArticle))
{
    $titre_article = $afficheInfosArticle['post_title'];
    $url_article = $afficheInfosArticle['post_slug'];
	$id_categorie = $afficheInfosArticle['post_categorie'];
}
 
//On va chercher la catégorie à qui appartient le commentaire
$infosCategories = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_id = '".pg_escape_string($id_categorie)."'");

while($afficheinfosCategoriess = pg_fetch_array($infosCategories))
{
    $dossier_article=$afficheinfosCategoriess['post_slug'];
} 
 
//On construit l'url de l'article et on supprime l'extension
$url_commentaire = 'http://'.ROOT.'/'.$dossier_article.'/'.sansPointPhp($url_article).'#'.$id_transmit.'';

//initialisation du masquage du formulaire
$masquer_formulaire = 0; 

//Traitement du formulaire
if(isset($_POST["Valider"]))
{
    $formulaire_pseudo = htmlspecialchars(stripcslashes(trim($_POST["pseudo"])));
    $formulaire_email = stripcslashes($_POST["email"]);
    $formulaire_commentaire = stripcslashes($_POST["commentaire"]);
    $formulaire_valide = $_POST["valide"];
	
	//Action de supprimer le commentaire
    if($formulaire_valide == 3){
	
        //On efface le commentaire
        $efface_commentaire = pg_query("DELETE FROM csf_comments WHERE comment_id = $id_transmit");
		
        //Si il y a une erreur
        if (!$efface_commentaire) {
            die('Requête invalide : ' . pg_last_error());
        }
		
        else{
            echo '<div class="ok">Le commentaire à été supprimé avec succès. <a href=commentaire.php>Retour à l\'administration.</a></div>';
            exit();
        }   
    }
	
	//Vérification du formulaire
    if(empty($formulaire_pseudo)){
        $alerte_pseudo ='<div class="erreur"><a name="ok"></a>Le pseudo est vide.</div>';
    }
	
    else if(empty($formulaire_commentaire)){
        $alerte_commentaire ='<div class="erreur"><a name="ok"></a>Le commentaire est vide.</div>';
    }
	
	//Si tout est ok
    else
    {
        //On enregistre les données
        $result = pg_query(" UPDATE csf_comments SET comment_pseudo='".pg_escape_string($formulaire_pseudo)."', comment_content = '".pg_escape_string($formulaire_commentaire)."', comment_approved = '".pg_escape_string($formulaire_valide)."' WHERE comment_id = '$id_transmit'");
 
        //Si il y a une erreur
        if (!$result) {
            die('Requête invalide : ' . pg_last_error());
        }
        else{
		
            //Si tout est ok, on informe le webmaster
            $message_ok = '<div class="ok"><a name="ok"></a>Commentaire modifiée avec succès ! Redirection automatique en cours... <img src="images/loading.gif" alt="Loading"/></div>';
               
			 //on masque le formulaire
			$masquer_formulaire=1;
            echo '<script> function redirection(page){ window.location=page; } setTimeout(\'redirection("commentaire.php")\',8000); </script>';
			
			if($formulaire_valide == 1 AND empty($statut)){
			
                //email de celui qui envoie
                $webmaster = $adresse_email;
				
                //email de celui qui reçoit
                $a_qui_j_envoie = $formulaire_email;
				
                //sujet
                $subject = "Commentaire en ligne";
				
                //message  
                $msg  = "Bonjour $formulaire_pseudo<br/><br/>";
                $msg .= "Votre commentaire concernant l'article « ".$titre_article." » à été accepté et est visible sur cette page ".$url_commentaire."<br/><br/>Cordialement,<br/>http://".$_SERVER['HTTP_HOST'].ROOT.".<br/>Courriel automatique (Ne pas répondre à ce message)";
                
				//permet de savoir qui envoie le mail et d'y répondre
                $mailheaders = "From: $webmaster\n";
                $mailheaders .= "MIME-version: 1.0\n";
                $mailheaders .= "Content-type: text/html; charset= iso-8859-1\n";
                
				//on envoie l'email
                mail($a_qui_j_envoie, $subject, $msg, $mailheaders);
				
				//On envoie un mail à ceux qui suivent le sujet tout en excluent le posteur
                $result1 = pg_query("SELECT MAX(comment_id), comment_email, comment_pseudo, comment_followed FROM csf_comments WHERE comment_email != '$formulaire_email' AND comment_followed = '1' AND comment_post = '$id_article' AND comment_approved = '1' GROUP BY comment_email");
               
			   //si il y a au moins un résultat
                if(pg_num_rows($result1) >= 1 ){
                   
				   //on va chercher tout ce qui correspond
                    while($envoi_mail = pg_fetch_array($result1))
                    {
                        $pseudo = $envoi_mail["comment_pseudo"];
                        $mail = $envoi_mail["comment_email"];
                        $subject1 = "Réponse au commentaire...";
                        //message  
                        $msg1  = "Bonjour $pseudo<br/><br/>";
                        $msg1 .= "Un nouveau commentaire concernant l'article « ".$titre_article." » a été déposé et est disponible sur la page $url_commentaire.<br/>";
                        $msg1 .= "Vous pouvez vous désabonner ici http://".$_SERVER['HTTP_HOST']."/desabonnement.php?contact=$mail&id=".$envoi_mail["MAX(comment_id)"]."<br/><br/>";
                        $msg1 .= "Cordialement http://".$_SERVER['HTTP_HOST'].".<br/>Courriel automatique (Ne pas répondre à ce message)";
 
                        //email des posteurs 
                        $recipient1 = $mail;
                        
						//email de celui qui envoie le mail
                        $mailheaders1 = "From: $adresse_email\n";
                        $mailheaders1 .= "MIME-version: 1.0\n";
                        $mailheaders1 .= "Content-type: text/html; charset= iso-8859-1\n";
                        
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
        
    }//On ferme if(isset($_POST["Valider"]))
    
}

?>

<?php if(isset($message_ok)) echo $message_ok;

//On masque le formulaire
if($masquer_formulaire == 0) {
?>

<form action="#ok" method="post">
	
	<p>Commentaire déposé le <?php echo $date;?> <br/> Url : <?php echo '<a target="_blank" href="'.$url_commentaire.'">'.$url_commentaire.'</a>';?></p>
	
	<?php if(isset($alerte_pseudo)) echo $alerte_pseudo;?>
	
	<p>Pseudo :<br/>
	<input name="pseudo" size="65" value="<?php echo $pseudo;?>" type="text"/>
	</p>
	<p>Email :<br/>
	<input name="email" size="65" value="<?php echo $email;?>" type="text"/>
	</p>
	
	<?php if(isset($alerte_commentaire)) echo $alerte_commentaire;?>
	
	<p>Commentaire :<br/>
	
	<textarea name="commentaire" rows="10" cols="50" ><?php echo $commentaire;?></textarea>
	
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
		<option value="0"<?php if ($valide == 0) {echo "selected='selected'";}?>>En attente</option>
		<option value="1"<?php if ($valide == 1) {echo "selected='selected'";}?>>Valider</option>
		<option value="2"<?php if ($valide == 2) {echo "selected='selected'";}?>>Blacklister</option>
		<option value="3"<?php if ($valide == 3) {echo "selected='selected'";}?>>Supprimer</option>
	</select>
	</p>
	 
	<p>
		<input name="Valider" value="Valider" type="submit"/>
		<input name="Effacer" value="Effacer" type="reset"/>
	</p>
	
</form>


<?php

}//Fin masque formulaire

?>

</div>
 
<?php include('footer.php');?>
 
</div>
 
</body>
 
</html>