<?php
	function getTags($con,$user,$depth){
		global $ad_hidden;
		if ($ad_hidden)
			$tags = mysqli_query($con,"SELECT * FROM tags WHERE user='$user' ORDER BY tag_name");
		else
			$tags = mysqli_query($con,"SELECT * FROM tags WHERE user='$user' AND hidden='0' ORDER BY tag_name");

		$lista = array();
		while($tag = mysqli_fetch_array($tags)) {
			$e = new Tag();
			$e->id = $tag['id'];
			$e->name = $tag['tag_name'];
			$e->user = $user;
			$e->hidden = $tag['hidden'];
			$e->posts = getPostsTag($con, $e->id,$depth);

			$sql = "SELECT count(*) AS c FROM post_tags p WHERE p.id_tag='".$e->id."'";
			$res = mysqli_query($con,$sql);
			$countQ = mysqli_fetch_array($res);
			$e->count = $countQ['c'];

			$lista[] = $e;	
			mysqli_free_result($res);
		}
		mysqli_free_result($tags);
		return $lista;
	}

	function getFolders($con,$user,$depth){
		global $ad_hidden;
		if ($ad_hidden)
			$folders = mysqli_query($con,"SELECT * FROM folders WHERE user='$user' ORDER BY name");
		else
			$folders = mysqli_query($con,"SELECT * FROM folders WHERE user='$user' AND hidden='0' ORDER BY name");

		$lista = array();
		while($folder = mysqli_fetch_array($folders)) {
			$e = new Folder();
			$e->id = $folder['id'];
			$e->name = $folder['name'];
			$e->user = $user;
			$e->hidden = $folder['hidden'];
			$e->feeds = getFeeds($con, $e->id,$depth);

			$sql = "SELECT count(*) AS c, IFNULL(sum(p.unread), 0) AS u FROM posts p JOIN feeds f ON p.id_feed=f.id WHERE f.id_folder='".$e->id."'";
			$res = mysqli_query($con,$sql);
			$countQ = mysqli_fetch_array($res);
			$e->unread = $countQ['u'];
			$e->count = $countQ['c'];

			$lista[] = $e;	
			mysqli_free_result($res);
		}
		mysqli_free_result($folders);
		return $lista;
	}

	function getFolder($con,$foldId,$depth){
		$foldId = mysqli_real_escape_string($con,$foldId);
		$folders = mysqli_query($con,"SELECT * FROM folders WHERE id='$foldId'");

		$e = new Folder();
		if ($folder = mysqli_fetch_array($folders)) {

			$e->id = $folder['id'];
			$e->name = $folder['name'];
			$e->user = $user;
			$e->hidden = $folder['hidden'];
			$e->feeds = getFeeds($con, $e->id,$depth);

			$sql = "SELECT count(*) AS c, IFNULL(sum(p.unread), 0) AS u FROM posts p JOIN feeds f ON p.id_feed=f.id WHERE f.id_folder='".$e->id."'";
			$res = mysqli_query($con,$sql);
			$countQ = mysqli_fetch_array($res);
			$e->unread = $countQ['u'];
			$e->count = $countQ['c'];

			mysqli_free_result($res);
		}
		mysqli_free_result($folders);
		return $e;
	}

	function getFeeds($con, $folderId,$depth){
		if ($depth < 1)
			return array();

		$feeds = mysqli_query($con,"SELECT * FROM feeds WHERE id_folder='$folderId' ORDER BY name");
		$lista = array();
		while($feed = mysqli_fetch_array($feeds)) {
			$e = new Feed();
			$e->id = $feed['id'];
			$e->name = $feed['name'];

			$e->folderId = $folderId;
			$e->upd_time = $feed['upd_time'];
			$e->last_date_post = $feed['last_date_post'];

			$e->link = $feed['link'];
			$e->rss_link = $feed['rss_link'];

			$e->enabled = $feed['enabled'];
			$e->deleted = $feed['deleted'];
			$e->posts = getPostsFeed($con, $e->id,$depth);

			$sql = "SELECT count(*) AS c, IFNULL(sum(p.unread), 0) AS u FROM posts p WHERE p.id_feed='".$e->id."'";
			$res = mysqli_query($con,$sql);
			$countQ = mysqli_fetch_array($res);
			$e->unread = $countQ['u'];
			$e->count = $countQ['c'];

			$lista[] = $e;
			mysqli_free_result($res);
		}
		mysqli_free_result($feeds);
		return $lista;
	}


	function getPostsFeed($con, $feedId,$depth){
		if ($depth < 2)
			return array();

		$posts = mysqli_query($con,"SELECT * FROM posts WHERE id_feed='$feedId' ORDER BY ´date´ LIMIT 0, 10");
		$lista = array();
		
		while($post = mysqli_fetch_array($posts)) {

			$e = new Post();
			$e->id = $post['id'];
			$e->feedId = $feedId;

			$e->title = $post['title'];
			$e->description = $post['description'];
			$e->link = $post['link'];
			$e->unread = $post['unread'];
			$e->favorite = $post['favorite'];
			$e->date = $post['date'];

			$lista[] = $e;
		}
		
		mysqli_free_result($posts);
		return $lista;
	}

	function getPostsTag($con, $tagId,$depth){
		if ($depth < 1)
			return array();

		$posts = mysqli_query($con,"SELECT * FROM posts WHERE id IN (SELECT id_post FROM post_tags WHERE id_tag='$tagId') ORDER BY ´date´ LIMIT 0, 10");
		$lista = array();

		while($post = mysqli_fetch_array($posts)) {

			$e = new Post();
			$e->id = $post['id'];
			$e->feedId = $feedId;

			$e->title = $post['title'];
			$e->description = $post['description'];
			$e->link = $post['link'];
			$e->unread = $post['unread'];
			$e->favorite = $post['favorite'];
			$e->date = $post['date'];

			$lista[] = $e;
		}

		return $lista;
	}

	function getUUID(){
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
	        mt_rand( 0, 0x0fff ) | 0x4000,
	        mt_rand( 0, 0x3fff ) | 0x8000,
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ));
	}

	function checkUserPassword($con,$user,$pass){
		$sql = "SELECT count(*) FROM users WHERE username=? AND password=?";
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"ss", $user, $pass); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$count); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value

			mysqli_stmt_close($stmt); // Close statement
		}
		return ($count == 1);
	}

	function checkUserHiddenPassword($con,$user,$pass){
		$sql = "SELECT count(*) FROM users WHERE username=? AND hidden_pass=?";
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"ss", $user, $pass); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$count); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value

			mysqli_stmt_close($stmt); // Close statement
		}
		return ($count == 1);
	}
?>