<?php
if(!$templatesdir) { //Then we have a call from js
	session_start();
	$relative="../";
	include_once($relative."dirs.php");
// 	print_r($templatesdir.$_POST["template"].".php");
	echo json_encode(get_template($_POST["template"],$_POST));
}
function get_template($filename,$args=array()) {
	global $log,$templatesdir,$shareddir,$mysqli,$relative,$backenddir;;
	extract($args);
	$istemplate=true;
// 	print_r($templatesdir.$filename.".php");
    if (is_file($templatesdir.$filename.".php")) {
        ob_start();
        include ($templatesdir.$filename.".php");
        if(!is_array($res)) $res=array();
        return array_merge(array("template"=>ob_get_clean(),"function"=>$filename),$res);
    } else echo "";
    return false;
}
