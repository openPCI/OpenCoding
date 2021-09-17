<?php
	$relative="../";
	include_once($functionsdir."database.php");
	checkperm("opencodingadmin");

	$q='select p.*, group_concat(u.username separator ", ") as permissions from projects p left join user_permissions up on up.unit_id=project_id and unittype="projectadmin" left join users u on u.user_id=up.user_id where 1 group by project_id order by project_name ';
	$result=$mysqli->query($q);
	
	
?>
<div class="container-fluid">

	<div class="row">
		<div class="col">
			<button class="btn btn-primary float-right" id="newproject"><?= _("New project"); ?></button>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<h3><?= _("OpenCoding Projects"); ?></h3>
			<table class="table table-sm table-hover mt-2">
				<thead>
					<tr>
					<th scope="col"></i><?= _('Project');?></th>
					<th scope="col"></i><?= _('Double-coding percent');?></th>
					<th scope="col"></i><?= _('Max responses per task per coder (%)');?></th>
					<th scope="col"></i><?= _('Permissions');?></th>
					<th scope="col"></i><?= _('Actions');?></th>
					</tr>
				</thead>
				<tbody class="table-striped " id="projectlist">
				<?php
					while($r=$result->fetch_assoc()) { ?>
						<tr data-project_id=<?= $r["project_id"];?>>
							<td class="editable" data-edittype="project_name" contenteditable><?= $r["project_name"];?></td>
							<td class="editable acceptnumber" data-edittype="doublecodingpct" contenteditable><?= $r["doublecodingpct"];?></td>
							<td class="editable acceptnumber" data-edittype="maxresponsespct" contenteditable><?= $r["maxresponsespct"];?></td>
							<td><?= $r["permissions"];?></td>
							<td>
  								<button type="button" class="btn btn-success changeproject"><?= _('Change to this project');?></button>
								<button type="button" class="btn btn-danger deleteproject"><?= _('Delete project');?></button>
							</td>
						<tr>
				<?php	}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
