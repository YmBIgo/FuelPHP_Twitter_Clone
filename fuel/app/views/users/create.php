<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('users/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('users/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('users/new','New');?></li>
	<li class='<?php echo Arr::get($subnav, "edit" ); ?>'><?php echo Html::anchor('users/edit','Edit');?></li>
	<li class='<?php echo Arr::get($subnav, "session_new" ); ?>'><?php echo Html::anchor('users/session/new','Session New');?></li>
</ul>
<h3>User Create</h3>
<?php if ($cookie_user == false) { ?>
	<?php if ($user_id == false){ ?>
		<h5>You have entered incorrect information.</h5>
	<?php } else { ?>
		<h5>User Create Success with ID = <?php echo $user_id ?></h5>
		<hr/>
		<p>Your User is <a href="/users/show/<?php echo $user_id ?>"><?php echo $email ?></a> with password of <?php echo $password ?></p>
	<?php } ?>
<?php } else { ?>
	<h5>You have already logged in...</h5>
<?php } ?>
<br/>