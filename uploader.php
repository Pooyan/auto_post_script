<?php
/*
 * TODO: hand information to samim's script
 */
require_once 'cache/constants.php';

//constants
$upcenters = unserialize(UPCENTERS);
$maxrate = MAXRATE;

//preparing process
//$upcenters = unserialize(UPCENTERS);
exec("mv /var/upload/* /var/upload/processing");
exec("ls /var/upload/processing", $t_files);

foreach($t_files as $t_file){
	$fname = $t_file;
	
	exec("file /var/upload/processing/$fname", $test);
	if($test == "Directory"){
		//incoming files should be directory
		exec("cd /var/upload/processing/$fname");
		exec("ls /var/upload/processing/$fname", $fparts);
	}
	else{
		exec("mv /var/upload/processing/$fname /var/upload/processing/$fname.tmp");
		exec("mkdir /var/upload/processing/$fname");
		exec("mv /var/upload/processing/$fname.tmp /var/upload/processing/$fname");		
		exec("cd /var/upload/processing/$fname");
		exec("ls /var/upload/processing/$fname", $fparts);	
	}
	
	$i = 0;
	foreach($fparts as $fpart){
		
		foreach($upcenters as $upcenter){
		$general_opts = "-r2 --max-rate=$maxrate/k --printf=%u%n%s --auth=$upcenter[1]" . ":" . "$upcenter[2]";
		$module_opts = '';
		exec("plowup $general_opts	$upcenter[0]	$module_opts	$fpart", $t_uplink[]);
		
		//seperate outputs
		$uplinks[] = $t_uplink[0];
		$upsizes[] = $t_uplink[1];
		}
		
		$i++;
	}
	
	$i = 0;
	$xml = new SimpleXMLElement("$sloc/content/$fname");
	foreach($uplinks as $uplink){
		$upload = $xml->addChild('upload');
		$upload->addChild('link', $uplink);
		$upload->addChild('size', $upsizes[$i]);
		$upload->addAttribute('part', $i);
		$i++;
	}
	
}

/*
 * Initiate Wordpress Post
* @author: Samim Pezeshki
*/

include_once 'library.php';

//Load file info from simpelXML object
$my_file_info = new file_info($xml);

//Instantiate movie class
//TODO: change $name
$myMovie= new movie($name);

//Put the info into the post
if ($myMovie->download) {
	$info=$myMovie->getInfo();
	$myPost=new post($info);
}

//Send the Post
$send = new XMLRPClientWordPress('http://downloadmoviz.com/xmlrpc.php', 'movie', 'movie');
$send->create_post2(
		$myMovie->title.' '.$myMovie->year, //Title
		$myPost->table1, // Content of the Post
		array_merge($myMovie->genres,array('Genre')), //Genres as categories
		$myMovie->casts, // Casts as tags
		array( //Custom Fields
				array( "key" => "format", "value" => $my_file_info->file_extention ), //File Info
				array( "key" => "file_size", "value" => $my_file_info->file_extention ),//File Info
		)
);