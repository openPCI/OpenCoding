<?php
#checkpermissions()
session_start();
$relative="../";
include_once($relative."dirs.php");
checkperm("projectadmin");
include_once($shareddir."database.php");

$res=array();
$q='select username,email,user_id from users where username LIKE "%'.$_POST["codername"].'%" or email LIKE "%'.$_POST["codername"].'%" ';
$result=$mysqli->query($q);
if($result->num_rows==1) $res["userfound"]=$result->fetch_assoc();
$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);
