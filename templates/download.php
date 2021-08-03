<?php

checkperm("projectadmin");
include_once($shareddir."database.php");

$q="select * from tests where project_id=".$_SESSION["project_id"];
$result=$mysqli->query($q);
	?>
<?php 
if($result->num_rows==0) echo _("You don't have any tests in this project. Upload data to create tests.");
else {
	?>
	<ul>
	<?php
	while($r=$result->fetch_assoc()) {
		?>
		<li class="testli">
			<input type="checkbox" class="form-check-input testcheck" id="task_<?= $r["test_id"];?>" data-test_id="<?= $r["test_id"];?>">
			<label class="form-check-label" for="task_<?= $r["test_id"];?>"><?= $r["test_name"];?></label>
			<ul>
			<?php
				$q="select * from tasks where test_id=".$r["test_id"]." GROUP BY 1 order by `group_id`";
				
				
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
