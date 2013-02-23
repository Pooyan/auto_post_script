<?php

include_once('helper/generals.php');

//init
define("SCRIPT_LOCATION", str_replace("install", "", dirname(__FILE__)));
$server_loc = $_SERVER['DOCUMENT_ROOT'];

cache_def('SCRIPT_LOCATION', SCRIPT_LOCATION);
cache_def('SERVER_LOC', $server_loc);
$sloc = SCRIPT_LOCATION;

//clean previus updates
exec("rm -f $sloc/cache/constants.php");

//load configure file
$vars = simplexml_load_file(SCRIPT_LOCATION . "conf/autoconf.xml");

//first part, var tags
$vartags= $vars->upload[0]->var;

$cache_define = null;
foreach($vartags as $vartag){
	$cache_define .= "define(";
	$cache_define .= strtoupper($vartag['id']);
	$cache_define .= ", ";
	$cache_define .= escapeshellarg($vartag->value);
	$cache_define .= ");";
	$cache_define .= "\n";
	//define(strtoupper($vartag['id']), escapeshellarg($vartag->value));
}
exec("echo " . "define('SCRIPT_LOCATION', " . SCRIPT_LOCATION . ")" . " >> $sloc/cache/constants.php");
exec("echo " . escapeshellarg($cache_define) . " >> $sloc/cache/constants.php"); //TODO: DEBUG check executation rights.

//seperator
exec("echo '\n//adding uploadcenters\n' >> $sloc/cache/constants.php");

//second part, upcenter tags
$upcentertags= $vars->upload[0]->upcenter;
$nupc = 0;
//will process this for each of upcenter tags
foreach($upcentertags as $upcenter){
	
	//makes an array with three strings, representing module name, and usr pass of that module
	$upcenter_array[0] = $upcenter->module;
	$upcenter_array[1] = $upcenter->username;
	$upcenter_array[2] = $upcenter->password;
	
	//saves this module in an array that holds all modules
	$upcenters[] = $upcenter_array;
}
//serialize upcenters array, and save it inside cached constants for fast retrival.
$ser_upc_arr = serialize($upcenters);
cache_def('UPCENTERS', $upcenters);
//TODO: remove this. exec("echo " . escapeshellarg("define(UPCENTERS, $ser_upc_arr)") . " >> $sloc/cache/constants.php");