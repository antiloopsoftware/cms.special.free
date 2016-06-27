<?php

//On ouvre une sessions
session_start();

//On arrive pour la première fois, donc rien n'est transmit
if(empty($_POST["fichier"]) && empty($_GET["fichier"])){
	$_SESSION['fichier'] = "index.php";
}

//Sinon si  la méthode POST est vide, ça passe par la méthode GET
else if(empty($_POST["fichier"])){
    $_SESSION['fichier'] = $_GET["fichier"];
}

//Sinon, la sessions correspond à la méthode POST
else {
    $_SESSION['fichier'] = $_POST["fichier"];
}

//On inclue le fichier des fonctions
include('../fonctions.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Éditer le fichier</title>
	<link href="style.css" rel="stylesheet" type="text/css"/>
	<style type="text/css" media="screen">
		#editor { 
			margin: 0px;
			position: relative;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			width: 820px;
			height: 820px;
			text-align: left;
		}
	</style>
	
</head>

<body>

<div id="moncadre">

<?php include('menu.php');?>

<div class="cadrecentrale">

<h1>Éditeur de fichier</h1>

<form method="post" action="">

<select name="fichier" onchange="javascript:submit(this)">

<?php

//On boucle sur le tableau $fichier_ok pour le formulaire
foreach($fichier_ok AS $valeur)
{
    echo '<option value="'.$valeur.'"';
    if ($_SESSION['fichier']==$valeur) {echo "selected='selected'";}
    echo '>'.$valeur.'</option>';
}

?>

</select>
<br/>
<br/>

<?php

//Si la session existe
if(isset($_SESSION['fichier'])){

	if($_SESSION['fichier'] == "fonctions.php"){
		echo '<div class="erreur">Attention : Si vous supprimez un dossier de la variable «$dossier_interdit» et que vous créez ou renommez une catégorie avec un nom de dossier existant sur votre serveur, celui-ci sera effacé ou supprimé ou remplacé et provoquera une erreur!</div>';
	}
	
	//Si le fichier n'existe pas
	if(!file_exists('../'.$_SESSION['fichier'].'')){
		echo '<div class="erreur">Le fichier '.$_SESSION['fichier'].' n\'existe pas à la racine du site!</div>';
	}

	//Si le fichier ne fait pas partie de la liste $fichier_ok
	else if(!in_array($_SESSION['fichier'] , $fichier_ok)){
		echo '<div class="erreur">Ce fichier est interdit d\'accès</div>';
	}

	//Tout semble ok   
	else{
	
		//r+ » Ouvre le fichier en lecture et écriture et place le pointeur au début du fichier.
		$fichier_a_ouvrir = fopen ('../'.$_SESSION['fichier'].'', "r+");
		
		echo '<form action="" method="post">';
		
		echo '<div id="editor">';
		echo '</div>';
		
		echo '<textarea name="modif" id="modif">';
		
		//On boucle et tant que l'on n'est pas à la fin du fichier, on continue de le lire. La fonction feof vérifie si on se trouve à la fin du fichier.
		while(!feof($fichier_a_ouvrir))
		{
			$contenu_du_fichier = fgets($fichier_a_ouvrir, 1024);
			
			//Affichage du contenu dans un textarea
			echo htmlspecialchars($contenu_du_fichier);
		}
		
		echo '</textarea>';
		
		//On ferme le fichier
		fclose ($fichier_a_ouvrir);
		
		echo '<div style="margin:25px"><input name="Valider" value="Valider" type="submit"/> <input name="Effacer" value="Effacer" type="reset"/></div></form>';
	
		//Si action de valider
		if(isset($_POST["Valider"])){
		
			//On ouvre le fichier et on l'efface
			$fichier_a_ouvrir = fopen ('../'.$_SESSION['fichier'].'', "w+");
			
			//On écrit dans le fichier ce que contient le textarea
			fwrite($fichier_a_ouvrir,"".stripcslashes($_POST["modif"])."");
			
			//on ferme
			fclose ($fichier_a_ouvrir);	
			
			//On redirige pour que la modification soit bien prise en compte
			 echo '<script type="text/javascript"> window.setTimeout("location=(\'editer-fichier.php?fichier='.$_SESSION['fichier'].'\');",10) </script>';
			}   
		}
	}

	else{
		echo '<b>Sélectionnez un fichier</b>';
	}

?>
	
<?php include('footer.php');?>

</div>

<script src="src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="jquery-2.0.3.min.js" type="text/javascript" charset="utf-8"></script>
<script>
	   var editor = ace.edit("editor");
	   editor.setTheme("ace/theme/twilight");
	   editor.getSession().setMode("ace/mode/php");
		var textarea = $('#modif').hide();
		editor.getSession().setValue(textarea.val());
		editor.getSession().on('change', function(){
		  textarea.val(editor.getSession().getValue());
		});
</script>

</body>

</html>