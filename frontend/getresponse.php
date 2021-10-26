<?php
session_start();
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
// $log.=print_r($_POST,true);
// $log.=print_r($_SESSION,true);
$res=array();
$codingadmin=$_SESSION["perms"]["codingadmin"][$_SESSION["project_id"]];
$task_id=$_POST["task_id"];
$finish=($_POST["next"]=="finish");
if($task_id) {
	$flaghandling=($_POST["flaghandling"]=="true");
	$training=($_POST["training"]=="true");
	if($_POST["codes"]) {
// 		if($_SESSION["response_id"]!=$_POST["response_id"]) $warning=_("Response-id is not correct: ".$_SESSION["response_id"]." and ".$_POST["response_id"]);
// 		else
		if($training) {
			$q="select codes from coded c left join trainingresponses tr on tr.response_id=c.response_id and c.coder_id=tr.manager_id where tr.response_id=".$_POST["response_id"];
			$result=$mysqli->query($q);
			if($result->num_rows) {
				$correctcodes=json_decode($result->fetch_assoc()["codes"],true);
				$codes=$_POST["codes"];
				function to_assoc($a) { foreach($a as $o) { $newa[$o["item_name"]]=$o["code"];} return $newa;};
				$_SESSION["training"][$_POST["response_id"]]["codes"]=$codes;#;
				$_SESSION["training"][$_POST["response_id"]]["correctcodes"]=$correctcodes;#to_assoc($correctcodes);
				$_SESSION["training"][$_POST["response_id"]]["correct"]=array_intersect_assoc(to_assoc($correctcodes),to_assoc($codes));#array_map(function($key) use ($codes,$correctcodes)  { return $codes[$key]==$correctcodes[$key]; },array_keys($correctcodes));
			} else $warning=sprintf(_("No correct codes for this response. Please contact the scoring manager. Response id: %d"),$_POST["response_id"]);
		}
		else {
			if($_POST["flagged"]!="true") {
				$hasminus=array_reduce($_POST["codes"],function($c,$i) {return ($c or $i["code"]<0);},false);
			}
			if($hasminus) $warning=_("You need to set the flag to send negative values.");
			else {
				$codes=json_encode($_POST["codes"]);
				if($flaghandling) $q="update coded set codes=CAST('".$codes."' as JSON) where response_id=".$_POST["response_id"]." and coder_id=".$_SESSION["coder_id"];
				else $q="insert into coded (response_id,coder_id,codes,isdoublecode) value (".$_POST["response_id"].",".$_SESSION["user_id"].",CAST('".$codes."' as JSON),".($_SESSION["isdoublecode"]?1:0).") on duplicate key update codes=values(codes), code_id=LAST_INSERT_ID(code_id)";
				$mysqli->query($q);
				if(!$flaghandling) $_SESSION["code_id"]=$mysqli->insert_id;
				if(!$flaghandling and !$_SESSION["isdoublecode"]) {
					$_SESSION["remainingresponses"]--;
					if(!$_SESSION["remainingresponses"]) $finish=true;
				}
				$log.=$q;
			}
		}
	}
	$revise=($_POST["revise"]=="true" or $_POST["next"]=="<" or ($_SESSION["stoprevision"] and $_SESSION["stoprevision"]>$_SESSION["code_id"]));
	if(!$revise and $_SESSION["stoprevision"]>0 and $_POST["next"]!="<" and $_SESSION["response_id"][$task_id]!=$_POST["response_id"]) { 
			$_POST["next"]=$_SESSION["response_id"][$task_id];
			$_SESSION["response_id"][$task_id]=0;
			$directtonew=true;
	}
	$_SESSION["stoprevision"]=(($revise and $_POST["revise"]!="true")?max($_SESSION["stoprevision"],$_SESSION["code_id"]):0);

// 	if(!$training and $_POST["next"]!="<" and $_SESSION["stoprevision"]>0 and $_SESSION["stoprevision"]==$_SESSION["code_id"]) {
// 		$revise=$_SESSION["stoprevision"]=0;
// 	}
	if(!$warning and !$finish) {
		$success=false;
		$tries=0;
		while(!$success and $tries<2) {
			$dodouble=(!$avoiddoublecoding and !$revise and $_SESSION["doublecodingpct"]>0 and !$training and !$flaghandling and (rand(1,round(100/$_SESSION["doublecodingpct"]))==1 or $forcedoublecoding));
			$log.="tries: ".$tries.", dodouble ".($dodouble?"true":"false").", forcedoublecoding ".($forcedoublecoding?"true":"false").", avoiddoublecoding ".($avoiddoublecoding?"true":"false").", doublecodingpct ".($_SESSION["doublecodingpct"]);
			$tries++;
			if($dodouble){
				$res["dodouble"]=true;
			}
			$justcoding=(!$training and !$flaghandling and !$revise);
			$direct=($_POST["next"]>0);
			$_SESSION["isdoublecode"]=($dodouble?1:0);
			$q='select task_name,r.task_id,response,testtaker,response_time,r.response_id'.
			($training?'':($dodouble?'':',codes')).
			($flaghandling?',f.coder_id,flag_id,flagstatus':'').
			(($codingadmin or $training)?',difficulty':'').' 
				from responses r 
				'.(($codingadmin or $training or $dodouble)?'left join trainingresponses tr on tr.response_id=r.response_id':'').
				($training?'
					left join tasks t on r.task_id=t.task_id 
					where '.(!$direct?' difficulty':' 1')
				:'
					left join tasks t on r.task_id=t.task_id 
					'.(($flagstatus or $codingadmin)?'left join flags f on f.response_id=r.response_id ':'')
					.(($codingadmin and $flagstatus)?'left join coded c on f.response_id=c.response_id ':'left join coded c on r.response_id=c.response_id ').
					($flaghandling?'
						where flagstatus="'.$_POST["flagstatus"].'"'.(!$direct?' and f.flag_id':'')
					:'
						where '.
						($dodouble?
							'(c.coder_id!='.$_SESSION["user_id"].' and c.coder_id IS NOT NULL and tr.response_id is NULL) '
						:
							((!$directtonew and ($revise or $direct))?
								'c.coder_id='.$_SESSION["user_id"].(!$direct?' and code_id ':'')
							:
								'c.coder_id IS NULL ')
						)
					)
				)
				.(($_POST["next"] and (!$justcoding or $_POST["next"]!=">"))?
					(is_numeric($_POST["next"])?
						" and r.response_id=".$_POST["next"]
					:
						$_POST["next"].(($training or ($revise and !$_POST["codes"]))?"=":"").$_SESSION[$flaghandling?"flag_id":($training?"difficulty":"code_id")]
					)
				:
					($flaghandling?'>0':'')
// 					($_POST["response_id"]?
// 						"=".$_POST["response_id"]
// 					:
// 						(($_SESSION["response_id"] and $_SESSION["activetask"]==$task_id)?">".$_SESSION["response_id"]:">0")
// 					)
				).
				(($training and !$direct)?
					' and r.response_id!='.$_POST["response_id"].(!$revise?' and (difficulty>'.$_SESSION["difficulty"].' or r.response_id>'.$_POST["response_id"].')':'')
				:
					''
				).'
				and 
				r.task_id='.$task_id.' 
				order by '.($training?'difficulty':($flaghandling?'flag_id':($revise?'code_id':'RAND()'))).($_POST["next"]=="<"?' DESC':' ASC').'
				limit 1';
		$log.=$q;
			$result=$mysqli->query($q);
			$success=($result->num_rows>0);
			if(!$success) {
				if ($dodouble) $avoiddoublecoding=true;
				else $forcedoublecoding=true;
			}
		}
		if($success) {
			$r=$result->fetch_assoc();
			if(!$revise) $_SESSION["response_id"][$task_id]=$r["response_id"];
			if($flaghandling) {
				$_SESSION["coder_id"]=$r["coder_id"];
				$_SESSION["flag_id"]=$r["flag_id"];
			}
			if($training) {
				$_SESSION["difficulty"]=$r["difficulty"];
			}
			$codes=($r["codes"]?json_decode($r["codes"]):(($training and $_SESSION["training"][$r["response_id"]]["codes"])?$_SESSION["training"][$r["response_id"]]["codes"]:array()));
			
			if($_POST["subtask_ids"]) {
				$q='select task_name,response from responses r left join tasks t on r.task_id=t.task_id where testtaker LIKE "'.$r["testtaker"].'" and response_time="'.$r["response_time"].'" and t.task_id in ('.$_POST["subtask_ids"].')';
// 				echo $q;
				$result=$mysqli->query($q);
				$responses=$result->fetch_all(MYSQLI_ASSOC);
			} else $responses=array();
			$responses[]=array_intersect_key($r,array_flip(array("task_name","response")));
			$res=array_merge($res,array("responses"=>$responses,"codes"=>$codes,"response_id"=>$r["response_id"],"flagstatus"=>$r["flagstatus"],"trainingresponse"=>($r["difficulty"]?$r["difficulty"]:0)));
			if($training and $_SESSION["training"][$r["response_id"]]["codes"] and $_SESSION["training"][$r["response_id"]]["correctcodes"]) $res["correctcodes"]=$_SESSION["training"][$r["response_id"]]["correctcodes"];
			
		} else {
			$warning=_("No more responses");
			$finish=true;
			unset($_SESSION["response_id"]);
		}
	} 
	if($finish) $res["returnto"]=($training?"training":($flaghandling?"codingmanagement":"mytasks"));

}
$res["log"]=$log;
$res["warning"]=$warning;
echo json_encode($res);
