<?php

$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
if($_POST["edittable"]=="tasktypes") checkperm("opencodingadmin");
else checkperm("projectadmin");
$res=array();
// for($i=0;$i<strlen($_POST["value"]);$i++) $log.=" ".mb_ord(substr($_POST["value"],$i,1));
$value=$mysqli->real_escape_string(trim($_POST["value"], "\x00..\x1F\xA0\xAD\xC2\xE2\x80\x8B"));
$edittype=$mysqli->real_escape_string(trim($_POST["edittype"], "\x00..\x1F\xA0\xAD\xC2\xE2\x80\x8B"));
$variable=$mysqli->real_escape_string(trim($_POST["variable"], "\x00..\x1F\xA0\xAD\xC2\xE2\x80\x8B"));
$oldvalue=$mysqli->real_escape_string(trim($_POST["oldvalue"], "\x00..\x1F\xA0\xAD\xC2\xE2\x80\x8B"));
switch($edittype) {
	case "items": 
	case "variables":
		if($_POST["edittype2"]=="delete") { 
			$value='JSON_REMOVE(`'.$edittype.'`,"$.'.$oldvalue.'")';
		}
		else if($_POST["edittype2"]=="name") {
			if($oldvalue==$value) {
				echo json_encode($res);
				exit;
			} else
			$value='JSON_REMOVE(JSON_SET(`'.$edittype.'`,"$.'.$value.'",`'.$edittype.'`->>"$.'.$oldvalue.'"),"$.'.$oldvalue.'")';
		} else 
		$value='JSON_SET(`'.$edittype.'`,"$.'.$oldvalue.'","'.$value.'")';
	break;
	case "tasktype_variables":
		$value='JSON_SET(`tasktype_variables`,"$.'.$variable.'","'.$value.'")';
	break;
	case "tasktype_id":
		$q="select variables from tasktypes where tasktype_id=".$value;
		$result=$mysqli->query($q);
		$variables=json_decode($result->fetch_assoc()["variables"]);
		ob_start();
		include("gettasktypevariables.php");
        $res["variables"]=ob_get_clean();
	break;
	default:
		$value='"'.$value.'"';
	break;
}
if($_POST["edittable"]=="tasktypes") 
	$q='update tasktypes set `'.$edittype.'`='.$value.' where tasktype_id='.$_POST["tasktype_id"];
else $q='update tasks set `'.$edittype.'`='.$value.' where task_id='.$_POST["task_id"];

$mysqli->query($q);
$test_id=$mysqli->insert_id;
$log.="\n".$q;
$res["task_id"]=$_POST["task_id"];
$res["log"]=$log;
echo json_encode($res);
