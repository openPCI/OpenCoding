<?php
// $relative="../";
// include($relative."dirs.php");
$res=array("log"=>"test");
checkperm("opencodingadmin");

?>
<div class="list-group list-group-flush">
<?php
$actions=array(
"tasktypes"=>"Task types",
"users"=>"Users",
"projects"=>"Projects",
);
$res["links"]=array_keys($actions);
foreach($actions as $action=>$name) {
?>
  <a href="#" class="list-group-item list-group-item-action" id="<?= $action?>"><?= $name?></a>
<?php } ?>
</div>
<?php
