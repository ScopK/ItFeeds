<?php
	header("content-type: text/css");
	$color = isset($_GET['color'])?"#".$_GET['color']:"#3386C6";

	$hsv = hex2hsv($color);
	$hsv[1] = 100; $hsv[2]=94;
	if ($hsv[0]>50 && 200>$hsv[0]){
		$hsv[2]=85;
	}
	$dark = hsv2hex($hsv);
	//$hsv[0] += 15;
	$hsv[1] = 29; $hsv[2]=100;
	if ($hsv[0]>50 && 200>$hsv[0]){
		$hsv[1]=33;
		$hsv[2]=95;
	}
	$bright = hsv2hex($hsv);
?>
/* COMMON */

body {
    font-size: small;
    font-family: "Segoe UI",dejavu sans,"Verdana","Trebuchet MS",Tahoma,sans-serif;
    margin: 0;
    background-color: #ddd;
    overflow-y: auto;
}

:focus {outline:none;}
::-moz-focus-inner {border:0;}

#page{
	width:1150px;
	margin:10px auto;
	background-color:white;
	box-shadow: 0 0 15px -10px black;
	position:relative;
	min-height: calc(100vh - 20px);
	transition: all 0.3s;
}
#profile{
	position:absolute;
	background-color:<?= $dark ?>;
	width: 250px;
	bottom:0; top:0;
	box-shadow: 12px 0 15px -15px <?= $dark ?>;
	transition: all 0.3s;
	z-index:1;
}
#post{
	margin-left:250px;
	background-color: white;
	transition: all 0.3s;
}
#title-bar{
	height:50px;
	background-color: #eee;
}
#title-bar a {
	text-decoration:none;
}
#title-bar a:hover {
	text-decoration:underline;
}
#feedname{
	display:table-cell;
	vertical-align:middle;
	background-color: <?= $dark ?>;
	height:50px;
	padding:0 10px;
	font-size:1.0em;
	font-weight:bold;
	cursor:pointer;
}
#feedname a {
	display:table-cell;
	vertical-align:middle;
	color:white;
}
#feedname:hover a {
	text-decoration:underline;
}
#postinfo{
	display:table-cell;
	vertical-align:middle;
	white-space:nowrap;
	height:50px;
	padding:0 10px;
}
#postinfo span {
	vertical-align:middle;
}
#postinfo a{
	display:block;
	color:black;
}
#description{
	padding: 15px 10px 55px;
	min-width: 500px;
	min-height:100%;
	
	overflow-x:auto;
	transition: all 0.3s;
}

#see_more{
	/*width:100%;*/
	background-color: #f0f0f0;
	display:block;
	text-align:center;
	padding: 10px 0;
	text-decoration: none;
	color:#555;
	transition: all 0.3s;

	position:absolute;
	bottom:0;
	right:0;left:250px;
}

#see_more:hover{
	background-color: #e0e0e0;
	color:black;
}

#page.noprofile{
	width:900px;
}
.noprofile #profile{
	width: 0px;
}
.noprofile #post{
	margin-left: 0px;
}
.noprofile #see_more{
	left: 0;
}


@media screen and (max-width: 1200px) {
	#page:not(.noprofile){
		width:100%;
		margin: 0 auto 0;
		min-height: 100vh;
	}
}

@media screen and (max-width: 950px) {
	#page.noprofile{
		width:100%;
		margin: 0 auto 0;
		min-height: 100vh;
	}
}

<?php
function hsv2rgb($hsv) {
    $H = $hsv[0]/360.;
    $S = $hsv[1]/100.;
    $V = $hsv[2]/100.;
    $H *= 6;
    $I = floor($H);
    $F = $H - $I;
    $M = $V * (1 - $S);
    $N = $V * (1 - $S * $F);
    $K = $V * (1 - $S * (1 - $F));
    switch ($I) {
        case 0:
            list($R,$G,$B) = array($V,$K,$M);
            break;
        case 1:
            list($R,$G,$B) = array($N,$V,$M);
            break;
        case 2:
            list($R,$G,$B) = array($M,$V,$K);
            break;
        case 3:
            list($R,$G,$B) = array($M,$N,$V);
            break;
        case 4:
            list($R,$G,$B) = array($K,$M,$V);
            break;
        case 5:
        case 6: //for when $H=1 is given
            list($R,$G,$B) = array($V,$M,$N);
            break;
    }
    return array(round($R*255), round($G*255), round($B*255));
}
function rgb2hsv($rgb){
    $R = ($rgb[0] / 255.);
    $G = ($rgb[1] / 255.);
    $B = ($rgb[2] / 255.);
    $maxRGB = max($R, $G, $B);
    $minRGB = min($R, $G, $B);
    $chroma = $maxRGB - $minRGB;
    $computedV = 100 * $maxRGB;
    if ($chroma == 0)
        return array(0, 0, $computedV);
    $computedS = 100 * ($chroma / $maxRGB);
    if ($R == $minRGB)
        $h = 3 - (($G - $B) / $chroma);
    elseif ($B == $minRGB)
        $h = 1 - (($R - $G) / $chroma);
    else // $G == $minRGB
        $h = 5 - (($B - $R) / $chroma);
    $computedH = 60 * $h;
    return array($computedH, $computedS, $computedV);
}
function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return $rgb;
}
function rgb2hex($rgb) {
   $hex = "#";
   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
   return $hex;
}
function hex2hsv($hex){
	return rgb2hsv(hex2rgb($hex));
}
function hsv2hex($hsv){
	if ($hsv[0]>360) $hsv[0] = 360;
	if ($hsv[1]>100) $hsv[1] = 100;
	if ($hsv[2]>100) $hsv[2] = 100;

	if ($hsv[0]<0) $hsv[0] = 0;
	if ($hsv[1]<0) $hsv[1] = 0;
	if ($hsv[2]<0) $hsv[2] = 0;
	return rgb2hex(hsv2rgb($hsv));
}
?>