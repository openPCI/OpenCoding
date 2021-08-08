<?php

$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("projectadmin");
$res=array();
// $log.=print_r($_POST,true);
$q='update tasks set `items`=JSON_SET(`items`,"$.order",CAST(\''.json_encode($_POST["order"]).'\' as JSON)) where task_id='.$_POST["task_id"];

if(!$mysqli->query($q)) $res["warning"]=$mysqli->error;

$log.="\n".$q;
$res["task_id"]=$_POST["task_id"];
$res["log"]=$log;
echo json_encode($res);

#
