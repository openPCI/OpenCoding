<!DOCTYPE html>
<html lang="<?= $locale; ?>">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="OpenCoding">
    <meta name="author" content="Jeppe Bundsgaard">
    <link rel="icon" href="favicon.png">

    <title>OpenCoding - <?=_('Human coding of test responses');?> ...</title>
    
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.1.0/styles/default.min.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.1.0/styles/github.min.css" rel="stylesheet">
	<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

	<link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/jquery-ui.min.css">
    <link rel="stylesheet" href="./css/bootstrap-toggle.min.css">
    <link rel="stylesheet" href="./css/bootstrap-timepicker.min.css">
    <link rel="stylesheet" href="./css/daterangepicker.css">
    <!-- Add icon library -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
<!-- 	 <script defer src="./js/fontawesome-all.min.js"></script> -->
    <!-- styles for OpenCoding -->
    <link rel="stylesheet" href="css/opencoding.css" rel="stylesheet">
  </head>

  <body>
<!-- Header --><!-- add? fixed-top -->
<nav class="navbar navbar-expand-sm navbar-dark justify-content-between sticky-top">
	<div class="col-2 col-md-4">
<!-- 		<a class="align-left" href="/" aria-label="OpenCoding - <?=_('Human coding of test responses');?>"> -->
			<span class="opencoding-logo"><i class="fas fa-user-edit fa-3x"></i></span>
<!-- 			<img class="opencoding-logo" src="img/<?= ($settings->logo?$settings->logo:'logo.png');?>"> -->
<!-- 		</a> -->
	</div>
	<div class="col-2 col-md-4 opencoding text-center align-left">
		OpenCoding<span class="d-none d-md-block small">&#8211; <?=_('Human coding of test responses');?></span><span class="d-block" id="OpenCodingHeader"></span>
	</div>
	<div class="col-3 d-md-none">
		<button class="navbar-toggler float-right" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
	</div>
	<div class="collapse navbar-collapse col-6 col-md-4 justify-content-end float-right" id="navbarToggler">
			<ul class="navbar-nav">
				<li class="nav-item active d-flex justify-content-end">
					<a class="nav-link menulink" href="./?p=mytasks"><?= _('Code');?></a>
				</li>
				<li class="nav-item active d-flex justify-content-end">
					<a class="nav-link menulink" href="./?p=training"><?= _('Training');?></a>
				</li>
<!--				<li class="nav-item active d-flex justify-content-end">
					<a class="nav-link menulink" href="./?p=manage"><?= _('Manage');?></a>
				</li>-->
				<?php session_start(); if($_SESSION["perms"]["codingadmin"][$_SESSION["project_id"]]) {?>
				<li class="nav-item active d-flex justify-content-end">
					<a class="nav-link menulink" href="./?p=codingmanagement"><?= _('Coding Management');?></a>
				</li>
				<?php } 
				if($_SESSION["perms"]["projectadmin"][$_SESSION["project_id"]]) { ?>
				<li class="nav-item active d-flex justify-content-end">
					<a class="nav-link menulink" href="./?p=projectadmin"><?= _('Project Admin');?></a>
				</li>
				<?php }
				if($_SESSION["perms"]["opencodingadmin"][0]) { ?>
				<li class="nav-item active d-flex justify-content-end">
					<a class="nav-link menulink" href="./?p=opencodingadmin"><?= _('OpenCoding Admin');?></a>
				</li>
				<?php } ?>
			</ul>
		
	</div>
</nav>
