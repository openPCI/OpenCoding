<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("projectadmin");

$task_ids=json_decode($_POST["tasks"]);

$q='select items,testtaker,codes from tasks t left join responses r on t.task_id=r.task_id left join coded c on r.response_id=c.response_id where t.task_id in ('.implode(",",$task_ids).') and isdoublecode=0';

$result=$mysqli->query($q);
$log.="\n".$q;
$list=array();
$allitems=array();
while($r=$result->fetch_assoc()) {
	$itemobj=json_decode($task["items"],true); 
	$items=$itemobj["items"];
	$itemorder=$itemobj["order"]?$itemobj["order"]:array();
	$extra=array_diff(array_keys($items),$itemorder);
	$items=array_merge($itemorder,$extra);

	$allitems=array_unique(array_merge($allitems,$items));
	$codes=json_decode($r["codes"],true);
	$tmpcodes=array();
	foreach($codes as $c) {
		$tmpcodes[$c["item_name"]]=$c["code"];
	}
	$coded=array();
	foreach($items as $i) {
		$coded[$i]=$tmpcodes[$i];
	}
	if(!$list[$r["testtaker"]]) $list[$r["testtaker"]]=array();
	$list[$r["testtaker"]]=array_merge($list[$r["testtaker"]],$coded);
}
$csv=array();
$csv[0]=$allitems;
array_unshift($csv[0],"testtaker");
foreach($list as $testtaker=>$coded) {
	$i=count($csv);
	$csv[$i][0]=$testtaker;
	foreach($allitems as $item) {
		$csv[$i][]=(isset($coded[$item])?$coded[$item]:"NA");
	}
}
$tmpout=$secretdir."tmpout".rand().".csv";

$fp = fopen($tmpout, 'w');

foreach ($csv as $fields) {
    fputcsv($fp, $fields,";");
}

fclose($fp);

header('Content-Type: text/csv');
header('Content-disposition: attachment; filename='._("opencoding".rand().".csv"));
// header('Content-Length: ' . filesize($tmpout));

readfile($tmpout);
unlink($tmpout);

