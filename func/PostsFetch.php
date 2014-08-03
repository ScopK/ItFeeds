<?php
	require_once "FeedReader.php";
	require_once "functions.php";

	class PostsFetch{
		private $con;
		private $fr;

		public function __construct(){
			$this->fr = new FeedReader();
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
			    echo $e->getMessage();
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
				echo date('Y-d-m H:i:s', time()).": $count added to:".$feed['rss_link']."\n";
		}

		private function addIfPosible($feed,$post){
			if (new DateTime($post->date) <= new DateTime($feed['last_date_post']))
				return;

			$sql = "SELECT id,title,description FROM posts WHERE id_feed=? AND link=?";
			$stmt=mysqli_stmt_init($this->con);
			if (mysqli_stmt_prepare($stmt,$sql)){
				mysqli_stmt_bind_param($stmt,"ss", $feed['id'], $post->link);
				mysqli_stmt_execute($stmt);
				//$done = mysqli_affected_rows($this->con);
				mysqli_stmt_bind_result($stmt,$id,$title,$description);
				mysqli_stmt_fetch($stmt);
			}
			if ($id){
				/*
				if ($description != $post->description) {
					$sql = "UPDATE posts SET title=?,description=? WHERE id=?";
					if (mysqli_stmt_prepare($stmt,$sql)){
						mysqli_stmt_bind_param($stmt,"sss",$post->title,$post->description,$id);
						mysqli_stmt_execute($stmt);
						$done = mysqli_affected_rows($this->con);
					}
					if ($done == 1)
						echo "Updated\n";
				}*/
			} else {
				$nid = getNewID();
				$sql = "INSERT INTO posts(id,id_feed,title,description,link,unread,favorite,date) VALUES('$nid',?,?,?,?,'1','0',?)";
				$stmt=mysqli_stmt_init($this->con);
				$done = 0;
				if (mysqli_stmt_prepare($stmt,$sql)){
					mysqli_stmt_bind_param($stmt,"sssss",$feed['id'],$post->title,$post->description,$post->link,$post->date);
					mysqli_stmt_execute($stmt);
					$done = mysqli_affected_rows($this->con);
				}
				if ($done == 1){
					mysqli_stmt_close($stmt);
					return 1;
				} else {
					echo "Error\n";
					echo $feed['id']."\n".$post->title."\n".$post->link."\n".$post->date;
					echo "\n";
					echo $post->description;
					echo "\n";
					echo "$sql\n";
					mysqli_stmt_close($stmt);
					return -1;
				}
			}
			mysqli_stmt_close($stmt);
			return 0;
		}

	}

?>