<?php

require_once('forcehttps.php');

session_start();

//Ha megvannak az adatok előrébb lép.
if (isset($_SESSION['nev']))
{
	header("Location: szoba.php");
	exit("index");
}

if(isset($_POST["s_nev_sub"]))
{
	$_SESSION['nev']=htmlspecialchars($_POST["s_nev"], ENT_QUOTES); // ne lehessen HTML vagy Javascript injection

	header("Location: szoba.php");
	exit();

}

require_once('nev_input.html');
?>