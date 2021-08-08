<?php
// $relative="../";
// include($relative."dirs.php");
$res=array("log"=>"test");
checkperm("opencodingadmin");

?>
<h2><?= _("OpenCoding administration");?></h2>
<div class="list-group list-group-flush">
<?php
$actions=array(
"tasktypes"=>_("Task types"),
"users"=>_("Users"),
"projects"=>_("Projects"),
);
$res["links"]=array_keys($actions);
foreach($actions as $action=>$name) {
?>
  <a href="#" class="list-group-item list-group-item-action" id="<?= $action?>"><?= $name?></a>
<?php } ?>
</div>
<?php
