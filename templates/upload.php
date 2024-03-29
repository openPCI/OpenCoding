<?php
# Maximum number of cases to import
// $newmax_input_vars=100000;
// ini_set("max_input_vars",$newmax_input_vars);
// $max_input_vars=ini_get("max_input_vars");
// if($newmax_input_vars!=$max_input_vars) echo ('<div class="alert alert-danger" role="alert">'.sprintf(_("Your server limits the number of cases to be uploaded to %d (max_input_vars). You can change that in the php-ini-file on the server (/etc/php/7.X/apache2/php.ini). Or keep the number of cases down in each upload."),$max_input_vars).'</div>');

checkperm("projectadmin");
?>
<form>
	<div class="form-group">
		<label for="test_name"><?= sprintf(_("Upload to: %s"),$_POST["test_name"]);?></label>
		<input type="hidden" id="test_id" value="<?= $_POST["test_id"];?>">
	</div>
  
	<div class="form-group">
    <label for="datafile"><?= _("Choose a CSV-file");?></label>
    <input type="file" class="custom-file-input" id="datafile" accept="text/csv">
  </div>
</form>
<div class="collapse" id="datafields">
	<h3 class=""><?= _("Matrix format");?></h3>
	<div class="form-check form-check-inline">
		<input class="form-check-input" type="radio" name="matrixformat" id="matrixformatwide" value="wide" checked>
		<label class="form-check-label" for="matrixformatwide"><?= _("Wide format");?></label>
	</div>
	<div class="form-check form-check-inline">
		<input class="form-check-input" type="radio" name="matrixformat" id="matrixformatlong" value="long">
		<label class="form-check-label" for="matrixformatlong"><?= _("Long format");?></label>
	</div>
	<div class="form-inline">
		<div class="collapse" id="longsettings">
		<label class="form-label" for="itemnamecol"><?= _("Item name column");?></label>
		<select id="itemnamecol" class="form-control longoptions"></select>
		<label class="form-label" for="responsecol"><?= _("Response column");?></label>
		<select id="responsecol" class="form-control longoptions"></select>
		</div>
	</div>

	<p class="lead">
		<?= _("Click on the username-column, then the testing time-column, and finally on the columns containing responses"); ?>
	</p>
	<div class="" id="cols">
	</div>
	<hr>
	<ul class="list-group " id="usedcols">
	<li class="list-group-item list-group-item-primary"><div><?= _("Test taker username");?></div><div id="username"></div></li>
	<li class="list-group-item list-group-item-secondary"><div><?= _("Test time");?></div><div id="testtime"></div></li>
	<li class="list-group-item"><div><?= _("Tasks");?></div><div id="tasks"></div></li>
	</ul>
	
	<hr>
	<p class="lead">
		<?= _("Filter the responses"); ?>
	</p>
	<div class="form-group">
		<div class="row">
			<div class="col">
			<label for="testtakerfilter"><?= _("Test taker username (regular expression accepted)");?></label>
			<input type="text" class="form-control"  id="testtakerfilter">
			</div>
			<div class="col">
			<label for="beforefilter"><?= _("Only include responses with test time before");?></label>
			<input type="text" class="form-control datetime" id="beforefilter">
			</div>
			<div class="col">
			<label for="afterfilter"><?= _("Only include responses with test time after");?></label>
			<input type="text" class="form-control datetime" id="afterfilter">
			</div>
		</div>
	</div>
	
	<hr>
	<button class="btn btn-primary" id="doUpload"><?= _("Upload");?>
</div>
