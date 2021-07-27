<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("projectadmin");


$userinfo=$_POST["userinfo"];
$q='insert into users (`username`, `email`,`password`) VALUE ("'.$userinfo["username"].'","'.$userinfo["email"].'","'.md5($userinfo["password"]).'")';
$mysqli->query($q);
$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);
