<?php

//Nombre d'article à afficher par page dans les catégories (pagination) : de 1 à ...
$messagesParPage = '10';
 
//Nombre d'article à afficher en Home Page : de 0 à ...
$nombre_article_csf_home = '5';
 
//Afficher le lien vers le formulaire de contact 0 = non et 1 = oui
$afficher_lien_formulaire_contacte = '1';
 
//Adresse email du site
$adresse_email = 'padapara@hotmail.com';
 
//Votre pseudo pour répondre aux commentaires
$pseudo_admin = "admin";

function removeAccents($str, $charset='utf-8')
{
  
    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    //$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
    
    return $str;
}

//fonction pour réécrire les url
function OptimiseUrl($chaine)
{   
	$chaine = removeAccents($chaine);

    $chaine = strtolower($chaine);
 

    $accents = Array("/é/", "/è/", "/ê/","/ë/", "/ç/", "/à/", "/â/","/á/","/ä/","/ã/","/å/", "/î/", "/ï/", "/í/", "/ì/", "/ù/", "/ô/", "/ò/", "/ó/", "/ö/");
    $sans = Array("e", "e", "e", "e", "c", "a", "a","a", "a","a", "a", "i", "i", "i", "i", "u", "o", "o", "o", "o");
 
    $chaine = preg_replace($accents, $sans, $chaine); 
    $chaine = preg_replace('#[^A-Za-z0-9]#', '-', $chaine);
 
    // Remplace les tirets multiples par un tiret unique
    $chaine = preg_replace('#-+#', '-', $chaine );
    // Supprime le dernier caractère si c'est un tiret
    $chaine = rtrim( $chaine, '-' );
 
    while (strpos($chaine,'--') !== false) $chaine = str_replace('--','-',$chaine);
 
    return $chaine;
}

//Nom de dossiers interdits à la création
$dossier_interdit = array('admin','images','protection','ckeditor','ckfinder','sessions');
//fonction pour supprimer un dossier et son contenu
function advRmDir( $dir )
{
    // ajout du slash a la fin du chemin s'il n'y est pas
    if( !preg_match( "#/^.*/$/#", $dir ) ) $dir .= '/';
 
    // Ouverture du répertoire demande
    $handle = @opendir( $dir );
 
    // si pas d'erreur d'ouverture du dossier on lance le scan
    if( $handle != false )
    {
        // Parcours du répertoire
        while( $item = readdir($handle) )
        {
            if($item != "." && $item != "..")
            {
                if( is_dir( $dir.$item ) )
                advRmDir( $dir.$item );
                else unlink( $dir.$item );
            }
        }
        // Fermeture du répertoire
        closedir($handle);
        //pour free qui n'efface pas les dossiers, on renomme le dossier
        rename($dir,"../supprime-moi");
        // suppression du répertoire
        //$res = rmdir( $dir );
    }
    else $res = false;
    return $res;
}

//Fonction permettant de supprimer l'extension du fichier ".php"
function sansPointPhp($supprime_extension)
{
    $supprime_extension = substr($supprime_extension, 0, -4);
    return $supprime_extension;
}

/*fonction pour convertir un timestamp en date française le jours et le mois étant écrit tout en minuscule, on ajoute "ucwords" qui transforme la première lettre de chaque mot en majuscule */
function convertDate($timestamp_actuel) {
    setlocale(LC_TIME,'fr_FR','french','French_France.1252','fr_FR.ISO8859-1','fra');
    return ucwords(strftime("%A %d %B %Y", strtotime(($timestamp_actuel))));
}

//Fichiers autorisés à la modification
$fichier_ok = array('index.php','categorie.php','url_rewriting.php','menu.php','footer.php','fonctions.php','style.css','sitemap.php','rss.php','contact.php','recherche.php','404.php','.htaccess','robots.txt','commentaire.php','pagination.php','desabonnement.php');

//Fonction pour tronquer une chaine
function tronquer($description)
{
    //nombre de caractères à afficher
    $max_caracteres=150;
    // Test si la longueur du texte dépasse la limite
    if (strlen($description)>$max_caracteres)
    {   
        // Séléction du maximum de caractères
        $description = substr($description, 0, $max_caracteres);
        // Récupération de la position du dernier espace (afin d'éviter de tronquer un mot)
        $position_espace = strrpos($description, " ");   
        $description = substr($description, 0, $position_espace);   
        // Ajout des "..."
        $description = $description."...";
    }
    return $description;
}

//Fonction pour l'ip
function getIp()
{
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif(isset($_SERVER['HTTP_CLIENT_IP']))
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    else
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//Fonction pour générer un captcha aléatoire
function generer_code($taille)
{
    $caracteres = array("a", "b", "c", "d", "e", "f","g", "h", "i", "j", "k", "l","m",
    "n", "o", "p", "q", "r","s", "t", "u", "v", "w", "x","y","z", 0, 1, 2, 3, 4, 5, 6,
    7, 8, 9);
    $caracteres_aleatoires = array_rand($caracteres, $taille);
    $pass = "";
    foreach($caracteres_aleatoires as $i)
    {
        $pass .= $caracteres[$i];
    }
        return $pass;   
}
$pass = generer_code(8);

//fonction pour convertir un timestamp en date (formulaire de contact + commentaire) ok
function convertit_timestamp_en_date($timestamp_actuel) {
    return date("j-m-Y @ H:i:s", $timestamp_actuel);
}

//Fonction pour récupérer l'url du site
function url_actuelle()
{
    return 'http://'.$_SERVER["SERVER_NAME"].'';
}
$url = (url_actuelle());

?>
