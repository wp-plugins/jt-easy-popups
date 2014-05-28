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
    $(".section_nav").click(function(){
        var sectionparent = $(this).parents("section");
        var basicsrc = $(".small_section > img", this).attr("src");
        if(sectionparent.height() > 50){
            var largescr = basicsrc.replace("small", "large");
            $(".small_section > img", this).attr("src", largescr);
            sectionparent.animate({
                height: "41px"
            }, 600, "easeOutCubic");
        }else{
            var smallscr = basicsrc.replace("large", "small");
            $(".small_section > img", this).attr("src", smallscr);
            $("article", sectionparent).each(function(){
                if($(this).css("display") != "none"){
                    sectionHeight = $(this).height() + 60;
                }
            })
            sectionparent.animate({
                height: sectionHeight
            }, 600, "easeOutCubic", function(){
               sectionparent.css("height","auto"); 
            });
        }
    });
    
    //Popup Radius Option
    $(".radius_box").click(function(){
       var optionSection = $(this).parents("section");
       $(".radius_box", optionSection).removeClass("select_radius");
       $(this).addClass("select_radius"); 
    });
    
    //Data Loading Icon
    $("form .jt_save_popup2").click(function(){
        $(".basic_save_loading").css("display","block");
        option_submit();
    });
    
    //Data Reset
    $("#jt_reset").click(function(){
        $(".reset_loading").css("display", "block");
        $("#basic_bgopacity").attr("value", "");
        $("#popup_style_option > input").removeAttr("checked");
        $("#popup_shadow_option > input").removeAttr("checked");
        option_submit();
    });
    
    //Data Submit
    function option_submit(){
        document.basic_option_form.submit();
    }    
    
});	