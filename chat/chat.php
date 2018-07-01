<?php

require_once('forcehttps.php');

session_start();

//Adatok meglétének ellenörzése
if (!isset($_SESSION['nev'])) 
{
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['szoba']))
{
    header("Location: szoba.php");
    exit;
}

//Vissza, kilépés bisztosítása
require_once("viki.php");

require_once("titkosit.php");
require_once("egyszerlink.php");


if(isset($_POST["s_kuld"]))
{ 
    ujuzenet($_SESSION['nev'],htmlspecialchars($_POST["s_szoveg"], ENT_QUOTES)); // ne lehessen HTML vagy Javascript injection - majd kliens oldalon
}

if (file_exists("../../private_html/chat/szobak/" . $_SESSION['szoba'] . ".szoba") and $db= fopen("../../private_html/chat/szobak/" . $_SESSION['szoba'] . ".szoba", "r")) 
{
    //Jelszó vizsgálata
    $sor = fgets($db);
    fclose($db);
    if (!isset($_SESSION['szoba_pw']) or $_SESSION['szoba_pw'] != dekodol($sor,$_SESSION['szoba_pw'])) //Ha nem jó, vagy nincs jelszó
    {
        if ($_SESSION['szoba']=='public') // Ha a public szobába akart belépni beálítja
        {
            $_SESSION['szoba_pw']="PublicPublicPubl";
        }
        else // Ha nem jó a jelszó, és nem public szobába akart belépni kilép
        {
            unset($_SESSION['szoba']);
            unset($_SESSION['szoba_pw']);

            header("Location: szoba.php");
            exit;
        }
    }
    // Jó jelszó, vagy public szoba

    if (!isset($_POST["s_kuld"]) and !isset($_POST["s_ujra"])) {ujuzenet("Szoba",$_SESSION['nev']." csatlakozott a szobához.");}

    //Chat és az üzenetek megjelenítése
    require_once('chat_begin.html');
    echo "<div id='header'><h1 style='padding-bottom: 40px; text-align: center'>" . $_SESSION['szoba'] . "</h2></div>\n<div id='CONTENT'>\n\t<div id='TEXT'>";
    require_once('chat_input.html');

    //Üzenetek olvasása
    require_once('beolvas.php'); // Hogy akinek le van tiltva az ajax is tudja használni.

} else { // Nemlétezik ilyen nevű szoba
    ujszoba();
}

require_once('chat_end.html');


//Függvények


function ujuzenet($nev, $uzenet)
{ 
        $sec = time();
        
        $uzenet = parancsok($uzenet);
  
        $_POST["s_szoveg"]=""; 

        $uzenet = titkosit($nev . "::" . $sec . "::" . $uzenet,$_SESSION['szoba_pw']) . "\n";

        //Berakja az üzenetet a file elejére, a header mögé.
        $file = file("../../private_html/chat/szobak/" . $_SESSION['szoba'] . ".szoba");
        $header = array_shift($file);  // kiszedi az első sort
        array_unshift($file, $uzenet); // berakja az üzenetet
        array_unshift($file, $header); // vissza a header

        $db = fopen("../../private_html/chat/szobak/" . $_SESSION['szoba'] . ".szoba", 'w'); // Visszaírj az egész fájlt.
        fwrite($db, implode("", $file));     
        fclose($db);
}

function ujszoba()
{
    //spacek kiszedése
    $_SESSION['szoba'] = str_replace(' ', '', $_SESSION['szoba']);

    //szoba létrehozása
    $db= fopen("../../private_html/chat/szobak/" . $_SESSION['szoba'] . ".szoba", "w");
    $megj = false;
    if(!isset($_SESSION['szoba_pw'])) 
    {
        $_SESSION['szoba_pw'] = substr(crypt(openssl_random_pseudo_bytes(16)), -16); // Megbízható 16 karakteres jelszó.
        $megj=true;
    } 

    fwrite($db,titkosit($_SESSION['szoba_pw'],$_SESSION['szoba_pw'])."\n");
    fclose($db);

    //Chat megjelenítése
    require_once('chat_begin.html');
    echo "<div id='header'><h1 style='padding-bottom: 40px; text-align: center'>" . $_SESSION['szoba'] . "</h2></div>\n<div id='CONTENT'>\n\t<div id='TEXT'>";
    if ($megj) { print("<br>A szoba [jelszo]: [".$_SESSION['szoba_pw']."]"); }
    print("<br>".ujlink($_SESSION['szoba'],$_SESSION['szoba_pw']));
    print("<br>Elerheto parancsok: /help");
    require_once('chat_input.html');
}

function parancsok($uzenet)
{
    if ($uzenet=="/help") 
    {
        $uzenet="Parancsok:<br>/help - kiirja ezt.<br>/del - torli a szobat.<br>/link - ad egy linket a szobahoz.<br>/img pelda.jpg - megjeleniti a kepet.";
    }
    elseif ($uzenet=="/del") // Törli a szobát
    { 
        unlink("../../private_html/chat/szobak/" . $_SESSION['szoba'] . ".szoba");

        unset($_SESSION['szoba']);
        unset($_SESSION['szoba_pw']);
        exit('<script>location.replace("");</script>');
    }
    elseif ($uzenet=="/link") 
    {
        $uzenet=ujlink($_SESSION['szoba'],$_SESSION['szoba_pw']); // Uj egyszer hasznalhato link
    }
    elseif ( strpos($uzenet,"/img ") !== false )
    {
        $uzenet="<img src=".substr($uzenet,5).">";
    }

    return $uzenet;
}

?>