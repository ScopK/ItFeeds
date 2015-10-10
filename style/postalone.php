<?php
	header("content-type: text/css");
	$color = isset($_GET['color'])?"#".$_GET['color']:"#3386C6";

	$hsv = hex2hsv($color);
	$hsv[1] = 100; $hsv[2]=94;
	if ($hsv[0]>50 && 200>$hsv[0]){
		$hsv[2]=85;
	}
	$dark = hsv2hex($hsv);
	$darkC = hex2rgb($dark);
	
	//$hsv[0] += 15;
	$hsv[1] = 29; $hsv[2]=100;
	if ($hsv[0]>50 && 200>$hsv[0]){
		$hsv[1]=33;
		$hsv[2]=95;
	}
	$bright = hsv2hex($hsv);
	$brightC = hex2rgb($bright);
?>
/* COMMON */
body {
  font-size: small;
  font-family: "Segoe UI",dejavu sans,"Verdana","Trebuchet MS",Tahoma,sans-serif;
  margin: 0;
  background-color: #F5F5F5;
  overflow-y: auto;
}

:focus {
  outline: none;
}

::-moz-focus-inner {
  border: 0;
}

#page {
  width:1100px;
  margin: 10px auto;
  position: relative;
  min-height: calc(100vh - 20px);
  transition: all 0.3s;
}

#profile {
  position: absolute;
  background-color: <?= $dark ?>;
  width: 250px;
  bottom: 0;
  top: 0;
  box-shadow: 12px 0 15px -15px <?= $dark ?>;
  transition: all 0.3s;
  z-index: 1;
}

#post {
  margin-left: 250px;
  transition: all 0.3s;
}


#title-bar {
  margin-bottom:6px;
}

#title-bar a {
  text-decoration: none;
}

#title-bar a:hover {
  text-decoration: underline;
}

#feedname {
  display: table-cell;
  vertical-align: middle;
  padding: 0 10px;
  font-size: 1.0em;
  font-weight: bold;
  cursor: pointer;
  border-right:2px solid <?= $dark ?>;
  box-shadow: inset -5px 0 5px -7px <?= $dark ?>;
  border-radius: 3px 0 0 3px;
  transition: all 0.3s;
}

#feedname:hover{
  background-color: rgba(<?= "$darkC[0],$darkC[1],$darkC[2]" ?>, 0.14);
  box-shadow: inset -9px 0 5px -7px <?= $dark ?>;
}

#feedname a {
  display: table-cell;
  vertical-align: middle;
  color:<?= $dark ?>;
}

#postinfo {
  display: table-cell;
  vertical-align: middle;
  white-space: nowrap;
  padding: 0 10px;
  font-family: Tahoma,Geneva,sans-serif;
  font-weight: bold;
}

#postinfo a{
  display: block;
  font-size:small;
  color:#6E6E6E;
}

#postinfo :not(a){
  color:#888;
  font-size:9px;
}

#postinfo span {
  vertical-align: middle;
}

#description {
  padding:20px 50px 25px;
  min-width: 500px;
  min-height: 100%;
  overflow-x: auto;
  transition: all 0.3s;
  outline: 1px solid rgba(0, 0, 0, 0.5);
  box-shadow: 0px 9px 10px -12px #000;
  background-color:white;
}

#see_more {
  width:18%;
  min-width:150px;
  /*background-color: #f0f0f0;*/
  display: block;
  text-align: center;
  padding: 10px 0;
  text-decoration: none;
  color: #555;
  transition: all 0.3s;
  margin-left:auto;
  margin-right:-1px;

  border-radius: 0 0 100px 100px ;
  border: solid transparent;
  border-width: 0 1px;
}

#see_more:hover {
  color: black;
  background-color:rgba(100,100,100,0.05);
  border-color:#7A7A7A;
}

#page.noprofile {
  width:850px;
}

.noprofile #profile {
  width: 0px;
}

.noprofile #post {
  margin-left: 0px;
}

.noprofile #see_more {
  left: 0;
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