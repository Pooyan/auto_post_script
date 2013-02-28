<?php
//return with status of 
if (count($argv) == 0) exit(1);
include_once(cache/constants.php);
$xml = new SimpleXMLElement("$sloc/content/" . $argv[1] . '.xml', 0, true, '', false);
$xml->addChild('title', $argv[2]);
$xml->asXML("$sloc/content/" . $argv[1] . '.xml');