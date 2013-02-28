<?php
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
 ?>
