<?php
error_reporting(E_ALL);
ini_set('display_errors','1');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Max-Age: 1000');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/json');

// optional - include if you want access to emojis by name
// full list here: http://unicode.org/emoji/charts/full-emoji-list.html
require 'chatfuel-emoji-list.php';

// include the chatfuel class.
require 'chatfuel.php';
?>