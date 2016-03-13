<?php 
	if (!isset($_fetchers)){
		$_fetchers = array();
	}
	array_unshift($_fetchers,new NoneFeedReader());

	class NoneFeedReader {
		public function getPluginName($url) {
			return "None Plugin";
		}
		public function feedMatch($url) {
			return true;
		}
		public function getContent($url) {
			return array();
		}
		public function correctDescription(&$p){
		}
	}