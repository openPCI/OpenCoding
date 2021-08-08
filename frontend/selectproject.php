<?php
session_start();
$_SESSION["project_id"]=$_POST["project_id"];
unset($_SESSION["project_name"]);
echo json_encode(array());
