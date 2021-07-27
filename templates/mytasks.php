<?php
session_start();
include_once($shareddir."database.php");
checkperm();

$q="select doublecodingpct from projects where project_id=".$_SESSION["project_id"];
$result=$mysqli->query($q);
$doublecodingpct=$result->fetch_assoc()["doublecodingpct"];
$q="select ta.task_id,count(*) as numresponses,sum(isdoublecode) as numdoublecoded,sum(if(c.response_id IS NOT NULL and c.isdoublecode=0,1,0)) as numcoded from responses r left join coded c on r.response_id=c.response_id left join tasks ta on ta.task_id=r.task_id left join tests t on t.test_id=ta.test_id where t.project_id=".$_SESSION["project_id"]." group by ta.task_id";
$result=$mysqli->query($q);
while($r=$result->fetch_assoc()) {
//reviseddoublecodedpct=number of responses to doublecoding 
	$numbertodoublecode=$r["numresponses"]*$doublecodingpct/100;
	$remainingtodoublecode=$numbertodoublecode-$r["numdoublecoded"];
	$remainingresponses=$r["numresponses"]-$r["numcoded"];
	$reviseddoublecodedpct=($remainingresponses>0?$remainingtodoublecode/$remainingresponses*100:0);
	$_SESSION["doublecodingpct"][$r["task_id"]]=$reviseddoublecodedpct;
}

$q='(select test_name as name, a.test_id as unit_id, t.test_id, 0 as coded, 0 as flagged,0 as maxcodedresponse_id,0 as manualauto,1 as coltype  from assign_test a left join tests t on t.test_id=a.test_id where coder_id='.$_SESSION["user_id"].')
UNION 
(select task_name,tt.task_id,tt.test_id,sum(if(c.coder_id='.$_SESSION["user_id"].',1,0)),sum(if(f.coder_id='.$_SESSION["user_id"].',1,0)),max(c.response_id),manualauto,2 as coltype from tasks tt left join tasktypes ttt on tt.tasktype_id=ttt.tasktype_id left JOIN responses r on r.task_id=tt.task_id left join coded c on c.response_id=r.response_id left join flags f on f.response_id=c.response_id and f.coder_id=c.coder_id where tt.group_id=0 and tt.task_id in (select task_id from assign_task where coder_id='.$_SESSION["user_id"].' UNION select task_id from assign_test a1 left join tasks t1 on a1.test_id=t1.test_id where coder_id='.$_SESSION["user_id"].') group by 1 order by task_name)
order by test_id,coltype';
// echo $q;
$result=$mysqli->query($q);
$all=$result->fetch_all(MYSQLI_ASSOC);

?>
    <div class="container">
		<div class="row">
			<table class="table">
				<thead>
					<tr>
					<th scope="col"><?= _("Test/task name");?></th>
					<th scope="col" class="text-right"><?= _("Coded");?></th>
					<th scope="col" class="text-right"><?= _("Flagged");?></th>
					</tr>
				</thead>
				<tbody id="tasklist">
			<?php
				
				
				foreach($all as $r) {
				?>
					<tr data-unit_id="<?= $r["unit_id"];?>" class="<?= ($r["coltype"]==1?"table-warning":"");?>">
						<?= ($r["coltype"]==1?'<th scope="row">':'<td><a href="#" class="'.($r["manualauto"]=="auto"?'autocodeTask':'codeTask').'" data-task_id="'.$r["unit_id"].'" data-maxcodedresponse_id="'.$r["maxcodedresponse_id"].'">');?><?= $r["name"];?><?= ($r["coltype"]==1?'</th>':"</a></td>");?>
						<td class="text-right"><?= ($r["coltype"]==2?$r["coded"]:"");?></td>
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
<div class="d-none">
#If user has permissions for more than one test, let him chose (if not already done)

#List of available tasks, number of responses in each, click->go to coding of task

#Training: List of available tasks, number of responses in each, click->go to training of task

</div>
