<?php
session_start();
include_once($shareddir."database.php");
checkperm();
$_SESSION["stoprevision"]=$_SESSION["code_id"]=0;
$codingadmin=$_SESSION["perms"]["codingadmin"][$_SESSION["project_id"]];

$q="SELECT doublecodingpct,maxresponsespct from projects where project_id=".$_SESSION["project_id"];

// echo $q;
if(!$result=$mysqli->query($q)) echo $mysqli->error;
else {
	$r=$result->fetch_assoc();
	$doublecodingfactor=1+$r["doublecodingpct"]/100;
	$maxresponsespct=$r["maxresponsespct"]/100;
}

// print_r($remainingresponses);
$q='(select "" as item_prefix, test_name as name, a.test_id as unit_id, t.test_id, 0 as remaining, 0 as numresponses, 0 as codedresponses, 0 as codedbycoder, 0 as flagged,0 as manualauto,1 as coltype  from assign_test a left join tests t on t.test_id=a.test_id where t.project_id='.$_SESSION["project_id"].' and coder_id='.$_SESSION["user_id"].')
UNION 
(select item_prefix, CONCAT(task_name,if(item_prefix!="",CONCAT(" (",item_prefix,")"),"")),tt.task_id,tt.test_id,(SELECT count(*) from responses rr left join coded cr on cr.response_id=rr.response_id where rr.task_id=tt.task_id and coder_id IS NULL) as remaining,(SELECT count(*) from responses r2 where r2.task_id=tt.task_id) as numresponses, (select count(*) from responses r3 left join coded cr on cr.response_id=r3.response_id where r3.task_id=tt.task_id and cr.coder_id IS NOT NULL) as codedresponses,sum(if(c.coder_id='.$_SESSION["user_id"].' OR manualauto="auto",1,0)),sum(if(f.coder_id='.$_SESSION["user_id"].',1,0)),manualauto,2 as coltype from tasks tt left join tasktypes ttt on tt.tasktype_id=ttt.tasktype_id left JOIN responses r on r.task_id=tt.task_id left join coded c on c.response_id=r.response_id left join flags f on f.response_id=c.response_id and f.coder_id=c.coder_id where tt.group_id=0 and tt.task_id in (select at.task_id from assign_task at left join tasks ta on at.task_id=ta.task_id left join tests te on ta.test_id=te.test_id where te.project_id='.$_SESSION["project_id"].' and coder_id='.$_SESSION["user_id"].' UNION select task_id from assign_test a1 left join tasks t1 on a1.test_id=t1.test_id left join tests te1 on a1.test_id=te1.test_id where te1.project_id='.$_SESSION["project_id"].' and coder_id='.$_SESSION["user_id"].') group by task_id order by task_name)
order by test_id, coltype, item_prefix, name';
// echo $q;
if(!$result=$mysqli->query($q)) {echo $q."<br>".$mysqli->error; $all=array();}
else $all=$result->fetch_all(MYSQLI_ASSOC);

?>
    <div class="container">
		<div class="row">
			<h3><?= _("My tasks");?></h3>
			<table class="table">
				<thead class="sticky-top">
					<tr class="table-light">
						<th scope="col"><?= _("Test/task name");?></th>
						<th scope="col" class="text-right"><?= _("Actions");?></th>
						<th scope="col" class="text-right"><?= _("Coded by you");?></th>
						<th scope="col" class="text-right"><?= _("Remaining");?></th>
						<th scope="col" class="text-right"><?= _("Flagged");?></th>
					</tr>
				</thead>
				<tbody id="tasklist">
			<?php
				
				
				foreach($all as $r) {
				$maxresponses=ceil($r["numresponses"]*$maxresponsespct);
				$manualremaining=max($r["remaining"],ceil($r["numresponses"]*$doublecodingfactor)-$r["codedresponses"]);
				$stillcodingtodo=($r["manualauto"]=="auto" or ($manualremaining and $r["codedbycoder"]<$maxresponses));//
				
				?>
					<tr data-unit_id="<?= $r["unit_id"];?>" data-remainingresponses="<?= ($maxresponses-$r["codedbycoder"]);?>" <?= ($r["coltype"]==1?'class="table-warning"':'data-task_id="'.$r["unit_id"].'"');?>">
						<?= ($r["coltype"]==1?'<th scope="row">':'<td>'
							.($stillcodingtodo?
								'<a href="#" class="docode" data-codetype="'.($r["manualauto"]=="auto"?'autocode':'code').'">'
								:
								''));?>
								<?= $r["name"];?>
							<?= ($r["coltype"]==1?'</th>':($stillcodingtodo?'</a>':'').'</td>');?>
						<td class="text-right"><?php if($r["coltype"]==2) { if($stillcodingtodo) { ?><button class="btn btn-sm btn-primary docode" data-codetype="<?= ($r["manualauto"]=="auto"?'autocode':'code');?>"><?= _("Code");?></button><?php } if($r["manualauto"]=="manual" and $r["codedbycoder"]) {?><button class="btn btn-sm btn-secondary docode ml-1" data-codetype="revise"><?= _("Revise");?></button><?php } 
							if($r["manualauto"]=="manual" and $codingadmin) {?><button class="btn btn-sm btn-secondary docode ml-1" data-codetype="reviseall"><?= _("Revise all coders' coding");?></button><?php }
						} ?></td>
						<td class="text-right"><?= ($r["coltype"]==2?$r["codedbycoder"]:"");?></td>
						<td class="text-right"><?= ($r["coltype"]==2?($r["manualauto"]=="manual"?$manualremaining:$r["remaining"]):"");?></td>
						<td class="text-right"><?= ($r["coltype"]==2?$r["flagged"]:"");?></td>
					</tr>
				
				<?php
				}
				?>


				</tbody>
			</table>
		</div>
  </div>

<?php
?>
