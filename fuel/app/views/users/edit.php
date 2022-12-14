<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('users/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('users/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('users/new','New');?></li>
	<li class='<?php echo Arr::get($subnav, "edit" ); ?>'><?php echo Html::anchor('users/edit','Edit');?></li>
	<li class='<?php echo Arr::get($subnav, "session_new" ); ?>'><?php echo Html::anchor('users/session/new','Session New');?></li>
</ul>
<h3>Edit</h3>
<hr/>
<?php if($cookie_user == false) { ?>
	<h5>You should login first to edit your user data...</h5>
<?php } else { ?>
	<?php echo Form::open(array("action" => "users/update", "method" => "POST")) ?>
		<?php echo Form::label("Name", "name"); ?>
		<?php echo Form::input("name", $cookie_user["name"], array('class' => 'form-control', 'style' => 'width:500px')) ?>
		<?php echo Form::label("Description", "description"); ?>
		<?php echo Form::textarea("description", $cookie_user["description"], array('class' => 'form-control', 'style' => 'width:500px')) ?>
		<?php echo Form::hidden($token['token_key'], $token['token']); ?>
		<br/>
		<?php echo Form::submit("submit", "送信する", array("class" => "btn btn-primary")); ?>
	<?php echo Form::close(); ?>
<?php } ?>
<br/>