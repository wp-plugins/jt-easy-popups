jQuery(document).ready(function($) {
if($("body").hasClass("home")){
    
    //JTPOP_parmas
    var popupcenter = JTPOP_parmas.pcpopupcenter;
    var pcpopupsize = JTPOP_parmas.pcpopupsize;
    
    $(window).load(function() {

        //PC Popup Cookie check
        for(i=1;i<=pcpopupsize;i++){
            if(jQuery.cookie("JTPOP_Show" + i) != "today_none" + i){
                $(".jtpop-bgs").eq(i - 1).fadeIn("fast");
                $(".jtpop-page").fadeIn("fast");
            }
        }

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
        var thisbgs = $("article.jtpop-bgs").index(thisparents);
        var today = new Date();

        for(i=0;i<pcpopupsize;i++){
            if(thisbgs == i) $.cookie( "JTPOP_Show" + (i + 1), "today_none" + (i + 1), {expires : 1, path: "/"});
        }
        
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
        
        

