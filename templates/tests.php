<?php

checkperm("projectadmin");
include_once($shareddir."database.php");

$q="select * from tests where project_id=".$_SESSION["project_id"];
$result=$mysqli->query($q);
	?>
<div class="row"><div class="col"><button class="btn btn-secondary " data-target=".collapse" data-toggle="collapse"><?= _("Collapse all"); ?></button></div><div class="col"><button class="btn btn-primary float-right" id="newtest"><?= _("New test"); ?></button></div></div>
<div class="" id="accordion">
<?php 
if($result->num_rows>0) {
	while($r=$result->fetch_assoc()) {
		?>
	<div class="card">
		<div class="card-header" >
		<h2 class="mb-0 float-left test_name" data-test_id="<?= $r["test_id"];?>" data-test_name="<?= htmlentities($r["test_name"]);?>">
			<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#test<?= $r["test_id"];?>" aria-expanded="true" aria-controls="collapseOne">
			<?= $r["test_name"];?>
			</button>
		</h2>
		<span class="deletetest text-danger float-right ml-2" title="<?= _("Delete test");?>"><i class="fa fa-trash-alt"></i></span>
		<span class="edittest float-right ml-2"><i class="fas fa-edit"></i></span>
		<span class="uploadresponses float-right" title="<?= _("Upload responses");?>"><i class="fas fa-file-upload"></i></i></span>
		</div>

		<div id="test<?= $r["test_id"];?>" data-test_id="<?= $r["test_id"];?>" class="collapse <?= ((!$_POST["openTest"] or $_POST["openTest"]==$r["test_id"])?"show":"");?> testdiv" aria-labelledby="test_name<?= $r["test_id"];?>" data-parent="#accordion">
			<div class="card-body">
				<table class="table sticky-column">
					<thead class="sticky-top">
						<tr class="table-light">
							<th scope="col"><?= _("Task name");?></th>
							<th scope="col"><?= _("Task description");?></th>
							<th scope="col"><?= _("Task image");?></th>
							<th scope="col"><?= _("Task type");?></th>
							<th scope="col"><?= _("Task variables");?></th>
							<th scope="col"><?= _("Item-prefix");?></th>
							<th scope="col"><?= _("Items");?></th>
							<th scope="col"><?= _("Coding rubrics");?></th>
							<th scope="col"><?= _("Count");?></th>
							<th scope="col"><?= _("Action");?></th>
						</tr>
					</thead>
					<tbody id="tasklist<?= $r["test_id"];?>">
				<?php
					$q="select t.*,tc.task_name as clone_task_name,tct.test_name as clone_test_name,count(r.response_id) as rcount,tasktype_name,i.variables from tasks t left join tasks tc on t.clone_task_id=tc.task_id left join tests tct on tc.test_id=tct.test_id left join tasktypes i on i.tasktype_id=t.tasktype_id left join responses r on r.task_id=t.task_id where t.test_id=".$r["test_id"]." GROUP BY 1 order by t.`group_id`, t.item_prefix, t.task_name";
// 					echo $q;
		////////////////
		// 		Due to this bug: https://bugs.mysql.com/bug.php?id=103225
		//
		// 			sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
		// 			sort_buffer_size = 512000000
		// 			sudo service mysql restart
		// 			
					
					$result1=$mysqli->query($q);
					while($r1=$result1->fetch_assoc()) {
					?>
						<tr data-task_id="<?= $r1["task_id"];?>" class="group_target" <?= ($r1["group_id"]>0?'data-group_id="'.$r1["group_id"].'"':'');?>>
						<th scope="row"><?= $r1["task_name"];?></th>
						<?php if($r1["clone_task_id"]>0) { ?>
							<td colspan="7" class="text-center bg-light"><span class="font-italic"><?= sprintf(_("Cloning <b>%s</b>"),$r1["clone_test_name"].": ".$r1["clone_task_name"]);?></span><span class="resetclone float-right" title="<?= _("Stop cloning");?>"><i class="fas fa-unlink"></i></span></td>
						
						<?php } else { ?>
						<td class="htmleditable" ><div class="htmleditablediv" id="description_<?= $r1["task_id"];?>"  data-edittype="task_description" ><?= $r1["task_description"];?></div></td>
						<td class="uploadedimg" data-toggle="modal" data-target="#uploadedimg" ><?= ($r1["task_image"]?'<img src="'.$r1["task_image"].'">':'');?></td>
						<td class="selectable" data-edittype="tasktype_id" data-tasktype_id="<?= $r1["tasktype_id"];?>"><?= $r1["tasktype_name"];?></td>
						<td class="variables">
						<?php 
						if($r1["variables"]) {
							$tasktype_variables=json_decode($r1["tasktype_variables"],true);
							$variables=json_decode($r1["variables"]);
							include($backenddir."gettasktypevariables.php");
						}
						?>
						</td>
						<td class="editable" data-edittype="item_prefix" contenteditable><?= $r1["item_prefix"];?></td>
						<td><!--<div><span class="itemsort" title="<?= _("Sort");?>"><i class="fas fa-random"></i></span></div>-->
						<div class="itemsdiv">
							<?php
							$itemobj=json_decode($r1["items"],true); 
							$items=$itemobj["items"];
							$itemorder=$itemobj["order"]?$itemobj["order"]:array();
							$extra=array_diff(array_keys($items),$itemorder);
							$itemorder=array_merge(array_intersect($itemorder,array_keys($items)),$extra); // Removing items in order, but not in items. Adding items in items, but not in order...
							echo implode("\n",
								array_map(
									function($k) use($items) {
										return '<div class="d-flex"><span class="sorthandle mx-1"><i class="fa-solid fa-sort"></i></span><span class="editable first name '.($items[$k]?'':'font-weight-bold').'" data-oldvalue="'.$k.'" data-edittype="items"  data-edittype2="name" contenteditable>'.$k.'</span>'.($items[$k]?($items[$k]=="string"?': string':': 0-<span class="editable" data-edittype="item"  data-edittype2="value" contenteditable>'.$items[$k].'</span>'):'').'<span class="deleteitem ml-auto"><i class="fa fa-trash-alt"></i></span></div>';
									},
									$itemorder
								)
							);
							?>
							<div>
								<span class="additem ml-2"><i class="fas fa-plus"></i></span>
								<span class="additemstring ml-2"><i class="fas fa-comment-dots"></i></span>
								<span class="additemcategory ml-2"><i class="fas fa-folder-plus"></i></span>
							</div>
						</div>
						</td>
						<td class="htmleditable"><div  class="htmleditablediv" id="rubrics_<?= $r1["task_id"];?>" data-edittype="coding_rubrics"><?= $r1["coding_rubrics"];?></div></td>
						<?php } // End not clone
						?>
						<td><?= $r1["rcount"];?></td>
						<td>
							<span class="deletetask text-warning float-right ml-2" title="<?= _("Delete task");?>"><i class="fa fa-trash-alt"></i></span>
							<span class="movetask text-primary float-right ml-2" title="<?= _("Move task");?>"><i class="fas fa-random"></i></span>
							<span class="clonetask text-primary float-right ml-2" title="<?= _("Clone task settings");?>"><i class="fas fa-link"></i></span>
						</td>
						</tr>
					
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php

	}
}
?>

<div class="modal" tabindex="-1" id="uploadedimg" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?= _("Upload image");?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<div class="showImg" id="modalimg"></div>
					<input type="FILE" class="custom-file-input picture" id="playerpicture" accept="image/jpeg, image/png" />
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _("Close");?></button>
				<button type="button" class="btn btn-primary" id="saveimg" data-task_id=""><?= _("Save");?></button>
			</div>
		</div>
	</div>
</div>
  <div class="d-none">
	<select class="custom-select" id="tasktypes">
		<option></option>
	<?php
		$q="select * from tasktypes where 1";
			$result1=$mysqli->query($q);
			while($r1=$result1->fetch_assoc()) {
			?>
			<option value="<?= $r1["tasktype_id"]; ?>"><?= $r1["tasktype_name"]; ?></option>
			<?php
			}
			?>
	</select>
	<select class="custom-select" id="tests">
		<option><?= _("Move to");?></option>
	<?php
		$q="select * from tests where project_id=".$_SESSION["project_id"];
			$result1=$mysqli->query($q);
			while($r1=$result1->fetch_assoc()) {
			?>
			<option value="<?= $r1["test_id"]; ?>"><?= $r1["test_name"]; ?></option>
			<?php
			}
			?>
	</select>
	<select class="custom-select" id="tasks">
		<option><?= _("Clone from");?></option>
	<?php
		$q="select * from tasks t left join tests ts on t.test_id=ts.test_id where project_id=".$_SESSION["project_id"];
			$result1=$mysqli->query($q);
			while($r1=$result1->fetch_assoc()) {
			?>
			<option value="<?= $r1["task_id"]; ?>"><?= $r1["test_name"].": ".$r1["task_name"]; ?></option>
			<?php
			}
			?>
	</select>
  </div>
