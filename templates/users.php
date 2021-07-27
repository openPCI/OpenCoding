<?php
	$relative="../";
	include_once($functionsdir."database.php");
$oca=(strpos($_SERVER["HTTP_REFERER"],"p=opencodingadmin"));
if($oca) checkperm("opencodingadmin");
else checkperm("projectadmin");
	global $res;
// 	$res["org_id"]=$_POST["org_id"];
	$q='select u.user_id,u.username,u.email,group_concat(p.unittype separator ", ") as permissions from users u left join user_permissions p on u.user_id=p.user_id where '.($oca?1:'unit_id='.$_SESSION["project_id"]).' group by 1 order by username ';//org_id=".($_POST["org_id"]?$_POST["org_id"]:$_SESSION["user_id"]);
// 	echo $q;
	$result=$mysqli->query($q);
	
	
?>
<div class="container-fluid">

	<div class="row">
		<div class="col">
			<button class="btn btn-primary" data-toggle="modal" data-target="#newuser"><?= _("New user"); ?></button>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<h3><?= _("OpenCoding Users"); ?></h3>
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
					while($r=$result->fetch_assoc()) { ?>
						<tr data-user_id=<?= $r["user_id"];?>>
							<td><?= $r["username"];?></td>
							<td><?= $r["email"];?></td>
							<td class="changePermissions" data-user="<?= $r["user_id"];?>"><?= $r["permissions"];?></td>
							<td><?php if($_SESSION["user_id"]==1) { ?>
								<button type="button" class="btn btn-danger remove" data-user="<?= $r["user_id"];?>"><?= _('Remove');?></button>
								<button type="button" class="btn btn-primary newpass" data-user="<?= $r["user_id"];?>"><?= _('New password');?></button>
							<?php } ?></td>
						<tr>
				<?php	}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="newuser">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= _("New user"); ?></h5>
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
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _("Close"); ?></button>
        <button type="button" class="btn btn-primary" id="savenewuser"><?= _("Create user"); ?></button>
      </div>
    </div>
  </div>
</div>
  <div class="d-none" >
	<div id="permissiontypes" class="form-group-inline">
	<?php
	$permtypes=array("codingadmin","projectadmin");
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


