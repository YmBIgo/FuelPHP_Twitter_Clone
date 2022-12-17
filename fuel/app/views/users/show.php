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
		<?php if ($cookie_user != false && $user["id"] == $cookie_user["id"]) { ?>
			<a href="/users/edit" class="btn btn-success">ユーザー情報を編集する</a>
		<?php } ?>
		<br/>
		<small><?php echo $user["email"]; ?></small>
	</h3>
	<br/>
	<p>紹介：<?php echo $user["description"]; ?></p>
	<hr/>
	<h4>ツイート一覧</h4>
	<hr/>
	<?php if (count($tweets) == 0) { ?>
		<h5>No Tweet Found...</h5>
	<?php } else { ?>
		<?php foreach ($tweets as $tweet){ ?>
			<div class="tweet_card">
				<?php if ($tweet[0]["is_retweet"] != 0) { ?>
					<p><small>[ Retweet Tweet ] <a href="/tweets/show/<?php echo $tweet[0]["is_retweet"] ?>">original tweet</a></small></p>
				<?php } ?>
				<h5><a href="/tweets/show/<?php echo $tweet[0]["id"] ?>"><?php echo $tweet[0]["content"] ?></a></h5>
				<hr/>
				<div class="row text-center">
					<?php if ($cookie_user != false) { ?>
						<div class="col-sm-4">
							Like
						</div>
						<div class="col-sm-4">
							<?php if ( $tweet[1] == false ) { ?>
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
			</div>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
	<h3>User not found...</h3>
	<hr/>
<?php } ?>
<br/>