<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("projectadmin");

$responses=json_decode($_POST["responses"]);
$cols=array_shift($responses);

//  $log.=print_r($cols,true);


// $q="insert into tests (test_name,project_id) VALUE ('".$_POST["test_name"]."',".$_SESSION["project_id"].") on duplicate key UPDATE test_id=LAST_INSERT_ID(test_id)";
// $mysqli->query($q);
$test_id=$_POST["test_id"];#$mysqli->insert_id;
// $newtests=$mysqli->affected_rows;
// $log.="\n".$q;
$tasks=array_slice($cols,2);
$ntasks=count($tasks);
$newtasks=0;
#$log.=print_r($tasks,true);
for($i=0;$i<$ntasks;$i++) {
	$q='insert into tasks (task_name,tasktype_id,items,tasktype_variables,task_data,test_id) VALUES ("'.$tasks[$i].'",1,\'{"items":{}}\',"{}","{}",'.$test_id.') on duplicate key UPDATE task_id=LAST_INSERT_ID(task_id)';
	// If the task_name exists in this test, these responses are added to that instead of created as new task.
if(!$mysqli->query($q)) {
	$warning=$mysqli->error;
}
	else {
		$task_ids[$i]=$mysqli->insert_id;
		$newtasks+=$mysqli->affected_rows;
	}
	$log.="\n".$q;
}
if(!$warning) {
	# To avoid this problem: https://dev.mysql.com/doc/refman/8.0/en/packet-too-large.html
	$variablesperquery=25;
	$queries=$ntasks*count($responses)/$variablesperquery;
	$numrowsperquery=ceil($variablesperquery/$ntasks);
	$newresponses=0;
	for($i=0;$i<=$queries;$i++) {
		if(count($responses)>=$i*$numrowsperquery) {
			$responsesslice=array_slice($responses,$i*$numrowsperquery,($i+1)*$numrowsperquery);
			foreach ($responsesslice as $response) {
				$values=array();
					$testtaker=array_shift($response);
					$response_time=array_shift($response);
					for($j=0;$j<count($task_ids);$j++) 
						$values[]='('.$task_ids[$j].',"'.$testtaker.'","'.$mysqli->real_escape_string($response[$j]).'","'.$response_time.'")';
				$q="insert IGNORE into responses (task_id,testtaker,response,response_time) VALUES ".implode(",",$values);
 				#$log.="\n".$q;
				$mysqli->query($q);
				$newresponses+=$mysqli->affected_rows;
			}

		}
	}
}
$res=array("log"=>$log,"newtests"=>$newtests,"newtasks"=>$newtasks,"newresponses"=>$newresponses,"warning"=>$warning);
echo json_encode($res);
