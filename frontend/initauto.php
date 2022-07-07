<?php
session_start();
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");


$q='select task_name,task_data,items from tasks where task_id='.$_POST["task_id"];
$result=$mysqli->query($q);
$task=$result->fetch_assoc();
$res["data"]=json_decode($task["task_data"]);

$itemobj=json_decode($task["items"],true); 
$items=$itemobj["items"];
$itemorder=$itemobj["order"]?$itemobj["order"]:array();
$extra=array_diff(array_keys($items),$itemorder);
$itemorder=array_merge($itemorder,$extra);
$res["items"]=$itemorder;



$q=($_POST["subtask_ids"]?
	'select r.response,r.response_id , concat(\'{\',group_concat(concat(\'"\',task_name,\'":"\',r2.response,\'"\') separator ","),\'}\') as subtasks from responses r left join responses r2 on r.testtaker=r2.testtaker and r.response_time=r2.response_time left join tasks t on t.task_id=r2.task_id where r.task_id='.$_POST["task_id"].' and r2.task_id in ('.$_POST["subtask_ids"].') group by r2.testtaker '
:
	'select response,response_id '.$subtasks.'
				from responses 
				where task_id='.$_POST["task_id"]);
$log.=$q;
$result=$mysqli->query($q);
$res["responses"]=array();
if($result) {
	while($r=$result->fetch_assoc()) {
		$response=array("response_id"=>$r["response_id"],"response"=>array($task["task_name"]=> $r["response"]));
		if($_POST["subtask_ids"] and is_array(json_decode($r["subtasks"],true))) $response["response"]=array_merge($response["response"],json_decode($r["subtasks"],true));
		$res["responses"][]=$response;
	}
}
$res["log"]=$log;
$res["warning"]=$warning;
echo json_encode($res);
