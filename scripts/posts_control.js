var postIdxSelected = 0;
var lastAction = 0;

$(document).ready(function(){
    $(document).bind('keydown', function(e) {
        if (e.ctrlKey || e.altKey || e.shiftKey) return;
        if ($("input").is(":focus")) return;
        switch (e.which) {
            case 32: //space
                alert("space");
                break;
            case 70: //f
                alert("f");
                break;
            case 66: //b
                alert("b");
                break;
            case 68: //d
                alert("d");
                break;
            case 83: //s
                alert("s");
                break;
            case 77: //m  
            case 78: //n
                alert("m/n");
                break;
            case 74: //j
                nextPost();
                break;
            case 75: //k
                prevPost();
                break;
            case 84: //t
                alert("t");
                break;
            case 86: //v
                alert("v");
                break;
            default:
                alert(e.which);
        }
    });
});

function postsInit(){
	$(".post").click(function(){
		$(".post").removeClass("selected");
		postIdxSelected = $(this).attr("idxpost");
		$(this).addClass("selected");
	});

	if (lastAction==1){
		lastAction=0;
		var newPost = $(".post[idxpost='"+postIdxSelected+"']");
		focusPost(newPost,0);
		newPost.addClass("selected");
	} else if (lastAction==-1){
		lastAction=0;
		var newPost = $(".post[idxpost='"+postIdxSelected+"']");
		focusPost(newPost,0);
		newPost.addClass("selected");
	}
}



function nextPost(){
	if (postIdxSelected < ((get.postspage)?get.postspage:10)){
		$(".post[idxpost='"+postIdxSelected+"']").removeClass("selected");
		postIdxSelected++;
		var newPost = $(".post[idxpost='"+postIdxSelected+"']");
		focusPost(newPost,100);
		newPost.addClass("selected");
	} else {
		postIdxSelected = 1;
		lastAction = 1;
		nextPage();
	}
}


function prevPost(){
	if (postIdxSelected > 1){
		$(".post[idxpost='"+postIdxSelected+"']").removeClass("selected");
		postIdxSelected--;
		var newPost = $(".post[idxpost='"+postIdxSelected+"']");
		focusPost(newPost,100);
		newPost.addClass("selected");
	} else {
		postIdxSelected = (get.postspage)?get.postspage:10;
		lastAction = -1;
		prevPage();
	}
}

function focusPost(post,speed){
	$('html,body').animate({scrollTop: post.offset().top}, speed); 
}