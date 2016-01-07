<?php

class PluginManager{
	private $youtubeapi;

	public function __construct(){
		$this->youtubeapi=null;
	}

	public function pluginCheck(&$post){
		return $this->checkYoutube($post)
		|| $this->checkCodingLove($post);
	}

	private function checkYoutube(&$post){
		$description = $post->description;
		if ($description==""){
			$link = $post->link;
			if (preg_match('/https?:\/\/(?:(?!\/).)*youtube\.com\/watch\?.*?v=([\w-]*)?(?:$|&)/',$link,$matches)){
				$code = $matches[1];

				if ($this->youtubeapi==null){
					$this->youtubeapi = new YoutubeAPI();
				}

				ob_start();
				include "plugin/youtube_sample.php";
				$dd = ob_get_contents();
				ob_end_clean();

				$post->description=$dd;
				return true;
			}
		}
		return false;
	}

	private function checkCodingLove(&$post){
		$link = $post->link;
		if (preg_match('/^https?:\/\/.*?thecodinglove\.com\//',$link)){
			$description = $post->description;
			if (preg_match('/^\/\* .*? \*\/$/s',$description) || $description==""){

				ob_start();
				include "plugin/codinglove_sample.php";
				$dd = ob_get_contents();
				ob_end_clean();

				$post->description=$dd.($description==""?"":("<p style='margin:0'>".$description."</p>"));

				return true;
			}
		}
		return false;
	}

	// Support function
	private function split2($string,$needle,$nth){
		$max = strlen($string);
		$n = 0;
		for($i=0;$i<$max;$i++){
		    if($string[$i]==$needle){
		        $n++;
		        if($n>=$nth){
		            break;
		        }
		    }
		}
		$arr[] = substr($string,0,$i);
		$arr[] = substr($string,$i+1,$max);
		return $arr;
	}
}


class YoutubeAPI{
	private static $googleApiKey = "AIzaSyDfG_bgb6iN74j8nOKsl2c87I3HfZNWMBg"; //GooCal Key

	//public function __construct(){}
	//public function __destruct() {}

	public static function videoExists($id){
		// Info: https://developers.google.com/youtube/v3/docs/videos/list?hl=en
		$urlExist = "https://www.googleapis.com/youtube/v3/videos?id=".$id."&part=id&key=".(self::$googleApiKey);
		$json = json_decode(file_get_contents($urlExist));
		return $json->pageInfo->totalResults>0;
	}

	public static function getVideoSnippet($id){
		// Info: https://developers.google.com/youtube/v3/docs/videos/list?hl=en
		$urlDurat = "https://www.googleapis.com/youtube/v3/videos?id=".$id."&part=snippet,contentDetails&key=".(self::$googleApiKey);
		$jsonDurat = json_decode(file_get_contents($urlDurat));
		if (isset($jsonDurat->items) && isset($jsonDurat->items[0]))
			return $jsonDurat->items[0];//->snippet;
		else
			return false;
	}
}

