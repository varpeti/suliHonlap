<?php 

if (isset($_POST["s_vissza"]))
{
	unset($_SESSION['szoba']);
	unset($_SESSION['szoba_pw']);

	header("Location: szoba.php");
	exit;
}

if (isset($_POST["s_kilepes"]))
{
	session_destroy();

	header("Location: index.php");
	exit;
}

?>