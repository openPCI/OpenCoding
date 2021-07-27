<?php
// Tasks are defined in the tasktype-table. Twig-templates are used, so you can include variables and iterate over them.
require_once $relative.'/vendor/autoload.php';
checkperm();
include_once($shareddir."database.php");


$codingadmin=$_SESSION["perms"]["codingadmin"][$_SESSION["project_id"]];

if($_POST["maxcodedresponse_id"]) $_SESSION["response_id"]=$_POST["maxcodedresponse_id"];
$q="select tt.* from tasks t left join tasktypes tt on t.tasktype_id=tt.tasktype_id where task_id=".$_POST["task_id"];
$result=$mysqli->query($q);
$tasktype=$result->fetch_assoc();

$loader = new \Twig\Loader\ArrayLoader([
    'playarea' => $tasktype["playareatemplate"],
    'responsearea' => $tasktype["responseareatemplate"],
    'insert_script' => $tasktype["insert_script"],
    'styles' => $tasktype["styles"]
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
if($_POST["special"]) {
?><input type="hidden" id="<?= $_POST["special"];?>" value="true"><?php
	$res[$_POST["special"]]=true;
}
	
if($_POST["first_id"]) $res["first_id"]=$_POST["first_id"];
?>
<script>
window.addEventListener('message', function(event){
  var type = event.data.type;
  if(messageListeners[type])
  while(messageListeners[type].length > 0){
    var handler = messageListeners[type][messageListeners[type].length-1];
    handler(event);
    messageListeners[type].pop();
  }
});

var messageListeners = {};
function onceMessage(type, cb){  
	if(!messageListeners[type]) 
	messageListeners[type] = [];  
	messageListeners[type].push(cb);
}
function sendMessage(type, value){
  if(window.parent)
    $("#playarea iframe")[0].contentWindow.postMessage({
      type: type,
      value: value
    },'*');
}
function insertResponse(json) {
 	<?=  
 	$twig->render('insert_script',$tasksettings);
 	?>
}
</script>
<style>
	<?=  
	$twig->render('styles',$tasksettings);
	?>
</style>
<!-- Main Container -->
<div class="container-fluid <?= $_POST["special"];?>">
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
		<div class="col" id="codeTable">
			<div class="row">
				<div class="col">
					<span class="float-right text-muted" data-toggle="collapse" data-target="#flagcommentsdiv" id="flag" title="<?= _("Flag response.");?>"><i class="fas fa-flag"></i></span>
					<?php if($codingadmin) {?><span class="float-right text-muted mr-2" data-toggle="tooltip" data-placement="top" id="trainingresponse" data-used="<?= _("This response is used in coder training. Difficulty: ");?>" data-notused="<?= _("Mark response as used in coder training.");?>" title=""><i class="fas fa-check-double"></i></span><?php } ?>

					<?php 
						foreach(json_decode($task["items"]) as $item_name=>$maxvalue) {
						?>
							<div class="form-group">
								<label for="item<?= $item_name;?>"><?= $item_name;?></label>
								<input type="number" data-item_name="<?= $item_name;?>" class="form-control itemvalue" name="<?= $item_name;?>" placeholder="" min="-1" max="<?= $maxvalue;?>" step="1" required>
							</div>
						<?php
						}
					?>
				</div>
			</div>
			<!-- Navigation Container -->
			<div class="row" style="max-width:300px;">
				<div class="col text-center">
					<button class="btn btn-primary nextresponse" data-next="&lt;">&lt;</button>
				</div>
				<div class="col text-center">
					<input type="text" pattern="[0-9]+" class="form-control" id="response_id" value="0" style="width:100px" readonly="readonly">
				</div>
				<div class="col text-center">
					<button class="btn btn-primary nextresponse" data-next="&gt;">&gt;</button>
				</div>
			</div>
			<div class="row">
				<div class="col text-center">
					<p class="text-muted small"><?= _("Use TAB and SHIFT+TAB to shift forward and back between item-codes. Use ARROWS to increase and decrease codes. Hit ENTER when in the last item-code to go to next response.");?></p>
				</div>
			</div>
	<!-- Interaction Container -->
			<div class="row">
				<!-- Coding Container -->
				<div class="col CodingRubrics">
				<?= $task["coding_rubrics"];?>
				</div>
			</div>
		</div>
		<div class="form-group collapse col" id="flagcommentsdiv">
			<div class="" ><?= _("Flagged by:");?> <span id="flaggedby"></span></div>
			<div class="" id="flagcommentshistory"></div>
			<textarea class="form-control" id="flagcomment"></textarea>
			<button class="btn btn-secondary" id="sendcomment"><?= _("Add comment");?></button>
		</div>
			
	</div>
	<div class="row">
<!-- 		<div class="content codingarea"> -->
		<div class="col d-flex justify-content-end">
			<button class="btn btn-primary nextresponse" data-next="finish"><?= _("Finish");?></button>
		</div> 
	</div> 
    
</div>
