<?php
	$fr = new FeedReader();
	$fr->setUrl($test3);
	$fr->getFeeds();

	class FeedReader{
		private $xmlDoc;
		private $url;

		public function __construct() {
			$this->xmlDoc = new DOMDocument();
		}

		public function setUrl($url){
			$this->url = $url;
		}

		public function getFeeds(){
			$this->xmlDoc->load($this->url);
			//$xmlDoc->save("xml.xml");

			$source = $this->xmlDoc->getElementsByTagName('channel')->item(0);
			if ($source) {
				$this->channelItem($source);
				return;
			}

			$source = $this->xmlDoc->getElementsByTagName('feed')->item(0);
			if ($source) {
				$this->feedEntry($source);
				return;
			}
		}




		private function channelItem($channel){
			$channel_title = $channel->getElementsByTagName('title')->item(0)->nodeValue;
//			echo "## channel_title: $channel_title\n";

			$channel_desc = $channel->getElementsByTagName('description')->item(0)->nodeValue;
//			echo "## channel_desc: $channel_desc\n";

			$items = $this->xmlDoc->getElementsByTagName('item');
			foreach ($items as $item){ 
				$item_link = $item->getElementsByTagName('link')->item(0)->nodeValue;
//				echo "## item_link: $item_link\n";

				$item_title = $item->getElementsByTagName('title')->item(0)->nodeValue;
//				echo "## item_title: $item_title\n";

				$item_desc = $item->getElementsByTagName('description')->item(0)->nodeValue;
//				echo "## item_desc: $item_desc\n";
			}
		}

		private function feedEntry($feed){
			$channel_title = $feed->getElementsByTagName('title')->item(0)->nodeValue;
//			echo "## channel_title: $channel_title\n";


			$entries = $this->xmlDoc->getElementsByTagName('entry');

			foreach ($entries as $entry){ 
				$entry_links = $entry->getElementsByTagName('link');
				$entry_link = "";
				foreach($entry_links as $link) {
				    if($link->getAttribute('rel') =='alternate') {
				        $entry_link = $link->getAttribute("href");
				        break;
				    }  
				}
				if ($entry_link == "")
					$entry_link = $entry_links->item(0)->getAttribute("href");

//				echo "## entry_link: $entry_link\n";

				$entry_title = $entry->getElementsByTagName('title')->item(0)->nodeValue;
//				echo "## entry_title: $entry_title\n";

				$entry_cont = $entry->getElementsByTagName('content')->item(0)->nodeValue;
//				echo "## entry_cont: $entry_cont\n";
			}
		}
	}
?> 