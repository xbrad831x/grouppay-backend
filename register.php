<?php
include "db_connect.php";

$en_pass = sha1($_REQUEST['Password']);

$db = new DB();
$db->register_user($_REQUEST['Username'], $en_pass);


?>