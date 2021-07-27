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


$locale="da_DK"; //en_US//$_SESSION["locale"];
putenv("LANGUAGE=".$locale.".UTF-8");
setlocale(LC_ALL,$locale.".UTF-8",$locale);
$domain="messages";
$pathToDomain = __DIR__ . "/locale";
$realpath=bindtextdomain($domain, $pathToDomain);
bind_textdomain_codeset($domain,"UTF-8");

$results = textdomain($domain);
$lang=explode("_",$locale)[0];
$dateformat=array("en"=>"Y/m/d","da"=>"d-m-Y")[$lang];
