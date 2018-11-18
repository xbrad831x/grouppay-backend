<?php
include "db_connect.php";

$owner = $_REQUEST["Owner"];
$title = $_REQUEST["Title"];
$amount = $_REQUEST["Amount"];
$people = $_REQUEST["People"];
$description = $_REQUEST["Description"];
$due_date = $_REQUEST["Date"];
$privacy = $_REQUEST["Privacy"];

$db = new DB();

$db->save_event($owner, $title, $amount, $people, $description, $due_date, $privacy);

?>