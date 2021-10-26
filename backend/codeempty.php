<?php
#checkpermissions()
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("codingadmin");
$task_id=$_POST["task_id"];
$q='select items from tasks where `task_id`='.$task_id;
$result=$mysqli->query($q);
$items="[".implode(",",array_map(function($i) {return '{\\"code\\":\\"0\\",\\"item_name\\":\\"'.$i.'\\"}';},
	array_keys(json_decode($result->fetch_assoc()["items"],true)["items"])))."]";
$log.="\n".$q;

$q='insert IGNORE into coded (`response_id`,`codes`,`coder_id`,`isdoublecode`) select `response_id`, \''.$items.'\',0,0 from responses where response="" and `task_id`='.$task_id ;
$mysqli->query($q);
$log.="\n".$q;
$res["log"]=$log;
$res["affected"]=$mysqli->affected_rows;
echo json_encode($res);
