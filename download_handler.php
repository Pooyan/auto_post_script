<?php
/*
 * TODO: add thumbnailer
 * TODO: add mediainfo 
 * TODO: hand information to samim's script
 */


//registered in rTorrent at system.method.set_key = event.download.finished, download_handler, "execute= PHP download_handler.php"
//registered in checker.php, invoked regularly based on Cron

//t_ means temp files

//TODO: Secure script from GET, POST.

require_once(cache/constants.php);

//constants
$sloc = SCRIPT_LOCATION;
$msgfileloc = MSGFILELOC;
$afmsgfileloc = AFMSGFILELOC;
$msg = MSG;
$msgfilename = MSGFILENAME;
$msgfileext = MSGFILEEXT;
$znamecom = ZNAMECOM;
$zsplit = ZSPLIT;
$zcom = ZCOM;

//init
exec("cd /var/download");
exec("ls -1p", $t_files); //returnes each file in one row, so exec will return an array of each row.
						  //option "p" will append a slash to end of directories
exec("mv /var/download/* /var/download/processing");//TODO: not all, those which is listed
exec("cd /var/download/processing");

//$fname is generated using ls

foreach($t_files as $t_file){
	$fname = $t_file;
	
	//make a new tmp dir per file
	exec("mkdir $fname" . ".dir");
	//make content folder of this file
	exec("mkdir $sloc/content/$fname");
	//moves $fname to $fname.dir
	exec("mv $fname $fname" . ".dir"); //DEBUG: check if mv promts for yes or not.
	exec("cd $fname" . ".dir");
	
	//uncompress
	exec("dtrx $fname -d ./", $chal); //TODO: remove if not failed.
	if($chal != ""/*TODO: error text*/){
		exec("rm -f $fname");
		exec("ls -1p", $fname);
	}
	
	
	//obtain mediainfo and save it to xml file
	exec("cd /var/download/processing/$fname.dir && mediainfo $fname", $medinf);
	exec("echo $medinf >> $sloc/content/$fname/$fname.xml");
	//generate thumbnails
	exec("mtn -o '.jpg' -b 0.6 -w 0 -c 3 -r 3 -O $sloc/content/$fname /var/download/processing/$fname.dir/$fname"); // TODO: config mtn.
	
	//to make a sample file
	//$msgfileloc, message file location is the header of sample file
	//$msg, message is body of the sample file
	//$afmsgfiledoc, after message file location is the footer of sample file
	//$msgfilename, message file name is name of sample file to save
	//$msgfileext, message file extenision is extenision of sample file. Recommended value is html
	//
	//prepares output file
	exec("cat $msgfileloc", $msgcontent);
	exec("cat $afmsgfileloc", $afmsgcontent);
	$tmp = $msgcontent . ' ' + $msg . ' ' . $afmsgcontent;
	//prepares the Unix command
	$tmp .= ' >> ' . $msgfilename . $msgfileext;
	//makes the file
	exec("echo $tmp");
	$tmp = null; //TODO: remove failsafe/debug code
	
	//compress and split
	$zname = $fname . $znamecom;
	//option v will splites archive, and option z will add a comment to it
	//exec("zip" + " " + "$zname" + " " +  "* -s" + " " + "$zsplit" + " -z" + "$zcom");
	exec("rar" + " " + "a" + " " + "$zname" + " " +  "*" + " " + "-v" + "$zsplit" + "k" + " -c" + "$zcom"); // $zsplit is in format of Mega Bytes not KBs 
	//exec("rar a $zname * -v$zsplit/k -c $zcom"); //TODO: use this if possible
	
	
	//preparing upload location
	exec("mkdir /var/upload/$fname");
	exec("mv /var/download/processing/$fname.dir/*.rar /var/upload/$fname");
	exec("mv /var/download/processing/$fname.dir/*.zip.* /var/upload/$fname");
	exec("rm -f /var/download/$fname");
	exec("rm -f /var/download/processing/$fname.dir");
	//implement thumbnailer
	code.google.com/p/ffmpegthumbnailer/
	
	//implement mediainfo
	
	exec("PHP -f $sloc" . "/upload.php");
}