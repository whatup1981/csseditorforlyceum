<?php
require_once('../../../private.php');
require_once('../../../wp-blog-header.php');
header("Content-type: text/css"); 
$css = get_option('cssfile');
echo stripcslashes($css[$_GET['cssid']]['csscontent']);

?>
