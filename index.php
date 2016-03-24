<?php
// Set Time zone
date_default_timezone_set("UTC");

// Load Config
require 'app/config.php';

// Composer
require __DIR__ . '/vendor/autoload.php';

// RedBeanPHP alias fix
class R extends RedBeanPHP\Facade {}
// RedBeanPHP setup
R::setup('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
R::freeze(DB_FREEZE);

$q = 
	'SELECT
		*
	FROM 
		`videos` 
	WHERE 
		`user` = ? 
		AND `date` >= "'.START_DATE.'"
	ORDER BY 
		`date` ASC
	';


$videos = R::getAll($q,[YOUTUBE_USER]);


$json = [];

// For View
foreach ($videos as $video) {
	$date = new DateTime($video['date']);
	$start = $date->format('Ymd');
	if(!$json[$start]){
		$json[$start]=[];
	}
	$json[$start][] = [
		't' 	=> $video['title'],
		'i' 	=> $video['ytid']
	];
}

$date_start = substr($videos[0]['date'], 0, 10);
$date_stop  = substr($videos[count($videos)-1]['date'], 0, 10);

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"> -->
		<meta name="viewport" content="initial-scale=0.36, width=device-width, height=device-height, minimum-scale=0.36, maximum-scale=2, user-scalable=yes" />
		<meta name="description" content="Casey Neistat Vlog Archive">
		<meta name="author" content="Rodrigo Polo">

		<title>Casey Neistat Vlog Archive</title>

		<!-- Bootstrap -->
		<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
		<link href="style.css" rel="stylesheet">
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<link href="//maxcdn.bootstrapcdn.com/js/ie10-viewport-bug-workaround.js" rel="stylesheet">
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<base target="_blank">
		<div class="main">


			<div class="fullcal">
				<h1>Casey Neistat Vlog Archive</h1>
				<p>On march 25, 2015, Casey Neistat started a YouTube daily video log, amazingly he delivered, not only a daily video but sometimes two videos on the same day for almost a year. I was stunned when I discover Casey vlog but I didn’t find a way to see all his videos in a chronological order, so I decided to code this page using YouTube’s API, if you want to know more about this or me, go to the page bottom.</p>

				<div class="cal"></div>


				<p id="about"><strong>About this:</strong> YouTube/Google provide different APIs and you can do any kind of crazy stuff with them, like getting the complete uploads video playlist from any YouTube user, but that is half of work, then you have to present the data in a way it is pleasant to view, I tried many libraries already available online, but none did what I wanted, I tried a time-line, many calendars, but nothing, then I coded a <a href="alpha1/">“zooming” calendar</a>, but it didn’t work well on iOS, so following Casey’s philosophy, I keep it as simple as it can be.</p>
				<p><strong>About me:</strong> My Name is Rodrigo Polo, a self-taught developer and entrepreneur with over 21 years of experience, currently working on two incredible projects, looking for <a href="http://rodrigopolo.com/contact">investors</a> for a third project. You can check my coding profiles on <a href="https://github.com/RodrigoPolo/">GitHub</a> and <a href="http://stackoverflow.com/users/218418/Rodrigo-Polo">Stack Overflow</a>, my Twitter profile in <a href="https://twitter.com/PoloPinetta">English</a> and <a href="https://twitter.com/RodrigoPolo">Spanish</a>, my YouTube channel in  <a href="http://www.youtube.com/c/RodrigoPolo">Spanish</a> and <a href="http://www.youtube.com/c/RodrigoPoloVlog">English</a>, my <a href="https://medium.com/@RodrigoPolo">Medium</a> profile, and my <a href="https://www.instagram.com/RodrigoPolo">Instagram</a> profile.</p>

			</div>
		</div>





		<!-- JS -->
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<!--
		<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>
		-->
		<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.12.0/moment.min.js"></script>

		<script>
			var date_start = '<?=$date_start?>';
			var date_stop  = '<?=$date_stop?>';
			var jsonx = <?=json_encode($json)?>

		</script>

		<script src="main.js"></script>
		
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-75532826-1', 'auto');
			ga('send', 'pageview');
		</script>
	</body>
</html>
