<?php 
foreach($variables as $variable=>$default) {
	?>
	<div class="form-group">
		<label for="<?=$variable;?>"><?=$variable;?></label>
		<input type="text" class="form-control tasktype_variable" id="<?=$variable;?>" data-variablename="<?=$variable;?>" value="<?=($tasktype_variables[$variable]?$tasktype_variables[$variable]:_($default));?>">
	</div>
<?php }
