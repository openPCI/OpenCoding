<?php

checkperm("opencodingadmin");
include_once($shareddir."database.php");

$q="select * from tasktypes where 1";
				
$result=$mysqli->query($q);
?>
<form id="importtasktypesform">
	<label class="btn btn-secondary float-right ml-1" id="importtasktypes">
		<?=_("Import task types");?><input type="file" name="importtasktypes" hidden>
	</label>
</form>
<button class="btn btn-primary float-right" id="newtasktype"><?= _("New task type");?></button>
<h1><?= _("Task types");?></h1>
<table class="table sticky-column" id="edittable" data-edittable="tasktypes">
	<thead class="sticky-top">
		<tr class="table-light" data-tasktype_id="0">
		<td><span class="exporttasktype" title="<?= _("Export all tasktypes");?>"><i class="fas fa-upload"></i></span></th>
		<th scope="col"><?= _("Name");?></th>
		<th scope="col"><?= _("Manual/auto coding");?></th>
		<th scope="col"><?= _("Description");?></th>
		<th scope="col"><?= _("Instructions");?></th>
		<th scope="col"><?= _("Playarea template");?></th>
		<th scope="col"><?= _("Responsearea template");?></th>
		<th scope="col"><?= _("Codearea template");?></th>
		<th scope="col"><?= _("Script");?></th>
		<th scope="col"><?= _("Styles");?></th>
		<th scope="col"><?= _("Variables");?></th>
		</tr>
	</thead>
	<tbody id="tasktypelist">
<?php
	while($r=$result->fetch_assoc()) {
	?>
		<tr class="table-light" data-tasktype_id="<?= $r["tasktype_id"];?>" >
			<td ><span class="exporttasktype" title="<?= _("Export this tasktype");?>"><i class="fas fa-upload"></i></span></td>
			<th scope="row" class="editable tasktype_name" data-edittype="tasktype_name" contenteditable><?= $r["tasktype_name"];?></th>
			<td class="manualautotoggle" data-manualauto="<?= $r["manualauto"];?>"><?= _($r["manualauto"]);?></td>
			<td class="editable" data-edittype="tasktype_description" contenteditable><?= $r["tasktype_description"];?></td>
			<td class="htmleditable " ><div class="htmleditablediv" id="instructions_<?= $r["tasktype_id"];?>" data-edittype="tasktype_instructions"><?= $r["tasktype_instructions"];?></div></td>
			<td class="scripting <?= ($r["playareatemplate"]!=""?"text-info":"text-muted");?>" data-edittype="playareatemplate" data-toggle="modal" data-target="#scripting" data-language="html"><i class="fas fa-edit"></i></td>
			<td class="scripting <?= ($r["responseareatemplate"]!=""?"text-info":"text-muted");?>" data-edittype="responseareatemplate" data-toggle="modal" data-target="#scripting" data-language="html"><i class="fas fa-edit"></i></td>
			<td class="scripting <?= ($r["codeareatemplate"]!=""?"text-info":"text-muted");?>" data-edittype="codeareatemplate" data-toggle="modal" data-target="#scripting" data-language="html"><i class="fas fa-edit"></i></td>
			<td class="scripting <?= ($r["insert_script"]!=""?"text-info":"text-muted");?>" data-edittype="insert_script" data-toggle="modal" data-target="#scripting" data-language="js"><i class="fas fa-edit"></i></td>
			<td class="scripting <?= ($r["styles"]!=""?"text-info":"text-muted");?>" data-edittype="styles" data-toggle="modal" data-target="#scripting" data-language="css"><i class="fas fa-edit"></i></td>
			<td><?php
			$variables=json_decode($r["variables"],true); 
			echo implode("\n",
				array_map(
					function($v,$k) {
						return '<div><span class="editable first" data-oldvalue="'.$k.'" data-edittype="variables"  data-edittype2="name" contenteditable>'.$k.'</span>: <span class="editable" data-edittype="variables"  data-edittype2="value" contenteditable>'.htmlspecialchars($v,ENT_QUOTES).'</span><span class="deletevariable float-right"><i class="fa fa-trash"></i></span></div>';
					},
					$variables,
					array_keys($variables)
				)
			);
			?><div class="addvariable"><i class="fas fa-plus"></i></div>
			</td>
		</tr>
	
	<?php
	}
	?>
	</tbody>
</table>
  <?php

?>

<div class="modal" tabindex="-1" id="scripting" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?= _("Edit");?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			<button type="button" class="btn btn-primary" id="syntaxhighlight"><?= _("Syntax Highlight");?></button>

			<div id="editor" style="overflow:scroll;max-height:700px">
			</div>
			
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _("Close");?></button>
				<button type="button" class="btn btn-primary savecode" data-doclose="dont" data-edittype="" data-tasktype_id=""><?= _("Save");?></button>
				<button type="button" class="btn btn-success savecode" data-doclose="do" data-edittype="" data-tasktype_id=""><?= _("Save & Close");?></button>
			</div>
		</div>
	</div>
</div>
