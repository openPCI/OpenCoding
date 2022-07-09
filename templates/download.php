<?php

checkperm("projectadmin");
include_once($shareddir."database.php");

$q="select * from tests where project_id=".$_SESSION["project_id"];
$result=$mysqli->query($q);
	?>
<div class="container">

	<?php 
if($result->num_rows==0) echo _("You don't have any tests in this project. Upload data to create tests.");
else {
	?>
	<h3><?= _("Data format");?></h3>
	<div class="form-check form-check-inline">
		<input class="form-check-input" type="radio" name="dataformat" id="dataformatmatrix" value="matrix" checked>
		<label class="form-check-label" for="dataformatmatrix"><?= _("Matrix, first coders code");?></label>
	</div>
	<div class="form-check form-check-inline">
		<input class="form-check-input" type="radio" name="dataformat" id="dataformatmultiple" value="multiple">
		<label class="form-check-label" for="dataformatmultiple"><?= _("Matrix, didiscrepancy codes on multiple lines");?></label>
	</div>
	<div class="form-check form-check-inline">
		<input class="form-check-input" type="radio" name="dataformat" id="dataformatcoders" value="coders">
		<label class="form-check-label" for="dataformatmultiple"><?= _("Long format, coders in columns");?></label>
	</div>
	<div>
		<input type="checkbox" class="form-check-input testcheck" id="alltasks">
		<label class="form-check-label" for="alltasks"><?= _("Select all tasks");?></label>
	</div>
	<ul>
	<?php
	while($r=$result->fetch_assoc()) {
		?>
		<li class="testli">
			<input type="checkbox" class="form-check-input testcheck" id="task_<?= $r["test_id"];?>" data-test_id="<?= $r["test_id"];?>">
			<label class="form-check-label" for="task_<?= $r["test_id"];?>"><?= $r["test_name"];?></label>
			<ul>
			<?php
				$q="select * from tasks where test_id=".$r["test_id"]." and `group_id`=0";
				
				
				$result1=$mysqli->query($q);
				while($r1=$result1->fetch_assoc()) {
				?>
					<li>
						<input type="checkbox" class="form-check-input taskcheck" id="task_<?= $r1["task_id"];?>" data-task_id="<?= $r1["task_id"];?>">
						<label class="form-check-label" for="task_<?= $r1["task_id"];?>"><?= $r1["task_name"];?></label>
					</li>
				<?php
				}
				?>
			</ul>
		</li>
	<?php

	}
	?>
	</ul>
	<?php
}
?>

<button type="button" class="btn btn-success" id="doDownload"><?= _("Download");?></button>
</div>
