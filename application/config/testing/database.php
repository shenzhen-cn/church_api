<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;


// $dbName = "192.168.1.105";
$dbName = "localhost";
$dbUser = "root";
$dbPassword = "root";


$db['default'] = array(
	'dsn'	=> '',
	'hostname' => $dbName,
	'username' => $dbUser,
	'password' => $dbPassword,
	'database' => 'church',
	'dbdriver' => 'mysqli', 
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
