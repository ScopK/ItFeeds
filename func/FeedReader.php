<?php

	$fr = new FeedReader();
	$fr->setUrl("http://rss");
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
				$this->rssFeed($source);
				return;
			}

			$source = $this->xmlDoc->getElementsByTagName('feed')->item(0);
			if ($source) {
				$this->atomFeed($source);
				return;
			}
		}

		private function rssFeed($channel){
			$channel_title = $channel->getElementsByTagName('title')->item(0)->nodeValue;
			$channel_desc = $channel->getElementsByTagName('description')->item(0)->nodeValue;

			$items = $this->xmlDoc->getElementsByTagName('item');
			foreach ($items as $item){ 
				$item_link = $item->getElementsByTagName('link')->item(0)->nodeValue;
				$item_title = $item->getElementsByTagName('title')->item(0)->nodeValue;
				$item_pubdate = $item->getElementsByTagName('pubDate')->item(0)->nodeValue;
				$item_desc = $item->getElementsByTagName('description')->item(0)->nodeValue;
			}
		}

		private function atomFeed($feed){
			$feed_title = $feed->getElementsByTagName('title')->item(0)->nodeValue;
			$feed_subtitle = $feed->getElementsByTagName('subtitle')->item(0)->nodeValue;

			$entries = $this->xmlDoc->getElementsByTagName('entry');
			foreach ($entries as $entry){ 
				$entry_links = $entry->getElementsByTagName('link');
				$entry_link = $entry_links->item(0)->getAttribute("href");
				foreach($entry_links as $link) {
				    if($link->getAttribute('rel') =='alternate') {
				        $entry_link = $link->getAttribute("href");
				        break;
				}   }  
				$entry_title = $entry->getElementsByTagName('title')->item(0)->nodeValue;
				$entry_pubdate = $entry->getElementsByTagName('published')->item(0)->nodeValue;
				$entry_cont = $entry->getElementsByTagName('content')->item(0)->nodeValue;
			}
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