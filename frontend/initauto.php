<?php
session_start();
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");

$q='select task_data,items from tasks where task_id='.$_POST["task_id"];
$result=$mysqli->query($q);
$task=$result->fetch_assoc();
$res["data"]=json_decode($task["task_data"]);
$res["items"]=json_decode($task["items"]);
$q='select response,response_id
				from responses 
				where task_id='.$_POST["task_id"];

$result=$mysqli->query($q);
$res["responses"]=$result->fetch_all(MYSQLI_ASSOC);

$res["log"]=$log;
$res["warning"]=$warning;
echo json_encode($res);
