<?php
session_start();
include_once($shareddir."database.php");
$q='SELECT project_id,project_name from projects where project_id IN ('.implode(",",$_SESSION["projects"]).') order by project_name';

$result=$mysqli->query($q);

?>
<div class="container">
<h3><?= _("Choose project");?></h3>
<?php
while($result and $r=$result->fetch_assoc()) {
?>	<p><a href="#" class="selectproject" data-project_id="<?= $r["project_id"];?>"><?= $r["project_name"];?></a></p>
<?php } ?>
</div>
