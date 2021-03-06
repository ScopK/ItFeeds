<?php 
	if (!isset($_fetchers)){
		$_fetchers = array();
	}
	array_unshift($_fetchers,new DefaultFeedReader());

	class DefaultFeedReader extends NoneFeedReader{
		public $xmlDoc;

		public function __construct() {
			$this->xmlDoc = new DOMDocument();
		}

		public function getPluginName($url) {
			return "Default Plugin";
		}

		public function getContent($url) {
			$rnd = rand(0,52378689273);
			$tempurl = $url;
			$tempurl.= (strpos($tempurl,"?")?"&":"?")."r".$rnd."=".$rnd;
			$error = @$this->xmlDoc->load($tempurl);

			if ($error === false){
				throw new Exception("Error loading page - ".$url."");
			}

			$source = $this->xmlDoc->getElementsByTagName('channel')->item(0);
			if ($source)
				return $this->rssFeed($source);

			$source = $this->xmlDoc->getElementsByTagName('feed')->item(0);
			if ($source)
				return $this->atomFeed($source);

			throw new Exception("Error data format - ".$url."");
		}


		protected function rssFeed($channel){
			//$channel_title = $this->getNodeValueByTagName($channel,'title');
			//$channel_desc = $this->getNodeValueByTagName($channel,'description');
			$posts = array();
			$items = $this->xmlDoc->getElementsByTagName('item');
			foreach ($items as $item){
				$p = new Post();
				$p->date = $this->getNodeValueByTagName($item,'pubDate');
				$time = new DateTime($p->date);
				date_default_timezone_set('Europe/Madrid');
				$p->date = date("Y-m-d H:i:s", $time->format('U')); 

				$p->link = $this->getNodeValueByTagName($item,'link');

				$p->description = $this->mb_htmlentities($this->getNodeValueByTagName($item,'description'));
				$p->title = $this->mb_htmlentities($this->getNodeValueByTagName($item,'title'));
				if ($p->title == "")
					$p->title = $this->mb_htmlentities($this->getNodeValueByTagName($channel,'title'));
				$posts[] = $p;
			}
			return $posts;
		}

		protected function atomFeed($feed){
			//$feed_title = $this->getNodeValueByTagName($feed,'title');
			//$feed_subtitle = $this->getNodeValueByTagName($feed,'subtitle');
			$posts = array();
			$entries = $this->xmlDoc->getElementsByTagName('entry');
			foreach ($entries as $entry){ 
				$p = new Post();

				$p->postId = $this->getNodeValueByTagName($entry,'id');

				$entry_links = $entry->getElementsByTagName('link');
				$p->link = $entry_links->item(0)->getAttribute("href");
				foreach($entry_links as $link) {
					if($link->getAttribute('rel') =='alternate') {
						$p->link = $link->getAttribute("href");
						break;
				}   }

				$p->date = $this->getNodeValueByTagName($entry,'published');
				$time = new DateTime($p->date);
				date_default_timezone_set('Europe/Madrid');
				$p->date = date("Y-m-d H:i:s", $time->format('U'));

				$p->date_updated = $this->getNodeValueByTagName($entry,'updated');
				if ($p->date_updated){
					$time = new DateTime($p->date_updated);
					$p->date_updated = date("Y-m-d H:i:s", $time->format('U')); 
				}

				$p->description = $this->mb_htmlentities($this->getNodeValueByTagName($entry,'content'));
				$p->title = $this->mb_htmlentities($this->getNodeValueByTagName($entry,'title'));
				if ($p->title == "")
					$p->title = $this->mb_htmlentities($this->getNodeValueByTagName($feed,'title'));

				$posts[] = $p;
			}
			return $posts;
		}

		private function getNodeValueByTagName($node,$tagname){
			if ($val = $node->getElementsByTagName($tagname)->item(0))
				return $val->nodeValue;
			else
				return "";
		}

		private function mb_htmlentities($str){
			return mb_convert_encoding($str, "HTML-ENTITIES" ,"utf-8");
		}

		private function repl($str){
			$aa = str_replace("&lt;","<",$str); 
			$aa = str_replace("&quot;",'"',$aa); 
			return str_replace("&gt;",">",$aa);
		}
	}