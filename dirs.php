<?php
ini_set("display_errors","true");
session_start();
function checkperm($type="") {
	global $user_id;
	if(!$_SESSION["user_id"]) {echo json_encode(array("relogin"=>true)); exit;}
	else $user_id=$_SESSION["user_id"];
	if($type and !$_SESSION["perms"][$type][$_SESSION["project_id"]] and !$_SESSION["perms"][$type][0]) { echo json_encode(array("template"=>_("You don't have access here"))); exit;}
}
if(!$relative) $relative="./";
$templatesdir=$relative."templates/";
$frontenddir=$relative."frontend/";
$shareddir=$relative."shared/";
$backenddir=$relative."backend/";
$jsdir=$relative."js/";
$secretdir=$relative."secrets/";#/var/www/opencodingsecrets/";


if(!$_SESSION["locale"]) {
	$accept=explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$keyaccept=array();
	foreach($accept as $la) {
		$tmp=explode(";q=",$la);
		$keyaccept[$tmp[0]]=($tmp[1]?$tmp[1]:1);
	}

	$langs=array("en"=>"en_US","us"=>"en_US","au"=>"en_US","sg"=>"en_US","hk"=>"en_US","nz"=>"en_US","da"=>"da_DK","en_US"=>"en_US","da_DK"=>"da_DK");
	$preferred=key(array_intersect_key($keyaccept,$langs));
	if(!$preferred) $preferred="en";
	$_SESSION["locale"]=$langs[$preferred];
}
$locale=$_SESSION["locale"];
putenv("LANGUAGE=".$locale.".UTF-8");
setlocale(LC_ALL,$locale.".UTF-8",$locale);
$domain="messages";
$pathToDomain = __DIR__ . "/locale";
$realpath=bindtextdomain($domain, $pathToDomain);
bind_textdomain_codeset($domain,"UTF-8");

$results = textdomain($domain);
$lang=explode("_",$locale)[0];
$dateformat=array("en"=>"Y/m/d","da"=>"d-m-Y")[$lang];
