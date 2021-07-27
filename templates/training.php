<?php
session_start();
include_once($shareddir."database.php");
checkperm("projectadmin");

$q="SELECT distinct ta.task_id,task_name,test_name,ta.test_id,count(tr.response_id) as trainingresponseno,min(tr.difficulty) as first_id, group_concat(tr.response_id separator ',') as response_ids FROM `trainingresponses` tr left join responses r on tr.response_id=r.response_id LEFT join tasks ta on ta.task_id=r.task_id left join tests t on t.test_id=ta.test_id WHERE project_id=".$_SESSION["project_id"]." group by ta.task_id order by test_name, task_name";
$result=$mysqli->query($q);
$all=$result->fetch_all(MYSQLI_ASSOC);
// print_r($_SESSION["training"]);
?>
    <div class="container">
		<div class="row">
			<table class="table">
				<thead>
					<tr>
					<th scope="col"><?= _("Test/task name");?></th>
					<th scope="col" class="text-right"><?= _("# training responses");?></th>
					<th scope="col" class="text-right"><?= _("# completed");?></th>
					<th scope="col" class="text-right"><?= _("Correct (items)");?></th>
					</tr>
				</thead>
				<tbody id="tasklist">
			<?php
				
				
				foreach($all as $r) {
					if($r["test_id"]!=$oldtest_id) {
				?>
					<tr class="table-warning"><td colspan="4"><?=$r["test_name"];?></td></tr>
						
						<?php } ?>
					<tr data-task_id="<?= $r["task_id"];?>" >
						<td><a href="#" class="codeTask" data-first_id="<?= $r["first_id"];?>" data-task_id="<?= $r["task_id"];?>"><?= $r["task_name"];?></a></td>
						<td class="text-right"><?= $r["trainingresponseno"];?></td>
						<td class="text-right"><?= array_reduce(explode(",",$r["response_ids"]),function($c,$x) { return $c+($_SESSION["training"][$x]?1:0);},0);?></td>
						<td class="text-right"><?= array_reduce(explode(",",$r["response_ids"]),function($c,$x) { return $c+($_SESSION["training"][$x]["correct"]?count($_SESSION["training"][$x]["correct"]):0);},0)." "._("of")." ".
						array_reduce(explode(",",$r["response_ids"]),function($c,$x) { return $c+($_SESSION["training"][$x]?count($_SESSION["training"][$x]["codes"]):0);},0); ?></td>
					</tr>
				
				<?php
				$oldtest_id=$r["test_id"];
				}
				?>


				</tbody>
			</table>
		</div>
  </div>

<?php
?>
