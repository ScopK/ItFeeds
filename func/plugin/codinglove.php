<?php 
	if (!isset($_fetchers)){
		$_fetchers = array();
	}
	array_unshift($_fetchers,new CodingLoveFeedReader());

	class CodingLoveFeedReader extends DefaultFeedReader{
		public function getPluginName($url) {
			return "CodingLove Plugin";
		}

		public function feedMatch($url) {
			//http://thecodinglove.com/rss
			return preg_match('/^https?:\/\/.*?thecodinglove\.com\//',$url);
		}

		public function correctDescription(&$p){
			$html = file_get_contents($p->link);

			preg_match('/<div class="bodytype">.*?(<img.*?>)/',$html,$matches);

			if (count($matches)>1){
				$p->description=$matches[1].($p->description==""?"":("<p style='margin:0'>".$p->description."</p>"));
			}
		}
	}
