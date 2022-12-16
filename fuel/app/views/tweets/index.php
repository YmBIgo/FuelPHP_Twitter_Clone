<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('tweets/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('tweets/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('tweets/new','New');?></li>
</ul>
<h3>Tweet Index</h3>
<hr/>
<?php if ($cookie_user == false) { ?>
	<div>
		<h5>You should login first to retweet ...</h5>
		<br/>
		<p>Click <a href="/users/new">here</a> to signup.</p>
		<p>Click <a href="/users/session/new">here</a> to login.</p>
	</div>
<?php } else { ?>
	<?php foreach ($tweets as $tweet) { ?>
		<div class="tweet_card">
			<?php if ($tweet[0]["is_retweet"] != 0) { ?>
				<p><small>[ Retweet Tweet ] <a href="/tweets/show/<?php echo $tweet[0]["is_retweet"] ?>">original tweet</a></small></p>
			<?php } ?>
			<h5><a href="/users/show/<?php echo $tweet[1]["id"] ?>"><?php echo $tweet[1]["name"] ?></a>'s Tweet</h5>
			<p><a href="/tweets/show/<?php echo $tweet[0]["id"] ?>"><?php echo $tweet[0]["content"] ?></a></p>
			<hr/>
			<div class="row text-center">
				<div class="col-sm-4">
					Like
				</div>
				<div class="col-sm-4">
					<?php if ($tweet[2] == false) { ?>
						<?php echo Form::open(array("action" => "/tweets/retweet/".$tweet[0]["id"], "method" => "POST")); ?>
							<?php echo Form::hidden($token["token_key"], $token["token"]); ?>
							<?php echo Form::submit("submit", "Retweet", array()); ?>
						<?php echo Form::close(); ?>
					<?php } else { ?>
						<?php echo Form::open(array("action" => "/tweets/unretweet/".$tweet[0]["id"], "method" => "POST")); ?>
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
			</div>
		</div>
	<?php } ?>
<?php } ?>
<br/>