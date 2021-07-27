<?php
	$relative="../";
	include_once($relative."/settings/conf.php");
	include_once($GLOBALS["backenddir"]."checklogin.php");
	
	$q="select * from users where user_id=".$_SESSION["user_id"];
	$result=$mysqli->query($q);
	$r=$result->fetch_assoc();

?>
<div id="usercontentdiv" class="container">
	<div class="row d-flex mt-4 justify-content-center">
		<div class="col">
			<h3><?= _("User Data"); ?></h3>
			<div class="form-group">
				<label for="username"><?= _('Username') ?></label>
				<input type="text" class="form-control userinput" id="username" value="<?= $r["username"];?>">
			</div>
			<div class="form-group">
				<label for="email"><?= _('E-mail') ?></label>
				<input type="email" class="form-control userinput" id="email" aria-describedby="emailHelp" value="<?= $r["email"];?>">
				<small id="emailHelp" class="form-text text-muted"><?= _('You are welcome to change your e-mail. Please remember to use the new address for login in the future.') ?></small>
			</div>
			<div class="form-group">
				<label for="password"><?= _('Password') ?></label>
				<input type="password" class="form-control userinput password" id="password" placeholder="<?= _('Unchanged'); ?>" value="">
			</div>
			<?php if($r["org_id"]) { ?>
			<?php } ?>
			<div class="form-group">
				<button class="btn btn-primary" id="finish" data-pagetype="frontpage"><?=_('Finish');?></button>
			</div>
		</div>
	</div>
</div>
