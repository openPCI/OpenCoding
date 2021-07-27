<?php
session_start();
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
include_once($shareddir."templates.php");
$log.=print_r($_POST,true);
if($_POST["inputUser"]) {
	$password=crypt($_POST["inputPassword"],base64_encode($_POST["inputPassword"]));
	$useremail=$mysqli->real_escape_string($_POST["inputUser"]);
	$q='select * from users where (username LIKE "'.$useremail.'" or email  LIKE "'.$useremail.'") and password LIKE "'.$password.'"';
	$log.=$q;
	$res=$mysqli->query($q);
	
	if($res->num_rows) {
		$r=$res->fetch_assoc();
		$_SESSION["user_id"]=$r["user_id"];
		$q='select DISTINCT unit_id, unittype from  user_permissions p where p.user_id='.$r["user_id"];
// 		echo $q;
		$res=$mysqli->query($q);
		
		
		$perms=array();
		while($r2=$res->fetch_assoc()) {
			$perms[$r2["unittype"]][$r2["unit_id"]]=true;
		}
		$_SESSION["perms"]=$perms;
		$welcome=_("Welcome back!");
		if($_POST["rememberMe"]) $_SESSION["rememberMe"]=true;
		$log.="p".$_POST["p"];
		$template=get_template($_POST["p"])["template"];
		
		#HACK!!!
		if(!$_SESSION["project_id"]) $_SESSION["project_id"]=1;
	}
 	else $warning=_("Username or password was wrong");
} else $warning=_("No username");
echo json_encode(array("log"=>$log,"warning"=>$warning,"user_id"=>$_SESSION["user_id"],"welcome"=>$welcome,"p"=>$_POST["p"],"template"=>$template));
