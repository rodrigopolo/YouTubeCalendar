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
		$r = $this->fetch_url_content($url);
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

	// 
	private function fetch_url_content($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$content = curl_exec($ch);

		if (curl_errno($ch)) {
			$error_msg = 'Error: ' . curl_error($ch);
			curl_close($ch);
			return $error_msg;
		}

		curl_close($ch);
		return $content;
	}

}
