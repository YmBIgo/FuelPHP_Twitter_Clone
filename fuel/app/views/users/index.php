<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('users/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('users/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('users/new','New');?></li>
	<li class='<?php echo Arr::get($subnav, "edit" ); ?>'><?php echo Html::anchor('users/edit','Edit');?></li>
	<li class='<?php echo Arr::get($subnav, "session_new" ); ?>'><?php echo Html::anchor('users/session/new','Session New');?></li>
</ul>
<h3>Index</h3>
<hr/>
<?php foreach ($users as $user) { ?>
	<h5>
		<a href="/users/show/<?php echo $user["id"] ?>"><?php echo $user["name"] ?></a>
		<br/>
		<small><?php echo $user["description"] ?></small>
	</h5>
	<hr/>
<?php } ?>
<br/>