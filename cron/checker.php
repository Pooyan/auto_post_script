<?php
//checks for new files to be downloaded
//instantiates a RSS parser
//reads global variables from conf
//reads RSS and check for new files
//TODO: initiate purge

include_once('helper/simplepie/autoloader');
include_once('helper/generals.php');
include_once('cache/constants.php');

$sloc = SCRIPT_LOCATION;

$dtfeed = new SimplePie();

$dtfeed->set_feed_url(''); // TODO: add url
$dtfeed->enable_cache(true);
$dtfeed->set_cache_location("$sloc/cache");// TODO, DEBUG: check if location is correct.
$dtfeed->init();
$dtfeed->handle_content_type();

$items = $dtfeed->get_items(0, 0);
$links = $dtfeed->get_links();

$pitems = unserialize(ITEMS);
$fplink = $pitems[0]->get_link();

//fplink, is first previous link that script downloaded last time, this loop will searches to find fplink's location in current links.
$n = 0;
foreach ($items as $item){
	$link = $item->get_link();
	if ($link != $fplink) $n++;
	else break;
}

//will execute and pass that number of new links to rTorrent if information of movie is available at IMDB.
for ($n>0; $n--;){
	if(is_info_available($items[$n]->get_title())) exec("rtorrent " . $items[$n]->get_link() . " &");
}

//serialize and save current links az previous links
$ser_items = serialize($items);
cache_def('ITEMS', $ser_items, "$sloc/cache/feeds.php");