description.find("script").remove();
//description.find("iframe").prop("sandbox",true);
description.find("video").prop("controls",true);
description.find("a").attr("target","_blank");
$(document).ready(function(){
	while (description.children().last().prop("tagName") == "BR"){
		description.children().last().remove();
	}
    $("#description").append(description.html());
    
	$(document).keydown(function(e) { 
			switch (e.which) {
				case 84: //T
					$("#page").toggleClass("noprofile")
					break;
			    default:
			        //alert("Key pressed:<br/>"+e.which+" - "+e.key,true);
			        return;
			}
	});
});