<?php
#checkpermissions()
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("projectadmin");
$log.=print_r($_POST,true);
$unittype=$_POST["unittype"];
$unit_id=($unittype=="opencodingadmin"?0:$_SESSION["project_id"]);
if($_POST["given"]=="true")
	$q='insert IGNORE into user_permissions (`user_id`, `unittype`,`unit_id`) VALUES ('.$_POST["user_id"].',"'.$unittype.'",'.$unit_id.')';
else 
	$q='delete from user_permissions where `user_id`='.$_POST["user_id"].' and `unittype`="'.$unittype.'" and `unit_id`='.$unit_id;
if($_POST["user_id"]==$_SESSION["user_id"]) {
	if($_POST["given"]=="true") $_SESSION["perms"][$unittype][$unit_id]=true;
	else unset($_SESSION["perms"][$unittype][$unit_id]);
}
$mysqli->query($q);
$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);
