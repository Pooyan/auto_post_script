<?php
 include_once 'library.php';
  $name=$_GET['name'];
        $myMovie= new movie($name);
   $format=$_GET['format'];
   $file_size=$_GET['file_size'];
 
 if ($myMovie->download) {
     echo 'Movie is available. ';
     echo ' Title : '.$myMovie->title;
      $info=$myMovie->getInfo();
      $myPost=new post($info);
      
   $XMLRPC=''; // XMLR Address
   $username = ''; //username
   $password= ''; // password
$send = new XMLRPClientWordPress($XMLRPC, $username , $password );


$result= $send->create_post2(
        $myMovie->title.' '.$myMovie->year, //Title
        $myPost->table1, // Content of the Post
        array_merge($myMovie->genres,array('Genre')), //Genres as categories
        $myMovie->casts, // Casts as tags
        array( //Custom Fields
            array( "key" => "format", "value" => $format ),
            array( "key" => "file_size", "value" => $file_size ),
                )
        ); 

//$result=$send->test();
   var_dump(xmlrpc_decode($result));
        
 } else {
	echo 'Warning: Movie is not available';
    $log = new Logging();
	$log->lfile('mylog');
	$log->lwrite($name);
	$log->lclose();

 }
 
