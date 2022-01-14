

<div class="alert alert-warning OpenCodingWarning" style="position: fixed;width:30%;z-index:1051;top:50%;left:50%;margin-left:-15%;text-align:center;display:none; " role="alert">
	<span class="roundAlertExtra">
	</span>
</div>
<div class="alert alert-success OpenCodingMessage" style="position: fixed;width:30%;z-index:1051;top:50%;left:50%;margin-left:-15%;text-align:center;display:none; " role="alert">
</div>
<div><br><br></div>
<footer class="footer d-print-none py-1 mt-auto site-footer fixed-bottom">
	<div class="collapse row" id="collapseLang">
		<div class="col justify-content-right">
			<?php
			$tag1='<img class="newLang flag-img float-right" src="locale/flags/';
			$tag2='.png">';
			$langs=array("da_DK","en_US");
			echo $tag1.implode($tag2.$tag1,$langs).$tag2;
			?>
		</div>
	</div>
	<div class="row ">
		<div class="col d-flex ml-2 justify-content-left">
	        &copy; OpenCoding 2021
<!--		<div class="col d-flex justify-content-center">
			<a class="footer-link text-center mr-3 mr-lg-4 d-none d-md-inline" href="?cookies=1"><?= _('Cookies');?></a>
		</div>-->
		<div class="col d-flex justify-content-end">
			<div class="row collapse <?= ($_SESSION["user_id"]?"show":"")?>" id="logoutform">
				<div class="col p-0">
					<button class="btn btn-link pb-0 text-muted " title="<?= _('My user');?>" id="userinfo" ><i class="far fa-user"></i></button>
				</div>
				<div class="col p-0">
					<form method="POST" action="?<?=$_SERVER['QUERY_STRING'];?>" class="form-inline">
						<button class="btn btn-link pb-0 text-muted logout" id="logout" title="<?= _('Log out');?>"><i class="fas fa-sign-out-alt"></i></button>
						<input type="hidden" name="logout" value="true">
					</form>
				</div>
			</div>
			<div class="collapse p-0 <?= (!$_SESSION["user_id"]?"show":"")?>" id="loginform">
				<button class="btn btn-link pb-0 text-muted ml-auto" id="showloginform" title="<?= _('Log in');?>"><i class="fas fa-sign-in-alt"></i></button>
			</div>
		</div>
		<div  class="icon-link  p-0" id="chooselang">
			<button class="btn btn-link pb-0 ml-auto"><img data-toggle="collapse" href="#collapseLang" role="button" aria-expanded="false" aria-controls="collapseLang" class="flag-img" src="locale/flags/<?= $locale; ?>.png"></button>
		</div>

	</div>
	<div class="row ">
		<div class="col d-flex justify-content-center">
			<a class="footer-link text-center mr-3 mr-lg-4 d-md-none" href="?cookies=1"><?= _('Cookies');?></a>
			<a class="footer-link text-center mr-3 mr-lg-4 d-md-none" href="?contact=1"><?= _('Contact');?></a>
		</div>
	</div>

</footer>

<div class="modal" id="pleaseWait" data-backdrop="static" data-keyboard="false">
</div>
<!-- <script src="./js/sletmig.js" ></script> -->
<script src="./js/jquery.min.js" ></script>
<script src="./js/popper.min.js"></script>
<script src="./js/papaparse.min.js"></script>

<script src="./js/jquery-ui.min.js"></script>
<script src="./js/jquery.ui.touch-punch.min.js"></script>

<script src="./js/DragDropTouch.js"></script>
<script src="./js/html5sortable.min.js"></script>

<script src="./js/translate.js"></script>
<script src="./js/tether.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<!-- <script src="./js/bootstrap-timepicker.min.js"></script> -->
<script src="./js/luxon.min.js"></script>

<script src="./js/flatpickr.min.js"></script>
<!-- <script src="./js/moment.min.js"></script> -->
<!-- <script src="./js/daterangepicker.js"></script> -->

<script src="./js/md5.min.js"></script>

<script src="js/opencoding.js?<?=$v;?>"></script>
<script src="js/bootstrap-toggle.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.1.0/highlight.min.js"></script>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
   
  </body>
</html>
