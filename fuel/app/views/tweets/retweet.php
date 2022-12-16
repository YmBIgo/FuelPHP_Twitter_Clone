<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('tweets/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('tweets/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('tweets/new','New');?></li>
</ul>
<h3>Tweet Retweet</h3>
<hr/>
<?php if ($cookie_user != false) { ?>
	<?php if ($tweet_id == false) { ?>
		<h5>Fail to create retweet.</h5>
	<?php } else { ?>
		<h5>Successfully create your retweet!</h5>
		<p>Your retweet is <a href="/tweets/show/<?php echo $tweet_id ?>">here</a></p>
	<?php } ?>
<?php } else { ?>
	<h5>You should login first to retweet ...</h5>
	<br/>
	<p>Click <a href="/users/new">here</a> to signup.</p>
	<p>Click <a href="/users/session/new">here</a> to login.</p>
<?php } ?>
<br/>