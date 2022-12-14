<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('users/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('users/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('users/new','New');?></li>
	<li class='<?php echo Arr::get($subnav, "edit" ); ?>'><?php echo Html::anchor('users/edit','Edit');?></li>
	<li class='<?php echo Arr::get($subnav, "session_new" ); ?>'><?php echo Html::anchor('users/session/new','Session New');?></li>
</ul>
<h3>Session New</h3>
<?php if ($cookie_user == false) { ?>
	<?php echo Form::open(array('action' => 'users/session/create', 'method' => 'POST')) ?>
		<?php echo Form::label("Email", "email"); ?>
		<?php echo Form::input("email", '', array('class' => 'form-control', 'style' => 'width:500px')) ?>
		<?php echo Form::label("Password", "password"); ?>
		<?php echo Form::password("password", '', array('class' => 'form-control', 'style' => 'width:500px')) ?>
		<?php echo Form::hidden($token['token_key'], $token['token']); ?>
		<br/>
		<?php echo Form::submit("submit", "送信する", array('class' => 'btn btn-primary')); ?>
	<?php echo Form::close(); ?>
<?php } else { ?>
	<h5>You have already logged in...</h5>
<?php } ?>
<br/>