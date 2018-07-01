<?php

require_once('forcehttps.php');

session_start();

$token = $_GET['q']; // Url beolvasás

require_once("titkosit.php");

if( strlen($token)>=32 )
{
	require_once("egyszerlink.html");
	echo '				<input name="s_token" value="'. $token .'" type="hidden">
			</div>
		</form>
	</div>
</div>';

}else
{
	if (isset($_POST["s_ktok"]))
	{
		$token = $_POST["s_token"];

		if (strlen($token)<32)
		{
			header("Location: index.php");
			exit;
		}
	
		$szoba = '';
		$kod = '';
		$ok = false;
		
		$file = "../../private_html/chat/linkek.lnk";
		
		$lines = file($file); // file beolvasás
		
		$f = fopen($file,"w");
		
		flock($f,LOCK_EX); //lock
		
		// Végigmegy az összes tokenen a fileba.
		for( $i = 0; $lines[$i]; $i++ )
		{	
			list($dat, $ido) = explode('::', $lines[$i], 2);
			$dat = dekodol($dat,$token);
			list($tok, $szoba, $kod) = explode('::', $dat, 3);
			if (time()-$ido<60*60*24) // Ha nem járt le.
			{
				if ($tok === $token) // Ha megvan.
				{
					if (strlen($szoba)>=1) {$ok = true;}
				}
				else // Visszaírjuk ami nem egyezik.
				{
					fwrite($f,$lines[$i]);
				}
			}
	
		}
		
		flock($f,LOCK_UN); //unlock
		
		fclose($f);
		
		if( $ok ) //ha van
		{
			$_SESSION['szoba']=trim($szoba);
			$_SESSION['szoba_pw']=trim($kod);
		
			header("Location: index.php");
			exit();
		}
		else // Ha nincs ilyen token.
		{
			$_SESSION['szoba']='';
			$_SESSION['szoba_pw']='';
	
			header("Location: index.php");
			exit();
		}
	}
}



function ujlink($szoba, $kod)
{
	$token = md5(uniqid(rand(),1));

	$file = "../../private_html/chat/linkek.lnk";
	
	$f = fopen($file,"a");
	
	flock($f,LOCK_EX); //lock

	require_once("titkosit.php");
	fwrite($f,titkosit($token."::".$szoba."::".$kod,$token)."::".time()."\n");

	flock($f,LOCK_UN); //unlock
	
	fclose($f);
	
	$cwd = substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],"/"));
	
	
	return "Egyszer hasznalhato URL: <a href='https://".$_SERVER['HTTP_HOST']."$cwd/egyszerlink.php?q=$token'>https://".$_SERVER['HTTP_HOST']."$cwd/egyszerlink.php?q=$token</a>";

}

?>