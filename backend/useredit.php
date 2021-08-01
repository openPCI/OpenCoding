<?php
session_start();
if(!$_SESSION["user_id"]) exit;
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");

$val=($_POST["type"]=="password"?password_hash($_POST["value"],PASSWORD_DEFAULT):$_POST["value"]);
$q='update users set `'.$_POST["type"].'`="'.$val.'" where user_id='.$_SESSION["user_id"];
$mysqli->query($q);
$log.="\n".$q;
$res["message"]=_("Changed");
$res["log"]=$log;
echo json_encode($res);
