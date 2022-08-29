<?php
// Tasks are defined in the tasktype-table. Twig-templates are used, so you can include variables and iterate over them.
require_once $relative.'/vendor/autoload.php';
checkperm();
include_once($shareddir."database.php");


$codingadmin=$_SESSION["perms"]["codingadmin"][$_SESSION["project_id"]];

$_SESSION["remainingresponses"]=$_POST["remainingresponses"];


if($_POST["codetype"]=="code") {
	$q="select (select doublecodingpct from projects where project_id=".$_SESSION["project_id"].") as doublecodingpct, count(*) as numresponses,sum(isdoublecode) as numdoublecoded,sum(if(c.response_id IS NOT NULL and c.isdoublecode=0,1,0)) as numcoded from responses r left join coded c on r.response_id=c.response_id where r.task_id=".$_POST["task_id"];

	// echo $q;
	if(!$result=$mysqli->query($q)) echo $mysqli->error;
	else $r=$result->fetch_assoc();
	//reviseddoublecodedpct=number of responses to doublecoding 
	$numbertodoublecode=$r["numresponses"]*$r["doublecodingpct"]/100;
	$remainingtodoublecode=$numbertodoublecode-$r["numdoublecoded"];
	$remaining=$r["numresponses"]-$r["numcoded"];
	$reviseddoublecodedpct=($remaining>0?$remainingtodoublecode/$remaining*100:0);
	$_SESSION["doublecodingpct"]=$reviseddoublecodedpct;
}
// echo "revised: " .$reviseddoublecodedpct;
$revise=($_POST["special"]=="revise" or $_POST["special"]=="reviseall");
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

$q="select t.task_id, if(t.clone_task_id!=0,tc.task_image,t.task_image) as task_image,if(t.clone_task_id!=0,tc.task_name,t.task_name) as task_name,if(t.clone_task_id!=0,tc.item_prefix,t.item_prefix) as item_prefix,if(t.clone_task_id!=0,tc.tasktype_variables,t.tasktype_variables) as tasktype_variables,if(t.clone_task_id!=0,tc.coding_rubrics,t.coding_rubrics) as coding_rubrics,if(t.clone_task_id!=0,tc.items,t.items) as items from tasks t left join tasks tc on t.clone_task_id=tc.task_id where t.task_id=".$_POST["task_id"]." or t.group_id=".$_POST["task_id"]." order by t.group_id ASC, t.task_id ASC";
// echo $q;
$result=$mysqli->query($q);
$task=$result->fetch_assoc();
$tasksettings=array();//json_decode($task["tasksettings"]);
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
// print_r($subtasks);
$tasksettings["subtasks"]=$subtasks;
if($_POST["special"]) {
?><input type="hidden" id="<?= $_POST["special"];?>" value="true"><?php
	$res[$_POST["special"]]=true;
}
if($_POST["flagstatus"]) { ?><input type="hidden" id="flagstatus" value="<?= $_POST["flagstatus"];?>"><?php }
	
// if($_POST["first_id"]) $res["first_id"]=$_POST["first_id"];
?>
<script>
var firstdone=false;
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
				<div class="col" >
				<h6><?= ($task["item_prefix"]?$task["item_prefix"].": ":"").$task["task_name"];?></h6>
				</div>
			</div>
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
			<?php if($revise) { ?>
			<div class="row">
				<div class="col">
					<button role="button" class="btn btn-info" data-toggle="collapse" data-target=".searchvalue"><?= _("Filter responses");?></button>
				</div>
				<input type="text" class="form-control searchvalue collapse" placeholder="<?= _("Search for text in item response");?>" data-item_name="itemtextsearch">
			</div>
			<?php } ?>
			<div class="row">
				<div class="col">
					<span class="float-right text-muted" id="flag" title="<?= _("Flag response.");?>"><i class="fas fa-flag"></i></span>
					<?php if($codingadmin) {?><span class="float-right text-muted mr-2" data-toggle="tooltip" data-placement="top" id="trainingresponse" data-used="<?= _("This response is used in coder training. Difficulty: ");?>" data-notused="<?= _("Mark response as used in coder training.");?>" title=""><i class="fas fa-check-double"></i></span><?php } ?>
					<div class="row">

					<?php 
						$itemobj=json_decode($task["items"],true); 
						$items=$itemobj["items"];
						$itemorder=$itemobj["order"]?$itemobj["order"]:array();
						$extra=array_diff(array_keys($items),$itemorder);
						$itemorder=array_merge($itemorder,$extra);

						foreach($itemorder as $item_name) {
						?>
							<div class="form-group col">
								<label for="item<?= $item_name;?>"><?= $item_name;?></label>
								<input type="number" data-item_name="<?= $item_name;?>" class="form-control itemvalue" name="<?= $item_name;?>" placeholder="" min="-1" max="<?= $items[$item_name];?>" step="1" required>
								<?= ($revise?'<input type="number" data-item_name="'.$item_name.'" class="form-control collapse searchvalue">':''); ?>
							</div>
						<?php
						}
					?>
					</div>
				</div>
			</div>
			<!-- Navigation Container -->
			<div class="row" style="">
				<div class="col text-center">
					<button class="btn btn-primary nextresponse" data-next="&lt;">&lt;</button>
				</div>
				<div class="col text-center">
					<input type="text" pattern="[0-9]+" class="form-control" id="response_id" value="0" style="width:100px" readonly="readonly">
				</div>
				<div class="col text-center">
					<button class="btn btn-primary nextresponse" data-next="&gt;">&gt;</button>
				</div>
				<?php if(count($itemorder)>1) { ?>
					<div class="col text-center">
						<button class="btn btn-secondary nextresponse" data-code0="true" data-next="code0"><?= _("Code all 0");?></button>
					</div>
				<?php } ?>
				<div class="col-4">
					<button class="btn btn-secondary nextresponse float-right" data-next="finish"><?= _("Finish");?></button>
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
    
</div>
