<?php
	if(!isset($isServer) || !$isServer){
		header("HTTP/1.1 403 Forbidden");
		die("HTTP/1.1 403 Forbidden");
	}
	
	function getTags($user,$depth,$showHidden){
		global $con;
		if ($showHidden)
			$tags = mysqli_query($con,"SELECT * FROM tags WHERE user='$user' ORDER BY tag_name");
		else
			$tags = mysqli_query($con,"SELECT * FROM tags WHERE user='$user' AND hidden='0' ORDER BY tag_name");

		$lista = array();
		while($tag = @array_map('utf8_encode',mysqli_fetch_assoc($tags))) {
			$e = new Tag();
			$e->id = $tag['id'];
			$e->name = $tag['tag_name'];
			$e->user = $user;
			$e->hidden = $tag['hidden'];
			$e->public = $tag['public'];
			$e->posts = array(); //getPostsTag($e->id,$depth);

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

	function getTag($tagId){
		global $con;
		$tagId = mysqli_real_escape_string($con,$tagId);
		$tags = mysqli_query($con,"SELECT * FROM tags WHERE id='$tagId'");

		$e = new Tag();

		if ($tag = @array_map('utf8_encode',mysqli_fetch_assoc($tags))) {
			$e->id = $tag['id'];
			$e->name = $tag['tag_name'];
			$e->hidden = $tag['hidden'];
			$e->public = $tag['public'];
			$e->posts = array(); //getPostsTag($e->id,$depth);

			$sql = "SELECT count(*) AS c FROM post_tags p WHERE p.id_tag='".$e->id."'";
			$res = mysqli_query($con,$sql);
			$countQ = mysqli_fetch_array($res);
			$e->count = $countQ['c'];

			mysqli_free_result($res);
		}
		mysqli_free_result($tags);
		return $e;
	}

	function getFolders($user,$depth,$showHidden){
		global $con;
		if ($showHidden)
			$folders = mysqli_query($con,"SELECT * FROM folders WHERE user='$user' ORDER BY name");
		else
			$folders = mysqli_query($con,"SELECT * FROM folders WHERE user='$user' AND hidden='0' ORDER BY name");

		$lista = array();

		while($folder = @array_map('utf8_encode',mysqli_fetch_assoc($folders))) {
			$e = new Folder();
			$e->id = $folder['id'];
			$e->name = $folder['name'];
			$e->user = $user;
			$e->hidden = $folder['hidden'];
			$e->feeds = getFeeds($e->id,$depth);

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

	function getFolder($foldId,$depth){
		global $con;
		$foldId = mysqli_real_escape_string($con,$foldId);
		$folders = mysqli_query($con,"SELECT * FROM folders WHERE id='$foldId'");

		$e = new Folder();

		if ($folder = @array_map('utf8_encode',mysqli_fetch_assoc($folders))) {

			$e->id = $folder['id'];
			$e->name = $folder['name'];
			//$e->user = $user;
			$e->hidden = $folder['hidden'];
			$e->feeds = getFeeds($e->id,$depth);

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

	function getFeeds($folderId,$depth){
		global $con;
		if ($depth < 1)
			return array();

		$feeds = mysqli_query($con,"SELECT * FROM feeds WHERE id_folder='$folderId' AND deleted='0' ORDER BY name ASC");
		$lista = array();

		while($feed = @array_map('utf8_encode',mysqli_fetch_assoc($feeds))) {
			$e = new Feed();
			$e->id = $feed['id'];
			$e->name = $feed['name'];

			$e->folderId = $folderId;
			$e->upd_time = $feed['upd_time'];
			$e->max_unread = $feed['max_unread'];
			$e->last_date_post = $feed['last_date_post'];

			$e->link = $feed['link'];
			$e->rss_link = $feed['rss_link'];

			$e->enabled = $feed['enabled'];
			$e->deleted = $feed['deleted'];
			$e->posts = array(); //getPostsFeed($e->id,$depth);

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

	function getFeed($feedId){
		global $con;
		$feeds = mysqli_query($con,"SELECT * FROM feeds WHERE id='$feedId'");
		$e = new Feed();

		if ($feed = @array_map('utf8_encode',mysqli_fetch_assoc($feeds))) {
			$e->id = $feed['id'];
			$e->name = $feed['name'];

			$e->folderId = $feed['id_folder'];
			$e->upd_time = $feed['upd_time'];
			$e->max_unread = $feed['max_unread'];
			$e->last_date_post = $feed['last_date_post'];

			$e->link = $feed['link'];
			$e->rss_link = $feed['rss_link'];

			$e->enabled = $feed['enabled'];
			$e->deleted = $feed['deleted'];
			$e->posts = array();
/*
			$sql = "SELECT count(*) AS c, IFNULL(sum(p.unread), 0) AS u FROM posts p WHERE p.id_feed='".$e->id."'";
			$res = mysqli_query($con,$sql);
			$countQ = mysqli_fetch_array($res);
			$e->unread = $countQ['u'];
			$e->count = $countQ['c'];
			mysqli_free_result($res);*/
		}
		mysqli_free_result($feeds);
		return $e;
	}

	function getUUID(){
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
	        mt_rand( 0, 0x0fff ) | 0x4000,
	        mt_rand( 0, 0x3fff ) | 0x8000,
	        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ));
	}

	function getNewID($length = 36) {
		//return getUUID();
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }
	    return $randomString;
	}

	function userExists($user){
		global $con;
		$sql = "SELECT count(*) FROM users WHERE username=?";
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,$sql)){
			mysqli_stmt_bind_param($stmt,"s", $user); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$count); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value

			mysqli_stmt_close($stmt); // Close statement
		}
		return ($count >= 1);
	}

	function checkUserPassword($user,$pass){
		global $con;
		$sql = "SELECT username,fav_color FROM users WHERE username=? AND password=?";
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"ss", $user, $pass); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$username,$color); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value

			mysqli_stmt_close($stmt); // Close statement
		}
		if(isset($username))
			return array('user'=>$username,'color'=>$color);
		return false;
	}

	function checkFolderAccess($user,$idFolder){
		global $con;
		$sql = "SELECT user FROM folders WHERE id=?";
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"s", $idFolder); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$username); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value

			mysqli_stmt_close($stmt); // Close statement
		}
		return (isset($username) && $username == $user);
	}

	function checkFeedAccess($user,$idFeed){
		global $con;
		$sql = "SELECT fo.user FROM feeds f JOIN folders fo ON f.id_folder=fo.id WHERE f.id=?";
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"s", $idFeed); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$username); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value

			mysqli_stmt_close($stmt); // Close statement
		}
		return (isset($username) && $username == $user);
	}

	function isTagPublic($idTag){
		global $con;
		$sql = "SELECT public FROM tags WHERE id=?";
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"s", $idTag); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$ppcc); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value

			mysqli_stmt_close($stmt); // Close statement
		}
		return (isset($ppcc) && $ppcc == 1);
	}

	function checkTagAccess($user,$idTag){
		global $con;
		$sql = "SELECT user FROM tags WHERE id=?";
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"s", $idTag); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$username); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value

			mysqli_stmt_close($stmt); // Close statement
		}
		return (isset($username) && $username == $user);
	}

	function checkUserHiddenPassword($user,$pass){
		global $con;
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

	function getPostTags($postid, $hidden){
		global $con;

		$hiddenSQL = ($hidden)? "" : "AND t.hidden='0'";
		$sql = "SELECT id,tag_name FROM tags t JOIN post_tags pt ON pt.id_tag=t.id WHERE pt.id_post='$postid' $hiddenSQL";

		$tags = mysqli_query($con,$sql);
		$lista = array();

		while($tag = @array_map('utf8_encode',mysqli_fetch_assoc($tags))) {
			$lista[] = array("id" => $tag['id'], "name" => $tag['tag_name']);
		}
		mysqli_free_result($tags);
		return $lista;
	}

	function getPost($postId,$hidden){
		global $con;
		$postId = mysqli_real_escape_string($con,$postId);
		$posts = mysqli_query($con,"SELECT * FROM posts WHERE id='$postId'");

		$e = new Post();

		if ($post = @array_map('utf8_encode',mysqli_fetch_assoc($posts))) {
			$e->id = $post['id'];
			$e->idx = $post['idx'];
			$e->feedId = $post['id_feed'];

			$e->title = $post['title'];
			$e->description = $post['description'];
			$e->link = $post['link'];
			$e->unread = $post['unread'];
			$e->favorite = $post['favorite'];
			$e->date = $post['date'];

			$e->tags = getPostTags($e->id, $hidden);
		}
		mysqli_free_result($posts);
		return $e;
	}


	function havePostAccess($user,$hidden,$postid){
		global $con;
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,"SELECT u.username,f.hidden FROM users u
										JOIN folders f ON f.user=u.username
										JOIN feeds fe ON fe.id_folder=f.id
										JOIN posts p ON p.id_feed=fe.id WHERE p.id=?")){

			mysqli_stmt_bind_param($stmt,"s", $postid); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query
			mysqli_stmt_bind_result($stmt,$username,$hid); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value
		}
		return $hid<=$hidden && $username==$user;
	}

	function searchQueryFromArray($con, $search){
		if (count($search)<1)
			return "";
		$str = "AND (";
		$first = true;
		foreach ($search as $key){
			if ($first) $first = false;
			else 		$str.= " OR ";
			$val = mysqli_real_escape_string($con,"%$key%");
			$str.= "title LIKE '$val' OR description LIKE '$val'";
		}
		$str.= ")";
		return $str;
	}

 	/**

			GET POSTS FUNCTIONS

 	*/

	function getPostsFeed($user, $feedId, $favs, $unread, $sort, $page, $postspage, $searchArr){
		global $con;
		$user = mysqli_real_escape_string($con,$user);
		$feedId = mysqli_real_escape_string($con,$feedId);
		$sort = mysqli_real_escape_string($con,$sort);
		$page = mysqli_real_escape_string($con,$page);

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";
		$filterSQL = searchQueryFromArray($con, $searchArr);

		$whereSQL = "id_feed='$feedId' $filterSQL $unreadSQL $favsSQL";

		$posts = getPosts($whereSQL, $sort, -1, $page, $postspage);
		$count = getPostsCount($whereSQL);
		$posts["total"] = $count;
		return $posts;
	}

	function getPostsFolder($user, $folderId, $favs, $unread, $sort, $page, $postspage, $searchArr){
		global $con;
		global $hidden;
		$user = mysqli_real_escape_string($con,$user);
		$folderId = mysqli_real_escape_string($con,$folderId);
		$sort = mysqli_real_escape_string($con,$sort);
		$page = mysqli_real_escape_string($con,$page);

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";
		$filterSQL = searchQueryFromArray($con, $searchArr);
		$hiddenSQL = ($hidden)? "WHERE" : "JOIN folders fo ON f.id_folder=fo.id WHERE fo.hidden='0' AND";

		$whereSQL = "id_feed IN (SELECT f.id FROM feeds f $hiddenSQL f.id_folder='$folderId') $filterSQL $unreadSQL $favsSQL";

		$posts = getPosts($whereSQL, $sort, -1, $page, $postspage);
		$count = getPostsCount($whereSQL);
		$posts["total"] = $count;
		return $posts;
	}

	function getPostsTag($tagId, $favs, $unread, $sort, $page, $postspage, $searchArr){
		global $con;
		global $hidden;
		$tagId = mysqli_real_escape_string($con,$tagId);
		$sort = mysqli_real_escape_string($con,$sort);
		$page = mysqli_real_escape_string($con,$page);

		// uncomment to don't use favorites and unread vars
		//$sql = "SELECT * FROM posts WHERE id IN (SELECT id_post FROM post_tags WHERE id_tag='$tagId') ORDER BY `idx` $sort LIMIT $page,$postspage";
		//$countsql = "SELECT count(*) AS c FROM post_tags WHERE id_tag='$tagId'";

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";
		$filterSQL = searchQueryFromArray($con, $searchArr);
		$hiddenSQL = ($hidden)? "WHERE" : "JOIN tags t ON t.id=pt.id_tag WHERE t.hidden='0' AND";

		$whereSQL = "id IN (SELECT pt.id_post FROM post_tags pt $hiddenSQL pt.id_tag='$tagId') $filterSQL $unreadSQL $favsSQL";

		$posts = getPosts($whereSQL, $sort, -1, $page, $postspage);
		$count = getPostsCount($whereSQL);
		$posts["total"] = $count;
		return $posts;
	}

	function getPostsAll($user, $favs, $unread, $sort, $page, $postspage, $searchArr){
		global $con;
		global $hidden;
		$user = mysqli_real_escape_string($con,$user);
		$favs = mysqli_real_escape_string($con,$favs);
		$unread = mysqli_real_escape_string($con,$unread);
		$sort = mysqli_real_escape_string($con,$sort);
		$page = mysqli_real_escape_string($con,$page);

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";
		$hiddenSQL = ($hidden)? "" : "AND hidden='0'";
		$filterSQL = searchQueryFromArray($con, $searchArr);

		$whereSQL = "id_feed IN (SELECT id FROM feeds WHERE id_folder IN (".
						"SELECT id FROM folders WHERE user='$user' $hiddenSQL)) $filterSQL $unreadSQL $favsSQL";

		$posts = getPosts($whereSQL, $sort, -1, $page, $postspage);
		$count = getPostsCount($whereSQL);
		$posts["total"] = $count;
		return $posts;
	}


 	/**

			GET MORE POSTS FUNCTIONS

 	*/
//CREATE TEMPORARY TABLE IF NOT EXISTS tt AS (SELECT (@cnt:=@cnt+1) AS idx, p.* FROM posts AS p CROSS JOIN (SELECT @cnt := 0) AS x ORDER BY p.date);
//SELECT id FROM tt s WHERE idx >= (SELECT idx FROM s WHERE id="50CBFC56-4E29-4519-A649-8D5CDF7ACF15") LIMIT 0,5;

	function getPostsNextFeed($user, $feedId, $favs, $unread, $sort, $postspage, $nextId, $searchArr){
		global $con;
		$user = mysqli_real_escape_string($con,$user);
		$feedId = mysqli_real_escape_string($con,$feedId);
		$sort = mysqli_real_escape_string($con,$sort);
		$postspage = mysqli_real_escape_string($con,$postspage);
		$nextId = mysqli_real_escape_string($con,$nextId);

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";
		$filterSQL = searchQueryFromArray($con, $searchArr);

		$whereSQL = "id_feed='$feedId' $filterSQL $unreadSQL $favsSQL";

		return getPosts($whereSQL, $sort, $nextId, 0, $postspage);
	}

	function getPostsNextFolder($user, $folderId, $favs, $unread, $sort, $postspage, $nextId, $searchArr){
		global $con;
		global $hidden;
		$user = mysqli_real_escape_string($con,$user);
		$folderId = mysqli_real_escape_string($con,$folderId);
		$sort = mysqli_real_escape_string($con,$sort);
		$postspage = mysqli_real_escape_string($con,$postspage);
		$nextId = mysqli_real_escape_string($con,$nextId);

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";
		$filterSQL = searchQueryFromArray($con, $searchArr);
		$hiddenSQL = ($hidden)? "WHERE" : "JOIN folders fo ON f.id_folder=fo.id WHERE fo.hidden='0' AND";

		$whereSQL = "id_feed IN (SELECT f.id FROM feeds f $hiddenSQL f.id_folder='$folderId') $filterSQL $unreadSQL $favsSQL";

		return getPosts($whereSQL, $sort, $nextId, 0, $postspage);
	}

	function getPostsNextTag($tagId, $favs, $unread, $sort, $postspage, $nextId, $searchArr){
		global $con;
		global $hidden;
		$tagId = mysqli_real_escape_string($con,$tagId);
		$sort = mysqli_real_escape_string($con,$sort);
		$postspage = mysqli_real_escape_string($con,$postspage);
		$nextId = mysqli_real_escape_string($con,$nextId);

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";
		$filterSQL = searchQueryFromArray($con, $searchArr);
		$hiddenSQL = ($hidden)? "WHERE" : "JOIN tags t ON t.id=pt.id_tag WHERE t.hidden='0' AND";

		$whereSQL = "id IN (SELECT pt.id_post FROM post_tags pt $hiddenSQL pt.id_tag='$tagId') $filterSQL $unreadSQL $favsSQL";

		return getPosts($whereSQL, $sort, $nextId, 0, $postspage);
	}

	function getPostsNextAll($user, $favs, $unread, $sort, $postspage, $nextId, $searchArr){
		global $con;
		global $hidden;
		$user = mysqli_real_escape_string($con,$user);
		$favs = mysqli_real_escape_string($con,$favs);
		$unread = mysqli_real_escape_string($con,$unread);
		$sort = mysqli_real_escape_string($con,$sort);
		$nextId = mysqli_real_escape_string($con,$nextId);

		$favsSQL = ($favs==1)? "AND favorite='1'" : "";
		$unreadSQL = ($unread==1)? "AND unread='1'" : "";
		$hiddenSQL = ($hidden)? "" : "AND hidden='0'";
		$filterSQL = searchQueryFromArray($con, $searchArr);

		$whereSQL = "id_feed IN (SELECT id FROM feeds WHERE id_folder IN (".
						"SELECT id FROM folders WHERE user='$user' $hiddenSQL)) $filterSQL $unreadSQL $favsSQL";

		return getPosts($whereSQL, $sort, $nextId, 0, $postspage);
	}

	function getPosts($whereSQL, $sort, $nextId, $page, $postspage){
		global $con;
		global $hidden;
		if ($nextId == -1){
			$sql = "SELECT * FROM posts WHERE $whereSQL ORDER BY `idx` $sort LIMIT $page,$postspage";
		} else {
			$threshold = "(SELECT idx FROM posts WHERE id='$nextId')";
			if ($sort == "ASC")
				$sql = "SELECT * FROM posts WHERE idx > $threshold AND $whereSQL ORDER BY `idx` ASC LIMIT 0,$postspage";
			else
				$sql = "SELECT * FROM posts WHERE idx < $threshold AND $whereSQL ORDER BY `idx` DESC LIMIT 0,$postspage";
		}
		$posts = mysqli_query($con,$sql);
		$lista = array();

		while($post = @array_map('utf8_encode',mysqli_fetch_assoc($posts))) {
			$e = new Post();
			$e->id = $post['id'];
			$e->idx = $post['idx'];
			$e->feedId = $post['id_feed'];

			$e->title = $post['title'];
			$e->description = $post['description'];
			$e->link = $post['link'];
			$e->unread = $post['unread'];
			$e->favorite = $post['favorite'];
			$e->date = $post['date'];

			$e->tags = getPostTags($e->id, $hidden);
			$lista[] = $e;
		}
		mysqli_free_result($posts);

		$data = array("posts" => $lista);
		return $data;
	}

	function getPostsCount($whereSQL){
		global $con;
		$countsql = "SELECT count(*) AS c FROM posts WHERE $whereSQL";
		$result = mysqli_query($con,$countsql);
		$rows = mysqli_fetch_assoc($result);

		$count = $rows['c'];
		mysqli_free_result($result);
		return $count;
	}

 	/**

			API 1.0

 	*/

	function getTokenUsername($token){
		global $con;
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,"SELECT user FROM active_user WHERE token=?")){

			mysqli_stmt_bind_param($stmt,"s", $token); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$user); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value
		}
		mysqli_stmt_close($stmt); // Close statement
		return isset($user)? $user : false;
	}

	function getTokenLock($token,$lock){
		global $con;
		$stmt=mysqli_stmt_init($con);
		if (mysqli_stmt_prepare($stmt,"SELECT count(*) FROM active_user WHERE token=? AND `lock`=?")){
			mysqli_stmt_bind_param($stmt,"ss", $token, $lock); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			mysqli_stmt_bind_result($stmt,$count); // Bind result variables
			mysqli_stmt_fetch($stmt); // Fetch value
		}
		mysqli_stmt_close($stmt); // Close statement
		return isset($count)? ($count==1) : false;
	}
?>
