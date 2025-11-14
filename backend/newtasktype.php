<?php

$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("opencodingadmin");

$q='insert into tasktypes (tasktype_name,variables,tasktype_description,playareatemplate,responseareatemplate,codeareatemplate,tasktype_instructions,insert_script,styles) values ("New tasktype","{}","","","","","","","") ON DUPLICATE KEY UPDATE tasktype_name=concat(tasktype_name," ","'.rand(1,1000).'");';

$mysqli->query($q);
$test_id=$mysqli->insert_id;
$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);
