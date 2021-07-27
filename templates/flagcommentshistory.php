<?php
session_start();
checkperm();
include_once($shareddir."database.php");

$q="select comments,username,email from flags f left join users u on coder_id=user_id where coder_id=".$_SESSION[$_POST["flaghandling"]=="true"?"coder_id":"user_id"]." and response_id=".$_SESSION["response_id"];
$log=$q;
$result=$mysqli->query($q);
$r=$result->fetch_assoc();
$comments=json_decode($r["comments"],true);
if(!empty($comments)) {
	foreach($comments as $comment) {
	?>
		<div class="comment">
			<div class="text-muted"><?= $comment["username"];?>, <?= $comment["commenttime"];?></div>
			<div class=""><?= $comment["comment"];?></div>
		<?php 
	}
}

$res=array("log"=>$log,"warning"=>$warning,"flaggedby"=>$r["username"]." (".$r["email"].")");
