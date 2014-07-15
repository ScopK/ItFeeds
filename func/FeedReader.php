<?php
	$isServer=1;
	require_once "classes.php";

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
			if ($source)
				return $this->rssFeed($source);

			$source = $this->xmlDoc->getElementsByTagName('feed')->item(0);
			if ($source)
				return $this->atomFeed($source);
		}

		private function rssFeed($channel){
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
				$p->title = $this->getNodeValueByTagName($item,'title');
				$p->description = $this->getNodeValueByTagName($item,'description');
				$posts[] = $p;
			}
			return $posts;
		}

		private function atomFeed($feed){
			//$feed_title = $this->getNodeValueByTagName($feed,'title');
			//$feed_subtitle = $this->getNodeValueByTagName($feed,'subtitle');
			$posts = array();
			$entries = $this->xmlDoc->getElementsByTagName('entry');
			foreach ($entries as $entry){ 
				$p = new Post();

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

				$p->title = $this->getNodeValueByTagName($entry,'title');
				$p->description = $this->getNodeValueByTagName($entry,'content');

				$posts[] = $p;
			}
			return $posts;
		}

		private function getNodeValueByTagName($node,$tagname){
			if ($val = $node->getElementsByTagName($tagname)->item(0))
				return $val->nodeValue;
			else
				return $val;
		}
	}

	// To test yet //
    function rsstotime($rss_time) {
        $day = substr($rss_time, 5, 2);
        $month = substr($rss_time, 8, 3);
        $month = date('m', strtotime("$month 1 2011"));
        $year = substr($rss_time, 12, 4);
        $hour = substr($rss_time, 17, 2);
        $min = substr($rss_time, 20, 2);
        $second = substr($rss_time, 23, 2);
        $timezone = substr($rss_time, 26);

        $timestamp = mktime($hour, $min, $second, $month, $day, $year);
        date_default_timezone_set('CET');

        if(is_numeric($timezone)) {
            $hours_mod = $mins_mod = 0;
            $modifier = substr($timezone, 0, 1);
            $hours_mod = (int) substr($timezone, 1, 2);
            $mins_mod = (int) substr($timezone, 3, 2);
            $hour_label = $hours_mod>1 ? 'hours' : 'hour';
            $strtotimearg = $modifier.$hours_mod.' '.$hour_label;
            if($mins_mod) {
                $mins_label = $mins_mod>1 ? 'minutes' : 'minute';
                $strtotimearg .= ' '.$mins_mod.' '.$mins_label;
            }
            $timestamp = strtotime($strtotimearg, $timestamp);
        }
        return $timestamp;
    }
?> 