<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("projectadmin");


$userinfo=$_POST["userinfo"];
if($userinfo["user_id"]) {
	$password=($userinfo["password"]?',password="'.password_hash($userinfo["password"],PASSWORD_DEFAULT).'"':'');
	$q='update users set `username`="'.$userinfo["username"].'", `email`="'.$userinfo["email"].'" '.$password.' where user_id='.$userinfo["user_id"];
}
else $q='insert into users (`username`, `email`,`password`) VALUE ("'.$userinfo["username"].'","'.$userinfo["email"].'","'.password_hash($userinfo["password"],PASSWORD_DEFAULT).'")';
$mysqli->query($q);
$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);
