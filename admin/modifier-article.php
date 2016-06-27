<?php

include '../config.php';
include('../fonctions.php');

session_start();
$_SESSION['IsAuthorized']=true;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
 
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>Modifier un article</title>
  <link href="style.css" rel="stylesheet" type="text/css"/>
  <script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
 
</head>
 
<body>
 
<div id="moncadre">
 
<?php include('menu.php');?>
 
<div class="cadrecentrale">
 
<h1>Modifier un article</h1>

<?php if(isset($message_ok)) echo $message_ok;

//On masque le formulaire
if(!isset($masquer_formulaire) || $masquer_formulaire == 0) {

?>

<form action="#ok" method="post">

	<?php if(isset($alerte_article_exist)) echo $alerte_article_exist;?>
	<?php if(isset($alerte0)) echo $alerte0;?>
  
  <p>Titre de l'article :<br/>
    <input name="titre" size="65" value="<?php if(isset($titre)) echo $titre;?>" type="text"/>
   </p>
 
	<?php if(isset($alerte1)) echo $alerte1;?>
  
  <p>Description de l'article :<br/>
   <textarea name="description" rows="10" cols="50" ><?php if(isset($desxription)) echo $description;?></textarea>
  </p>
 
  <?php if(isset($alerte2)) echo $alerte2;?>
  
  <p>Contenu de l'article :<br/>
	<textarea name="contenu" rows="10" cols="50" ><?php if(isset($contenu)) echo $contenu;?></textarea>
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
 
  <?php if(isset($alerte3)) echo $alerte3;?>
  
  <p>Catégorie :<br/>
  
	  <select name="cat">

          <option value="">Selectionnez une catégorie</option>

            <?php

                //On sélectionne les données pour créer le formulaire déroulant
                $categorie = pg_query("SELECT categorie_id, categorie_name FROM csf_categories ORDER BY categorie_id ASC");

                while($affiche = pg_fetch_array($categorie))
                {
                    echo '<option value="'.$affiche['categorie_id'].'" ';
                    if ($affiche['categorie_id'] == $id_categorie) {echo "selected = 'selected'";}
                    echo '>'.$affiche['categorie_name'].'</option>';
                }
            ?>
		
	  </select>
	  
  </p>
  
  <?php if(isset($alerte4)) echo $alerte4;?>
  
  <p>Afficher l article ? :<br/>

      <select name="valide">

          <option value="">Sélectionnez un choix</option>
          <option value="1"<?php if ($valide == "1") {echo "selected = 'selected'";}?>>Oui</option>
          <option value="0"<?php if ($valide == "0") {echo "selected = 'selected'";}?>>Non</option>

      </select>

  </p>
  
  <?php if(isset($alerte5)) echo $alerte5;?>
  
  <p>Afficher le système de commentaire ? :<br/>

      <select name="commentaire">

          <option value="">Sélectionnez un choix</option>
          <option value="1" <?php if($commentaire == "1"){echo "selected = 'selected'";}?>>Oui</option>
          <option value="0" <?php if($commentaire == "0"){echo "selected = 'selected'";}?>>Non</option>

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

<?php

//on récupère tout ce qui est transmit par l'url
$id_transmit=$_GET['id'];
$numero_categorie_transmit = $_GET['cat'];
$slug = ''.$_GET['slug'].'';

//On se connecte à la base de données
include('../db_connect.php');
db_open();

//initialisation du masquage du formulaire
$masquer_formulaire = 0;

//On sélectionne les données pour les afficher dans le formulaire
$article = pg_query("SELECT * FROM csf_posts WHERE post_id = '".pg_escape_string($id_transmit)."'");

while($affiche = pg_fetch_array($article))
{
    $titre = $affiche['post_title'];
    $description = nl2br($affiche['post_description']);
    $contenu = stripcslashes($affiche['post_content']);
    $valide = $affiche['post_approved'];
    $commentaire = $affiche['post_comment'];
	$id_categorie = $affiche['post_categorie'];
}

//Traitement du formulaire
if(isset($_POST["Valider"]))
{
    $titre = htmlspecialchars(stripcslashes(trim($_POST["titre"])));
    $description = htmlspecialchars(stripcslashes($_POST["description"]));
    $contenu = stripcslashes($_POST["contenu"]);
    $categorie = $_POST["cat"];
	
    //Pour le pseudo url rewriting
    $slug = ''.OptimiseUrl($titre).'.php';
    $valide = $_POST["valide"];
    $commentaire = $_POST["commentaire"];
	
	//On regarde si le titre existe déjà. Si le titre na pas changé, on risque une erreur puisque la requête ci-dessous va chercher un titre qui correspond..c'est pour ça que l'on ajoute une recherche sur un identifiant différent.
	$article_exist = pg_query("SELECT post_slug FROM csf_posts WHERE post_slug = '".pg_escape_string($slug)."' AND post_id != '".pg_escape_string($id_transmit)."'");
	$total = pg_num_rows($article_exist);
	
	if($total >= 1){
		$alerte_article_exist ='<div class="erreur"><a name="ok"></a>Il existe déjà un article portant ce titre.</div>';
	}
	
	//Vérification du formulaire
    else if(empty($titre)){
        $alerte0 ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie de titre.</div>';
    }
    
	else if(empty($description)){
        $alerte1 ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie de description.</div>';
    }
   
   else if(empty($contenu)){
        $alerte2 ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas saisie de contenu.</div>';

    }
    
	else if(empty($categorie)){
        $alerte3 ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas sélectionné de catégorie.</div>';
    }
    
	else if(empty($valide)){
        $alerte4 ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas sélectionné votre choix.</div>';
    }
    
	else if(empty($commentaire)){
        $alerte5 ='<div class="erreur"><a name="ok"></a>Vous n\'avez pas sélectionné votre choix.</div>';
    }
	
	//Si tout est ok
    else
    {
		// On va chercher le nom du dossier (catégorie) au cas ou l'article change de repertoire
        $dossier = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_id = '".pg_escape_string($numero_categorie_transmit)."'");
		
        while($nom_transmit = pg_fetch_array($dossier))
        {
            $nom_du_dossier_actuel = $nom_transmit['categorie_slug'];
        }
		
        //Ci dessous l'url du fichier actuel (article) à supprimer au cas ou l'article à changé de catégorie. 
		//Note : On récupère l'url du fichier avant la modification en bd afin de comparer si l'article à changé de répertoire
        $url_du_fichier_actuel = '../'.$nom_du_dossier_actuel.'/'.$slug.'';

		//On enregistre les données
        $result = pg_query(" UPDATE csf_posts SET post_title = '".pg_escape_string($titre)."', post_description = '".pg_escape_string($description)."', post_content = '".pg_escape_string($contenu)."', post_slug = '".pg_escape_string($slug)."', post_approved = '".pg_escape_string($valide)."', post_comment = '".pg_escape_string($commentaire)."', post_categorie = '".pg_escape_string($categorie)."' WHERE post_id = '$id_transmit'");
 
        //Si il y a une erreur
        if (!$result) {
            die('Requête invalide : ' . pg_last_error());
        }
		
        else{
            //Si tout est ok, on informe le webmaster
            $message_ok = '<div class="ok"><a name="ok"></a>Article modifié avec succès ! Redirection automatique en cours... <img src="images/loading.gif" alt="Loading"/></div>';
        
			//on masque le formulaire
			$masquer_formulaire=1;

			// On va maintenant chercher le nom du dossier une fois de plus mais après enregistrement
			   
			$dossier_article = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_id = '".pg_escape_string($categorie)."'");
			
			while($nom_dossier = pg_fetch_array($dossier_article))
			{
				$nom_du_dossier = $nom_dossier['categorie_slug'];
			} 
			
			//---------------- Cas 1 - Le fichier n'existe pas --------------------------------------

            //Si pour une raison inconnu le fichier n'existe pas, on le créé
            if(!file_exists($url_du_fichier_actuel)){
               
			   //On créé le nouveau fichier
                $fichier_a_ouvrir = fopen ('../'.$nom_du_dossier.'/'.$slug.'', "w+");
                
				//on écrit le code php suivant
                fwrite($fichier_a_ouvrir,"<?php include('../url_rewriting.php');?>");
                
				//on ferme
                fclose ($fichier_a_ouvrir);
            }

			//---------------------- Cas 2 - Aucun changement de catégorie mais le titre a été modifié ------------
            
			//Si la catégorie est la même mais que le titre a été modifié
			else if($categorie == $numero_categorie_transmit && $slug != $slug){
                
				//on teste dabord si le fichier existe (normalement oui)
                if(file_exists($url_du_fichier_actuel))
                {
                    //on renomme le fichier
                    rename ($url_du_fichier_actuel, '../'.$nom_du_dossier.'/'.$slug.'');
                }
				
                else{
                    echo 'Impossible de renommer le fichier car il est inexistant!';
                }
            }

			//------------------------- Cas 3 - L'article change de catégorie --------------------------------------
           
		   //Si l'article change de catégorie, le fichier change de répertoire
            else if($categorie != $numero_categorie_transmit){
                
				//on supprime le fichier correspondant à l'article
                unlink($url_du_fichier_actuel);
                
				//puis on créé le nouveau fichier dans la nouvelle catégorie
                $fichier_a_ouvrir = fopen ('../'.$nom_du_dossier.'/'.$slug.'', "w+");
                
				//on écrit le code php suivant
                fwrite($fichier_a_ouvrir,"<?php include('../url_rewriting.php');?>");
                
				//on ferme
                fclose ($fichier_a_ouvrir);
            }
			
			//on redirige vers la catégorie correspondante
            echo '<script type="text/javascript"> window.setTimeout("location=(\'categorie-articles.php?id='.$categorie.'\');",2000) </script>';
        }
       
    }//On ferme else
   
}//On ferme if(isset($_POST["Valider"]))

?>

</div>
 
<?php include('footer.php');?>
 
</div>
 
</body>
 
</html>