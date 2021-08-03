<?php

checkperm("projectadmin");
include_once($shareddir."database.php");

$q="select * from tests where project_id=".$_SESSION["project_id"];
$result=$mysqli->query($q);
	?>
<div class="accordion" id="accordion">
<?php 
if($result->num_rows==0) echo _("You don't have any tests in this project. Upload data to create tests.");
else {
	while($r=$result->fetch_assoc()) {
		?>
	<div class="card">
		<div class="card-header" id="headingOne">
		<h2 class="mb-0 float-left">
			<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#test<?= $r["test_id"];?>" aria-expanded="true" aria-controls="collapseOne">
			<?= $r["test_name"];?>
			</button>
		</h2>
		<span class="edittest float-right"><i class="fas fa-edit"></i></span>
		</div>

		<div id="test<?= $r["test_id"];?>" class="collapse" aria-labelledby="test_name<?= $r["test_id"];?>" data-parent="#accordion">
			<div class="card-body">
				<table class="table">
					<thead>
						<tr>
						<th scope="col"><?= _("Task name");?></th>
						<th scope="col"><?= _("Task description");?></th>
						<th scope="col"><?= _("Task image");?></th>
						<th scope="col"><?= _("Task type");?></th>
						<th scope="col"><?= _("Task variables");?></th>
						<th scope="col"><?= _("Items");?></th>
						<th scope="col"><?= _("Coding rubrics");?></th>
						<th scope="col"><?= _("Count");?></th>
						</tr>
					</thead>
					<tbody id="tasklist">
				<?php
					$q="select t.*,count(r.response_id) as rcount,tasktype_name,i.variables from tasks t left join tasktypes i on i.tasktype_id=t.tasktype_id left join responses r on r.task_id=t.task_id where test_id=".$r["test_id"]." GROUP BY 1 order by `group_id`";
					
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
						<td><?php
						$items=json_decode($r1["items"],true); 
						echo implode("\n",
							array_map(
								function($v,$k) {
									return '<div><span class="editable first" data-oldvalue="'.$k.'" data-edittype="items"  data-edittype2="name" contenteditable>'.$k.'</span>: 0-<span class="editable" data-edittype="item"  data-edittype2="value" contenteditable>'.$v.'</span><span class="deleteitem float-right"><i class="fa fa-trash"></i></span></div>';
								},
								$items,
								array_keys($items)
							)
						);
						?><div class="additem"><i class="fas fa-plus"></i></div></td>
						<td class="htmleditable"><div  class="htmleditablediv" id="rubrics_<?= $r1["task_id"];?>" data-edittype="coding_rubrics"><?= $r1["coding_rubrics"];?></div></td>
						<td><?= $r1["rcount"];?></td>
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
  </div>
