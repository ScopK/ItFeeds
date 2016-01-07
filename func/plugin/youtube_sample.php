<?php 
	$info = $this->youtubeapi->getVideoSnippet($code);
	if ($info!==false){
	$snippet = $info->snippet;
	$duration = $info->contentDetails->duration;

	preg_match('/(\d*)S/',$duration,$matches);
	$ss = count($matches)==0? "00" :($matches[1]<10? "0".$matches[1] : $matches[1]);
	preg_match('/(\d*)M/',$duration,$matches);
	$mm = count($matches)==0? "0" : $matches[1];
	preg_match('/(\d*)H/',$duration,$matches);
	$hh = count($matches)==0? "0" : $matches[1];

	if ($hh==0){
		$duration = "$mm:$ss";
	} else {
		if ($mm<10){
			$mm = "0".$mm;
		}
		$duration = "$hh:$mm:$ss";
	}

	$description = $snippet->description;
	$arr = $this->split2($description,"\n",10);
	if ($arr[0]!=$description){
		$description = $arr[0];
		$description.="\n...";
	}
?>
<table style='max-width:900px; font-family:Arial,Helvetica,sans-serif'>
	<tr>
		<td rowspan="4" style='vertical-align:top'>
			<a target="_blank" href="<?php echo $post->link ?>"><img style='width:300px;border:0px' src="http://i.ytimg.com/vi/<?php echo $code ?>/0.jpg"/></a>
		</td>
		<td style='padding:0 5px; height:1px; font-size:14px'>
			<b><a target="_blank" href="<?php echo $post->link ?>"><?php echo mb_convert_encoding($snippet->title, "HTML-ENTITIES" ,"utf-8") ?></a></b>
		</td>
	</tr>
	<tr>
		<td style='padding:0 5px 10px; height:1px; font-size:11px'>
			<span style='color:#666'>From:</span> <a target="_blank" href="https://www.youtube.com/channel/<?php echo $snippet->channelId ?>"><?php echo utf8_decode($snippet->channelTitle) ?></a>
		</td>
	</tr>
	<tr>
		<td style='padding:10px 5px; vertical-align:top; font-size:12px'><?php echo mb_convert_encoding(str_replace("\n","<br/>",$description), "HTML-ENTITIES" ,"utf-8") ?></td>
	</tr>

	<tr>
		<td style='padding:10px 5px 5px; height:1px; font-size:11px'>
			<span style='color:#666'>Time:</span> <b><?php echo $duration ?></b>
		</td>
	</tr>
</table>
<?php } ?>
