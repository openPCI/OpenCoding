<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("opencodingadmin");


$q='insert into projects (`project_name`, `unit_id`,`doublecodingpct`) VALUE ("'.$_POST["project_name"].'",1,5)';
$mysqli->query($q);
$project_id=$mysqli->insert_id;
$q='insert into user_permissions (`user_id`, `unit_id`,`unittype`) VALUE ("'.$_SESSION["user_id"].'",'.$project_id.',"projectadmin")';
$mysqli->query($q);
$_SESSION["perms"]["projectadmin"][$project_id]=true;
$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);
