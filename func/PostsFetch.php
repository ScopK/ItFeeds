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
		    $this->con = mysqli_connect("localhost","root","admin","fydepdb");
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
			} catch (Exception $e) {
			    echo $e->getMessage();
			    return;
			}

			$feedDate = new DateTime($feed['last_date_post']);
			$mostRecentDate = $feedDate;
			$count = 0;
			foreach($posts as $post)
			{
				$count += $this->addIfPosible($feed,$post);			
				$date = new DateTime($post->date);
				if ($mostRecentDate < $date) $mostRecentDate = $date;
			}
			if ($mostRecentDate != $feedDate)
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
				$sql = "INSERT INTO posts VALUES('$nid',?,?,?,?,?,?,?)";
				$stmt=mysqli_stmt_init($this->con);
				$done = 0;
				if (mysqli_stmt_prepare($stmt,$sql)){
					mysqli_stmt_bind_param($stmt,"sssssss",$feed['id'],$post->title,$post->description,$post->link,$post->unread,$post->favorite,$post->date);
					mysqli_stmt_execute($stmt);
					$done = mysqli_affected_rows($this->con);
				}
				if ($done == 1){
					mysqli_stmt_close($stmt);
					return 1;
				} else {
					echo "Error\n";
					echo $feed['id'].",".$post->title.",".$post->link.",".$post->unread.",".$post->favorite.",".$post->date;
					echo "\n";
					echo $post->description;
					echo "\n";
				}
			}
			mysqli_stmt_close($stmt);
			return 0;
		}

	}

?>