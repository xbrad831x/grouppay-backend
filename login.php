<?php

include "db_connect.php";

if(!isset($_REQUEST["Username"]) || !isset($_REQUEST["Password"]))
{
	echo json_encode(array("value" => "error"));
	exit;
}

$en_pass = sha1($_REQUEST['Password']);

$db = new DB();

$db->log_in_user($_REQUEST['Username'], $en_pass);
