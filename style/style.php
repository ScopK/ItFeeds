<?php
	header("content-type: text/css");
	$color = isset($_GET['color'])?"#".$_GET['color']:"#EF7502";//"#C8F56F";

$hsv = hex2hsv($color);
$hsv[1] = 99; $hsv[2]=94;
$dark = hsv2hex($hsv);
$hsv[0] += 15;
$hsv[1] = 39; $hsv[2]=100;
$bright = hsv2hex($hsv);
?>
/* FONTS */

@import url(http://fonts.googleapis.com/css?family=Open+Sans:600);
@import url(http://fonts.googleapis.com/css?family=Montserrat);
@import url(http://fonts.googleapis.com/css?family=Noto+Sans:700);
@import url(http://fonts.googleapis.com/css?family=Roboto:400);
/*
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 600;
  src: local('Open Sans Semibold'), local('OpenSans-Semibold'), url(fonts/opensans.woff) format('woff');
}
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 400;
  src: local('Montserrat-Regular'), url(fonts/montserrat.woff) format('woff');
}
@font-face {
  font-family: 'Noto Sans';
  font-style: normal;
  font-weight: 700;
  src: local('Noto Sans Bold'), local('NotoSans-Bold'), url(fonts/notosans.woff) format('woff');
}
@font-face {
  font-family: 'Roboto';
  font-style: normal;
  font-weight: 400;
  src: local('Roboto Regular'), local('Roboto-Regular'), url(fonts/roboto.woff) format('woff');
}*/
/* END FONTS */


body:not(.description),button,input,select,.feed,.tag{
	font-family: "Open Sans",Tahoma, Geneva,sans-serif;
}
.header{
	font-family: "Roboto",Tahoma, Geneva,sans-serif;
}
.description{
	font-family: Tahoma, Geneva,sans-serif;
}
.options_panel .button-panel, .folder{
	font-family: "Noto Sans",Tahoma, Geneva,sans-serif;
}
#loadMoreLabel{
	font-family: "Montserrat",Tahoma, Geneva,sans-serif;
}

th{
	font-size: 14px;
}

body {
    font-size: small;
    margin: 0;
    /*background-image: url("../imgs/back2.jpg");*/
    background-attachment: fixed;
    background-size: 100% 100%;
  	/*background-blend-mode: color-burn;*/
    background-color:#292929;
    overflow-y: scroll;
}
button::-moz-focus-inner {
   border: 0;
}
:focus{
    outline:0;
}

#page {
	display:none;
	position:relative;
	background-color: #f5f5f5;
	margin: auto 40px;

	overflow:hidden;
}

#lateral_menu{
	position:fixed;
	top:0; bottom:0;

	background-color: #e5e5e5;
	padding: 10px;
	width: 300px;
	float:left;
	font-size: 12.5px;
	/*transition: margin-left 0.4s, border 0.4s;*/
	border-right: 0px solid #000;
	z-index:1;
	transition: opacity 0.4s, visibility 0s 0s;
}

#lateral_menu.hidden{
	transition-delay: 0s,0.4s;
	opacity: 0;
	visibility: hidden;
}

#settings_panel{
	position:absolute;
	background-color:#444;
	z-index:1;
	height:100%;
	top:0;	left:0;	right:0; bottom:0;
	color:white;
	padding:5px 10px;
	overflow-y:auto;
	transition: padding 0.2s, opacity 0.3s, visibility 0.3s, top 0.3s;
}

#settings_panel hr{
	border: dotted #666;
	border-width: 0 0 1px;
	height:1px;
	margin:10px 0;
}

#settings_panel.hidden{
	transition-delay: 0s, 0s, 0.3s;
	padding-top:0px;
	top:-5px;
	padding-bottom:30px;
	opacity:0;
	visibility:hidden;
	pointer-events:none;
}

#mouse_bottom{
	position:fixed;
	bottom:0;
	left:0;
	width:40px;
	z-index: 2;
}

.mouse-button{
	width:40px;
	left:0;
	height: 50px;
	border: 0px;
	color:white;
	font-weight: bold;
	cursor:pointer;
	background-color:transparent;
	transition: background-color 0.15s, color 0.15s;
}

.mouse-button:hover{
	background-color:black;
	color:white;
}

.mouse-button.colored{
	background-color: <?= $dark ?>;
	color:white;
}

#show-lateral-button{
	position:fixed;
	top:0;
	z-index:2;
}

#show-video-button{
	position:fixed;
	top:100px;
	z-index:2;
	font-size:10px;
	transition: top 0.3s;
}

#more-options-button{
	position:fixed;
	top:50px;
	z-index:2;
	font-size:26px;
	background-color:#E5E5E5;
	color:#222;
	transition: background-color 0.15s,margin 0.25s, z-index 0s 0.25s;
}

#more-options-button:hover{
	box-shadow: inset 0px 0px 30px 3px yellowgreen, 0px 0px 5px 0px yellowgreen;
}

#more-options-button.hidden{
	/*background-color:#7DA628;*/
	transition-delay: 0s;
	margin-left: 40px;
	z-index:-1;
}

#content{
	position:relative;
	margin-left: 320px;
	transition: margin 0.4s;
}

#navopts_top{
	text-align: center;
	height: 30px;
	margin: -10px -10px 10px;
	padding: 10px 0;
}

#navopts_bottom{
	position:absolute;
	bottom:0;
	width:100%;
	text-align: center;
	height: 30px;
	margin: 0 -10px;
	padding: 10px 0;
}

#navigation_panel{
	position:absolute;
	overflow-y: auto;
	padding-top: 10px;
	width:300px;
	top:50px; bottom: 50px;
	left: 10px; padding-right:10px;
}


/*############################################################################################################
LATERAL MENU FOLDER STYLES
##############################################################################################################
############################################################################################################*/

.folder {
	padding: 5px;
	cursor:pointer;
	overflow:hidden;
}

.folder .feed{
	margin-left: 10px;
	position:relative;
}

.folderfeeds{
	display: none;
}

.feed, .tag{
	cursor:pointer;
	color: #333;
	padding: 2px 5px;
	border-radius:3px;
}

.feed{
	border-left: 2px solid transparent;
}

.tag{
	margin: 1px;
	display: inline-block;
}

#tags{
	border-top: 1px dotted rgba(0,0,0,0.1);
	padding-top: 10px;
	margin-top: 10px;
}

.tag.hidden{
	background-color: #eee;
	color: #444;
}

.feed:hover{
	border-color: #333;
}

.tag:hover{
	background-color: rgba(0,0,0,0.1);
}

.feed.selected, .tag.selected, .folder.selected{
	background-color: rgba(0,0,0,0.1);
	border-radius: 3px;
}

.tag.public{
	color:#85b32b;
}

.feed.unread{
	font-weight:bold;
}

.feed .count, .folder .count{
	color: #777;
	font-weight: bold;
}

.tag .count{
	color: #777;
}

.count.hidden{
	display:none;
}

.folderHeader{
	display:inherit;
	font-size: 14px;
	font-weight:bold;
	width: 100%;
}

.folderHeader.hidden .folderTitle{
	background-color: #eee;
	color: #777;
	padding: 1px 5px 2px;
	border-radius:3px;
}


.folder.selected .folderHeader.hidden .folderTitle{
	background-color: initial;
}

.folderHeader.hidden:hover .folderTitle{
	background-color: initial;
	color: #777;
}

.folderHeader:hover .expander {
	border-right: 2px solid #333;
}

.folderTitle{
	transition: margin 0.3s;
}
.folder:hover .folderTitle{
	margin-left: 5px;
}

.expander{
	display: inline-table;
	text-align: center;
	margin-right: 4px;
	padding: 3px 7px;
	font-size: 13.5px;
	border: 2px solid transparent;
	border-radius:0px;
	width: 30px;
	background-color: transparent;
}

.expander:hover{
	background-color: <?= $dark ?>;
	color: white;
}

.select-panel{
	background-color: #555;
	color: white;
	border: 0;
	padding: 2px 4px;
	text-decoration: none;
	font-size: 12px;
	border-radius:2px;
}
.select-panel:hover{
	background-color: #404040;
}


#settings_panel .button-panel{
	border:0px solid rgba(255,255,255,0.20);
	background-color: rgba(255,255,255,0.14);
	border-radius:1px;
	padding: 5px;
	color: white;
	font-weight: bold;
	text-decoration: none;
	cursor:pointer;
	font-size: 13px;
	transition: border-color 0s,background-color 0.2s;
}


#settings_panel .button-panel:hover{
	border-color: #000;
	background-color: #404040;
}

#settings_panel .button-panel:active{
	border-color: <?= $dark ?>;
	background-color: <?= $dark ?>;
}

#settings_panel .button-panel:disabled{
	color: #333;
}
#settings_panel .button-panel:disabled:hover, #settings_panel .button-panel.disabled:hover{
	background-color: #555;
	border: 2px solid black;
}

#settings_panel .button-panel.marked{
	background-color: #AAA;
		border-color: #AAA;
	transition: border-color 0.2s,background-color 0.3s;
}

#settings_panel .button-panel.marked:hover{
	border-color: black;
	background-color: #888;
}

#settings_panel .button-panel.marked:active{
	border-color: <?= $bright ?>;
	background-color: <?= $bright ?>;
}

#settings_panel .highlight-color{
	background-color: <?= $dark ?>;
	border: 1px solid <?= $dark ?>;
}

#settings_panel .highlight-color:hover{
	border-color: #444;
	background-color: #444;
}

#settings_panel .highlight-color:active{
	border-color: <?= $bright ?>;
	background-color: <?= $bright ?>;
}

#settings_panel button,#settings_panel a{
	margin:2px 0;
}
#settings_panel a{
	padding-left:7px;
}

#settings_panel .highlight-color{
	margin:3px 0;
}

#navopts_top .button-panel,
#navopts_bottom .button-panel{
	background-color:transparent;
	padding: 5px 7px;
	margin: 0 0;
	border: solid transparent;
	border-width: 0 0 2px;
	cursor:pointer;
	transition: all 0.2s;
}

#navopts_top .button-panel:hover,
#navopts_bottom .button-panel:hover{
	border-color: rgba(0,0,0,0.4);
}

#navopts_top .button-panel.marked,
#navopts_bottom .button-panel.marked{
	border-color: <?= $dark ?>;
}

#navopts_top .button-panel:active,
#navopts_bottom .button-panel:active{
	background-color: rgba(0,0,0,0.1);
}

#navopts_top .highlight-color,
#navopts_bottom .highlight-color{
	color: <?= $dark ?>;
	border-width: 0 0 2px;
}

#navopts_top .highlight-color:hover,
#navopts_bottom .highlight-color:hover{
	border-color: <?= $dark ?>;
}



/*############################################################################################################
POSTS
##############################################################################################################
############################################################################################################*/

#posts_panel{
	padding: 7px;
	margin: 0;
}

.post{
	position:relative;
}

.post .header{
	border: solid transparent;
	border-width: 1px 0 2px;
	height: 38px;
	background-color: rgba(60,60,60,0.2);

	width:100vw;
	display: table-cell;
	vertical-align: middle;
	padding-left:15px;

	border: solid #666;
	border-width: 0 0 1px 0;
	border-radius: 4px 4px 0 0;
	padding-bottom:1px;
}

.post.selected .header{
	padding-bottom:0px;
	border-bottom-width: 2px;
	box-shadow: 0 5px 14px -5px black;
}

.post.selected.unread .header{
	box-shadow: 0 5px 14px -5px <?= $dark ?>;
}

.post.unread .header{
	background-color: <?= $bright ?>;
	border-color: <?= $dark ?>;
}

.post .title{
	overflow:hidden;
	font-size:14px;
}

.post .subtitle{
	overflow:hidden;
	font-size: 10.5px;
}

.post .header a{
	color: #333;
	text-decoration: none;
	white-space: nowrap;
}

.post .header .title a{
	font-weight: bold;
}

.post .header a:hover{
	color: #000;
	text-decoration: underline
}

.post .header .subtitle{
	color: #555;
	text-decoration: none;
	white-space: nowrap;
}

.post .header .subtitle a.date{
	color: #555;
}

.post .controller{
	text-align:right;
	padding:6px 10px 5px 5px;
	position:absolute;
	top:0;
	right:0;
}

.post:not(.minimized) .controller.fixed{
	position:fixed;
	right:47px;
}

.post .controller button{
	cursor:pointer;
	width: 25px;
	height:25px;
	margin: 0 5px;
	background-color:transparent;
	border:0;
	background-repeat: no-repeat;
	background-position:center;
}

.post .controller button:hover{
	box-shadow: 0 0 10px -2px rgba(0,0,0,0.5);
	background-color: rgba(0,0,0,0.1);
	/*inset*/
}

.post .controller button.maxminimize{
	margin:0 -5px 0 0;
}

.post .controller button.addTag{
	background-image: url("../imgs/addTag.png");
}

.post .controller button.maxminimize{
	background-image: url("../imgs/less.png");
}

.post.minimized .controller button.maxminimize{
	background-image: url("../imgs/more.png");
}

.post .controller button.setUnread{
	background-image: url("../imgs/read.png");
}

.post.unread .controller button.setUnread{
	background-image: url("../imgs/unread.png");
}

.post .controller button.setFav{
	background-image: url("../imgs/starOff.png");
}

.post.favorite .controller button.setFav{
	background-image: url("../imgs/starOn.png");
}

button.lock-icon{
	background-repeat: no-repeat;
	background-position: right 10px top 6px;
	background-image: url("../imgs/locked.png");
}

button.lock-icon.highlight-color{
	background-image: url("../imgs/unlocked.png");
}

#mouse_bottom button{
	background-repeat: no-repeat;
	background-position:center;
}

#mouse_bottom button.markunread{
	background-image: url("../imgs/w_read.png");
}

#mouse_bottom button.markunread.colored{
	background-image: url("../imgs/w_unread.png");
}

#mouse_bottom button.markfav{
	background-image: url("../imgs/w_starOff.png");
}

#mouse_bottom button.markfav.colored{
	background-image: url("../imgs/w_starOn.png");
}

#mouse_bottom button.addtag{
	background-image: url("../imgs/w_addTag.png");
}

.post.minimized .tagList{
	display:none;
}

.post .tagList{
	font-size:10px;
	font-weight: bold;
	color:#333;
	margin-top:10px;
}

.post .tagList *{
	vertical-align: middle;
}

.post .tagList button{
	margin: 5px 0px 5px 5px;
	background-image: url("../imgs/remTag.png");
}

.post .description{
	/*margin: 25px 25px 0px;
	padding-bottom:50px;*/
	padding: 25px 25px 50px;
	overflow-x: auto;
}



/*############################################################################################################
PAGES
##############################################################################################################
############################################################################################################*/

#pages {
	display: inline-block;
	margin-top: 5px;
}

#pages #totalPages{
	font-weight: bold;
}

/*###########################################################################################################
DIALOGS
##############################################################################################################
############################################################################################################*/

.background-modal{
	display:table;
	position: fixed;
	top:0; left:0;
	height: 100%;
	width: 100%;
	background-color: rgba(20,20,20,0.78);
	z-index: 10;
}

.dialog-dim {
	width: 400px;
	margin: auto;
	background-color: #fff;
	text-align:center;

	/*border: 2px solid #e5e5e5;*/
	box-shadow:0px 0px 30px -15px #FFF
}

.background-modal table{
	width: 100%;
	border-spacing: 0;
	margin: 0;padding: 0;
}
.background-modal th{
	padding: 10px;
	background-color: #e5e5e5;
	font-weight: bold;
	border-bottom: 1px solid #ddd;
}
.background-modal td{
	padding: 10px 0px;
}

.background-modal .slim td{
	padding: 3px 0px;
}
.background-modal .slim tr:nth-child(2) td{
	padding-top: 10px;
}
.background-modal .slim tr:last-child td{
	padding:7px 0;
}

.dialog_buttons button{
	margin: 0 6px 5px;
	padding: 5px 8px;
	background-color: #ececec;
	border: 1px solid #acacac;
	border-radius: 3px;
}

#delete_dialog button{
	background-color: #ff8b8b;
	border: 1px solid #bc5656;
}

.dialog_buttons button:active, #delete_dialog button:active{
	border-width: 2px;
	padding: 4px 7px;
}

.background-modal .delete{
	background-color: #faa;
	box-shadow:0px 0px 30px -5px #F00;
}

.background-modal .delete th{
	background-color: #a55;
	border-bottom: 1px solid #a55;
}

/*################################################################
TAG DIALOG
###################################################################
#################################################################*/

#add_tag_content .taglist{
	padding: 10px 10px 0;
}

#add_tag_content .taglist p{
	display: inline-block;
	padding: 2px 5px;
	border-radius: 5px;
	margin: 0 1px 1px 0;
}

#add_tag_content .taglist p:hover{
	cursor:pointer;
	background-color: #f4f4f4;
}

#add_tag_content .taglist p.selected{
	background-color: #ddd;
}

#add_tag_content .taglist p.hiddenTag{
	color: rgba(0,0,0,0.4);
}

/*################################################################
VIDEO DIALOG
###################################################################
#################################################################*/

#youtube_viewer_dialog tr{
	position:relative;
}

#ytv_window_controls{
	position:absolute;
	right:5px;
	top:4px;
}

#ytv_window_controls button{
	border: 1px solid transparent;
	background-color: rgba(255,255,255,0.7);
	height:30px;
	width:30px;
	text-align:center;
	font-weight:bold;
	font-size:20px;
	margin-top:1px;
	border-radius:3px;
}

#ytv_window_controls button:hover{
	background-color: rgba(255,255,255,0.9);
}
.ytv_controls{
	padding:0!important;
	width:50%;
}
.ytv_controls button{
	font-weight:bold;
	border:0;
	width:100%;
	/*height:100%;*/
	padding:10px;
	margin:0;
	cursor:pointer;
	display:table-cell;
}

.ytv_controls button:hover{
	color: #333;
}

#youtube_viewer_dialog.selected th{
	box-shadow: 0px 5px 14px 0px #F07602;
	background-color: #FFE59C;
	border-bottom-color: #F07602;
}

#video_unread_button{
	background-position: 1px 1px;
}

#youtube_viewer_dialog #video_unread_button{
	background-image: url("../imgs/read.png");
}

#youtube_viewer_dialog.selected #video_unread_button{
	background-image: url("../imgs/unread.png");
}

/*###########################################################################################################
CONTROL PANEL
##############################################################################################################
############################################################################################################*/


#actions_panel button{
	width: 25px;
	height: 25px;
	border: 0px;
	background-size: 100% 100%;
	background-color: transparent;
	transition: background-color 0.3s;
}

#control_panel button{
	margin: 0 3px;
}

#actions_panel button:disabled{
	opacity: 0.5;
}

#actions_panel button:hover{
	background-color: rgba(0,0,0,0.1);
	cursor: pointer;
}

#control_panel button.addTag{
	background-image: url("../imgs/addTag.png");
}

#control_panel button.setUnread{
	background-image: url("../imgs/read.png");
}

#control_panel button.setUnread.unread{
	background-image: url("../imgs/unread.png");
}

#control_panel button.setFav{
	background-image: url("../imgs/starOff.png");
}

#control_panel button.setFav.fav{
	background-image: url("../imgs/starOn.png");
}

div.tagname button{	
	background-image: url("../imgs/remTag.png");
}

#tagList .tagname{
	margin: 10px 10px;
}

#tagList .tagname button{
	margin-left: 10px;
	vertical-align: middle;
}

.post.minimized .description{
	display:none;
}

.post.minimized .header{
	border-bottom:1px dotted rgba(0,0,0,0.5);
	border-top:1px solid transparent;
	box-shadow:unset;
}

#mouse_nav{
	z-index:1;
	position:fixed;
	width:40px;
	background-color: transparent;
	border: 0;
	padding:0;
	bottom: 0; top: 0; left: 0;
	height: 100%;
	transition: width 0.3s, background-color 0.3s;
}

#mouse_nav:hover{
	transition-delay:0s;
	cursor:pointer;
	background-color: black;
	width:40px;
}

#mouse_nav-h{
	z-index:1;
	position:fixed;
	width:100%;
	background-color: transparent;
	border: 0px;
	bottom: 0; right: 0; left: 0;
	height: 15px;
	transition: height 0.3s, background-color 0.3s;
}

#mouse_nav-h:hover{
	transition-delay:0s;
	cursor:pointer;
	background-color: black;
	height: 50px;
}


#loadMoreLabel {
	position:absolute; bottom: 0;
    background-image: url("../imgs/backPosts.png");
    background-repeat: repeat-x;
	background-color: rgba(0,0,0,0.1);
	color: rgba(0,0,0,0.5);
	width:100%;
	margin: 0;
	cursor:pointer;
	height: inherit;

	font-weight: bold;
	font-size: 30px;
	padding: 60px 0 0;
	transition: background-color 0.4s;
	text-align:center;

	-webkit-user-select: none; /* Chrome/Safari */        
	-moz-user-select: none; /* Firefox */
	-ms-user-select: none; /* IE10+ */
	/* Rules below not implemented in browsers yet */
	-o-user-select: none;
	user-select: none;
}

#loadMoreLabel:hover{
	background-color: rgba(0,0,0,0.2);
}

#loadMoreLabel.disabled{
	background-color: rgba(0,0,0,0.1);
	color: transparent;
	cursor: default;
}

.gear-icon{
	background-image: url("../imgs/gear.png");
	background-repeat: no-repeat;
	background-position: center center;
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