<?php
 
checkperm("codingadmin");
include_once($shareddir."database.php");
$l = localeconv();



$q="(select test_name as name,t.test_id,t.test_id as unit_id,1 as coltype from tests t left join tasks tt on t.test_id=tt.test_id where project_id=".$_SESSION["project_id"]." GROUP by test_id order by test_name) 
union 
(select task_name as name,t.test_id,t.task_id, 2 from tasks t left join tests tt on t.test_id=tt.test_id left join responses r on r.task_id=t.task_id where t.group_id=0 and tt.project_id=".$_SESSION["project_id"]." order by task_name)
order by test_id,coltype,name";

$all=array();
if(!$result=$mysqli->query($q)) echo $mysqli->error;
else $all=$result->fetch_all(MYSQLI_ASSOC);
$unit_ids=array_map(function($x) { return $x["unit_id"];},$all);


$qstart='SELECT username, user_id ';

$qarr["test"]=$qstart.' from `assign_test` a left join tests t on a.test_id=t.test_id left join users u on a.coder_id=u.user_id where project_id='.$_SESSION["project_id"].' group by user_id';
$qarr["task"]=$qstart.' from `assign_task` a left join tasks tt on tt.task_id=a.task_id left join tests t on tt.test_id=t.test_id left join users u on a.coder_id=u.user_id where project_id='.$_SESSION["project_id"].' group by user_id';

$coders=array();
$overall=array();

foreach($qarr as $unittype=>$q) {
	if($result=$mysqli->query($q)) while($c=$result->fetch_assoc()) {
		$coders[$c["user_id"]]=$c["username"];
		$overall[$c["user_id"]]=array();
	}
	else echo $q;
}
// print_r($coders);
?>
    <div >
		<div class="row">
			<div class="col">
				<h3><?= _("Coding Management");?></h3>
				<div class="">
					<table class="table sticky-column">
						<thead class="sticky-top">
							<tr class="table-light">
							<th scope="col"></th>
						<?php foreach($coders as $coder_id=>$codername) {?>
							<th scope="col" colspan="3" class="text-center border-left" data-coder_id<?= $coder_id;?>><?= $codername;?></th>
						<?php }?>
							</tr>
							<tr class="table-light">
							<th scope="col"><?= _("Test/task name");?></th>
						<?php for($i=0;$i<count($coders);$i++) {?>
							<td scope="col" class="text-right border-left"><?= _("Coded");?></td>
							<td scope="col" class="text-right"><?= _("Double coded");?></td>
							<td scope="col" class="text-right"><?= _("Agreement");?></td>
						<?php }?>
							</tr>
						</thead>
						<tbody id="tasklist">
					<?php
						
						
						foreach($all as $r) {
						?>
							<tr data-unit_id="<?= $r["unit_id"];?>" data-<?= ($r["coltype"]==1?"test":"task");?>_id="<?= $r["unit_id"];?>" data-unittype="<?= ($r["coltype"]==1?"test":"task");?>" class="<?= ($r["coltype"]==1?"table-warning":"");?>">
								<?= ($r["coltype"]==1?'<th scope="row">':"<td>");?><?= $r["name"];?><?= ($r["coltype"]==1?'</th>':"</td>");?>
								<?php
								foreach($coders as $coder_id=>$codername) {
									if($r["coltype"]==2) {
										$q="select count(*) as numcodes from coded c left join responses r on r.response_id=c.response_id where coder_id=".$coder_id." and task_id=".$r["unit_id"];
										if($result=$mysqli->query($q)) 
											$numcodes=$result->fetch_assoc()["numcodes"];
										else echo $q;
										$agreement=array();
										$numdoublecodes=0;
										if($numcodes>0) {
											$q="select c.codes, c1.codes as doublecodes from coded c left join coded c1 on c.response_id=c1.response_id left join responses r on r.response_id=c.response_id where (c1.coder_id!=c.coder_id) and c.coder_id=".$coder_id." and task_id=".$r["unit_id"];
											$log.=$q;
											$result=$mysqli->query($q);
											$numdoublecodes=$result->num_rows;
											$alld=$result->fetch_all(MYSQLI_ASSOC);
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
										}
										$total=0;
										if($agreement) {
											$agreement=array_merge(...$agreement);
											$agree=array_sum($agreement);
											$total=count($agreement);
										} else $agree=0;
										$overall[$coder_id]["numcodes"]+=$numcodes;
										$overall[$coder_id]["numdoublecodes"]+=$numdoublecodes;
										$overall[$coder_id]["agree"]+=$agree;
										$overall[$coder_id]["total"]+=$total;
										?>
										<td class="text-right border-left"><?= $numcodes;?></td>
										<td class="text-right"><?= $numdoublecodes;?></td>
										<td class="text-right"><?= (number_format(($total?$agree/$total*100:0),1,$l["decimal_point"],$l["thousands_sep"])." %");?></td>
									<?php 
								} else {
								?>
									<td class="text-right border-left"></td>
									<td class="text-right"></td>
									<td class="text-right"></td>
								<?php 
									}
								}
								?></td>
							</tr>
						<?php
						} ?>
						<tr class="table-success">
						<th ><?= _("Overall");?></th>
						<?php
						foreach($coders as $coder_id=>$codername) {
							?>
									<th class="text-right border-left"><?= $overall[$coder_id]["numcodes"];?></th>
									<th class="text-right"><?= $overall[$coder_id]["numdoublecodes"];?></th>
									<th class="text-right"><?= (number_format(($overall[$coder_id]["total"]?$overall[$coder_id]["agree"]/$overall[$coder_id]["total"]*100:0),1,$l["decimal_point"],$l["thousands_sep"])." %");?></th>
							<?php	
						}
						?>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
  </div>

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
