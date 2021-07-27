<?php
session_start();
#checkpermissions()
// checkperm();
// else $user_id=$_SESSION["user_id"];
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("opencodingadmin");

if($_POST["getsave"]=="get") $q='select '.$_POST["edittype"].' as content from tasktypes where tasktype_id='.$_POST["tasktype_id"];
elseif($_POST["getsave"]=="save") $q='update tasktypes set '.$_POST["edittype"].'=CONCAT_WS(CHAR(10 using utf8),"'.preg_replace("/\\\\n/",'","',$mysqli->real_escape_string($_POST["content"])).'") where tasktype_id='.$_POST["tasktype_id"];
$result=$mysqli->query($q);
if($_POST["getsave"]=="get") $res["content"]=str_replace("&slashn;","\\n",$result->fetch_assoc()["content"]);
$log.="\n".$q;
$res["log"]=$log;
$res["warning"]=$warning;
echo json_encode($res);
