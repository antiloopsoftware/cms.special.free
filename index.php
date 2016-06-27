<?php

if($_SERVER['REQUEST_URI'] == "/index.php")
{
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: http://".$_SERVER['HTTP_HOST']."/");
    exit;
}

include('fonctions.php');

//On se connecte à la base de données
include('db_connect.php');
db_open();

?>

<!-- ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- -->



<!DOCTYPE html>
<html lang="fr-fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Description" content="<?php if(isset($description)) echo $description;?>" />
    <meta name="author" content="">

    <title><?php if(isset($titre)) echo $titre;?></title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="css/modern-business.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
</head>

<body>
	
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<!-- You'll want to use a responsive image option so this logo looks good on devices - I recommend using something like retina.js (do a quick Google search for it and you'll find it) -->
				<a class="navbar-brand" href="index.html">CMS Spécial Free</a>
			</div>
			
			<?php include('navbar.php');?>
			
		</div>
		<!-- /.container -->
	</nav>
	
    <div id="myCarousel" class="carousel slide">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner">
            <div class="item active">
                <div class="fill" style="background-image:url('http://placehold.it/1900x1080&text=Slide One');"></div>
                <div class="carousel-caption">
                    <h1>Réécriture des URL SEO friendly</h1>
                </div>
            </div>
            <div class="item">
                <div class="fill" style="background-image:url('http://placehold.it/1900x1080&text=Slide Two');"></div>
                <div class="carousel-caption">
                    <h1>Simplicité et fiabilité</h1>
                </div>
            </div>
            <div class="item">
                <div class="fill" style="background-image:url('http://placehold.it/1900x1080&text=Slide Three');"></div>
                <div class="carousel-caption">
                    <h1>Code PHP et configuration Apache spécialisé en fonction de la configuration exotique de l'hébergeur Free.fr</a>
                    </h1>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <span class="icon-next"></span>
        </a>
    </div>

    <div class="section">

        <div class="container">

            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <h3><i class="fa fa-check-circle"></i> Responsive design</h3>
                    <p>La mise en page du CMS Spécial Free est assuré par <a href="http://getbootstrap.com">Bootstrap 3</a>, le framework HTML, CSS et JS le plus populaire pour le développement d'interface mobiles et réactives.</p>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h3><i class="fa fa-pencil"></i> Facile à personnaliser</h3>
                    <p>Vous pouvez démarrer avec l'architecture pré-installée, tout ce que vous avez à faire est de customiser le style ! D'autres thèmes sont disponible sur <a href="http://bootswatch.com">Bootswatch</a>, ou personalisable avec <a href="http://getbootstrap.com/customize/">le Bootstrap customizer </a>!</p>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h3><i class="fa fa-folder-open"></i> Plein de type de page fournie</h3>
                    <p>Ce CMS avec son thème de base Modern Business inclus les types de page suivantes : à propos, contact, plusieurs styles de portfolio, billet de blog, tarifs, FAQ, 404, services, et diverses pages multi-fonction.</p>
                </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->

    <div class="section-colored text-center">

        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <h2>Modern Business : un template de site Clean &amp; Simple édité par Start Bootstrap.</h2>
                    <p>Un design de site complet incluant diverses pages issues de librairies gratuites.</p>
                    <hr>
                </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->

    </div>
    <!-- /.section-colored -->

    <div class="section">

        <div class="container">

            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>Display Some Work on the Home Page Portfolio</h2>
                    <hr>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <a href="portfolio-item.html">
                        <img class="img-responsive img-home-portfolio" src="http://placehold.it/700x450">
                    </a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <a href="portfolio-item.html">
                        <img class="img-responsive img-home-portfolio" src="http://placehold.it/700x450">
                    </a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <a href="portfolio-item.html">
                        <img class="img-responsive img-home-portfolio" src="http://placehold.it/700x450">
                    </a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <a href="portfolio-item.html">
                        <img class="img-responsive img-home-portfolio" src="http://placehold.it/700x450">
                    </a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <a href="portfolio-item.html">
                        <img class="img-responsive img-home-portfolio" src="http://placehold.it/700x450">
                    </a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <a href="portfolio-item.html">
                        <img class="img-responsive img-home-portfolio" src="http://placehold.it/700x450">
                    </a>
                </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->

    <div class="section-colored">

        <div class="container">

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <h2>Modern Business Features Include:</h2>
                    <ul>
                        <li>Bootstrap 3 Framework</li>
                        <li>Mobile Responsive Design</li>
                        <li>Predefined File Paths</li>
                        <li>Working PHP Contact Page</li>
                        <li>Minimal Custom CSS Styles</li>
                        <li>Unstyled: Add Your Own Style and Content!</li>
                        <li>Font-Awesome fonts come pre-installed!</li>
                        <li>100% <strong>Free</strong> to Use</li>
                        <li>Open Source: Use for any project, private or commercial!</li>
                    </ul>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <img class="img-responsive" src="http://placehold.it/700x450/ffffff/cccccc">
                </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->

    </div>
    <!-- /.section-colored -->

    <div class="section">

        <div class="container">

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <img class="img-responsive" src="http://placehold.it/700x450">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <h2>Modern Business Features Include:</h2>
                    <ul>
                        <li>Bootstrap 3 Framework</li>
                        <li>Mobile Responsive Design</li>
                        <li>Predefined File Paths</li>
                        <li>Working PHP Contact Page</li>
                        <li>Minimal Custom CSS Styles</li>
                        <li>Unstyled: Add Your Own Style and Content!</li>
                        <li>Font-Awesome fonts come pre-installed!</li>
                        <li>100% <strong>Free</strong> to Use</li>
                        <li>Open Source: Use for any project, private or commercial!</li>
                    </ul>
                </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->

    <div class="container">

        <div class="row well">
            <div class="col-lg-8 col-md-8">
                <h4>'Modern Business' is a ready-to-use, Bootstrap 3 updated, multi-purpose HTML theme!</h4>
                <p>For more templates and more page options that you can integrate into this website template, visit Start Bootstrap!</p>
            </div>
            <div class="col-lg-4 col-md-4">
                <a class="btn btn-lg btn-primary pull-right" href="http://startbootstrap.com">See More Templates!</a>
            </div>
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->

   <?php include('footer.php');?>
   <?php 
   //<!-- ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- -->
	  
    //On sélectionne les données de la Home Page
	$index = pg_query("SELECT home_title, home_description, home_content FROM csf_home");

	while($csf_home = pg_fetch_array($index))
	{
		$titre = $csf_home['home_title'];
		$description = $csf_home['home_description'];
		$contenu = $csf_home['home_content'];
	}
	
	//Si le titre ou la description sont vides, on informe par un message d'alerte
	/*if(empty($titre) || empty($description)){
		echo '<big style="color:red">Le titre et/ou la description de la Home Page est vide.</big><br/>';
	}*/

	//Si du texte est présent, on l'affiche
	if(isset($contenu) && !empty($contenu))
	{
		echo '<div class="cadre">'.$contenu.'</div>';
	}

	$nombre_article_csf_home = 5;

	//On sélectionne les données pour afficher les 5 derniers articles
	$result1 = pg_query("SELECT post_id, post_title, post_description, post_slug, post_date, post_categorie FROM csf_posts WHERE post_approved = '1' ORDER BY post_id DESC LIMIT ".
			   $nombre_article_csf_home);

	//S'il y a quelque chose
	if(pg_num_rows($result1) != 0)
	{
		if($nombre_article_csf_home == 1){
			echo '<span class="dernier-article">Le dernier article</span>';
		}
		
		else{
			echo '<span class="dernier-article">Les '.$nombre_article_csf_home.' derniers articles</span>';
		}
		
		while($affiche = pg_fetch_array($result1))
		{
			//on va chercher le nom du dossier pour chaque article
			$dossier_article = pg_query("SELECT categorie_name, categorie_slug FROM csf_categories WHERE categorie_id = '".pg_escape_string($affiche['post_categorie'])."'");
		   
		   while($nom_dossier = pg_fetch_array($dossier_article))
			{
				$titre_categorie=$nom_dossier['categorie_name'];
				$nom_du_dossier=$nom_dossier['categorie_slug'];
			}
			//------fin nom de dossier---------------
			
			echo '<div class="cadre"><h2 class="h2"><a title="'.$affiche['post_title'].'" href="http://'.$_SERVER['HTTP_HOST'].'/'.$nom_du_dossier.'/'.sansPointPhp($affiche['post_slug']).'">'.
				  $affiche['post_title'].'</a></h2>     <p>'.tronquer(nl2br($affiche['post_description'])).'<br/><br/><span class="date">'.convertDate($affiche['post_date']).' � <strong><a title="'.
				  $titre_categorie.'" href="http://'.$_SERVER['HTTP_HOST'].'/'.$nom_du_dossier.'/">'.$titre_categorie.'</a></strong></span></p></div>';
		}
	}

	?>
   
    <!-- ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- -->
  
    <!-- JavaScript -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/modern-business.js"></script>

</body>

</html>



