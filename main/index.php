<?php  

require_once("b1.html");

if ($handle = opendir('posts')) 
{
    $entries = array();
    while (false !== ($entry = readdir($handle))) 
    {
        array_push($entries, $entry);
    } 
    sort($entries);

    for($i=sizeof($entries)-1; $i>=0; $i--)
    {
        $entry = $entries[$i];

        if ($entry != "." && $entry != "..") 
        {

            //echo "$entry\n";

            $file = fopen('posts/'.$entry, "r") or die("Unable to open file!");

            if ($file) 
            {
                require("p1.html");

                if (($line = fgets($file)) !== false) 
                {
                    echo '<div class="pique-panel-background" style="background-image:url(img/'.rtrim($line).')"></div>';
                }

                require("p2.html");

                if (($line = fgets($file)) !== false) 
                {
                    echo $line;
                }

                require("p3.html");

                while (($line = fgets($file)) !== false)  
                {
                    echo $line;
                }

                require("p4.html");
            }

            fclose($file);
        }
    }

    closedir($handle);
}

require_once("b2.html");

?>