<?php
//TODO: Repeated movies
//TODO: Decide what to do if a movie does have some infos on library, and POST in  NEW
//TODO: Directors clickable tags

class movie {
	
	
	var $title='';
	var $time='';
	var $genres=array();
	var $rating='';
	var $plot='';
	var $director='';
	var $casts=array();
        var $rawTitle='';
        var $poster='';
        var $year=1 ;
        var $download=false;
        var $imdbid='';
		
        //constructs by the raw title
    function __construct($rawTitle) {
             $this->rawTitle=$rawTitle;
             $this->setInfo();
        }
       
    //TODO: add tmdb
    //TODO: add backdrops
        
    function setInfo() {
        //Sanatize using Regex
        $santizied=$this->sanitize($this->rawTitle);
        
        
        //IMDB API Options
        $plot='full'; //Plot format Full or Simple
        $limit=1; //Number of results
        
        $reply=file_get_contents('http://imdbapi.org/?title='.$santizied.'&type=json&plot='.$plot.'&episode=0&limit='.$limit.'&yg=0&mt=M&lang=en-US&offset=&aka=simple&release=simple');
        $obj = json_decode($reply,true);
		

        
        if (@is_null($obj[0]['title'])) { 
                $this->download=false;
                }
             else {  
              $this->title=$obj[0]['title'];
              $this->time=$obj[0]['runtime'][0];
              $this->genres=$obj[0]['genres'];
              $this->rating=$obj[0]['rating'];
              $this->plot=$obj[0]['plot_simple'];
              $this->director=$obj[0]['directors'][0];
              $this->casts=$obj[0]['actors'];
              $this->year=$obj[0]['year'];
			  $this->imdbid=$obj[0]['imdb_id'];
              //TODO: Uncomment this
             //$this->poster=$obj[0]['poster'];
			 $this->poster=$this->thePoster($obj[0]['poster']);
              //$this->poster='http://localhost:8080/wordpress/wp-content/uploads/2013/01/d.jpg';
              
              $this->download=TRUE;

             } 
     

    }
	
	//Extracts the movixe title from its XML title
    function sanitize($title) {
	//TODO: Add [req], critical issues, if no YEAR it catches nothing
        $matches='';
	preg_match('/.+(?=20[0-9]{2}|19[0-9]{2})/',$title,$matches);
	
	return @$matches[0];
		}
                
    function thePoster($URL) {

    file_put_contents('../wp-content/uploads/'.$this->imdbid.'.jpg',
        file_get_contents($URL));
		//TODO: Test if local host is OK.
    return 'http://downloadmoviz.com/wp-content/uploads/'.$this->imdbid.'.jpg';

}

    function getInfo() {
        $info=array(
            array('Poster',$this->poster),
            array('Title',$this->title),
            array('Year',$this->year),
            array('Run Time',$this->time),  
            array('Rating',$this->rating),
            array('Plot',$this->plot),
            array('Director',$this->director),
            array('Casts',$this->casts),
            array('Genres',$this->genres),
            
        );
        return $info;
    }
}


class XMLRPClientWordPress
{
    var $XMLRPCURL = "";
    var $UserName  = "";
    var $PassWord = "";


    // Constructor
    public function __construct($xmlrpcurl, $username, $password)
    {
        $this->XMLRPCURL = $xmlrpcurl;
        $this->UserName  = $username;
        $this->PassWord = $password;
    }


    function send_request($requestname, $params)
    {
        $request = xmlrpc_encode_request($requestname, $params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_URL, $this->XMLRPCURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        $results = curl_exec($ch);
        curl_close($ch);
        return $results;
    }
    //TODO: add rating as field
	//Uses Metaweblog XMLRPC
    function create_post($title,$body,$category,$keywords='',$encoding='UTF-8')
    {
        $title = htmlentities($title,ENT_NOQUOTES,$encoding);
        $keywords = htmlentities($keywords,ENT_NOQUOTES,$encoding);
        $content = array(
            'title' => $title,
            'description' => $body,
            'mt_allow_comments' => 0,  // 1 to allow comments
            'mt_allow_pings' => 0,  // 1 to allow trackbacks
            'post_type' => 'post',
            'mt_keywords' => $keywords,
            'categories' => $category //TODO:Add all categories for slider.
        );

        $params = array(0,$this->UserName,$this->PassWord,$content,true);
        return $this->send_request('metaWeblog.newPost',$params);
    }

	// Uses Wordpress XMLRPC
        //TODO: check categories and tags
    function create_post2($title,$body,$category,$keywords='',$fields)
    {
        $title = htmlentities($title,ENT_NOQUOTES,'UTF-8');
        //$keywords = htmlentities($keywords,ENT_NOQUOTES,'UTF-8');
        $content = array(
            'post_title' => $title,
            'post_content' => $body,
            'post_status'    =>  'publish',
            //'post_type'      => 'post' ,
            'custom_fields' => $fields, 
            'terms_names'=>array( "category" => $category,"post_tag"=>$keywords )
        );

        $params = array(0,$this->UserName,$this->PassWord,$content);
        return $this->send_request('wp.newPost',$params);
    }

function test()
    {
       
        $params = array(0,$this->UserName,$this->PassWord);
        return $this->send_request('wp.getTaxonomies',$params);
    }

}

class post {
var $table1='';

    function __construct($movieInfo) {
    $this->makeTable1($movieInfo);
}
    function makeTable1 ($info) {
       
        //Make generes clickable
        $cont='';
         for ($i=0;$i<sizeof($info[8][1]);$i++) {
            $cont=$cont.'<a href="http://downloadmoviz.com/category/genre/'.$info[8][1][$i].'/">'.$info[8][1][$i].'</a>, ';
           }
        
           //Make Table
        $result=
            '<table class="imdbtable">
            <tr>
		<td></td>
		<td><img class="imdbposter" src="'.$info[0][1].'"></img></td>
	</tr>';
	for ($i=1;$i<7;$i++) {
            
            $result=$result.'<tr>
		<td>'.$info[$i][0].'</td>
		<td>'.$info[$i][1].'</td>
	</tr>';
            
        }
        
     
        for ($i=7;$i<8;$i++) {
            
            $result=$result.'<tr>
		<td>'.$info[$i][0].'</td>
		<td>'.implode(', ', $info[$i][1]).'</td>
	</tr>';
            
        }
        $result=$result.'<tr>
		<td>'.$info[8][0].'</td>
		<td>'.$cont.'</td>
	</tr>';
        
       $result=$result.'</table>';
       $this->table1=$result;
    }

}

class Logging {
    // declare log file and file pointer as private properties
    private $log_file, $fp;
    // set log file (path and name)
    public function lfile($path) {
        $this->log_file = $path;
    }
    // write message to the log file
    public function lwrite($message) {
        // if file pointer doesn't exist, then open log file
        if (!is_resource($this->fp)) {
            $this->lopen();
        }
        // define script name
        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        // define current time and suppress E_WARNING if using the system TZ settings
        // (don't forget to set the INI setting date.timezone)
        $time = @date('[d/M/Y:H:i:s]');
        // write current time, script name and message to the log file
        fwrite($this->fp, "$time ($script_name) $message" . PHP_EOL);
    }
    // close log file (it's always a good idea to close a file when you're done with it)
    public function lclose() {
        fclose($this->fp);
    }
    // open log file (private method)
    private function lopen() {
        // in case of Windows set default log file
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $log_file_default = 'c:/php/logfile.txt';
        }
        // set default log file for Linux and other systems
        else {
            $log_file_default = '/tmp/logfile.txt';
        }
        // define log file from lfile method or use previously set default
        $lfile = $this->log_file ? $this->log_file : $log_file_default;
        // open log file for writing only and place file pointer at the end of the file
        // (if the file does not exist, try to create it)
        $this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");
    }
}

