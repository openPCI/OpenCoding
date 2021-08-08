<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("projectadmin");


if($_POST["test_id"]) {
	$q='delete from tests where test_id='.$_POST["test_id"];
	$mysqli->query($q);
}$log.="\n".$q;
$res["log"]=$log;
echo json_encode($res);
