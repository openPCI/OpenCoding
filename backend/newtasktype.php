<?php

$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("opencodingadmin");

$q='insert into tasktypes (tasktype_name,variables) values ("New tasktype","{}")';

$mysqli->query($q);
$test_id=$mysqli->insert_id;
$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);
