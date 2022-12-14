<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "show" ); ?>'><?php echo Html::anchor('tweets/show/1','Show');?></li>
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('tweets/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('tweets/new','New');?></li>
	<li class='<?php echo Arr::get($subnav, "create" ); ?>'><?php echo Html::anchor('tweets/create','Create');?></li>
</ul>
<h3>Show</h3>
<hr/>
<?php if ($tweet != false) { ?>
	<h5>UserName : <a href="/users/show/<?php echo $user["id"] ?>"><?php echo $user["name"] ?></a></h5>
	<small><?php echo $tweet["created_at"] ?></small>
	<p>Content : <?php echo $tweet["content"] ?></p>
<?php } else { ?>
	<h5>Tweet Not Found...</h5>
<?php } ?>
<br/>