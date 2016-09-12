	<div class='content-container'>
<?php
	echo "<h3 class='welcome-message'>Welcome <em>" . ($Account->team_name != "" ? $Account->team_name : $Account->email) . "</em>!</h3>";
?>

		<ul class='homepage-list'>
			<li class='homepage-list-item'>
				<a href='<?php echo $sitemap[5]['page_url'];?>'><span class='fa fa-pencil-square'></span> Make Picks <span class='fa fa-chevron-circle-right'></a>
			</li>
			<li class='homepage-list-item'>
				<a href='<?php echo $sitemap[8]['page_url'];?>'><span class='fa fa-list'></span> View Leaderboard <span class='fa fa-chevron-circle-right'></a>
			</li>
			<li  class='homepage-list-item'>
				<a href='<?php echo $sitemap[8]['page_url'];?>'><span class='fa fa-book'></span>Rules<span class='fa fa-chevron-circle-right'></a>
			</li>
			<li  class='homepage-list-item'>
				<a href='<?php echo $sitemap[8]['page_url'];?>'><span class='fa fa-male'></span>Edit Profile<span class='fa fa-chevron-circle-right'></a>
			</li>
		</ul>
	</div>