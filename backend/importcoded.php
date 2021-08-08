<pre>
<?php 
# This script was developed to import data from a manual human coding process. It is not directly useful, but it can be used as a templete for similar tasks.


ini_set("auto_detect_line_endings", true);
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
$project_id=$_SESSION["project_id"];
$basedir='/home/jeppe/Sciencedata/GBL21/AP2/Kompetencetest/Scoring/Data/Kodere 2019/';
$basedir='../../Kodere 2019/';
echo $basedir;
$itemarr=array();
if ($handle = opendir($basedir)) {
    while (false !== ($coder = readdir($handle))) {
		if ($coder!="." and $coder!=".." and is_dir($basedir.$coder)) {
			echo "\n\n\n*********** ".$coder;
			$coderdir=$basedir.$coder."/Kodede besvarelser/";
			$q='insert into users (username,email,password) VALUES ("'.$coder.'","'.str_replace(" ",".",$coder).'@edu.au.dk","'.password_hash($coder,PASSWORD_DEFAULT).'") on duplicate key UPDATE user_id=LAST_INSERT_ID(user_id)';
			// If the task_name exists in this test, these responses are added to that instead of created as new task.
// 			echo "\n".$q;
			$mysqli->query($q);
			$coder_id=$mysqli->insert_id;

			if($handle2 = opendir($coderdir)) {
				while (false !== ($file = readdir($handle2))) {
					if (!strpos($file,"norm") and !strpos($file,"lock") and strpos($file,".csv") and ($handlecsv = fopen($coderdir.$file, "r")) !== FALSE) {
						echo "\n+++ ".$file;
						$test_name=ucfirst(preg_replace("/(.*?)_.*/","\\1",$file))." E2019";
						$q="insert into tests (test_name,project_id) VALUE ('".$test_name."',".$project_id.") on duplicate key UPDATE test_id=LAST_INSERT_ID(test_id)";
// 			echo "\n".$q;
						$mysqli->query($q);
						$test_id=$mysqli->insert_id;
						$first = true;
						while (($data = fgetcsv($handlecsv, 0, ";")) !== FALSE) {
							if($first) {
								$colnames=array_flip($data);
								$first=false;
								$taskpos=0;
								for($i=1;$i<count($data);$i++) if(!strpos($data[$i],"-") and strpos($data[$taskpos],$data[$i])===false) $taskpos=$i;
// 								echo "\n".$taskpos;
								$task=preg_replace("/\.(?!\.|[0-9]\.|RESPONSE)/"," ",substr($data[$taskpos],1)); //Fjerner X og gør . til mellemrum
								$items=array_map(function($v) {
									if(strpos($v,"-")) 
										return substr($v,strpos($v,"-")+1);
								},array_slice($data,$taskpos+1));
								$items=array_diff($items,array(""));
								if(empty($items)) echo "\n\n!!!!!!!!!!!!!!!!!!!!!!!!! ALERT: Ingen items... !!!!!!!!!!!!!!!!!!!!!!\n";
								$sortitems=$items;
								sort($sortitems);
								echo("\nItems: ".implode(", ",$sortitems));
								if(!$itemarr[$test_id][$task]) {
									$itemarr[$test_id][$task]=$sortitems;
								} else {
									if($itemarr[$test_id][$task]!=$sortitems) echo "\n\n!!!!!!!!!!!!!!!!!!!!!!!!! ALERT !!!!!!!!!!!!!!!!!!!!!!\n!=".implode(", ",$itemarr[$test_id][$task])."\n";
								}
								array_walk($items,function(&$v){$v=preg_replace(array("/æ/","/ø/","/å/"),array("ae","oe","aa"),$v);});
								$itemcols=array_flip($items);
								array_walk($itemcols,function(&$v) use($taskpos) {$v=$v+$taskpos+1;});
								
								$items1=array_fill_keys($items,1);
								$q='insert into tasks (task_name,tasktype_id,items,tasktype_variables,task_data,test_id) VALUES ("'.$task.'",0,\'"items":{'.json_encode($items1).'}\',"{}","{}",'.$test_id.') on duplicate key UPDATE items=\'"items":{'.json_encode($items1).'}\', task_id=LAST_INSERT_ID(task_id)';
									// If the task_name exists in this test, these responses are added to that instead of created as new task.
// 								echo "\n".$q;
	 							$mysqli->query($q);
	 							$task_id=$mysqli->insert_id;
								

							} else {
								if(substr($data[0],0,1)=="g") {
									$q='select response_id from responses where testtaker="'.$data[0].'" and task_id='.$task_id;
// 									echo "\n".$q;
									$result=$mysqli->query($q);
									if($result->num_rows==0) echo "\n******** ALERT!!! *******\n".$data[0]." missing\n";
									else {
										$response_id=$result->fetch_assoc()["response_id"];
										$codes=[];
										$flagged=false;
										foreach($itemcols as $item=>$col) {
											$codes[]=array("item_name"=>$item,"code"=>$data[$col]);
											if($data[$col]==9) $flagged=true;
										}
										$q='insert into coded (response_id,coder_id,codes) value ('.$response_id.','.$coder_id.',CAST(\''.json_encode($codes).'\' as JSON)) on duplicate key update codes=VALUES(codes)';
										$mysqli->query($q);
//  										echo "\n".$q;
										if($flagged) {
											$q='insert into flagged (response_id,coder_id,flagstatus) value ('.$response_id.','.$coder_id.',"flagged") on duplicate key update response_id=response_id';
											$mysqli->query($q);
											echo "\n".$q;
										
										}
									}
								}
							}
						}
						fclose($handlecsv);
					}
				}
				closedir($handle2);
			}
        }
    }
    closedir($handle);
}

$q='update coded set isdoublecode=1 where concat(response_id,"_",coder_id) IN (select dblsc from (SELECT concat(response_id ,"_", if(rand()>0.5,max(coder_id),min(coder_id))) as dblsc FROM `coded` WHERE isdoublecode!=1 GROUP BY response_id HAVING COUNT(1) > 1) as dbls)'; 
for($i=0;$i<3;$i++) {
	$mysqli->query($q);
}
