<?php
//install's script
//TODO: promts for db username and password.
//TODO: checks for requiered extenisions. (although there is no need for explicitly checking)

//install rTorrent
//configu rtorrent
//install wordpress
//install plowshare

//init
$sloc = str_replace("install", '', dirname(__FILE__));

//first make sure system is clean.
exec("php -f $sloc/install/purge.php");

//		DEPENDENCIES
//curl and php-cli
echo "<h1>building dependencies</h1>";
exec("yum install curl");
echo 'cURL has been installed \n';
exec("yum install php-cli");
echo 'Command Line PHP has been installed \n';

echo '<h1>Installing Software Packages</h1>';
//7zip
echo "<b>installing 7zip<b>";
exec("cd /tmp");
exec("wget http://downloads.sourceforge.net/project/p7zip/p7zip/9.20.1/p7zip_9.20.1_src_all.tar.bz2?r=http%3A%2F%2Fsourceforge.net%2Fprojects%2Fp7zip%2Ffiles%2Fp7zip%2F9.20.1%2F&ts=1360501694&use_mirror=superb-dca3");
exec("tar -xvjf p7zip_9.20.1_src_all.tar.bz2");
exec("cd p7zip_9.20.1");
exec("./configure");
exec("make");
exec("make install");
exec("make clean");



//rTorrent
echo "<b><h3>installing rTorrent</h3></b>";
//download libtorrent and rtorrent
exec("cd /tmp 					&& 	wget 	http://libtorrent.rakshasa.no/downloads/libtorrent-0.13.3.tar.gz");
exec('cd /tmp 					&& 	wget 	http://libtorrent.rakshasa.no/downloads/rtorrent-0.9.3.tar.gz');
exec('cd /tmp					&& 	wget	http://ftp.gnome.org/pub/GNOME/sources/libsigc++/2.3/libsigc++-2.3.1.tar.xz');
//uncompress
exec('cd /tmp/ 					&& 	tar -xzf libtorrent-0.13.3.tar.gz');
exec('cd /tmp/ 					&& 	tar -xzf rtorrent-0.9.3.tar.gz');
exec('cd /tmp/ 					&& 	tar -xJf libsigc++-2.3.1.tar.xz');

//build libsigc++
exec('cd /tmp/libsigc++-2.3.1 	&& 	./configure');
exec('cd /tmp/libsigc++-2.3.1 	&& 	make');
exec('cd /tmp/libsigc++-2.3.1 	&& 	make 	install');
//add libsigc++ to PKGCONFIGPATH
exec('export PKG_CONFIG_PATH=/usr/local/lib/pkgconfig');
//build libtorrent
exec('cd /tmp/libtorrent-0.13.3 && 	./configure');
exec('cd /tmp/libtorrent-0.13.3 && 	make');
exec('cd /tmp/libtorrent-0.13.3 && 	make 	install');
//build rtorrent
exec('cd /tmp/rtorrent-0.9.3 	&& 	./configure');
exec('cd /tmp/rtorrent-0.9.3 	&& 	make');
exec('cd /tmp/rtorrent-0.9.3 	&& 	make 	install');
//configures rtorrent's rc file
exec("php -f $sloc/conf/rtorrent.rc.php > ~/.rtorrent.rc");


/*
 * TODO: install and configure wordpress
echo "<h1>installing wordpress</h1>";
exec("php wordpress/install.php?username=&password=");
*/

//plowshare
echo "<b>installing plowshare</b>";
exec("cd /tmp");
exec("wget http://plowshare.googlecode.com/files/plowshare4-snapshot-git20130126.0caced8.tar.gz");
exec("tar -xvzf plowshare4-snapshot-git20130126.0caced8.tar.gz");
exec("cd plowshare4-snapshot-git20130126.0caced8");
exec("./configure");
exec("make");
exec("make install");


//install mediainfo
echo "<b>installing mediainfo</b>";
exec("rpm -i http://mediaarea.net/download/binary/libzen0/0.4.28/libzen0-0.4.28-1.x86_64.CentOS_6.rpm");
exec("rpm -i http://mediaarea.net/download/binary/libmediainfo0/0.7.61/libmediainfo0-0.7.61-1.x86_64.CentOS_6.rpm");
exec("rpm -i http://mediaarea.net/download/binary/mediainfo/0.7.61/mediainfo-0.7.61-1.x86_64.CentOS_6.rpm");

/*
 * except ffmpegthumbnailer, this script will use movie thumbnailer (mtn)
	//		ffmpegthumbnailer
	//	ffmpeg
	//EPEL
	exec('rpm -Uvh http://download.fedoraproject.org/pub/epel/6/i386/epel-release-6-8.noarch.rpm');
	//RPM FUSION
	exec('yum localinstall --nogpgcheck http://download1.rpmfusion.org/free/el/updates/6/i386/rpmfusion-free-release-6-1.noarch.rpm http://download1.rpmfusion.org/nonfree/el/updates/6/i386/rpmfusion-nonfree-release-6-1.noarch.rpm');
	//ffmpeg
	exec('yum install ffmpeg');
	//config ffmpeg
	
	
	//	ffmpegthumbnailer
	exec('cd /tmp && wget http://ffmpegthumbnailer.googlecode.com/files/ffmpegthumbnailer-2.0.8.tar.gz');
	exec('cd /tmp/ffmpegthumbnailer-2.0.8 && ./configure');
	exec('cd /tmp/ffmpegthumbnailer-2.0.8 && make');
	exec('cd /tmp/ffmpegthumbnailer-2.0.8 && make install');
*/

//Configuration
echo "<hr /><br />";
echo "<h1>Configuring Installation</h1>";

exec("mkdir /var/download");
exec("mkdir /var/download/processing");
exec("mkdir /var/upload");
exec("mkdir /var/upload/processing");

exec("mkdir $sloc/content");
exec("mkdir /var/dottorrent");

echo "<h1><b>Done</b></h1>";
