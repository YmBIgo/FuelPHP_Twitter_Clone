<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('users/show','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('users/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('users/new','New');?></li>
	<li class='<?php echo Arr::get($subnav, "edit" ); ?>'><?php echo Html::anchor('users/edit','Edit');?></li>
	<li class='<?php echo Arr::get($subnav, "session_new" ); ?>'><?php echo Html::anchor('users/session/new','Session New');?></li>
</ul>
<h3>Session New</h3>
<?php if ($cookie_value == null) { ?>
<?php } else { ?>
	<h5>You have already logged in...</h5>
<?php } ?>
<br/>