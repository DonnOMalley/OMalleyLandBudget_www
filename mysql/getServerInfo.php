<?php
mysql_connect('localhost', 'root', 'Dreck1030J')
    or die('Could not connect: ' .mysql_error());

echo("MySQL server version: " .mysql_get_server_info());
?>
