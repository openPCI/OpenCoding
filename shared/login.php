<?php
session_start();
$relative="../";
include_once($relative."dirs.php");
include_once($shareddir."database.php");
include_once($shareddir."templates.php");

if($_POST["inputUser"]) {
	$useremail=$mysqli->real_escape_string($_POST["inputUser"]);
	$q='select * from users where (username LIKE "'.$useremail.'" or email  LIKE "'.$useremail.'")';
	$log.=$q;
	$res=$mysqli->query($q);
	$accept=false;	
	if($res->num_rows) {
		while(!$accept and $r=$res->fetch_assoc()) {
			$accept=password_verify($_POST["inputPassword"],$r["password"]);
		}
	}
	if($accept) {
		$_SESSION["user_id"]=$r["user_id"];
		$q='select DISTINCT unit_id, unittype from  user_permissions p where p.user_id='.$r["user_id"];
// 		echo $q;
		$res=$mysqli->query($q);
		
		
		$projects=$perms=array();
		while($r2=$res->fetch_assoc()) {
			$perms[$r2["unittype"]][$r2["unit_id"]]=true;
			$projects[]=$r2["unit_id"];
		}
		$log.=print_r($projects,true);
		$projects=array_diff(array_unique($projects),array(0));
		$log.=print_r($projects,true);
		if(!$perms) $warning=_("You are not authorized to work on any projects. Please ask your manager to assign a task to you.");
		else {
			$_SESSION["perms"]=$perms;
			$welcome=_("Welcome back!");
			if($_POST["rememberMe"]) $_SESSION["rememberMe"]=true;
			#$log.="p".$_POST["p"];
			#$template=get_template($_POST["p"])["template"];
			if(count($projects)>1) {
				$chooseproject=true;
				$_SESSION["projects"]=$projects;
			}
			else $_SESSION["project_id"]=array_pop($projects);
		}
	}
 	else $warning=_("Username or password was wrong");
} else $warning=_("No username");
echo json_encode(array("log"=>$log,"warning"=>$warning,"user_id"=>$_SESSION["user_id"],"welcome"=>$welcome,"p"=>$_POST["p"],"chooseproject"=>$chooseproject));
