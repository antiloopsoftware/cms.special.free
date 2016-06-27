<?php

include 'config.php';

//Si le système de commentaire est affiché directement, on stop tout
if(basename($_SERVER["SCRIPT_NAME"])=="commentaire.php"){
    echo '<b>La ressource demand� est inaccessible!</b>';
    exit();
}

//initialisation du masquage du formulaire
$masquer_formulaire = 0;

if(isset($_POST["Valider"]))
{
    //Variables
    $pseudo = htmlspecialchars(stripcslashes($_POST["pseudo"]));
    $email = htmlspecialchars(stripcslashes($_POST["email"]));
    $commentaire = htmlspecialchars(stripcslashes($_POST["commentaire"]));
    $suivre_sujet = $_POST["suivre_sujet"];
    $capcha = htmlspecialchars(stripcslashes($_POST["capcha"]));
    $verification_capcha = htmlspecialchars(stripcslashes($_POST["verification_capcha"]));
    $ip = getIp();
    $timestamp = time();
    $validation = 0;
	
	//L'auteur du poste est il banni (2)
    $ban = pg_query("SELECT comment_email, comment_ip FROM csf_comments WHERE comment_approved = '2' AND (comment_ip = '".pg_escape_string($ip)."' OR comment_email = '".pg_escape_string($email)."')");
    
	//Si le résultat est différent de zéro, c'est que l'auteur est banni
    if(pg_num_rows($ban) != 0)
    {
        echo '<div class="erreur"><a name="ok"></a>Le système de commentaires vous est refusé !</div>';
    }
	
	//Le pseudo est il celui de l'admin?
    else if($pseudo == $pseudo_admin){
        $alerte_pseudo_admin ='<div class="erreur"><a name="ok"></a>Vous ne pouvez pas utiliser ce pseudo. Merci d\'en choisir un autre.</div>';
    }
	
    //Le pseudo est vide
    else if(empty($pseudo)){
        $alerte_pseudo ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie votre pseudo.</div>';
    }
	
    //L'adresse mail est vide
    else if(empty($email)){
        $alerte_email ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie votre email.</div>';
    }
	
    //on contrôle la validité de l'email
    else if (!preg_match("#^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\.[a-z]{2,4}$#",$email))
    {
        $alerte_email_bis ='<div class="erreur"><a name="ok"></a>Votre email semble ne pas être valide.</div>';
    }
	
    //Le commentaire est vide
    else if(empty($commentaire)){
        $alerte_commentaire ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie votre commentaire.</div>';
    }
	
    //Le posteur n'a pas sélectionné son choix
    else if(empty($suivre_sujet)){
        $alerte_suivre_sujet ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas sélectionné votre choix.</div>';
    }
	
    //Le captcha est vide
    else if(empty($capcha)){
        $alerte_capcha_vide ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie le captcha.</div>';
    }
	
    //Le captcha est incorrecte
        else if($capcha != $verification_capcha){
        $alerte_capcha_erreur ='<div class="erreur"><a name="ok"></a>Le captcha que vous avez saisie est incorrecte.</div>';
    }
	
	//Tout est ok
    else {
        // on enregistre les données
        $result = pg_query("INSERT INTO csf_comments VALUES ('".pg_escape_string($pseudo)."', '".pg_escape_string($email)."', '".pg_escape_string($commentaire)."', '".pg_escape_string($ip)."', '".pg_escape_string($timestamp)."', '".pg_escape_string($validation)."', '".pg_escape_string($suivre_sujet)."', '".pg_escape_string($id)."' ) ");
	
		//Si il y a une erreur
		if (!$result) {
			die('Requète invalide : ' . pg_last_error());
		}
	
		//pas d'erreur d'enregistrement
		else {
			//on informe le webmaster qu'un commentaire a été déposé
			//email de celui qui envoie
			$webmaster = $adresse_email;
			
			//email de celui qui reçoit
			$a_qui_j_envoie = $adresse_email;
			
			//sujet
			$subject = "Commentaire en attente";
			
			//message  
			$msg  = "Bonjour <br/><br/>";
			$msg .= "Un commentaire concernant l'article \" ".$titre." \"est en attente de validation sur votre site <a href=\"http://".$_SERVER['HTTP_HOST']."\">http://".$_SERVER['HTTP_HOST']."</a><br/>";
			$msg .= "Date et heure :\tle ".convertit_timestamp_en_date("$timestamp")."\n";
			
			//permet de savoir qui envoie le mail et d'y répondre
			$mailheaders = "From: $webmaster\n";
			$mailheaders .= "MIME-version: 1.0\n";
			$mailheaders .= "Content-type: text/html; charset= iso-8859-1\n";
			
			//on envoie l'email
			mail($a_qui_j_envoie, $subject, $msg, $mailheaders);
		
			//on informe que le message est enregistré
			$message_ok = '<div class="ok"><a name="ok"></a>Votre commentaire est en attente de validation. Merci de votre participation ;)! Vous allez être redirigé automatiquement d\'ici quelques secondes.<img src="http://'.$_SERVER['HTTP_HOST'].'/images/loading.gif" alt="Loading"/></div>';
			
			//on masque le formulaire
			$masquer_formulaire=1;
			
			//on redirige vers la page en cours pour éviter la répétition d'envoi du formulaire
			echo '<script> function redirection(page){ window.location=page; } setTimeout(\'redirection("'.$_SERVER['REQUEST_URI'].'")\',8000); </script>';
		   
		
		}//on ferme else
    
}//Fermeture de if(isset($_POST["Valider"]))

?>

<span class="commentaire-thematique">Laisser un commentaire</span>
<div class="cadre">

<?php if(isset($message_ok)) echo $message_ok;

//On masque le formulaire
if($masquer_formulaire == 0) {

    ?>
	
    <form class="form" action="#ok" method="post">
    
	<?php if(isset($alerte_pseudo_admin)) echo $alerte_pseudo_admin;?>
    <?php if(isset($alerte_pseudo)) echo $alerte_pseudo;?>
    
	<label for="pseudo">Pseudo:</label>
    <input name="pseudo" size="22" value="<?php if (!empty($_POST["pseudo"])) { echo stripcslashes(htmlspecialchars($_POST["pseudo"],ENT_QUOTES)); } ?>" type="text"/>
    <br/>
    
	<?php if(isset($alerte_email)) echo $alerte_email;?>
    <?php if(isset($alerte_email_bis)) echo $alerte_email_bis;?>
    
	<label for="email">Email:</label>
    <input name="email" size="22" value="<?php if (!empty($_POST["email"])) { echo stripcslashes(htmlspecialchars($_POST["email"],ENT_QUOTES)); } ?>" type="text"/>
    <br/>
    
	<script type="text/javascript" src="http://<?php echo ROOT;?>/ckeditor/ckeditor.js"></script>
    
	<?php if(isset($alerte_commentaire)) echo $alerte_commentaire;?>
    
	<textarea name="commentaire" rows="10" cols="50"><?php
    if (!empty($_POST["commentaire"])) {
        echo stripcslashes(htmlspecialchars($_POST["commentaire"],ENT_QUOTES));
    }
	
    ?>
	
	</textarea>
	
    <script type="text/javascript">
		CKEDITOR.replace( 'commentaire',
			{
				toolbar :
				[
			['Link','-','NumberedList','BulletedList','-','Blockquote','-','Image','Smiley','-','Undo','Redo'],
				]
			});
	</script>
 
    <br/>
	
    <?php if(isset($alerte_suivre_sujet)) echo $alerte_suivre_sujet;?>
    
	<label for="suivre_sujet">Suivre les réponses :</label>
    <select name="suivre_sujet">
    <option value="">Sélectionnez</option>
    <option value="0"<?php if(isset($suivre_sujet) && $suivre_sujet == "0") { echo "selected='selected'";}?>>Non</option>
    <option value="1"<?php if(isset($suivre_sujet) && $suivre_sujet == "1") { echo "selected='selected'";}?>>Oui</option>
    </select>
    <br/>
    
	<?php
		if (isset($alerte_capcha_vide)) echo $alerte_capcha_vide;
		if (isset($alerte_capcha_erreur)) echo $alerte_capcha_erreur;
    ?>
    
	<label for="capcha">Captcha :</label>
    <input name="capcha" size="22" value="<?php if (!empty($_POST["capcha"])) { echo stripcslashes(htmlspecialchars($_POST["capcha"],ENT_QUOTES)); } ?>" type="text"/>
	<span class="ok"><?php echo $pass;?></span>
    <input name="verification_capcha" size="22" value="<?php echo $pass;?>" type="hidden"/>
	<br/>
    <label for="Valider">Action :</label>
    <input name="Valider" value="Valider" type="submit"/>
    <input name="Effacer" value="Effacer" type="reset"/>
    <br/>
    </form>
    </div>
	
	<?php

}//On ferme if($masquer_formulaire == 0)

//On va chercher les commentaires en rapport avec la page
$affiche_commentaire = pg_query("SELECT comment_id, comment_pseudo, comment_content, comment_date FROM csf_comments WHERE comment_post = '".pg_escape_string($id)."' AND comment_approved = '1' ORDER BY comment_id ASC");

//Si il y a quelque chose, on affiche
if(pg_num_rows($affiche_commentaire) != 0)
{
    while($affiche = pg_fetch_array($affiche_commentaire))
    {
        //On change la couleur du cadre des réponses de l'admin
        if($affiche['comment_pseudo'] == $pseudo_admin){
            echo '<div class="cadre-commentaire-admin">';
        }
		
        else{
            echo '<div class="cadre-commentaire">';
        }
		
        echo '<a name="'.$affiche['comment_id'].'"></a> <b>'.$affiche['comment_pseudo'].' <span class="date">'.convertDate($affiche['comment_date']).'</span></b><br/><br/>'.nl2br($affiche['comment_content']).'</div>';
    }
}

	while($affiche = pg_fetch_array($affiche_commentaire))
	{
		//On change la couleur du cadre des réponses de l'admin
		if($affiche['pseudo']==$pseudo_admin){
			echo '<div class="cadre-commentaire-admin">';
		}
		
		else{
			echo '<div class="cadre-commentaire">';
		}
		
		echo '<a name="'.$affiche['comment_id'].'"></a> <b>'.$affiche['comment_pseudo'].' <span class="date">'.convertDate($affiche['comment_date']).'</span></b><br/><br/>'.nl2br($affiche['comment_content']).'</div>';
	}
}

else{
    echo '<div class="cadre-commentaire-admin"><b>'.$pseudo_admin.' <span class="date">'
	.convertDate(time()).'</span></b><br/><br/>Aucun commentaire pour le moment concernant le sujet \" <strong>'.$titre.'</strong> \" !</div>';
}

?>