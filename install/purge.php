<?php

//TODO: implement purge using http://www.cyberciti.biz/tips/uninstall-files-installed-from-a-source-code-tar-ball.html
//TODO: purge applications

exec('rm -rf /var/download');
exec('rm -rf /var/upload');
exec("rm -f  ~/.rtorrent.rc");
