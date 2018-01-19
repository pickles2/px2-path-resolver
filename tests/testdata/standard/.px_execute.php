<?php
@ini_set('display_errors', 1);
@ini_set('error_reporting', E_ALL);

chdir(__DIR__);
@require_once( './../../../vendor/autoload.php' );
new picklesFramework2\pickles('./px-files/');
