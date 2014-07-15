<?php
	require_once "FeedReader.php";

	$pf = new PostsFetch();
	$pf->fetchAll();

	class PostsFetch{
		private $con;

		public function __construct() {
		    $this->con = mysqli_connect("localhost","root","admin","fydepdb");
		    if (mysqli_connect_errno())
		      die("Failed to connect to MySQL: " . mysqli_connect_error());

			mysqli_query($this->con,"SET NAMES utf8");
			mysqli_set_charset($this->con,'utf8');

			//mysqli_query($this->con,"DELETE FROM posts");
			//mysqli_query($this->con,"UPDATE feeds SET last_date_post='2000-01-01 00:00:00'");
		}

		public function __destruct() {
			mysqli_close($this->con);
		}

		public function fetchAll(){
			$fr = new FeedReader();
			
			$sql = "SELECT * FROM feeds";
			$feeds = mysqli_query($this->con,$sql);
			foreach($feeds as $feed){
				$feed = array_map('utf8_encode',$feed);

				$link = $feed['rss_link'];

				$fr->setUrl($link);

				try {
					$posts = $fr->getFeeds();
				} catch (Exception $e) {
				    echo $e->getMessage();
				    continue;
				}

				$feedDate = new DateTime($feed['last_date_post']);
				$mostRecentDate = $feedDate;
				foreach($posts as $post)
				{
					$this->addIfPosible($feed,$post);			
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

				
			}
			mysqli_free_result($feeds);
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
				$sql = "INSERT INTO posts VALUES(UUID(),?,?,?,?,?,?,?)";
				$stmt=mysqli_stmt_init($this->con);
				if (mysqli_stmt_prepare($stmt,$sql)){
					mysqli_stmt_bind_param($stmt,"sssssss",$feed['id'],$post->title,$post->description,$post->link,$post->unread,$post->favorite,$post->date);
					mysqli_stmt_execute($stmt);
					$done = mysqli_affected_rows($this->con);
				}
				if ($done != 1){
					echo "Error\n";
					echo $feed['id'].",".$post->title.",".$post->link.",".$post->unread.",".$post->favorite.",".$post->date;
					echo "\n";
					echo $post->description;
					echo "\n";
				}
			}
			mysqli_stmt_close($stmt);
		}

	}

?>