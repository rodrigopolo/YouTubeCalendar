<?php
class YoutubePub{

	public $key;
	public $api_url;
	public $json;

	// Init
	public function __construct($key) {
		$this->key = $key;
		$this->api_url = 'https://www.googleapis.com/youtube/v3/';
		$this->json = false;
	}

	// Simple get
	public function get($endpoint, $op){
		$op['key'] = $this->key;
		$url = $this->api_url.$endpoint.'?'.$this->query($op);
		$r = file_get_contents($url);
		if($this->json){
			return $r;
		}else{
			return json_decode($r, true);
		}
	}

	// Recursive HTML List
	private function query($query){
		$query_array = array();
		foreach( $query as $key => $key_value ){
			$query_array[] = urlencode( $key ) . '=' . urlencode( $key_value );
		}
		return implode( '&', $query_array );
	}

}