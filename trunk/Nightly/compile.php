<?php
/*
* This file auto-generates a nightly build of local contents
*/
echo "<h1>Computing Nightly</h1>";

require("PEAR/Tar.php");
$tar = new Archive_Tar("latest.tar.gz","gz");
$files = array();



echo "Creating memory backup of current config.php.<br>";
$oldconfig = file_get_contents("../config.php");
echo "CHECK: ".strlen($oldconfig)."<br>";

echo "Loading default config.php<br>";
$defconfig = file_get_contents("./data/config.php");
echo "CHECK: ".strlen($defconfig)."<br>";

echo "Temporarily replacing config.php<br>";

file_put_contents("../config.php", $defconfig);

function getDirectory( $path = '.', $level = 0 ){ 
    global $files;
    $ignore = array('error_log', 'Nightly', '.svn', '.', '..' ); 
    $dh = @opendir( $path ); 
    while( false !== ( $file = readdir( $dh ) ) ){ 
        if( !in_array( $file, $ignore ) ){ 
            $spaces = str_repeat( '&nbsp;', ( $level * 4 ) ); 
            if( is_dir( "$path/$file" ) ){ 
                echo "<strong>$spaces $file</strong><br />"; 
                getDirectory( "$path/$file", ($level+1) );              
            } else {              
                echo "$spaces $file<br />";
                array_push($files, "$path/$file");             
            } 
        } 
    } 
    closedir( $dh ); 
}


echo "<hr> <h1>Computing Directory Tree</h1>";

getDirectory("../");

echo "<hr> <h1>Listing Linear Files</h1>";

print_r($files);


echo "<hr> <h1>Creating Tar</h1>";

$tar->create($files) or die("Could not create archive!");

echo "Sucessfully created nightly archive.";

echo "<h1>Restoring Config</h1>";

file_put_contents("../config.php", $defconfig);

echo "CHECK: ".strlen($oldconfig)."<br>";
?>

