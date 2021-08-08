<?php
	$relative="../";
	include_once($functionsdir."database.php");
$oca=(strpos($_SERVER["HTTP_REFERER"],"p=opencodingadmin"));
if($oca) checkperm("opencodingadmin");
else checkperm("projectadmin");
	global $res;
// 	$res["org_id"]=$_POST["org_id"];
if($oca) 
		$q='(select u.user_id,u.username,u.email,p.unit_id,if(p.unit_id=0,"'._("System").'", project_name) as project,group_concat(p.unittype separator ", ") as permissions, 1 as permtype from users u left join user_permissions p on u.user_id=p.user_id left join projects pr on pr.project_id=p.unit_id where '.($oca?1:'unit_id='.$_SESSION["project_id"]).' group by project_id,1 order by username , project_name )';
else	$q='(select u.user_id,u.username,u.email,p.unit_id,group_concat(p.unittype separator ", ") as permissions, 1 as permtype from users u left join user_permissions p on u.user_id=p.user_id where '.($oca?1:'unit_id='.$_SESSION["project_id"]).' group by 1 order by username)';

// 	echo $q;
if(!$result=$mysqli->query($q)) {echo $q."<br>".$mysqli->error; }
else $all=$result->fetch_all(MYSQLI_ASSOC);
	
?>
<div class="container-fluid">

	<div class="row">
		<div class="col">
			<button class="btn btn-success float-right" data-toggle="modal" data-target="#edituser"><?= _("New user"); ?></button>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<h3><?= _("Users"); ?></h3>
			<table class="table table-sm table-hover mt-2">
				<thead>
					<tr>
					<th scope="col"></i><?= _('Username');?></th>
					<th scope="col"></i><?= _('E-mail');?></th>
					<th scope="col"></i><?= _('Permissions');?></th>
					<th scope="col"></i><?= _('Actions');?></th>
					</tr>
				</thead>
				<tbody class="table-striped " id="userlist">
				<?php
					for($i=0; $i<count($all);$i++) { 
					$r=$all[$i];
					$permissions="";
					if($r["project"]) {
						$permissions='<p><span class="font-weight-bold">'.$r["project"].'</span>: <span class="changePermissions" data-user="'. $r["user_id"].'" data-unit_id="'.$r["unit_id"].'">'.$r["permissions"].'</span></p>';
						while($r["user_id"]==$all[$i+1]["user_id"] and $i<count($all)) {
							$i++;
							$permissions.='<p><span class="font-weight-bold">'.$all[$i]["project"].'</span>: <span class="changePermissions" data-user="'. $all[$i]["user_id"].'" data-unit_id="'.$all[$i]["unit_id"].'">'.$all[$i]["permissions"].'</span></p>';
						};
					} else $permissions='<p><span class="changePermissions" data-user="'. $r["user_id"].'" data-unit_id="'.$r["unit_id"].'">'.$r["permissions"].'</span></p>';

					?>
						<tr data-user_id=<?= $r["user_id"];?>>
							<td data-type="username"><?= $r["username"];?></td>
							<td data-type="email"><?= $r["email"];?></td>
							<td><?= $permissions;?></td>
							<td><?php if($oca) { ?>
 								<button type="button" class="btn btn-danger deleteuser"><?= _('Delete user');?></button>
								<?php } ?>
								<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edituser" ><?= _('Edit');?></button>
							</td>
						<tr>
				<?php	}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	$q='(SELECT u.user_id,u.username,u.email,group_concat(t.test_name separator ", ") as permissions, 2 as permtype from users u left join assign_test p on u.user_id=p.coder_id left join tests t on p.test_id=t.test_id where t.project_id='.$_SESSION["project_id"].' group by 1 order by username )
	UNION
	(SELECT u.user_id,u.username,u.email,group_concat(concat("<b>",t.test_name,"</b>: ",tt.task_name) separator ", ") as permissions, 3 as permtype from users u left join assign_task p on u.user_id=p.coder_id left join tasks tt on tt.task_id=p.task_id left join tests t on tt.test_id=t.test_id where t.project_id='.$_SESSION["project_id"].' group by 1 order by username )
	order by username';
	
// 	echo $q;
if(!$result=$mysqli->query($q)) {echo $q."<br>".$mysqli->error; }
if(!$oca) {
?>
	<div class="row">
		<div class="col">
			<h3><?= _("Coding tasks"); ?></h3>
			<p class="text-muted"><?= _("Assign and remove coding tasks in coding management"); ?></p>
			<table class="table table-sm table-hover mt-2">
				<thead>
					<tr>
					<th scope="col"></i><?= _('Username');?></th>
					<th scope="col"></i><?= _('E-mail');?></th>
					<th scope="col"></i><?= _('Permissions');?></th>
					</tr>
				</thead>
				<tbody class="table-striped " id="userlist">
				<?php
					while($result and $r=$result->fetch_assoc()) { ?>
						<tr data-user_id=<?= $r["user_id"];?>>
							<td data-type="username"><?= $r["username"];?></td>
							<td data-type="email"><?= $r["email"];?></td>
							<td class="" data-user="<?= $r["user_id"];?>"><?= $r["permissions"];?></td>
						<tr>
				<?php	}
				?>
				</tbody>
			</table>
		</div>
	</div>
<?php } ?>

</div>
<div class="modal" tabindex="-1" role="dialog" id="edituser">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= _("User"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<div class="col">
			<div class="form-group">
				<label for="username"><?= _('Username') ?></label>
				<input type="text" class="form-control userinput" id="username" value="<?= $r["username"];?>">
			</div>
			<div class="form-group">
				<label for="email"><?= _('E-mail') ?></label>
				<input type="email" class="form-control userinput" id="email" value="<?= $r["email"];?>">
			</div>
			<div class="form-group">
				<label for="password"><?= _('Password') ?></label>
				<div class="form-inline">
					<input type="password" class="form-control userinput password" id="password" value=""> <button class="btn btn-small btn-info" id="createpass"><?= _("Create a password"); ?></button>
				</div>
			</div>
			<input type="hidden" class="userinput" id="user_id" value="">
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _("Close"); ?></button>
        <button type="button" class="btn btn-primary" id="saveuser"><?= _("Save information"); ?></button>
      </div>
    </div>
  </div>
</div>
  <div class="d-none" >
	<div id="permissiontypes" class="form-group-inline">
	<?php
	$permtypes=array("coding","codingadmin","projectadmin");
	if($oca) $permtypes[]="opencodingadmin";
		foreach($permtypes as $unittype) {
			?>
			<input type="checkbox" class="form-check-control <?= $unittype; ?>" value="<?= $unittype; ?>" >
			<label for=".<?= _($unittype); ?>"><?= _($unittype); ?></label>
			<?php
			}
			?>
	</div>
  </div>


