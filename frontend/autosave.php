<?php
session_start();
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");

// $log.=print_r($_POST,true);

$resp=$_POST["responses"];
$slicelen=100;
$start=0;
while($start<count($resp)) {
	$values=implode(",",array_map(function($response) {
		$items=array_diff_key($response,array_flip(array("response_id","response")));
		global $mysqli;
		return "(".$response["response_id"].",'[".implode(",",array_map(function($i,$k) {
			global $mysqli;
			return '{"item_name":"'.$k.'","code":"'.$mysqli->real_escape_string($i).'"}';
		},$items,array_keys($items)))."]')";
	},array_slice($resp,$start,$start+$slicelen)));
	$q='INSERT INTO coded (response_id,codes) VALUES '.$values.' ON DUPLICATE KEY UPDATE codes=VALUES(codes)';
	$mysqli->query($q);
	$log.="\n".$q;
	$start+=$slicelen;
}
$q="UPDATE tasks set task_data=CAST('".$mysqli->real_escape_string(json_encode($_POST["data"]))."' as JSON) where task_id=".$_POST["task_id"];
$log.="\n".$q;
$result=$mysqli->query($q);

$res["log"]=$log;
$res["warning"]=$warning;
echo json_encode($res);
