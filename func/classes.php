<?php
	if(!isset($isServer) || !$isServer){
		header("HTTP/1.1 403 Forbidden");
		die("HTTP/1.1 403 Forbidden");
	}
	
	class Tag {
		public $id = "";
		public $name = "";
		public $posts = array();
		public $user = "";
		public $hidden = "";

		public $count =0;		
	}

	class Folder {
		public $id = "";
		public $name = "";
		public $feeds = array();
		public $user = "";
		public $hidden = "";

		public $unread =0;
		public $count =0;
	}

	class Feed {
		public $id = "";
		public $folderId = "";
		public $name = "";
		public $upd_time = "";

		public $link = "";
		public $rss_link = "";

		public $last_date_post = "";

		public $enabled = true;
		public $deleted = false;

		public $posts = array();

		public $unread =0;
		public $count =0;
	}

	class Post {
		public $id = "";
		public $idx = "";
		public $feedId = "";
		public $title = "";
		public $description = "";
		public $link = "";
		public $unread = true;
		public $favorite = false;
		public $date = "";
		public $tags = array();
	}
?>