<?php
 
checkperm("codingadmin");
include_once($shareddir."database.php");
$l = localeconv();

$q="(select test_name as name,t.test_id,t.test_id as unit_id,sum(if(group_id=0,1,0)) as unitcount,'' as coded,'' as doublecoded, '' as flagged, '' as resolved,'' as first_id,1 as coltype from tests t left join tasks tt on t.test_id=tt.test_id where project_id=".$_SESSION["project_id"]." GROUP by test_id order by test_name) 
union 
(select task_name as name,t.test_id,t.task_id,count(r.response_id) as rcount,count(DISTINCT c.response_id) as ccount, count(c.response_id)-count(DISTINCT c.response_id) as dcount, sum(if(f.flagstatus='flagged',1,0)),sum(if(f.flagstatus='resolved',1,0)), min(f.flag_id),2 from tasks t left join tests tt on t.test_id=tt.test_id left join responses r on r.task_id=t.task_id left join coded c on c.response_id=r.response_id left join flags f on f.response_id=r.response_id where t.group_id=0 and tt.project_id=".$_SESSION["project_id"]." GROUP BY task_id order by task_name)
order by test_id,coltype";

$result=$mysqli->query($q);
$all=$result->fetch_all(MYSQLI_ASSOC);
$unit_ids=array_map(function($x) { return $x["unit_id"];},$all);


$qstart='SELECT GROUP_CONCAT(CONCAT(username," <span class=\'deletecoder\' data-user_id=\'",u.user_id,"\'><i class=\'fas fa-trash\'></i></span>") order by username SEPARATOR ", ") as coders, ';

$qarr["test"]=$qstart.' a.`test_id` as `unit_id` from `assign_test` a left join tests t on a.test_id=t.test_id left join users u on a.coder_id=u.user_id where project_id='.$_SESSION["project_id"].' group by unit_id';
$qarr["task"]=$qstart.' a.`task_id` as `unit_id` from `assign_task` a left join tasks tt on tt.task_id=a.task_id left join tests t on tt.test_id=t.test_id left join users u on a.coder_id=u.user_id where project_id='.$_SESSION["project_id"].' group by unit_id';

$coderbadges=array();
	$coders=array();
foreach($qarr as $unittype=>$q) {
	$result=$mysqli->query($q);
	$allcoders=$result->fetch_all(MYSQLI_ASSOC);
	foreach($allcoders as $c) {
		$coders[$unittype][$c["unit_id"]]=$c["coders"];
	}
}
// print_r($coders);
?>
    <div >
		<div class="row">
			<div class="col">
				<h3><?= _("Coding Management");?></h3>
				<table class="table">
					<thead>
						<tr>
						<th scope="col"><?= _("Test/task name");?></th>
						<th scope="col" class="text-right"><?= _("Count");?></th>
						<th scope="col" class="text-right"><?= _("Coded");?></th>
						<th scope="col" class="text-right"><?= _("Double coded");?></th>
						<th scope="col" class="text-right"><?= _("Agreement");?></th>
						<th scope="col" class="text-right"><?= _("Flagged");?></th>
						<th scope="col" class="text-right"><?= _("Resolved");?></th>
						<th scope="col" colspan="2"><?= _("Assigned Coders");?></th>
						</tr>
					</thead>
					<tbody id="tasklist">
				<?php
					
					
					foreach($all as $r) {
					?>
						<tr data-unit_id="<?= $r["unit_id"];?>" data-unittype="<?= ($r["coltype"]==1?"test":"task");?>" class="<?= ($r["coltype"]==1?"table-warning":"");?>">
							<?= ($r["coltype"]==1?'<th scope="row">':"<td>");?><?= $r["name"];?><?= ($r["coltype"]==1?'</th>':"</td>");?>
							<td class="text-right"><?= ($r["coltype"]==2?$r["unitcount"]:"");?></td>
							<td class="text-right"><?= ($r["coltype"]==2?($r["unitcount"]==0?0:$r["coded"]." (".number_format($r["coded"]/$r["unitcount"]*100,1,$l["decimal_point"],$l["thousands_sep"])." %)"):"");?></td>
							<td class="text-right"><?= ($r["coltype"]==2?$r["doublecoded"]." (".number_format(($r["coded"]>0?$r["doublecoded"]/$r["coded"]*100:0),1,$l["decimal_point"],$l["thousands_sep"])." %)":"");?></td>
							<td class="text-right"><?php
							if($r["coltype"]==2) {
								$q="select c.codes as doublecodes,c1.codes from coded c left join coded c1 on c.response_id=c1.response_id left join responses r on r.response_id=c.response_id where c1.coder_id!=c.coder_id and c.isdoublecode=1 and task_id=".$r["unit_id"];
								$log=$q;
								$result=$mysqli->query($q);
								$alld=$result->fetch_all(MYSQLI_ASSOC);
								$agreement=array();
								foreach($alld as $r1) {
									$codes=json_decode($r1["codes"],true);
									$doublecodes=json_decode($r1["doublecodes"],true);
									$agreement[]=array_map(function($c,$d) {
										global $warning;
										if($c["item_name"]!=$d["item_name"]) $warning=_("Item names are not the same");
										else {
											return ($c["code"]==$d["code"]?1:0);
										}
									},$doublecodes,$codes);
								}
								if($agreement) {
									$agreement=array_merge(...$agreement);
									$agree=array_sum($agreement);
									$total=count($agreement);
									echo ($total?$agree." of ".$total." (".number_format(($agree/$total*100),1,$l["decimal_point"],$l["thousands_sep"])." %)":"0");
								} else echo 0;
							}
							?></td>
							<td class="text-right"><?= ($r["coltype"]==2?'<a href="#" class="codeTask" data-first_id="'. $r["first_id"].'" data-task_id="'. $r["unit_id"].'">'. $r["flagged"].'</a>':"");?></td>
							<td class="text-right"><?= ($r["coltype"]==2?'<a href="#" class="codeTask" data-first_id="'. $r["first_id"].'" data-task_id="'. $r["unit_id"].'">'. $r["resolved"].'</a>':"");?></td>
							<td ><?= ($r["coltype"]==2?$coders["task"][$r["unit_id"]].'<button class="btn btn-primary float-right" data-toggle="modal" data-target="#addcodermodal" >'. _("Add coder").'</button>':"");?></td>
							<?php if($r["coltype"]==1) { ?> <td class="table-info " style="text-align: center;vertical-align: middle;" rowspan="<?= ($r["unitcount"]+1);?>"><button class="btn btn-primary float-right" data-toggle="modal" data-target="#addcodermodal" ><?= _("Add coder");?></button><?= $coders["test"][$r["unit_id"]];?></td><?php }?>
						</tr>
					
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
  </div>
<input type="hidden" id="task_ids" value="<?= implode(",",$task_ids);?>">
<div class="modal" tabindex="-1" id="addcodermodal" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?= _("Add coders");?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<label for="knowncoders"><?= _('Coders on project') ?></label>
				<div id="knowncoders">
				<?php
					$q='SELECT CONCAT("<a href=\'#\' class=\'badge badge-primary mr-2 column addcoder\' data-user_id=\'",u.user_id,"\'>",username," (",email,")</a>") as coderbadge	from user_permissions p left join users u on p.user_id=p.user_id where unittype="coding" and unit_id='.$_SESSION["project_id"].' order by username';
					$result=$mysqli->query($q);
					while($c=$result->fetch_assoc())
						$coderbadges[]=$c["coderbadge"];
					echo implode(" ",array_unique($coderbadges));
				?>
				</div>
				<div>
					<label for="newcoder"><?= _('Find coder') ?></label>
					<input type="text" class="form-control" id="newcoder">
					<div id="newcoderdiv"></div>
				</div>
				<hr>
				<label for="newcoders"><?= _('Add coders') ?></label>
				<div id="newcoders">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _("Close");?></button>
				<button type="button" class="btn btn-primary " id="addcoders" data-unittype="" data-unitid=""><?= _("Save");?></button>
			</div>
		</div>
	</div>
</div>
