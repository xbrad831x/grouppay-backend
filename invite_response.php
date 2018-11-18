<?php
include "db_connect.php";

$userid = $_REQUEST["userid"];
$event_id = $_REQUEST["eventid"];
$response = $_REQUEST["response"];
$amountdue = $_REQUEST["amountdue"];

$db = new DB();

$db->invite_response($event_id, $userid, $response, $amountdue);

?>