<div class="container">
	<div class="jumbotron jumbotron-fluid">
		<div class="row d-flex mt-4 justify-content-center">
			<div class="col  " id="loginform">
				<div class="d-flex justify-content-center">
					<div class="card card-block homepage ">
						<form class="form-signin" method="POST" >
						<label for="inputUser" class="sr-only"><?= _('Username');?></label>
						<input type="text" id="inputUser" class="form-control menuform" placeholder="<?=_('Username');?>" required>
						<label for="inputPassword" class="sr-only"><?= _('Password');?></label>
						<input type="password" id="inputPassword" class="form-control menuform mt-2" placeholder="<?=_('Password');?>" required>
						<div class="checkbox">
						<label>
							<input type="checkbox" id="rememberMe"> <?= _('Remember Me');?>
						</label>
						</div>
						<button class="btn btn-lg btn-primary loginsignup-button login login-button" id="login" type="button"><?= _('Login');?></button>
						</form>
					
					</div>
				
				</div>
			</div>
		</div>
	</div>
  </div>
</div>
