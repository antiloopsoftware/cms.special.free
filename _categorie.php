<?php

$nom_categorie = explode("/", $_SERVER["REQUEST_URI"]);

if (count($nom_categorie) == 3)
{
	$possede_2_niveaux = false;
	
	$nom_du_dossier = $nom_categorie[1];
	$url_canonique_categorie = $nom_categorie[2];

	$numero_page = str_replace('?article=' , '' , $nom_categorie[2]);
}
	
else if (count($nom_categorie) == 4)
{
	$possede_2_niveaux = true;
	
	$nom_du_dossier_parent = $nom_categorie[1];
	$nom_du_dossier_enfant = $nom_categorie[2];
	$url_canonique_categorie = $nom_categorie[3];

	$numero_page = str_replace('?article=' , '' , $nom_categorie[3]);
}	

if(!empty($numero_page) AND !is_numeric($numero_page))
{
    header("Status : 301 Moved Permanently");
    header('location:http://'.$_SERVER['HTTP_HOST'].'/'.$nom_du_dossier.'/');
    exit();
}

if($numero_page > 1){
    $numero_pagination = ' page '.$numero_page.'';
}

if($nom_categorie[2] == '?article=1')
{
    header("Status : 301 Moved Permanently");
    header('location:http://'.$_SERVER['HTTP_HOST'].'/'.$nom_du_dossier.'/');
    exit();
}

include('db_connect.php');
include('fonctions.php');

db_open();

if (!$possede_2_niveaux)
{
	$nom_categorie = pg_query("SELECT categorie_id, categorie_name, categorie_description FROM csf_categories WHERE categorie_slug = '".pg_escape_string($nom_du_dossier)."'");
	
	if(pg_num_rows($nom_categorie) == 1000)
	{
		//Il n'y a rien, on redirige vers l'index
		header("Status : 301 Moved Permanently");
		header('location:http://'.$_SERVER['HTTP_HOST'].'');
		
		exit();
	}

	else
	{
		while($cat = pg_fetch_array($nom_categorie))
		{
			$id_categorie = $cat['categorie_id'];
			$titre_categorie = $cat['categorie_name'];
			$description_categorie = $cat['categorie_description'];
		}
	}
}

else
{
	$nom_categorie_parent = pg_query("SELECT categorie_id, categorie_name, categorie_description FROM csf_categories WHERE categorie_slug = '".pg_escape_string($nom_du_dossier_parent)."'");
	$nom_categorie_enfant = pg_query("SELECT categorie_id, categorie_name, categorie_description FROM csf_categories WHERE categorie_slug = '".pg_escape_string($nom_du_dossier_enfant)."'");
		
	if (pg_num_rows($nom_categorie_parent) == 0 && pg_num_rows($nom_categorie_enfant) == 0)
	{
		//Il n'y a rien, on redirige vers l'index
		header("Status : 301 Moved Permanently");
		header('location:http://'.$_SERVER['HTTP_HOST'].'');
		
		exit();		
	}

	else
	{
		while($cat = pg_fetch_array($nom_categorie_parent))
		{
			$titre_categorie = $cat['categorie_name'];
			$description_categorie = $cat['categorie_description'];
		}
		
		while($cat = pg_fetch_array($nom_categorie_enfant))
		{
			$id_categorie = $cat['categorie_id'];
			$titre_categorie .= "/".$cat['categorie_name'];
			$description_categorie .= "/".$cat['categorie_name'];
		}
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr-fr">
 
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>
		<?php
			if(isset($titre_categorie)) echo $titre_categorie;
			if(isset($numero_pagination)) echo $numero_pagination;
		?>
	</title>
	<meta name="Description" content="<?php if(isset($description_categorie)) echo $description_categorie; if(isset($numero_pagination)) echo $numero_pagination;?>" />
	<link href="http://<?php echo $_SERVER['HTTP_HOST'];?>/style.css" rel="stylesheet" type="text/css"/>
	<link rel="canonical" href="http://<?php echo $_SERVER['HTTP_HOST'];?>/<?php echo $nom_du_dossier;?>/<?php echo $url_canonique_categorie;?>" />
</head>
 
<body>
 
<div id="moncadre">

<?php include('menu.php');?>

<div class="cadrecentrale">

<?php include('formulaire.php');?>

<h1>

<?php 

echo $titre_categorie;
if(isset($numero_pagination)) echo $numero_pagination;

?>

</h1>

<p class="fil-ariane">
	<strong><a title="<?php echo $titre_categorie;?>" href="http://<?php echo $_SERVER['HTTP_HOST'];?>/<?php echo $nom_du_dossier;?>/<?php echo $url_canonique_categorie;?>"><?php echo $titre_categorie; if(isset($numero_pagination)) echo $numero_pagination;?></a></strong>
</p>

<?php

$retour_total = pg_query("SELECT COUNT(*) AS total FROM csf_posts WHERE post_categorie = '".pg_escape_string($id_categorie)."' AND post_approved = '1'");
$donnees_total = pg_fetch_assoc($retour_total);
$total = $donnees_total['total'];

$nombreDePages = ceil($total / $messagesParPage);

if(isset($_GET['article']))
{
    $pageActuelle = intval($_GET['article']);
 
    if($pageActuelle > $nombreDePages)
    {
        $pageActuelle = $nombreDePages;
    }
}

else
{
    $pageActuelle = 1;
}
 
$premiereEntree = ($pageActuelle-1) * $messagesParPage;

$page = pg_query("SELECT post_title, post_description, post_slug, post_date FROM csf_posts WHERE post_categorie = '".pg_escape_string($id_categorie)."' AND post_approved = '1' ORDER BY post_id DESC LIMIT ".$premiereEntree." OFFSET ".$messagesParPage);

if(pg_num_rows($page) == 0)
{
    //S'il n'y a rien, on informe le visiteur
}

else if ($numero_page > $nombreDePages)
{
	if (!possede_2_niveaux)
	{
		echo '<script type="text/javascript"> window.setTimeout("location=(\'http://'.$_SERVER['HTTP_HOST'].'/'.$nom_du_dossier.'/);",10) </script>';
	}
	
	else
	{
		echo '<script type="text/javascript"> window.setTimeout("location=(\'http://'.$_SERVER['HTTP_HOST'].'/'.$nom_du_dossier_parent.'/'.$nom_du_dossier_enfant.'/);",10) </script>';
	}
}

else
{
	if (!$possede_2_niveaux)
	{
		while($affiche = pg_fetch_array($page))
		{
			echo '<div class="cadre"><h2 class="h2"><a title="'.$affiche['post_title'].'" href="http://'.$_SERVER['HTTP_HOST'].'/'.$nom_du_dossier.'/'.sansPointPhp($affiche['post_slug']).'">'.$affiche['post_title'].'</a></h2><p>'.tronquer(nl2br($affiche['post_description'])).'<br/><br/><span class="date">'.convertDate($affiche['post_date']).'</span></p></div>';
		}
	}
	
	else
	{
		while($affiche = pg_fetch_array($page))
		{
			echo '<div class="cadre"><h2 class="h2"><a title="'.$affiche['post_title'].'" href="http://'.$_SERVER['HTTP_HOST'].'/'.$nom_du_dossier_parent.'/'.$nom_du_dossier_enfant.'/'.sansPointPhp($affiche['post_slug']).'">'.$affiche['post_title'].'</a></h2><p>'.tronquer(nl2br($affiche['post_description'])).'<br/><br/><span class="date">'.convertDate($affiche['post_date']).'</span></p></div>';
		}
	}
}

include('pagination.php');

?>

</div>
<?php include('footer.php');?>
</div>
 
</body>
</html>