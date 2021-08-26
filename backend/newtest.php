<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("projectadmin");


$q='insert into tests (`test_name`, `project_id`) VALUE ("'.$_POST["test_name"].'",'.$_SESSION["project_id"].')';
$mysqli->query($q);
#$test_id=$mysqli->insert_id;
$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);
