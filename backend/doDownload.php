<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("projectadmin");

$task_ids=json_decode($_POST["tasks"]);
$dataformat=$_POST["dataformat"];



$q='select if(t.clone_task_id!=0,tc.item_prefix,t.item_prefix) as item_prefix,if(t.clone_task_id!=0,tc.items,t.items) as items,testtaker,codes'.($dataformat=="coders"?',username':'').' from tasks t left join tasks tc on t.clone_task_id=tc.task_id left join responses r on t.task_id=r.task_id left join coded c on r.response_id=c.response_id '.($dataformat=="coders"?'left join users u on coder_id=user_id ':'').' where t.task_id in ('.implode(",",$task_ids).')'.($dataformat=="matrix"?' and isdoublecode=0':''); //If matrix: Select the first code

$result=$mysqli->query($q);
if(!$result) {
	echo "ERROR: \n".$q;
	exit;
}
$list=array();
$allitems=array();
$allcoders=array();
while($r=$result->fetch_assoc()) {
	$itemobj=json_decode($r["items"],true); 
	$items=$itemobj["items"];
	if($items) {
		$itemorder=$itemobj["order"]?$itemobj["order"]:array();
		$extra=array_diff(array_keys($items),$itemorder);
		$item_prefix=$r["item_prefix"];
		$items=array_map(function($i) use($item_prefix) {return $item_prefix.$i;},array_merge($itemorder,$extra));
		$allitems=array_unique(array_merge($allitems,$items));
		$codes=json_decode($r["codes"],true);
		$tmpcodes=array();
		foreach($codes as $c) {
			$tmpcodes[$item_prefix.$c["item_name"]]=$c["code"];
		}
		$coded=array();
		foreach($items as $i) {
			$coded[$i]=$tmpcodes[$i];
		}
		$tt=$r["testtaker"];
		if($dataformat=="coders") {
			if(!$list[$tt]) $list[$tt]=array();
			$coder=$r["username"]?$r["username"]:_("Auto-coded");
			if(!in_array($coder, $allcoders)) $allcoders[]=$coder;
			foreach($coded as $item=>$code) {
				if(!$list[$tt][$item]) $list[$tt][$item]=array();
				$list[$tt][$item]=array_merge($list[$tt][$item],array($coder=>$code));
			}
		} else {
			if(!$list[$tt]) $list[$tt]=array();
			for($ttno=0;$ttno<=count($list[$tt]);$ttno++) {
				if(!$list[$tt][$ttno]) $list[$tt][$ttno]=array();
				if(!array_intersect_key($coded,$list[$tt][$ttno])) break;
			} 
			$list[$tt][$ttno]=array_merge($list[$tt][$ttno],$coded);
		}
	}
}
$csv=array();
if($dataformat=="coders") {
	$csv[0]=$allcoders;
	array_unshift($csv[0],"testtaker","item");
	foreach($list as $testtaker=>$items) {
		foreach($items as $item=>$coded) {
			$i=count($csv);
			$csv[$i]=array($testtaker,$item);
			foreach($allcoders as $coder) {
				$csv[$i][]=(isset($coded[$coder])?$coded[$coder]:"NA");
			}
		}
	}	
} else {
	$csv[0]=$allitems;
	array_unshift($csv[0],"testtaker");
	foreach($list as $testtaker=>$codeline) {
		foreach($codeline as $coded) {
			$i=count($csv);
			$csv[$i][0]=$testtaker;
			foreach($allitems as $item) {
				$csv[$i][]=(isset($coded[$item])?$coded[$item]:"NA");
			}
		}
	}
}
// $tmpout=$secretdir."tmpout".rand().".csv";
// 
// $fp = fopen($tmpout, 'w');
header('Content-Type: text/csv');
header('Content-disposition: attachment; filename='._("opencoding".rand().".csv"));
$fp = fopen('php://output', 'w');

foreach ($csv as $fields) {
    fputcsv($fp, $fields,";");
}

fclose($fp);

// header('Content-Length: ' . filesize($tmpout));

// readfile($tmpout);
// unlink($tmpout);

