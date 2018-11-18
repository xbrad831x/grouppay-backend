<?php

if(!isset($_REQUEST["userid"]))
{
	echo json_encode(array("value" => "Invalid Login"));
	exit;
}

include "db_connect.php";
$id = $_REQUEST["userid"];

 $db = new DB();

 $db->request_data($id);


?>