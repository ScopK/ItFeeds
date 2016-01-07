<?php 
	$html = file_get_contents($link);

	preg_match('/<div class="bodytype">.*?(<img.*?>)/',$html,$matches);

	if (count($matches)>1){
		echo $matches[1];
	}
?>
