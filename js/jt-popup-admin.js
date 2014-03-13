jQuery(document).ready(function($){
    
    //jQuery-UI datepicker
	$(".pop_date").datepicker({
		dateFormat 		: "yy-mm-dd",
		altFormat		: "yy-mm-dd",
		dayNamesMin		: [ "일", "월", "화", "수", "목", "금", "토" ],
		monthNames		: [ "1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월" ]
	});	
	
	//Wordpress Image Upload 
	$(".jt-cuztom-td").on( "click", ".js-cuztom-upload", function(){
	var that	= $(this),
		type 	= that.data("cuztom-media-type"),
		parent	= that.closest(".jt-cuztom-td"),
		hidden 	= $( ".cuztom-hidden", parent ),
		preview = $( ".cuztom-preview", parent );
		wp.media.editor.send.attachment = function(props, attachment){
			if( type == 'image' ){
	    			var thumbnail = attachment.sizes.medium ? attachment.sizes.medium : attachment.sizes.full;
	    			preview.html('<img src="' + thumbnail.url + '" height="' + "108px" + '" width="' + "192px" + '" />')
	    			hidden.val(attachment.id);
	    	}
	    }
	    wp.media.editor.open();
	return false;
	});
	
    //Popup Tab Big & Small
    $(".small_section").click(function(){
        var sectionparent = $(this).parents("section");
        var basicsrc = $("img", this).attr("src");
        if(sectionparent.height() > 50){
            var largescr = basicsrc.replace("small", "large");
            $("img", this).attr("src", largescr);
            sectionparent.animate({
                height: "41px"
            }, 500);
        }else{
            var smallscr = basicsrc.replace("large", "small");
            $("img", this).attr("src", smallscr);
            $("article", sectionparent).each(function(){
                if($(this).css("display") != "none"){
                    sectionHeight = $(this).height() + 60;
                }
            })
            sectionparent.animate({
                height: sectionHeight
            }, 500, function(){
               sectionparent.css("height","auto"); 
            });
        }
    });
    
    //Popup Clase Color
    $(".close_color").click(function(){
        var spanparent = $(this).parents("section");
        $(".close_color", spanparent).css("color", "#666").css("font-weight", "normal");
        $(".close_color", spanparent).removeClass("jtpop_select");
        $(this).css("color", "#f7938e").css("font-weight", "bold");
        $(this).addClass("jtpop_select");
    });
    
    //Data Submit
    function option_submit(){
        document.basic_option_form.submit();
    }
    
    //Data Loading Icon
    $("form .jt_save_popup2").click(function(){
        $(".basic_save_loading").css("display","block");
        option_submit();
    });
    
    //Data Reset
    $("#jt_reset").click(function(){
        $(".reset_loading").css("display", "block");
        $("#basic_bgimg").attr("value", "");
        $("#basic_bgopacity").attr("value", "");
        $("#container_width").attr("value", "");
        option_submit();
    });
    
    
});	