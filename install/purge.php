<?php

//TODO implement purge using http://www.cyberciti.biz/tips/uninstall-files-installed-from-a-source-code-tar-ball.html
exec('rpm -e mediainfo');
exec('yum erase rtorrent');
exec('yum erase 7zip');

exec('rm -rf /var/download');
exec('rm -rf /var/upload');