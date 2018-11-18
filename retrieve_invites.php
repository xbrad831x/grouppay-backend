<?php
include "db_connect.php";

$id = $_REQUEST["id"];

$db = new DB();
$db->retrieve_invites($id);

?>