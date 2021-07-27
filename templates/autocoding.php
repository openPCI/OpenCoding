<?php
require_once $relative.'/vendor/autoload.php';
checkperm();
include_once($shareddir."database.php");


$codingadmin=$_SESSION["perms"]["codingadmin"][$_SESSION["project_id"]];

$q="select tt.* from tasks t left join tasktypes tt on t.tasktype_id=tt.tasktype_id where task_id=".$_POST["task_id"];
$result=$mysqli->query($q);
$tasktype=$result->fetch_assoc();


$loader = new \Twig\Loader\ArrayLoader([
    'playarea' => str_replace("&slashn;","\\n",$tasktype["playareatemplate"]),
    'responsearea' => str_replace("&slashn;","\\n",$tasktype["responseareatemplate"]),
    'codearea' => str_replace("&slashn;","\\n",$tasktype["codeareatemplate"]),
    'insert_script' => str_replace("&slashn;","\\n",$tasktype["insert_script"]), # Script should include init and save functions. Init can use responses and data from sessionStorage. Save should return an object with data and responses-object with items and their code included {itemname:"an item",code:"value"} for all responses on the items defined in project admin, and provided to script in sessionStorage: items.
    'styles' => str_replace("&slashn;","\\n",$tasktype["styles"]),
    'instructions' => $tasktype["tasktype_instructions"]
]);
$twig = new \Twig\Environment($loader);

$q="select * from tasks t where task_id=".$_POST["task_id"]." or group_id=".$_POST["task_id"]." order by group_id";
$result=$mysqli->query($q);
$task=$result->fetch_assoc();
$tasksettings=json_decode($task["tasksettings"]);
$tasksettings["task_image"]=$task["task_image"];
$tasksettings["task_name"]=$task["task_name"];
$variables=($tasktype["variables"]?json_decode($tasktype["variables"],true):array());
$tasktype_variables=($task["tasktype_variables"]?json_decode($task["tasktype_variables"],true):array());
$tasksettings=array_merge($tasksettings,$variables,$tasktype_variables);
$subtasks=array();
if($result->num_rows>1) {
	while($r=$result->fetch_assoc()) {
		$subtasks[$r["task_id"]]=$r["task_name"];
	}
}
$tasksettings["subtasks"]=$subtasks;

?>
<script src="js/coding.js"></script>
<script>
	<?=  
		$twig->render('insert_script',$tasksettings);
	?>
</script>
<style>
	<?=  
	$twig->render('styles',$tasksettings);
	?>
</style>
<!-- Main Container -->
<div class="container-fluid <?= $_POST["special"];?>">
	<div class="row p-2 sticky-top">
		<div class="col  justify-content-end d-flex">
			<div class="">
				<button class="btn btn-success autosave" data-type="saved"><?= _("Save");?></button>
				<button class="btn btn-primary autosave ml-2" data-type="finish"><?= _("Finish");?></button>
			</div> 
		</div> 
	</div> 

	<div class="row">
<!-- 		<div class="content codingarea"> -->
		<div class="col">
			<div class="row">
				<div class="col" id="playarea" data-task_id="<?= $_POST["task_id"];?>" data-subtask_ids="<?= implode(",",array_keys($subtasks));?>">
				<?= 
					$twig->render('playarea',$tasksettings);

				?>
				</div>
			</div>
			<div class="row">
				<div class="col" id="responsearea">
				<?= 
					$twig->render('responsearea',$tasksettings);
				?>
				</div>
			</div>
		</div>
		<div class="col" >
			<div class="row">
<?php if($tasktype["codeareatemplate"]) { ?>
				<div class="col" id="responsearea">
				<?= 
					$twig->render('codearea',$tasksettings);
				?>
				</div>
					<?php 
					}
					else {
						foreach(json_decode($task["items"]) as $item_name=>$maxvalue) {
						?>
							<div class="form-group col-3">
								<label for="item<?= $item_name;?>"><?= $item_name;?></label>
								<input type="number" data-item_name="<?= $item_name;?>" class="form-control itemvalue" name="<?= $item_name;?>" placeholder="" min="-1" max="<?= $maxvalue;?>" step="1" disabled>
							</div>
						<?php
						}
					?>
			</div>
			<!-- Navigation Container -->
			<div class="row" style="max-width:300px;">
				<div class="col text-center">
					<button class="btn btn-primary nextautoresponse" data-next="&lt;">&lt;</button>
				</div>
				<div class="col text-center d-flex justify-content-center">
					<input type="text" pattern="[0-9]+" class="form-control" id="response_id" value="0" style="width:100px" readonly="readonly">
					<div id="waiticon" class="position-absolute align-self-center text-center w-100" style="display:none;"><i class="fas fa-sync-alt fa-spin"></i></div>
				</div>
				<div class="col text-center">
					<button class="btn btn-primary nextautoresponse" data-next="&gt;">&gt;</button>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<h3><?= _("Statistics");?></h3>
					<button class="btn btn-success" id="updatestats"><?= _("Update");?></button>
				</div>
			</div>
			<div class="row">
		<?php 
			foreach(json_decode($task["items"]) as $item_name=>$maxvalue) {
			?>
				<div class="form-group col-3">
					<h6><?= $item_name;?></h6>
					<p class="itemstat" data-item_name="<?= $item_name;?>"></p>
				</div>
			<?php
			}
		}
		?>
			</div>

<?php ?>
<!-- Interaction Container -->
			<div class="row">
				<!-- Coding Container -->
				<div class="col CodingRubrics">
				<?= $task["coding_rubrics"];?>
				</div>
			</div>
			<div class="row">
				<div class="col instructions">
				<?= 	
					$twig->render('instructions',$tasksettings);
				?>
				</div>
			</div>
		</div>
			
	</div>
    
</div>