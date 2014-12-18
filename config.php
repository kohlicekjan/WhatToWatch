<?php

//vychozi slozka
define('URL', '/whattowatch/');

//nastaveni databaze
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'what_to_watch');
define('DB_USER', 'root');
define('DB_PASS', '');

//nastaveni cest
define('PATH_CONTROLLERS', './app/controllers/');
define('PATH_LIBS', './app/libs/');
define('PATH_MODELS', './app/models/');
define('PATH_VIEWS','./app/views/');
define('PATH_TEMPLATE','./app/views/template/');
define('PATH_NAVIGATION','./app/views/navigation/');

//vychozi nastaveni stranek
define('DEFAULT_CONTROLLER', 'Index');
define('DEFAULT_METHOD', 'index');
define('DEFAULT_SESSION','whattowatch_sid');

//titulek
define('PAGE_TITLE', 'WhatToWatch');

//heslo
define('CRYPT_KEY','6d4g#n9&3w@r7&');

//pocet radek v tabulce
define('DEFAULT_ROWS_TABLE',50);

//adresa CSFD
define('CSFD_MOVIE','http://csfdapi.cz/movie/');
define('CSFD_AUTHOR','http://csfdapi.cz/author/');

define('NICE_URL','url');

error_reporting(0);