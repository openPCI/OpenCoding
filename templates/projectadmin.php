<?php
// $relative="../";
// include($relative."dirs.php");
$res=array("log"=>"test");
checkperm("projectadmin");

?>
<div class="list-group list-group-flush">
<?php
$actions=array(
"upload"=>_("Upload data"),
"tests"=>_("Administer Tests"),
"users"=>_("Users"),
"download"=>_("Download coded data")
);
$res["links"]=array_keys($actions);
foreach($actions as $action=>$name) {
?>
  <a href="#" class="list-group-item list-group-item-action" id="<?= $action?>"><?= $name?></a>
<?php } ?>
</div>
<?php
