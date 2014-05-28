jQuery(document).ready(function($) {
if($("body").hasClass("home")){
    
    //JTPOP_parmas
    var popupcenter = JTPOP_parmas.pcpopupcenter;
    var pcpopupsize = JTPOP_parmas.pcpopupsize;
    
    $(window).load(function() {
        
        //PC Popup Cookie check
        $(".jtpop-bgs").each(function(){
           if($.cookie($(this).attr("id")) != "today_none"){
               $(this).fadeIn("fast");
               $(".jtpop-page").fadeIn("fast");
           }
        });
       
        //PC Popup One
        if(popupcenter == true){
            $(".jtpop-bgs").css({
                "position": "fixed",
                "margin-top" : - $(".jtpop-bgs").height()/2 + "px",
                "margin-left": - $(".jtpop-bgs").width()/2 + "px",
                "top" : "50%",
                "left" : "50%"
            });
        }
       
    });
    
    //Close Popup
    $(".popclose").click(function(){
        $(this).parents("article").fadeOut();
        article_display_check();
    });
    $(".jtpop-page").click(function(){
        article_display_check();
    });
  
    //Popups Cookie Script
    $(".notoday").click(function(){

        var thisparents = $(this).parents("article.jtpop-bgs");
        $.cookie(thisparents.attr("id"), "today_none", {expires : 1, path: "/"});
        
        thisparents.fadeOut();
        article_display_check();
    });
    
    //Display:block - popup size check
    function article_display_check(){
        var article_ck = 0;
        $("article.jtpop-bgs").each(function(){
            if($(this).css("display") == "block"){
                article_ck += 1;
            }
        });
        if(article_ck == 1){
            $(".jtpop-page").fadeOut();
            $("article.jtpop-bgs").fadeOut();
        } 

    }

}
});
        
        

