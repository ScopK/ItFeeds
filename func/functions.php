<?php
	if(!isset($isServer) || !$isServer){
		header("HTTP/1.1 403 Forbidden");
		die("HTTP/1.1 403 Forbidden");
	}
	
	function getTags($con,$user,$depth,$showHidden){
		if ($showHidden)
			$tags = mysqli_query($con,"SELECT * FROM tags WHERE user='$user' ORDER BY tag_name");
		else
			$tags = mysqli_query($con,"SELECT * FROM tags WHERE user='$user' AND hidden='0' ORDER BY tag_name");

		$lista = array();
		while($tag = array_map('utf8_encode',mysqli_fetch_assoc($tags))) {
			$e = new Tag();
			$e->id = $tag['id'];
			$e->name = $tag['tag_name'];
			$e->user = $user;
			$e->hidden = $tag['hidden'];
			$e->posts = array(); //getPostsTag($con, $e->id,$depth);

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

	function getTag($con,$tagId){
		$tagId = mysqli_real_escape_string($con,$tagId);
		$tags = mysqli_query($con,"SELECT * FROM tags WHERE id='$tagId'");

		$e = new Tag();

		if ($tag = array_map('utf8_encode',mysqli_fetch_assoc($tags))) {
			$e->id = $tag['id'];
			$e->name = $tag['tag_name'];
			$e->user = $user;
			$e->hidden = $tag['hidden'];
			$e->posts = array(); //getPostsTag($con, $e->id,$depth);

			$sql = "SELECT count(*) AS c FROM post_tags p WHERE p.id_tag='".$e->id."'";
			$res = mysqli_query($con,$sql);
			$countQ = mysqli_fetch_array($res);
			$e->count = $countQ['c'];

			mysqli_free_result($res);
		}
		mysqli_free_result($tags);
		return $e;
	}

	function getFolders($con,$user,$depth,$showHidden){
		if ($showHidden)
			$folders = mysqli_query($con,"SELECT * FROM folders WHERE user='$user' ORDER BY name");
		else
			$folders = mysqli_query($con,"SELECT * FROM folders WHERE user='$user' AND hidden='0' ORDER BY name");

		$lista = array();

		while($folder = array_map('utf8_encode',mysqli_fetch_assoc($folders))) {
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

		if ($folder = array_map('utf8_encode',mysqli_fetch_assoc($folders))) {

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

		$feeds = mysqli_query($con,"SELECT * FROM feeds WHERE id_folder='$folderId' AND deleted='0' ORDER BY name ASC");
		$lista = array();

		while($feed = array_map('utf8_encode',mysqli_fetch_assoc($feeds))) {
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
			$e->posts = array(); //getPostsFeed($con, $e->id,$depth);

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

/*
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
*/
	function getUUID(){
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
	        mt_rand( 0, 0x0fff ) | 0x4000,
	        mt_rand( 0, 0x3fff ) | 0x8000,
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ));
	}

	function checkUserPassword($con,$user,$pass){
		$sql = "SELECT username FROM users WHERE username=? AND password=?";
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"ss", $user, $pass); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$username); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value

			mysqli_stmt_close($stmt); // Close statement
		}
		if(isset($username))
			return $username;
		return false;
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






 	/**

			GET POSTS FUNCTIONS

 	*//*
	function getIDFeed($con, $folderIdx, $feedIdx, $showHidden){
		$folderId = getIDFolder($con,$folderIdx, $showHidden);
		$sql = "SELECT name FROM feeds WHERE id_folder=? ORDER BY name LIMIT ?,1";
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"ss", $folderId, $feedIdx); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$id); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value

			mysqli_stmt_close($stmt); // Close statement
		}
		return $id;

	}

	function getIDFolder($con, $folderIdx, $showHidden){
		if ($showHidden)
			$sql = "SELECT id FROM folders ORDER BY name LIMIT ?,1";
		else
			$sql = "SELECT id FROM folders WHERE hidden='0' ORDER BY name LIMIT ?,1";		

		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"s", $folderIdx); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$id); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value

			mysqli_stmt_close($stmt); // Close statement
		}
		return $id;
	}*/


	function getPostsFeed($con, $user, $hidden, $feedId, $favs, $unread, $sort, $page, $postspage){
		$user = mysqli_real_escape_string($con,$user);
		$feedId = mysqli_real_escape_string($con,$feedId);
		$sort = mysqli_real_escape_string($con,$sort);
		$page = mysqli_real_escape_string($con,$page);
		$postspage = mysqli_real_escape_string($con,$postspage);

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";

		$sql = "SELECT * FROM posts WHERE id_feed='$feedId' $unreadSQL $favsSQL ORDER BY `date` $sort LIMIT $page,$postspage";
		$countsql = "SELECT count(*) AS c FROM posts WHERE id_feed='$feedId' $unreadSQL $favsSQL";

		return getPosts($con, $sql, $countsql, $hidden);
	}

	function getPostsFolder($con, $user, $hidden, $folderId, $favs, $unread, $sort, $page, $postspage){
		$user = mysqli_real_escape_string($con,$user);
		$folderId = mysqli_real_escape_string($con,$folderId);
		$sort = mysqli_real_escape_string($con,$sort);
		$page = mysqli_real_escape_string($con,$page);
		$postspage = mysqli_real_escape_string($con,$postspage);

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";

		$sql = "SELECT * FROM posts WHERE id_feed IN (SELECT id FROM feeds WHERE id_folder='$folderId') $unreadSQL $favsSQL ORDER BY `date` $sort LIMIT $page,$postspage";
		$countsql = "SELECT count(*) AS c FROM posts WHERE id_feed IN (SELECT id FROM feeds WHERE id_folder='$folderId') $unreadSQL $favsSQL";

		return getPosts($con, $sql, $countsql, $hidden);
	}

	function getPostsTag($con, $user, $hidden, $tagId, $favs, $unread, $sort, $page, $postspage){
		$user = mysqli_real_escape_string($con,$user);
		$tagId = mysqli_real_escape_string($con,$tagId);
		$sort = mysqli_real_escape_string($con,$sort);
		$page = mysqli_real_escape_string($con,$page);
		$postspage = mysqli_real_escape_string($con,$postspage);

		// uncomment to don't use favorites and unread vars
		//$sql = "SELECT * FROM posts WHERE id IN (SELECT id_post FROM post_tags WHERE id_tag='$tagId') ORDER BY `date` $sort LIMIT $page,$postspage";
		//$countsql = "SELECT count(*) AS c FROM post_tags WHERE id_tag='$tagId'";

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";
		$sql = "SELECT * FROM posts WHERE id IN (SELECT id_post FROM post_tags WHERE id_tag='$tagId') $unreadSQL $favsSQL ORDER BY `date` $sort LIMIT $page,$postspage";
		$countsql = "SELECT count(*) AS c FROM posts WHERE id IN (SELECT id_post FROM post_tags WHERE id_tag='$tagId') $unreadSQL $favsSQL";

		return getPosts($con, $sql, $countsql, $hidden);
	}

	function getPostsAll($con, $user, $hidden, $favs, $unread, $sort, $page, $postspage){
		$user = mysqli_real_escape_string($con,$user);
		$favs = mysqli_real_escape_string($con,$favs);
		$unread = mysqli_real_escape_string($con,$unread);
		$sort = mysqli_real_escape_string($con,$sort);
		$page = mysqli_real_escape_string($con,$page);
		$postspage = mysqli_real_escape_string($con,$postspage);

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";
		$hiddenSQL = ($hidden)? "" : "AND hidden='0'";

		$sql = "SELECT * FROM posts WHERE id_feed IN (".
					"SELECT id FROM feeds WHERE id_folder IN (".
						"SELECT id FROM folders WHERE user='$user' $hiddenSQL)) ".
				"$unreadSQL $favsSQL ORDER BY `date` $sort LIMIT $page,$postspage";

		$countsql = "SELECT count(*) AS c FROM posts WHERE id_feed IN (".
					"SELECT id FROM feeds WHERE id_folder IN (".
						"SELECT id FROM folders WHERE user='$user' $hiddenSQL)) ".
				"$unreadSQL $favsSQL";

		return getPosts($con, $sql, $countsql, $hidden);
	}

	function getPosts($con, $sql, $countsql, $hidden){
		$posts = mysqli_query($con,$sql);
		$lista = array();

		while($post = array_map('utf8_encode',mysqli_fetch_assoc($posts))) {

			$e = new Post();
			$e->id = $post['id'];
			$e->feedId = $post['id_feed'];

			$e->title = $post['title'];
			$e->description = $post['description'];
			$e->link = $post['link'];
			$e->unread = $post['unread'];
			$e->favorite = $post['favorite'];
			$e->date = $post['date'];

			$e->tags = getPostTags($con, $e->id, $hidden);
			$lista[] = $e;
		}
		mysqli_free_result($posts);

		$result = mysqli_query($con,$countsql);
		$rows = mysqli_fetch_assoc($result);

		$data = array("posts" => $lista, "total" => $rows['c']);

		mysqli_free_result($result);
		return $data;
	}

	function getPostTags($con, $postid, $hidden){

		$hiddenSQL = ($hidden)? "" : "AND t.hidden='0'";
		$sql = "SELECT id,tag_name FROM tags t JOIN post_tags pt ON pt.id_tag=t.id WHERE pt.id_post='$postid' $hiddenSQL";

		$tags = mysqli_query($con,$sql);
		$lista = array();

		while($tag = array_map('utf8_encode',mysqli_fetch_assoc($tags))) {
			$lista[] = array("id" => $tag['id'], "name" => $tag['tag_name']);
		}
		mysqli_free_result($tags);
		return $lista;
	}
?>

