<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("opencodingadmin");

$tasktype_id=$_POST["tasktype_id"];

$q='select * from tasktypes where '.($tasktype_id?'tasktype_id='.$tasktype_id:'1');

$result=$mysqli->query($q);
if(!$result) {
	echo "ERROR: \n".$q;
	exit;
}
header('Content-Type: text/csv');
header('Content-disposition: attachment; filename='._("opencoding".rand().".csv"));
$fp = fopen('php://output', 'w');
$first=true;
while($r=$result->fetch_assoc()) {
	unset($r["tasktype_id"]);
	if($first) fputcsv($fp, array_keys($r),";");
	$first=false;
	fputcsv($fp, $r,";");
}

fclose($fp);

// header('Content-Length: ' . filesize($tmpout));

// readfile($tmpout);
// unlink($tmpout);

