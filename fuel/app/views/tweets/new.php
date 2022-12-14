<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('tweets/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('tweets/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('tweets/new','New');?></li>
</ul>
<h3>Tweet New</h3>
<hr/>
<?php if ($cookie_user != false) { ?>
	<div>
		<p>Your are currently log in as <a href="/users/show/<?php echo $cookie_user["id"] ?>"><?php echo $cookie_user["name"] ?></a></p>
		<?php echo Form::open(array("action" => "/tweets/create", "method" => "POST")); ?>
			<?php echo Form::label("Content", "content"); ?>
			<?php echo Form::textarea("content", "", array('class' => 'form-control', 'style' => 'width:500px')) ?>
			<?php echo Form::hidden($token['token_key'], $token['token']); ?>
			<br/>
			<?php echo Form::submit("submit", "送信する", array("class" => "btn btn-primary")) ?>
		<?php echo Form::close(); ?>
	</div>
<?php } else { ?>
	<h5>You should login first to enter your tweet...</h5>
	<br/>
	<p>Click <a href="/users/new">here</a> to signup.</p>
	<p>Click <a href="/users/session/new">here</a> to login.</p>
<?php } ?>
<br/>