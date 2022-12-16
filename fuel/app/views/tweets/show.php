<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('tweets/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('tweets/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('tweets/new','New');?></li>
</ul>
<h3>Tweet Show</h3>
<hr/>
<?php if ($tweet != false) { ?>
	<?php if($tweet["is_retweet"] != 0) { ?>
		<p><small>[ Retweet Tweet ] <a href="/tweets/show/<?php echo $tweet["is_retweet"] ?>">original tweet</a></small></p>
	<?php } ?>
	<h5>UserName : <a href="/users/show/<?php echo $user["id"] ?>"><?php echo $user["name"] ?></a></h5>
	<small><?php echo $tweet["created_at"] ?></small>
	<p>Content : <?php echo $tweet["content"] ?></p>
	<hr/>
	<div class="row text-center">
		<?php if ($cookie_user != false) { ?>
			<div class="col-sm-4">
				Like
			</div>
			<div class="col-sm-4">
				<?php if ($is_retweet_exist == false) { ?>
					<?php echo Form::open(array("action" => "/tweets/retweet/".$tweet["id"], "method" => "POST")); ?>
					<?php echo Form::hidden($token["token_key"], $token["token"]); ?>
					<?php echo Form::submit("submit", "Retweet", array()); ?>
					<?php echo Form::close(); ?>
				<?php } else { ?>
					<?php echo Form::open(array("action" => "/tweets/unretweet/".$tweet["id"], "method" => "POST")); ?>
					<?php echo Form::hidden($token["token_key"], $token["token"]); ?>
					<?php echo Form::submit("submit", "Unretweet", array()); ?>
					<?php echo Form::close(); ?>
				<?php } ?>
			</div>
			<div class="col-sm-4">
			</div>
			<div class="col-sm-12 text-left twitter-card-reply-area">
				<textarea class="form-control"></textarea>
				<input type="submit" class="btn btn-primary" value="Reply"/>
			</div>
		<?php } else { ?>
			<div class="col-sm-3">
			</div>
			<div class="col-sm-6">
				<p>You can login and use Like or Retweet or Reply.</p>
				<p>Click <a href="/users/new">here</a> to signup.</p>
				<p>Click <a href="/users/session/new">here</a> to login.</p>
			</div>
			<div class="col-sm-3">
			</div>
		<?php } ?>
	</div>
	<hr/>
<?php } else { ?>
	<h5>Tweet Not Found...</h5>
<?php } ?>
<br/>