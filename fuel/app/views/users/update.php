<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('users/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('users/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('users/new','New');?></li>
	<li class='<?php echo Arr::get($subnav, "edit" ); ?>'><?php echo Html::anchor('users/edit','Edit');?></li>
	<li class='<?php echo Arr::get($subnav, "session_new" ); ?>'><?php echo Html::anchor('users/session/new','Session New');?></li>
</ul>
<h3>Update</h3>
<hr/>
<?php if ($user_id == false){ ?>
	<h5>Please enter correct inputs...</h5>
<?php } else { ?>
	<h5>User is successfully updated!</h5>
	<p>
		You can see your profile at <a href="/users/show/<?php echo $user_id; ?>">Your Page</a> or <a href="/users/edit">Edit your profile</a>.
	</p>
<?php } ?>
<br/>