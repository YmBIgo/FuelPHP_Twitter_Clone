<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('tweets/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('tweets/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('tweets/new','New');?></li>
	<li class='<?php echo Arr::get($subnav, "create" ); ?>'><?php echo Html::anchor('tweets/create','Create');?></li>

</ul>
<h3>Index</h3>
<hr/>
<?php foreach ($tweets as $tweet) { ?>
	<h5><a href="/users/show/<?php echo $tweet[1]["id"] ?>"><?php echo $tweet[1]["name"] ?>さん</a>のツイート</h5>
	<p><a href="/tweets/show/<?php echo $tweet[0]["id"] ?>"><?php echo $tweet[0]["content"] ?></a></p>
	<hr/>
<?php } ?>
<br/>