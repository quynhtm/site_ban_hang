<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
$base_url = str_replace('\\','/','http://'.$_SERVER['HTTP_HOST'] . (dirname($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : ''));
$base_url .= $base_url[strlen($base_url)-1] != '/' ? '/' : '';

$dir_root = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT'] . (dirname($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : ''));
$dir_root .= $dir_root[strlen($dir_root)-1] != '/' ? '/' : '';

return array(
    'BASE_URL' 	=> $base_url,
    'DIR_ROOT' 	=> $dir_root,
);