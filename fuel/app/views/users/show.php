<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('users/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('users/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('users/new','New');?></li>
	<li class='<?php echo Arr::get($subnav, "edit" ); ?>'><?php echo Html::anchor('users/edit','Edit');?></li>
	<li class='<?php echo Arr::get($subnav, "session_new" ); ?>'><?php echo Html::anchor('users/session/new','Session New');?></li>
</ul>
<?php if ($user != null){ ?>
	<h3>
		<?php echo $user["name"]; ?>さん
		<br/>
		<small><?php echo $user["email"]; ?></small>
	</h3>
	<hr/>
	<p>紹介：<?php echo $user["description"]; ?></p>
<?php } else { ?>
	<h3>ユーザーが見つかりません。</h3>
	<hr/>
<?php } ?>
<br/>