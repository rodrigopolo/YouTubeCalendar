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



// New YT Instance
$yt = new YoutubePub(YOUTUBE_KEY);

// Get channel uploads playlist
$channel = $yt->get('channels',
	[
		'part'			=> 'snippet,contentDetails',
		'forUsername'	=> YOUTUBE_USER,
		'maxResults'	=> 50
	]
);


$page = 0;
$ops = 	[
		'part'			=> 'snippet',
		'maxResults'	=> '50',
		'playlistId'	=> $channel['items'][0]['contentDetails']['relatedPlaylists']['uploads']
];


// Loop
function sloop(){
	global $page, $yt, $ops;

	$page++;
	echo 'Loading page: '.$page.".\n";

	// Get uploads playlist items
	$playlist = $yt->get('playlistItems', $ops);
	$gotext = populate($playlist['items']);

	if($gotext){
		if($playlist['nextPageToken']){
			$ops['pageToken'] = $playlist['nextPageToken'];
			sloop();
		}
	}

}

// Populate DB
function populate($arr){

	foreach ($arr as $key => $item) {

		$dbitem  = R::findOne('videos', ' ytid = ?', [$item['snippet']['resourceId']['videoId']]);

		if($dbitem){
			return false;
		}else{

			$video = R::dispense('videos');
			$video->user = $item['snippet']['channelTitle'];
			//$video->date = DateTime::createFromFormat('Y-m-d\TH:i:s', $item['snippet']['publishedAt']);
			$video->date = new DateTime($item['snippet']['publishedAt']);
			$video->ytid = $item['snippet']['resourceId']['videoId'];
			$video->title = $item['snippet']['title'];
			$video->description = $item['snippet']['description'];

			R::store($video);
		}

	}
	return true;
}


// Start
sloop();


