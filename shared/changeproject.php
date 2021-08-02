<?php 
session_start();
if($_POST["project_id"]) $_SESSION["project_id"]=$_POST["project_id"];
unset($_SESSION["project_name"]);
echo json_encode(array("page"=>$_POST["page"]));
