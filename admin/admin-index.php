<?php

//On vérifie que la connexion est autorisée
session_start();
$_SESSION['IsAuthorized'] = true;

include('../fonctions.php');

?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
 
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="style.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
	<title>Home Page</title> 

</head> 
  
<body>  
 
<div id="moncadre">

<?php include('menu.php');?>

<div class="cadrecentrale">

<h1>Gestion de la Home Page du site</h1>

</div>
 
<?php

//On se connecte à la base de données
include('../db_connect.php');

db_open();
 
//Traitement du formulaire
if(isset($_POST["Valider"]))
{
    $titre = htmlspecialchars(stripcslashes(trim($_POST["home_title"])));
    $description = htmlspecialchars(stripcslashes($_POST["home_description"]));
    $contenu = stripcslashes($_POST["home_content"]);
 
    //Vérification du formulaire
    if(empty($titre)){
        $alerte0 ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie de titre.</div>';
    }
	
    else if(empty($description)){
        $alerte1 ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie de description.</div>';
    }

	//Si tout est ok
    else
    {
        /*------------------------Enregistrement des données---------------------------------*/
        
		//on vérifie si il y a un enregistrement
        $verif = pg_query("SELECT * FROM csf_home");
        
		//on voie si il y a quelque chose
        if(pg_num_rows($verif) == 0)
        {
            //Si il n'y a rien, on enregistre les données
            $result = pg_query("INSERT INTO csf_home VALUES ('".pg_escape_string($home_title)."', '".pg_escape_string($home_description)."', '".pg_escape_string($home_content)."' ) ");
            
			//Si il y a une erreur
            if (!$result) {
                die('Requête invalide : ' . pg_last_error());
            }
			
            else{
                //Si tout est ok, on informe le webmaster
                $message_ok = '<div class="ok"><a name="ok"></a><b>Home Page enregistrée avec succès !</b></div>';
            }
        }

		//Sinon si la 1ere requête retourne 0, c'est qu'il y a 1 enregistrement
        else if(pg_num_rows($verif) == 1)
        {
            //On modifie les données de l'enregistrement
            $result = pg_query("UPDATE csf_home SET home_title = '".pg_escape_string($titre)."', home_description = '".pg_escape_string($description)."', home_content = '".pg_escape_string($contenu)."' WHERE home_id = 1");
            
			//Si il y a une erreur
            if (!$result) {
                die('Requête invalide : ' . pg_last_error());
            }
			
            else{
                //Si tout est ok, on informe le webmaster
                $message_ok = '<div class="ok"><a name="ok"></a>Home Page enregistrée avec succès !</div>';
            }
        }
        
    }//On ferme else
    
}//On ferme if(isset($_POST["Valider"]))

/*------------------------Fin enregistrement des données---------------------------------*/		

//On sélectionne les données pour les ré afficher dans le formulaire
$index = pg_query("SELECT home_title, home_description, home_content FROM csf_home WHERE home_id = 1");
 
while($csf_home = pg_fetch_array($index))
{
    $titre=$csf_home['home_description'];
    $description=$csf_home['home_description'];
    $contenu=$csf_home['home_content'];
}

?>

<?php
if(isset($message_ok)) echo $message_ok;
?>
 
<form action="#ok" method="post">
 
<?php if(isset($alerte0)) echo $alerte0;?>
<p>Titre de la Home Page :<br/>
    <input name="titre" size="65" value="<?php     if(isset($titre) && !empty($titre)) echo $titre; else if(!empty($_POST["titre"])) { echo stripcslashes(htmlspecialchars($_POST["titre"],ENT_QUOTES)); } ?>" type="text"/>
</p>
 
<?php if(isset($alerte1)) echo $alerte1;?>
<p>Description de la Home Page :<br/>
<textarea name="description" rows="10" cols="50" ><?php
if(isset($description) && !empty($description)) echo $description;
else if(!empty($_POST["description"])) {
    echo stripcslashes(htmlspecialchars($_POST["description"],ENT_QUOTES));
}
?></textarea>
</p>
 
<?php if(isset($alerte2)) echo $alerte2;?>
<p>Contenu de la Home Page :<br/>
<textarea name="contenu" rows="10" cols="50" ><?php
if(isset($contenu) && !empty($contenu)) echo $contenu;
else if(!empty($_POST["contenu"])) {
echo stripcslashes(htmlspecialchars($_POST["contenu"],ENT_QUOTES));
}
?></textarea>
<script type="text/javascript">
CKEDITOR.replace( 'contenu',
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
 
<p>
<input name="Valider" value="Valider" type="submit"/>
<input name="Effacer" value="Effacer" type="reset"/>
</p>
</form>
 
<?php include('footer.php');?>
 
</div>
 
</body>
 
</html>