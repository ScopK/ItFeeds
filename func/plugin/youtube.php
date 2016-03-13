<?php 
	if (!isset($_fetchers)){
		$_fetchers = array();
	}
	array_unshift($_fetchers,new YoutubeFeedReader());

	class YoutubeFeedReader extends DefaultFeedReader{
		private $youtubeapi;

		public function __construct(){
			$this->youtubeapi = new YoutubeAPI();
			parent::__construct();
		}

		public function getPluginName($url) {
			return "Youtube Plugin";
		}

		public function feedMatch($url) {
			//https://www.youtube.com/feeds/videos.xml?channel_id=XXXXXXX
			return preg_match('/^https?:\/\/(?:www\.)?youtube\.com\/feeds\/videos\.xml/',$url);
		}

		public function correctDescription(&$p){
			if (!preg_match('/https?:\/\/(?:(?!\/).)*youtube\.com\/watch\?.*?v=([\w-]*)?(?:$|&)/',$p->link,$matches))
				return;
			$code = $matches[1];

			$info = $this->youtubeapi->getVideoSnippet($code);
			if ($info!==false){
				$snippet = $info->snippet;
				$duration = $info->contentDetails->duration;

				preg_match('/(\d*)S/',$duration,$matches);
				$ss = count($matches)==0? "00" :($matches[1]<10? "0".$matches[1] : $matches[1]);
				preg_match('/(\d*)M/',$duration,$matches);
				$mm = count($matches)==0? "0" : $matches[1];
				preg_match('/(\d*)H/',$duration,$matches);
				$hh = count($matches)==0? "0" : $matches[1];

				if ($hh==0){
					$duration = "$mm:$ss";
				} else {
					if ($mm<10){
						$mm = "0".$mm;
					}
					$duration = "$hh:$mm:$ss";
				}

				$description = $snippet->description;
				$arr = $this->split2($description,"\n",10);
				if ($arr[0]!=$description){
					$description = $arr[0];
					$description.="\n...";
				}

				ob_start();
				?>
<table style='max-width:900px; font-family:Arial,Helvetica,sans-serif'>
	<tr>
		<td rowspan="4" style='vertical-align:top'>
			<a target="_blank" href="<?php echo $p->link ?>"><img style='width:300px;border:0px' src="http://i.ytimg.com/vi/<?php echo $code ?>/0.jpg"/></a>
		</td>
		<td style='padding:0 5px; height:1px; font-size:14px'>
			<b><a target="_blank" href="<?php echo $p->link ?>"><?php echo mb_convert_encoding($snippet->title, "HTML-ENTITIES" ,"utf-8") ?></a></b>
		</td>
	</tr>
	<tr>
		<td style='padding:0 5px 10px; height:1px; font-size:11px'>
			<span style='color:#666'>From:</span> <a target="_blank" href="https://www.youtube.com/channel/<?php echo $snippet->channelId ?>"><?php echo utf8_decode($snippet->channelTitle) ?></a>
		</td>
	</tr>
	<tr>
		<td style='padding:10px 5px; vertical-align:top; font-size:12px'><?php echo mb_convert_encoding(str_replace("\n","<br/>",$description), "HTML-ENTITIES" ,"utf-8") ?></td>
	</tr>

	<tr>
		<td style='padding:10px 5px 5px; height:1px; font-size:11px'>
			<span style='color:#666'>Time:</span> <b><?php echo $duration ?></b>
		</td>
	</tr>
</table>
	<?php
				$dd = ob_get_contents();
				ob_end_clean();

				$p->description=$dd;
			}
		}

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