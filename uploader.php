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
		$upsize[] = $t_uplink[1];
		}
		
		$i++;
	}
	
	$i = 0;
	foreach($uplinks as $uplink){
		$xml .= make_2level_xml('upload', 'link', 'size', $uplink[$i], $upsize[$i]);
		$i++;
	}
	$xml = escapeshellarg(xml_finalize($xml));
	exec("echo $xml >> $sloc/content/$fname/$fname.xml");

	
	/*
	$uploaded_net = "-r2 --max-rate=$maxrate/k --printf=%u%n%s --auth=$username[$i]" + ":" + "$password" + "--auth-free=$freeusername[$i]" + ":" + "$freepassword" + "--description=$fdesc";
	$rapidgator =	"-r2 --max-rate=$maxrate/k --printf=%u%n%s --auth=$username[$i]" + ":" + "$password" + "--auth-free=$freeusername[$i]" + ":" + "$freepassword" + "--description=$fdesc";
	$ryushare =     "-r2 --max-rate=$maxrate/k --printf=%u%n%s --auth=$username[$i]" + ":" + "$password" + "--auth-free=$freeusername[$i]" + ":" + "$freepassword" + "--description=$fdesc";
	
	exec("plowup $uploaded_net	uploaded_net	$fpart");
	exec("plowup $rapidgator	rapidgator		$fpart");
	exec("plowup $ryushare 		ryushare		$fpart");
	*/
}