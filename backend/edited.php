<?php

$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
if($_POST["edittable"]=="tasktypes") checkperm("opencodingadmin");
else checkperm("projectadmin");
$res=array();
// for($i=0;$i<strlen($_POST["value"]);$i++) $log.=" ".mb_ord(substr($_POST["value"],$i,1));
$rawvalue=$value=$mysqli->real_escape_string(trim($_POST["value"], "\x00..\x1F\xA0\xAD\xC2\xE2\x80\x8B"));
$edittype=$mysqli->real_escape_string(trim($_POST["edittype"], "\x00..\x1F\xA0\xAD\xC2\xE2\x80\x8B"));
$variable=$mysqli->real_escape_string(trim($_POST["variable"], "\x00..\x1F\xA0\xAD\xC2\xE2\x80\x8B"));
$oldvalue=$mysqli->real_escape_string(trim($_POST["oldvalue"], "\x00..\x1F\xA0\xAD\xC2\xE2\x80\x8B"));
$res["value"]=$_POST["value"];
switch($edittype) {
	case "items": 
	case "variables":
		$path='"$.'.($edittype=="items"?"items.":"");
		if($_POST["edittype2"]=="delete") { 
			$value='JSON_REMOVE(`'.$edittype.'`,'.$path.$oldvalue.'")';
		}
		else if($_POST["edittype2"]=="name") {
			if($oldvalue==$value) {
				echo json_encode($res);
				exit;
			} else {
				$value='JSON_REMOVE(JSON_SET(`'.$edittype.'`,'.$path.$value.'",`'.$edittype.'`->>'.$path.$oldvalue.'"),'.$path.$oldvalue.'")';
				if($edittype=="items") { //Update sort
					$q='update tasks set `items`=IFNULL(JSON_REPLACE(`items`,JSON_UNQUOTE(JSON_SEARCH(`items`,\'one\',"'.$oldvalue.'",NULL,"$.order")),"'.$rawvalue.'"),`items`) where task_id='.$_POST["task_id"];
					if(!$mysqli->query($q)) $res["warning"]=$mysqli->error;
					$log.="\n".$q;
				}
			}
		} else {
			$value='JSON_SET(`'.$edittype.'`,'.$path.$oldvalue.'","'.$value.'")';
		}
		if($_POST["order"]) {
			$q='update tasks set `items`=JSON_SET(`items`,"$.order",CAST(\''.json_encode($_POST["order"]).'\' as JSON)) where task_id='.$_POST["task_id"];
			if(!$mysqli->query($q)) $res["warning"]=$mysqli->error;
		}

	break;
	case "tasktype_variables":
		$value='JSON_SET(`tasktype_variables`,"$.'.$variable.'","'.htmlspecialchars_decode($value).'")';
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
switch($_POST["edittable"]) {
	case "tasktypes":
		$q='update tasktypes set `'.$edittype.'`='.$value.' where tasktype_id='.$_POST["tasktype_id"];
	break;
	case "tests":
		$q='update tests set `'.$edittype.'`='.$value.' where test_id='.$_POST["test_id"];
	break;
	case "projects":
		$q='update projects set `'.$edittype.'`='.$value.' where project_id='.$_POST["project_id"];
	break;
	default: 
		$q='update tasks set `'.$edittype.'`='.$value.' where task_id='.$_POST["task_id"];
}
if(!$mysqli->query($q)) $res["warning"]=$mysqli->error;

$log.="\n".$q;
$res["task_id"]=$_POST["task_id"];
$res["log"]=$log;
echo json_encode($res);
