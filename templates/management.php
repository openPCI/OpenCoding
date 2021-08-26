<?php
// $relative="../";
// include($relative."dirs.php");
$res=array("log"=>"test");
checkperm("codingadmin");

?>
<h2><?= _("Management");?></h2>
<div class="list-group list-group-flush">
<?php
$actions=array(
// "upload"=>_("Upload data"),
"codingmanagement"=>_("Coding management"),
"codermanagement"=>_("Coder management")
);
$res["links"]=array_keys($actions);
foreach($actions as $action=>$name) {
?>
  <a href="#" class="list-group-item list-group-item-action" id="<?= $action?>"><?= $name?></a>
<?php } ?>
</div>
<?php
