<?php

include 'config.php';
include('db_connect.php');
include('fonctions.php');

db_open();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Formulaire de recherches</title>
	<meta name="Description" content="Formulaire de recherches" />
	<link rel="canonical" href="http://<?php echo $_SERVER['HTTP_HOST'];?>/recherche.php" /> <link href="http://<?php echo $_SERVER['HTTP_HOST'];?>/style.css" rel="stylesheet" type="text/css"/>
</head>
 
<body>
 
<div id="moncadre">

<?php include('menu.php');?>

<div class="cadrecentrale">

<h1>Formulaire de recherches</h1>

<p>
	<a title="home" href="http://<?php echo $_SERVER['HTTP_HOST'];?>">home</a> »
	<a title="Formulaire de recherches" href="http://<?php echo $_SERVER['HTTP_HOST'];?>/recherche.php">Formulaire de recherches</a>
</p>

<form class="form" method="post" action="recherche.php">
	<label for="requete">Recherche</label>
	<input size="50" name="requete" value="<?php if (!empty($_POST["requete"])) { echo stripcslashes(htmlspecialchars($_POST["requete"],ENT_QUOTES)); } ?>" type="text"/>
	<input value="Go" name="submit" type="submit"/><br/>
</form>

<?php

if(isset($_POST['submit']))
{
    $requete = trim(stripcslashes(htmlspecialchars($_POST['requete'])));
	
	if(empty($requete) OR $requete=="Votre recherche..."){
        echo '<div class="erreur">Un mot serait utile pour effectuer une recherche...</div>';
    }
	
	 else{
	 
        $query = pg_query("SELECT post_id, post_title, post_description, post_slug, post_date, post_categorie FROM csf_posts WHERE post_title REGEXP '[[:<:]]".pg_escape_string($requete)."[[:>:]]' OR post_description REGEXP '[[:<:]]".pg_escape_string($requete)."[[:>:]]' OR post_content REGEXP '[[:<:]]".pg_escape_string($requete)."[[:>:]]' AND post_approved = '1' ORDER BY post_date DESC")
        or die (pg_last_error());
		
		//On utilise la fonction pg_num_rows pour compter les résultats
        $nb_resultats = pg_num_rows($query);
		
        //Si le nombre de résultats est différent de 0, on continue
        if($nb_resultats != 0)
        {
            //On affiche le nombre de résultats
            echo 'Il existe <b>'.$nb_resultats.'</b>';
            
			if($nb_resultats > 1)
            
			// on vérifie le nombre de résultats pour orthographier correctement.
            {
                echo ' résultats';
            }
			
            else
            {
                echo ' résultat';
            }
            
			echo ' pour votre recherche " <b>'.$requete.'</b> " :<br/>';
			
			$i = "1";
			
            while($donnees = pg_fetch_array($query))
            {
				//On va chercher le nom de la catégorie
                $nom_categorie = pg_query("SELECT categorie_slug FROM csf_categories WHERE categorie_id = '".pg_escape_string($donnees['post_categorie'])."'");
               
			    while($cat = pg_fetch_array($nom_categorie))
                {
                    echo '<div class="cadre"><big><big>'.$i.'-<a title="'.$donnees['post_title'].'" href="http://'.$_SERVER['HTTP_HOST'].'/'.$cat['categorie_slug'].'/'.sansPointPhp($donnees['post_slug']).'">'.$donnees['post_title'].'</a></big></big><span class="date">'.convertDate($donnees['post_date']).'</span><br/><p>'.$donnees['post_description'].'</p></div>';
                    $i++;
                }
            }
            
        }//on ferme if($nb_resultats > 1)
		
		//S'il n'y a rien
        else {
            echo '<div class="erreur">Nous n\'avons trouvé aucun résultats pour votre recherche " '.$requete.' " !</div>';
        }
    }
    
}//On ferme if(isset($_POST['requete'])

?>

</div>
<?php include('footer.php');?>
</div>
 
</body>
</html>