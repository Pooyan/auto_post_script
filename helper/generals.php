<?php
include_once ('../cache/constants.php');


/*
 * This function designed to manipulate a set of constants as cache.
 * You can simply add new constants to it. however it will handle duplicated
 * $name's.
 * 
 * report bugs to pekhee@gmail.com whith title of BUG:APS. thanks in advance.
 * 
 * $name: name of the constant to add
 * $value: value of the constant.
 * $location: where the file should be loaded and saved.
 * 
 * @author: Pooyan Khosravi
 * @url: pooyankhosravi.me/auto_post_script/helper/generals.php#cache_def
 */
function cache_def ($name, $value, $location = null) {
	//init
	if ($location == null) $location = "$t_sloc/cache/constants.php";
	$t_sloc = SCRIPT_LOCATION;
	
	//load cache and delete it.
	exec("cat $location", $cache);
	exec("rm -f $location");
	
	//make new string.
	$def_str = 'define(' . strtoupper($name) . ', ' . "$value" . ");\n";
	
	//if the constant exists, replace it, if not add it.
	$id = array_search(strtoupper($name), $cache);
	if($id == false || $id == 0 || $id == null) $cache[] = $def_str;
	else $cache[$id] = $def_str;
	
	//make new string from manipulated array.
	$t_full_string = null;
	foreach($cache as $t_appendable_string){
		$t_full_string .= $t_appendable_string . '/n';
	}
	$cache_str = $t_full_string;
	
	//write string to constants file.
	$echo_to_file = 'echo ' . escapeshellarg(cache_str) . " >> $location";
	exec($echo_to_file);
	$t_sloc = null; // TODO: remove failsafe/DEBUG code.
}

	/*
	 * checks if movie info with this title is avaible 
	 * 
	 * $title: Ussualy in the form of MOVIE_NAME YEAR , the function returns a boolean.
	 * @author: Samim Pezeshki
	 * @url: pooyankhosravi.me/auto_post_script/helper/generals.php#is_info_available
	 */
function is_info_available($title) {
	//IMDB API Options
	$plot='full'; //Plot format full or simple
	$limit=1; //Number of results

	$matches = '';
	preg_match('/.+(?=20[0-9]{2}|19[0-9]{2})/',$title,$matches);
	$santizied = @$matches[0];
	 
	$reply=file_get_contents('http://imdbapi.org/?title='.$santizied.'&type=json&plot='.$plot.'&episode=0&limit='.$limit.'&yg=0&mt=M&lang=en-US&offset=&aka=simple&release=simple');
	$obj = json_decode($reply,true);

	if (@is_null($obj[0]['title'])) {
		$download=FALSE;
	} else {
		$download=TRUE;
	}
	return $download;
}
















