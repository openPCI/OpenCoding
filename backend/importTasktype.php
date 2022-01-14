<?php
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
checkperm("opencodingadmin");


$fields=array("manualauto","tasktype_name","tasktype_description","playareatemplate","responseareatemplate","codeareatemplate","tasktype_instructions","insert_script","variables","styles");
if (($handle = fopen($_FILES["importtasktypes"]["tmp_name"], "r")) !== FALSE) {
	$head = fgetcsv($handle, 0, ";");
	if(array_intersect($head,$fields)!=$fields) {
		print_r($head);
		echo "notcompatible";exit;
	}
	$quothead=array_map(function($h) { return "`".$h."`";},$head);
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        array_walk($data,function(&$d) use($mysqli) {$d=$mysqli->real_escape_string($d);});
		$quotdata=array_map(function($h) { return '"'.$h.'"';},$data);
		
		$q='insert into tasktypes ('.implode(',',$quothead).') VALUES ('.implode(',',$quotdata).') on duplicate key update '.implode(", ",array_map(function($h) { return $h.'=VALUES('.$h.')';},$quothead));
// 		echo $q;
		$result=$mysqli->query($q);
		if(!$result) {
			echo _("Error in query").": ".$q;
			exit;
		}
    }
    fclose($handle);
}
echo "success";
