<?php
#checkpermissions()
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("projectadmin");
 $log.=print_r($_POST,true);
$unittype=$_POST["unittype"];
$unit_id=$_POST["unit_id"];
$q='delete from `assign_'.$unittype.'` where `coder_id`='.$_POST["user_id"].' and `'.$unittype.'_id`='.$unit_id;
$mysqli->query($q);
$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);
