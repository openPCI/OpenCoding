<?php
#checkpermissions()
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("codingadmin");
 $log.=print_r($_POST,true);
$unittype=$_POST["unittype"];
$unit_id=$_POST["unit_id"];
$q='insert IGNORE into `assign_'.$unittype.'` (`coder_id`, `'.$unittype.'_id`) VALUES '.implode(",",array_map(function($user_id) use($unit_id) {
									return '('.$user_id.','.$unit_id.')';
								},$_POST["user_ids"]));
$mysqli->query($q);
$q='insert IGNORE into `user_permissions` (`user_id`, `unittype`,`unit_id`) VALUES '.implode(",",array_map(function($user_id) use($unit_id) {
									return '('.$user_id.',"coding",'.$_SESSION["project_id"].')';
								},$_POST["user_ids"]));
$mysqli->query($q);
$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);
