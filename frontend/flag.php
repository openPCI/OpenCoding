<?php
session_start();

$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm();

if($_SESSION["response_id"]!=$_POST["response_id"]) $warning=_("Response-id doesn't match.");
else {
	switch($_POST["actiontype"]) {
		case "flag": 
			$q='insert into flags (response_id,flagstatus,coder_id,comments,manager_id) VALUE ('.$_POST["response_id"].',"'.$_POST["status"].'",'.($_POST["flaghandling"]=="true"?$_POST["coder_id"]:$_SESSION["user_id"]).',"[]",0) on duplicate key update flagstatus="'.$_POST["status"].'", manager_id='.($_POST["flaghandling"]=="true"?$_SESSION["user_id"]:'manager_id');
			$mysqli->query($q);
		break;
		case "comment":
			if(trim($_POST["comment"])) {
				$comment=$mysqli->real_escape_string(trim($_POST["comment"]));
				$q='update flags set `comments`=JSON_ARRAY_APPEND(`comments`,"$",CAST(CONCAT(\'{"username":"\',(SELECT username from users where user_id='.$_SESSION["user_id"].'),\'","commenttime":"\',NOW(),\'","comment": "'.$comment.'"}\') AS JSON)) where response_id='.$_POST["response_id"];
				$mysqli->query($q);
			}
		break;
	}
}
$log.="\n".$q;
$res["log"]=$log;
$res["warning"]=$warning;
echo json_encode($res);
