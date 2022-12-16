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
			<div>
				<?php if ($tweet["is_retweet"] != 0) { ?>
					<p><small>[ Retweet Tweet ] <a href="/tweets/show/<?php echo $tweet["is_retweet"] ?>">original tweet</a></small></p>
				<?php } ?>
				<h5><a href="/tweets/show/<?php echo $tweet["id"] ?>"><?php echo $tweet["content"] ?></a></h5>
				<hr/>
			</div>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
	<h3>User not found...</h3>
	<hr/>
<?php } ?>
<br/>