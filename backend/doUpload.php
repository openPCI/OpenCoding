<?php
#checkpermissions()
// if(!$_SESSION["user_id"]) exit;
// else $user_id=$_SESSION["user_id"];
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
$responses=$_POST["responses"];
$cols=array_shift($responses);

//  $log.=print_r($cols,true);


$q="insert into tests (test_name,project_id) VALUE ('".$_POST["test_name"]."',".$_SESSION["project_id"].") on duplicate key UPDATE test_id=LAST_INSERT_ID(test_id)";
$mysqli->query($q);
$test_id=$mysqli->insert_id;
$newtests=$mysqli->affected_rows;
$log.="\n".$q;
$tasks=array_slice($cols,2);
$ntasks=count($tasks);
$newtasks=0;
$log.=print_r($tasks,true);
for($i=0;$i<$ntasks;$i++) {
	$q='insert into tasks (task_name,tasktype_id,items,tasktype_variables,test_id) VALUES ("'.$tasks[$i].'",0,"{}","{}",'.$test_id.')';
	$mysqli->query($q);
	$task_ids[$i]=$mysqli->insert_id;
	$newtasks+=$mysqli->affected_rows;
 	$log.="\n".$q;
}

# To avoid this problem: https://dev.mysql.com/doc/refman/8.0/en/packet-too-large.html
$variablesperquery=100;
$queries=$ntasks*count($responses)/$variablesperquery;
$numrowsperquery=ceil($variablesperquery/$ntasks);
$newresponses=0;
for($i=0;$i<=$queries;$i++) {
	if(count($responses)>=$i*$numrowsperquery) {
		$responsesslice=array_slice($responses,$i*$numrowsperquery,($i+1)*$numrowsperquery);
		$q="insert IGNORE into responses (task_id,testtaker,response,response_time) VALUES ".
			implode(",",
				array_map(
					function($respondent) use($task_ids,$ntasks,$mysqli) {
						$testtaker=array_shift($respondent);
						$response_time=array_shift($respondent);
						return implode(",",
							array_map(
								function($response,$thistask_id) use($testtaker,$response_time,$mysqli) {
									return '('.$thistask_id.',"'.$testtaker.'","'.$mysqli->real_escape_string($response).'","'.$response_time.'")';
								},$respondent,$task_ids
							)
						);
					},$responsesslice)
				);
		$log.="\n".$q;
 		$mysqli->query($q);
 		$newresponses+=$mysqli->affected_rows;
	}
}
$res=array("log"=>$log,"newtests"=>$newtests,"newtasks"=>$newtasks,"newresponses"=>$newresponses);
echo json_encode($res);
