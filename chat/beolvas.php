<?php

require_once('forcehttps.php');

session_start();

require_once("titkosit.php");

$db= fopen("../../private_html/chat/szobak/" . $_SESSION['szoba'] . ".szoba", "r");
$l = fgets($db);
while ( ($l = fgets($db)) !== false)
{ 
	list($ki, $mikor, $mit) = explode('::', dekodol($l,$_SESSION['szoba_pw']), 3);
	$mikor = sec2time($mikor);
	echo "\n\t\t\t<div>";
	echo "\n\t\t\t\t<p style='text-align: left'>" . $ki . ": " . $mit . "</p>";
	echo "\n\t\t\t\t<p style='text-align: right'>" . $mikor . "</p>";
	echo "\n\t\t\t</div>"; 
}
fclose($db);

function sec2time($seconds)
{
	$days = floor($seconds / (60 * 60 * 24));

	$divisor_for_hours = $seconds % (60 * 60 * 24);
	$hours = floor($divisor_for_hours / (60 * 60));

	$divisor_for_minutes = $divisor_for_hours % (60 * 60);
	$minutes = floor($divisor_for_minutes / (60));

	$divisor_for_seconds = $divisor_for_minutes % (60);
	$seconds = ceil($divisor_for_seconds);

	$ido = $hours . "." . $minutes . "." . $seconds;
	return $ido;
}

?>