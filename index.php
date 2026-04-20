<?php

/**
 * index.php — Calendar view of a YouTube channel's video archive.
 *
 * Queries the local database for all videos uploaded by the configured
 * channel since START_DATE, groups them by day, and renders a calendar UI.
 */

date_default_timezone_set('UTC');

require __DIR__ . '/app/config.php';
require __DIR__ . '/app/lib/Database.php';

// --- Data ---

$videos = Database::getInstance()->query(
    'SELECT *
     FROM `videos`
     WHERE `user` = ?
       AND `date` >= ?
     ORDER BY `date` ASC',
    [YOUTUBE_USER, START_DATE]
);

/** @var array<string, list<array{t: string, i: string}>> $json */
$json = [];

foreach ($videos as $video) {
    $date  = new DateTimeImmutable($video['date']);
    $start = $date->format('Ymd');

    if (!isset($json[$start])) {
        $json[$start] = [];
    }

    $json[$start][] = [
        't' => $video['title'],
        'i' => $video['ytid'],
    ];
}

$date_start = substr($videos[0]['date'], 0, 10);
$date_stop  = substr($videos[count($videos) - 1]['date'], 0, 10);

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Casey Neistat Vlog Archive">
		<meta name="author" content="Rodrigo Polo">

		<title>Casey Neistat Vlog Archive</title>

		<link href="style.css" rel="stylesheet">
	</head>
	<body>
		<base target="_blank">
		<div class="main">


			<div class="fullcal">
				<h1>Casey Neistat Archive (updated)</h1>
				<p>On march 25, 2015, Casey Neistat started a YouTube daily video log, amazingly he delivered, not only a daily video but sometimes two videos on the same day for almost a year. I was stunned when I discover Casey vlog but I didn't find a way to see all his videos in a chronological order, so I decided to code this page using YouTube's API, if you want to know more about this, <span style="background: #ff0;">the latest update</span>, source code or me, go to the page bottom.</p>

				<div class="cal"></div>

				<p id="about"><strong>About this:</strong> YouTube/Google provide different APIs and you can do any kind of crazy stuff with them, like getting the complete uploads video playlist from any YouTube user, but that is half of work, then you have to present the data in a way it is pleasant to view, I tried many libraries already available online, but none did what I wanted, I tried a time-line, many calendars, but nothing, then I coded a <a href="alpha1/">"zooming" calendar</a>, but it didn't work well on iOS, so following Casey's philosophy, I keep it as simple as it can be.</p>
				<p><strong>2026 update:</strong> PHP has changed a lot, and the amount of videos made the page to long to scroll, so the whole project was refactored, if you have issues with the page, clean your cookies and cache, and reload the page.</p>
				<p><strong>About me:</strong> My Name is Rodrigo Polo, a self-taught developer and entrepreneur with over 31 years of experience. You can check my coding profiles on <a href="https://github.com/RodrigoPolo/">GitHub</a> and <a href="http://stackoverflow.com/users/218418/Rodrigo-Polo">Stack Overflow</a>, my X profile in <a href="https://x.com/RealRodrigoPolo">@RealRodrigoPolo</a>, my tech YouTube channel in  <a href="https://www.youtube.com/@PolosTechLab">@PolosTechLab</a>, and my <a href="https://www.instagram.com/RodrigoPolo">Instagram</a> profile.</p>

			</div>
		</div>




		<!-- JS -->
		<script src="//cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.13/dayjs.min.js"></script>

		<script>
			var date_start = '<?= htmlspecialchars($date_start, ENT_QUOTES) ?>';
			var date_stop  = '<?= htmlspecialchars($date_stop,  ENT_QUOTES) ?>';
			var jsonx = <?= json_encode($json, JSON_HEX_TAG | JSON_HEX_AMP) ?>;

		</script>

		<script src="main.js"></script>

		<!-- TODO: migrate to GA4 — UA-75532826-1 was sunset July 2024; replace with GA4 property ID -->
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
