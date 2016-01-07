<?php
	require_once "FeedReader.php";
	require_once "functions.php";
	require_once "PluginManager.php";

	class PostsFetch{
		private $con;
		private $fr;
		private $pm;

		public function __construct(){
			$this->fr = new FeedReader();
			$this->pm = new PluginManager();
		}

		public function initConnection(){
		    $this->con = mysqli_connect("localhost","fydep_u","4syouwI5h","fydepdb");
		    if (mysqli_connect_errno())
		      die("Failed to connect to MySQL: " . mysqli_connect_error());

			//mysqli_query($this->con,"DELETE FROM posts");
			//mysqli_query($this->con,"UPDATE feeds SET last_date_post='2000-01-01 00:00:00'");
		}

		public function setConnection($con){
			$this->con = $con;
		}

		public function __destruct() {
			mysqli_close($this->con);
		}

		public function fetchAll(){
			$sql = "SELECT * FROM feeds";
			$feeds = mysqli_query($this->con,$sql);
			foreach($feeds as $feed){
				$this->fetchFeed($feed);
			}
			mysqli_free_result($feeds);
		}

		public function fetchFeed($feed){
			$feed = array_map('utf8_encode',$feed);

			$link = $feed['rss_link'];

			$this->fr->setUrl($link);

			try {
				$posts = $this->fr->getFeeds();
				$posts = array_reverse($posts);
			} catch (Exception $e) {
			    echo date('Y-d-m H:i:s', time()).": ".$e->getMessage()."\n";
			    file_put_contents ("log.txt", date('Y-d-m H:i:s', time()).": ".$e->getMessage()."\n",FILE_APPEND);
			    return;
			}

			$feedDate = new DateTime($feed['last_date_post']);
			$mostRecentDate = $feedDate;
			$count = 0;
			$updatedate = true;
			foreach($posts as $post)
			{
				$errval = $this->addIfPosible($feed,$post);
				if ($errval == -1)
					$updatedate = false;
				else
					$count += $errval;		
				$date = new DateTime($post->date);
				if ($mostRecentDate < $date) $mostRecentDate = $date;
			}
			if ($updatedate && $mostRecentDate != $feedDate)
			{
				$dateChangeSql = "UPDATE feeds SET last_date_post='";
				$dateChangeSql.= date("Y-m-d H:i:s", $mostRecentDate->format('U'))."' WHERE id='";
				$dateChangeSql.= $feed['id']."'";
				mysqli_query($this->con,$dateChangeSql);
			}
			if ($count>0)
				echo date('Y-d-m H:i:s', time()).": $count post".(($count>1)?"s":"")." added - ".$feed['rss_link']."\n";
			$this->updateDeleteCounter($posts,$feed['id']);
		}

		private function addIfPosible($feed,$post){
			if (!$post->postId)	$post->postId = $this->createId($post,$feed);

			if ($post->date_updated){
				$sql = "SELECT id,updated,description FROM posts WHERE id_feed=? AND post_id=? AND updated<?";
				$stmt=mysqli_stmt_init($this->con);
				if (mysqli_stmt_prepare($stmt,$sql)){
					mysqli_stmt_bind_param($stmt,"sss", $feed['id'], $post->postId, $post->date_updated);
					mysqli_stmt_execute($stmt);
					//$done = mysqli_affected_rows($this->con);
					mysqli_stmt_bind_result($stmt,$id,$updated,$desc);
					mysqli_stmt_fetch($stmt);
				}
				if ($id){
					if ($post->description==""){
						$post->description=$desc;
					}
					$sql = "UPDATE posts SET title=?,description=?,updated=? WHERE id=?";
					if (mysqli_stmt_prepare($stmt,$sql)){
						mysqli_stmt_bind_param($stmt,"ssss",$post->title,$post->description,$post->date_updated,$id);
						mysqli_stmt_execute($stmt);
						$done = mysqli_affected_rows($this->con);
					}
					if ($done == 1){
						//file_put_contents("log.txt", date('Y-d-m H:i:s', time())." Updated post: $feed[name] - $id\n",FILE_APPEND);
					} else {
						file_put_contents("log.txt", date('Y-d-m H:i:s', time())." Error Updating $feed[name] - $id\n",FILE_APPEND);
						file_put_contents("log.txt", "# Title: ".$post->title."\n",FILE_APPEND);
						file_put_contents("log.txt", "# Descr: ".$post->description."\n",FILE_APPEND);
						file_put_contents("log.txt", "# Dateu: ".$post->date_updated."\n",FILE_APPEND);
					}
					return 0;
				}
			}

			//$last_date_post=new DateTime($feed['last_date_post']);
			//if (new DateTime($post->date) <= $last_date_post)
			//	return 0;

			$sql = "SELECT id FROM posts WHERE post_id=? AND id_feed=?";
			$stmt=mysqli_stmt_init($this->con);
			if (mysqli_stmt_prepare($stmt,$sql)){
				mysqli_stmt_bind_param($stmt,"ss", $post->postId, $feed['id']);
				mysqli_stmt_execute($stmt);
				//$done = mysqli_affected_rows($this->con);
				mysqli_stmt_bind_result($stmt,$id);
				mysqli_stmt_fetch($stmt);
			}
			if ($id){
				//file_put_contents("log.txt", date('Y-d-m H:i:s', time())." ID_feed & Link already exists, not adding again: $feed[name] - $id\n",FILE_APPEND);
				return 0;
			}
			$count =0;
			$this->pm->pluginCheck($post);
			repeat:
			//$nid = getNewID();
			$sql = "INSERT INTO posts(id,id_feed,title,description,link,unread,favorite,date,post_id,updated) VALUES(newID(36,\"posts\"),?,?,?,?,'1','0',?,?,?)";
			$stmt=mysqli_stmt_init($this->con);
			$done = 0;
			if (mysqli_stmt_prepare($stmt,$sql)){
				mysqli_stmt_bind_param($stmt,"sssssss",$feed['id'],$post->title,$post->description,$post->link,$post->date,$post->postId,$post->date_updated);
				mysqli_stmt_execute($stmt);
				$done = mysqli_affected_rows($this->con);
			}
			if ($done == 1){
				mysqli_stmt_close($stmt);
				return 1;
			} else {
				if ($count < 3){
					$count++;
					echo "Failed adding 1 post. Counter $count\n";
					goto repeat;
				}
				file_put_contents ("log.txt", date('Y-d-m H:i:s', time())."\n",FILE_APPEND);
				file_put_contents ("log.txt", "Done value: $done (!= 1)\n",FILE_APPEND);
				file_put_contents ("log.txt", "FeedId: ".$feed['id']."\n",FILE_APPEND);
				file_put_contents ("log.txt", print_r($post, true)."\n",FILE_APPEND);
				file_put_contents ("log.txt", "$sql\n",FILE_APPEND);
				file_put_contents ("log.txt", mysqli_error($this->con)."\n",FILE_APPEND);
				file_put_contents ("log.txt", "###############################################\n",FILE_APPEND);
				mysqli_stmt_close($stmt);
				//die();
				return -1;
			}

			mysqli_stmt_close($stmt);
			return 0;
		}

		public function markUnread($feed){
			$feedid = $feed['id'];

			//Text Filter
			if ($feed['text_filter']!=""){
				$search = explode("::",$feed['text_filter']);

				$where = searchQueryFromArray($this->con,$search);
				if ($where!=""){
					$where = "AND NOT".substr($where,3);
					$sql = "UPDATE posts SET unread='0' WHERE id_feed=? $where";
					$stmt=mysqli_stmt_init($this->con);
					if (mysqli_stmt_prepare($stmt,$sql)){
						mysqli_stmt_bind_param($stmt,"s", $feedid);
						mysqli_stmt_execute($stmt);
						//$done = mysqli_affected_rows($this->con);
						//mysqli_stmt_bind_result($stmt,$idxLimit);
						//mysqli_stmt_fetch($stmt);
					}
				}
			}

			//Max Limit
			$sql = "SELECT idx FROM posts WHERE id_feed=? AND unread='1' ORDER BY idx DESC LIMIT ".$feed['max_unread'].",1";
			$stmt=mysqli_stmt_init($this->con);
			if (mysqli_stmt_prepare($stmt,$sql)){
				mysqli_stmt_bind_param($stmt,"s", $feedid);
				mysqli_stmt_execute($stmt);
				//$done = mysqli_affected_rows($this->con);
				mysqli_stmt_bind_result($stmt,$idxLimit);
				mysqli_stmt_fetch($stmt);
			}
			if (isset($idxLimit)){
				$sql = "UPDATE posts SET unread='0' WHERE id_feed=? AND unread='1' AND idx <= ?";
				$stmt=mysqli_stmt_init($this->con);
				if (mysqli_stmt_prepare($stmt,$sql)){
					mysqli_stmt_bind_param($stmt,"ss", $feedid, $idxLimit);
					mysqli_stmt_execute($stmt);
					$done = mysqli_affected_rows($this->con);
					//mysqli_stmt_bind_result($stmt,$idxLimit);
					mysqli_stmt_fetch($stmt);
				}
			}
			return isset($done)?$done:0;
		}

		private function createId($p,$f){
			//$id = hash("crc32", $p->date);
			$id = hash("haval192,3", $p->link);
			$id.= hash("crc32", $f['id'].$f['id']);
			return $id;
		}

		private function updateDeleteCounter($posts,$feed_id){
			if (count($posts)<=0) return;
			$ids="(";
			foreach($posts as $p){
				if ($ids!="(") $ids.=",";
				$ids.="'".$p->postId."'";
			}
			$ids.=")";

			$sql = "UPDATE posts SET deleted=deleted+1 WHERE deleted>0 AND id_feed=? AND post_id NOT IN $ids";
			$stmt=mysqli_stmt_init($this->con);
			if (mysqli_stmt_prepare($stmt,$sql)){
				mysqli_stmt_bind_param($stmt,"s", $feed_id);
				mysqli_stmt_execute($stmt);
				//$done = mysqli_affected_rows($this->con);
				//mysqli_stmt_bind_result($stmt,$idxLimit);
				mysqli_stmt_fetch($stmt);
			}
		}

		public function finalDelete(){
			$sql = "DELETE FROM posts WHERE deleted>=2880";
			$stmt=mysqli_stmt_init($this->con);
			if (mysqli_stmt_prepare($stmt,$sql)){
				mysqli_stmt_execute($stmt);
				//$done = mysqli_affected_rows($this->con);
				//mysqli_stmt_bind_result($stmt,$idxLimit);
				mysqli_stmt_fetch($stmt);
			}
		}
	}
?>
