<?php
/*
 * Plugin Name: JT Easy Popups
 * Plugin URI: http://www.studio-jt.co.kr
 * Description: 간단한 이미지 팝업에 기간을 설정하여 쉽게 사용할 수 있습니다.
 * Version: 1.0.1
 * Author: 스튜디오 제이티 (support@studio-jt.co.kr)
 * Author URI: studio-jt.co.kr
*/

if ( !defined( 'ABSPATH' ) ) exit;

/*----------------------------------------------------------------------------
 * Install & Uninstall Hook
 ---------------------------------------------------------------------------*/
register_activation_hook(__FILE__, 'jt_wp_init');
register_uninstall_hook(__FILE__, 'jt_wp_destroy');


/*----------------------------------------------------------------------------
 * Add Action
 ---------------------------------------------------------------------------*/
add_action('admin_menu', 'JTPOP_create_menu');
add_action('admin_enqueue_scripts', 'JTPOP_enqueue_scripts');
add_action('admin_footer', 'JTPOP_ajax_script');
add_action('wp_ajax_add_action', 'JTPOP_add_popup');
add_action('wp_ajax_del_action', 'JTPOP_del_popup');
add_action('wp_ajax_save_action', 'JTPOP_save_popup');
add_action('wp_footer','JTPOP_show_popup');


/*----------------------------------------------------------------------------
 * Admin Page Script & Stylesheet
 ---------------------------------------------------------------------------*/
function JTPOP_enqueue_scripts(){
    wp_enqueue_media(); 
    wp_enqueue_script('media-upload');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jQuery-easing', Plugins_url('/js/jquery.easing.1.3.min.js', __FILE__), array('jquery'), '1.3.1', true);
    wp_enqueue_script('JTPOP-admin-script', Plugins_url('/js/jt-popup-admin.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_enqueue_style('jquery-datepicker', Plugins_url('/css/jquery-ui-1.9.2.custom.css', __FILE__), false, '1.0.0');
    wp_enqueue_style('JTPOP-admin-style', Plugins_url('/css/jt-popup-admin.css', __FILE__), false, '1.0.0');
}


/*----------------------------------------------------------------------------
 * Admin Option setting
 ---------------------------------------------------------------------------*/
function JTPOP_create_menu(){
    add_menu_page('STUDIO-jt POPUP', 'JT Easy Popups', 'administrator', __FILE__, 
                'JTPOP_settings_page', Plugins_url('/img/jt-icon.png', __FILE__));
                
    add_action('admin_init', 'JTPOP_basic_settings');
}
function JTPOP_basic_settings(){
    register_setting('JTPOP_bs_setting', 'JTPOP_bg_opacity');
    register_setting('JTPOP_bs_setting', 'JTPOP_popup_style');
    register_setting('JTPOP_bs_setting', 'JTPOP_popup_shadow');
}


/*----------------------------------------------------------------------------
 * JT_POPUPS DB Create Table
 ---------------------------------------------------------------------------*/
function jt_wp_init() {
    
    $JTPOP_db_version = "1.0.1";
    
    global $wpdb;
    $table = $wpdb->prefix . "jt_popups";
    if ($wpdb->get_var("SHOW TABLES LIKE '".$table."'") != $table) {
        $sql = "CREATE TABLE " . $table . 
                "(`rowid` INT( 10 ) NOT NULL AUTO_INCREMENT ,
                 `pcimg` BIGINT( 20 )  DEFAULT NULL ,
                 `imglink` VARCHAR( 255 ) DEFAULT NULL ,
                 `popuptop` VARCHAR( 10 ) DEFAULT NULL ,
                 `popupleft` VARCHAR( 10 ) DEFAULT NULL ,
                 `startdate` DATE NOT NULL ,
                 `enddate` DATE NOT NULL ,
                 `zindex` INT( 10 )  DEFAULT NULL ,
                 `closecolor` VARCHAR( 20 ) DEFAULT NULL ,
                 `radius` INT( 10 )  DEFAULT NULL ,
                  PRIMARY KEY (  `rowid` )
                );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        add_option('JTPOP_db_version', $JTPOP_db_version );
    }   
}


/*----------------------------------------------------------------------------
 * JT_POPUPS DB Drop Table
 ---------------------------------------------------------------------------*/
function jt_wp_destroy() {
    global $wpdb;
    $table = $wpdb->prefix . "jt_popups";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
        $sql = "DROP TABLE `" . $table . "`";
        
    $wpdb -> query($sql);
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    }
    
    delete_option('JTPOP_bg_opacity');
    delete_option('JTPOP_popup_style');
    delete_option('JTPOP_popup_shadow');
    delete_option('JTPOP_db_version');
}


/*----------------------------------------------------------------------------
 * Plugin AJAX Script
 ---------------------------------------------------------------------------*/
function JTPOP_ajax_script(){
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        //Add Popup
        $("#jt_add_popup").click(function(){
            $("#add_loading").css("display","block");
            var data = {
                action : 'add_action'
            };
            $.post(ajaxurl, data, function(response) {
                var sectionsize = $("section").size();
                var nonesize = $("section[style]").size();
                if(sectionsize - nonesize > 7){
                    $("#add_loading").css("display","none");
                    $("#no_add_popup").fadeIn("slow").delay(2500).fadeOut("slow");
                }else{
                    location.reload();
                }
                
            });
        });
        //delete Popup
        $(".delete_popup").click(function(){
            $(this).parents("section").fadeOut("slow", function(){
                if($("#popups_container").height() == 0){
                    $("#popups_container").append('<p id="empty_section">\'팝업 추가\'를 통해 새로운 팝업을 추가하세요.</p>')
                } 
            });
            var rowid = $("input", this).val();
            var data = {
                rowid : rowid,
                action : 'del_action'
            };
            $.post(ajaxurl, data, function(response){
                
            }); 
        })
        //Update Popus
        $("section .jt_save_popup").click(function(){
            var inputsection = $(this).parents("section");
            $(".loading_icon", inputsection).css("display","block");
            var saveval = [];
            saveval[0] = $(".jtpop_pc_img", inputsection).val();
            saveval[1] = $(".jtpop_link", inputsection).val();
            saveval[2] = $(".jtpop_top", inputsection).val();
            saveval[3] = $(".jtpop_left", inputsection).val();
            saveval[4] = $(".jtpop_startdate", inputsection).val();
            saveval[5] = $(".jtpop_enddate", inputsection).val();
            saveval[6] = $(".jtpop_zindex", inputsection).val();
            saveval[7] = $(".jtpop_row", inputsection).val();
            saveval[8] = $(".close_color_option:checked", inputsection).val();
            saveval[9] = $(".select_radius", inputsection).attr("data-value");
            
            var data = {
                saveval : saveval,
                action : 'save_action'
            };
            $.post(ajaxurl, data, function(response) {
                $(".loading_icon", inputsection).css("display","none");
                $(".save_ok", inputsection).fadeIn("slow").delay(1500).fadeOut("slow");
            });
        });
    }); 
</script>
<?php
}


/*----------------------------------------------------------------------------
 * Insert Popup Data
 ---------------------------------------------------------------------------*/
function JTPOP_add_popup(){
    
    global $wpdb;
      
    $table = $wpdb->prefix . "jt_popups";
        $sql = "SELECT COUNT(*) FROM `" . $table .
               "` WHERE rowid";
    $query = mysql_query($sql); 
    $row=mysql_fetch_row($query);
    
    $result = $row[0];
    
    if($result < 8){
    $table = $wpdb->prefix . "jt_popups";
        $sql = "INSERT INTO `" . $table .
               "` (`rowid` ,
               `pcimg` ,
               `imglink` ,
               `popuptop` ,
               `popupleft` ,
               `startdate` ,
               `enddate` ,
               `zindex` ,
               `closecolor`,
               `radius`
               )
               VALUES (
               NULL , NULL , NULL, NULL , NULL ,  '',  '', NULL, NULL, NULL
               );";           
    $wpdb->query($sql);
    }
    
}


/*----------------------------------------------------------------------------
 * Delete Popup Data
 ---------------------------------------------------------------------------*/
function JTPOP_del_popup(){
    $rowid = $_POST['rowid'];
    global $wpdb;
    $table = $wpdb->prefix . "jt_popups";
        $sql = "DELETE FROM `" . $table .
        "` WHERE `" . $table . 
        "`.`rowid` = " . $rowid . ";";
   $wpdb->query($sql);
}


/*----------------------------------------------------------------------------
 * Update Popup Data
 ---------------------------------------------------------------------------*/
function JTPOP_save_popup(){
    $saveval = $_POST['saveval'];
    if($saveval[0] == ""){
        $saveval[0] = NULL ;
    }
    var_dump($saveval);
    global $wpdb;
    $table = $wpdb->prefix . "jt_popups";
        $sql = "UPDATE `" . $table . "` SET
        `pcimg` = '" . $saveval[0] . "',
        `imglink` = '" . $saveval[1] . "',
        `popuptop` = '" . $saveval[2] . "',
        `popupleft` = '" . $saveval[3] . "',
        `startdate` = '" . $saveval[4] . "',
        `enddate` = '" . $saveval[5] . "',
        `zindex` = '" . $saveval[6] . "',
        `closecolor` = '" . $saveval[8] . "',
        `radius` = '" . $saveval[9] . "'
        WHERE `" . $table . "`.`rowid` = " . $saveval[7] . ";";
    $wpdb -> query($sql);
}


/*----------------------------------------------------------------------------
 * Admin Page Popup List
 ---------------------------------------------------------------------------*/
function JTPOP_popup_list(){

    global $wpdb;
    $table = $wpdb->prefix . "jt_popups";
    if ($wpdb->get_var("SELECT * FROM `" . $table . "`") != "") {
            
        $fivesdrafts = $wpdb->get_results(
        "SELECT * FROM `" . $table . "` where rowid"
        );
        foreach ($fivesdrafts as $key => $value) {    
        ?>
        <section class="jtpop_section">
            <div class="section_nav"><p class="pc_version_ck">POPUP <?php echo $value -> rowid ?></p><p class="delete_popup"><img src="<?php echo Plugins_url('/img/close.png', __FILE__); ?>" alt="popup delete"/><input type="hidden" class="jtpop_row" value="<?php echo $value -> rowid; ?>" /></p><p class="small_section"><img class="smallsize" src="<?php echo Plugins_url('/img/small.png', __FILE__); ?>" alt="sections size change"></p></div>
            <article class="pc_option">
                <ul class="popup_img">
                    <li class="jt-cuztom-td">
                            <?php if($value -> pcimg != 0){
                            $gallery_image_id = $value -> pcimg;
                            $image_attributes = wp_get_attachment_image_src( $gallery_image_id, 'medium' );
                            echo "<span class=\"cuztom-preview\"><img src=\"$image_attributes[0]\" width=\"180\" height=\"150\" alt=\"popup image\" /></span></br >"; 
                        }else{?><span class="cuztom-preview"></span><?php }?>
                        <input type="hidden" class="cuztom-hidden cuztom-input jtpop_pc_img" value="<?php if($value -> pcimg != "") echo $value -> pcimg; ?>">
                        <input type="button" class="button js-cuztom-upload" data-cuztom-media-type="image" value="팝업 이미지 선택">
                    </li>
                </ul>
                <ul class="option_list">
                    <li>팝업 이미지를 클릭하면 <input type="text" class="jtpop_link" value="<?php if($value -> imglink) echo $value -> imglink; else echo "http://"; ?>" />로 이동합니다.</li>
                    <li>팝업의 상단 여백을 <input type="text" class="jtpop_top" value="<?php echo $value -> popuptop; ?>" />px, 좌측 여백을<input type="text" class="jtpop_left" value="<?php echo $value -> popupleft; ?>" />px로 합니다.<br>
                       [팝업이 하나일때, 여백값을 입력하지 않으면 화면 중앙에 위치합니다]
                    </li>
                    <li class="form_title">팝업의 개시 기간을 <input type="text" class="pop_date jtpop_startdate" value="<?php if($value -> startdate == "0000-00-00" ) echo date("Y-m-d"); else echo $value -> startdate; ?>" />부터 <input type="text" class="pop_date jtpop_enddate" value="<?php if($value -> enddate == "0000-00-00") echo date("Y-m-d"); else echo $value -> enddate; ?>" />까지로 합니다.</li>
                    <li>두개 이상의 팝업이 겹칠때 이 팝업의 우선순위를 <input type="text" class="jtpop_zindex" value="<?php echo $value -> zindex; ?>" maxlength="1" />로 합니다.<br />
                       [ 0 ~ 9, 숫자가 클수록 우선순위 높음]
                    </li>
                    
                    <?php if(get_option('JTPOP_popup_style') == "type1"){ ?>
                        
                        <li class="popup_close_option">팝업의 '닫기' 버튼을 &nbsp;&nbsp;  
                            <input type="radio" class="close_color_option" name="close_color<?php echo $value -> rowid; ?>" id="close_black<?php echo $value -> rowid; ?>" value="jtpop_black" <?php if($value->closecolor == "jtpop_black" || $value->closecolor == NULL) echo "checked"; ?> /><label for="close_black<?php echo $value -> rowid; ?>">검은색</label> &nbsp;&nbsp;/&nbsp;&nbsp; 
                            <input type="radio" class="close_color_option" name="close_color<?php echo $value -> rowid; ?>" id="close_white<?php echo $value -> rowid; ?>" value="jtpop_white" <?php if($value->closecolor == "jtpop_white") echo "checked"; ?> /><label for="close_white<?php echo $value -> rowid; ?>">하얀색</label> &nbsp;&nbsp; 으로 합니다.
                        </li>
                        
                    <?php } ?>
                    
                    <li><span class="radius_text">팝업 이미지의 모서리를 둥글게 할 수 있습니다.</span><br />
                         <div class="radius_box <?php if($value->radius == 0 ||  $value->radius == NULL) echo "select_radius"; ?>"><span data-value="0" class="radius_0"></span></div>
                         <div data-value="5" class="radius_box <?php if($value->radius == 5) echo "select_radius"; ?>"><span class="radius_5"></span></div>
                         <div data-value="10" class="radius_box <?php if($value->radius == 10) echo "select_radius"; ?>"><span class="radius_10"></span></div>
                         <div data-value="15" class="radius_box <?php if($value->radius == 15) echo "select_radius"; ?>"><span class="radius_15"></span></div>
                         <div style="clear:left;"></div>
                    </li>
                </ul>
                <div style="clear:left;"></div>
            <div class="jt_save_box">
                <div class="jt_save_popup">
                    <img src="<?php echo Plugins_url('/img/save_icon.png', __FILE__); ?>" alt="Add Icon" /> &nbsp;저장 
                </div>
                <div class="loading_icon"><img src="<?php echo Plugins_url('/img/loading.gif', __FILE__); ?>" alt="loading" /></div>
                <div class="save_ok">저장되었습니다</div>
            </div>
            </article>
        </section>
       <?php
       }
    }else{
    ?>
        <p id="empty_section">'팝업 추가'를 통해 새로운 팝업을 추가하세요.</p>
    <?php
    }
}


/*----------------------------------------------------------------------------
 * Admin Setting Page
 ---------------------------------------------------------------------------*/
function JTPOP_settings_page(){
?>  
<!--[if lt IE 9]>
<script src="<?php echo Plugins_url('/js/html5shiv.js', __FILE__); ?>"></script>
<![endif]-->
<div id="jt_pop_admin_page">
    <div id="jtpop_header">
        <img src="<?php echo Plugins_url('/img/jt_logo.png', __FILE__); ?>" alt="STUDIO JT" />
        <p id="jt_plugin_title" class="jt_popups_subtitle">JT BASIC SETTINGS</p>
    </div>
    
    <div id="popups_option_container">
        <form name="basic_option_form" method="post" action="options.php">
        <?php settings_fields('JTPOP_bs_setting'); ?>
            <ul>
                <li>배경색상의 투명도는 <input type="text" id="basic_bgopacity" name="JTPOP_bg_opacity" value="<?php echo get_option('JTPOP_bg_opacity'); ?>" maxlength="3"/>% 로 합니다. (100% 불투명, 0% 투명) </li>
                <li id="popup_shadow_option">팝업에 그림자를 &nbsp;&nbsp; <input type="radio" id="shadow" name="JTPOP_popup_shadow" value="shadow" <?php if(get_option('JTPOP_popup_shadow') == "shadow" || get_option('JTPOP_popup_style') == "") echo "checked"; ?> /><label for="shadow">사용합니다.</label> &nbsp;&nbsp;/&nbsp;&nbsp; <input type="radio" id="noshadow" name="JTPOP_popup_shadow" value="noshadow" <?php if(get_option('JTPOP_popup_shadow') == "noshadow") echo "checked"; ?> /><label for="noshadow">사용하지 않습니다.</label> </li>
                <li>팝업의 스타일을 정합니다.
                    <div id="popup_style_option">
                    <input type="radio" id="style1" name="JTPOP_popup_style" value="type1" <?php if(get_option('JTPOP_popup_style') == "type1" || get_option('JTPOP_popup_style') == "") echo "checked"; ?>/><label for="style1"><img src="<?php echo Plugins_url('/img/popups_style1.png', __FILE__); ?>" alt="type 1" /></label>
                    <input type="radio" id="style2" name="JTPOP_popup_style" value="type2" <?php if(get_option('JTPOP_popup_style') == "type2") echo "checked"; ?> /><label for="style2"><img src="<?php echo Plugins_url('/img/popups_style2.png', __FILE__); ?>" alt="type 1" /></label>
                </div></li>
            </ul>
            <div class="save_box">
                <div class="jt_save_popup2">
                    <img src="<?php echo Plugins_url('/img/save_icon.png', __FILE__); ?>" alt="Add Icon" /> &nbsp;저장 
                </div>
                <div class="loading_icon2 basic_save_loading"><img src="<?php echo Plugins_url('/img/loading.gif', __FILE__); ?>" alt="loading" /></div>
                <div id="jt_reset">초기화</div>
                <div class="loading_icon2 reset_loading"><img src="<?php echo Plugins_url('/img/loading.gif', __FILE__); ?>" alt="loading" /></div>
                <div style="clear:left;"></div>
            </div>
        </form>
    </div>
    <div id="popups_option_title" class="jt_popups_subtitle">
        <p>JT POPUP SETTINGS</p>
    </div>
    <div id="jtpop_menu">
        <p id="jt_add_popup">
            <img src="<?php echo Plugins_url('/img/add_icon.gif', __FILE__); ?>" alt="Add Icon" />
         &nbsp;팝업 추가</p>
        <p id="add_loading"><img src="<?php echo Plugins_url('/img/loading.gif', __FILE__); ?>" alt="loading" /></p>
        <p id="no_add_popup">팝업은 최대 8개까지 등록할 수 있습니다.</p>
    </div>
    <div id="popups_container">
    <?php
    JTPOP_popup_list();
    ?>
    <div style="clear:left;"></div>
    </div>
    <div id="popups_reference" class="jt_popups_subtitle">
        <p>JT POPUP REFERENCE</p>
    </div>
    <div id="jtpop_footer">
        <div id="footer_help">
            <ul>
                <li>팝업은 테마의 첫 페이지에만 나타나며 최대 8개까지 등록할 수 있습니다.</li>
                <li>모바일 환경에서는 팝업이 나타나지 않습니다.</li>
            </ul>
        </div>
    </div>
    
</div>
<?php 
                    
} 


/*----------------------------------------------------------------------------
 * JT_POPUPS Show Popups Background
 ---------------------------------------------------------------------------*/
function JTPOP_show_popup(){
    
    global $wpdb;

    $table = $wpdb -> prefix . "jt_popups";
    $fivesdrafts = $wpdb->get_results(
            "SELECT * FROM `" . $table . 
            "` WHERE startdate <= CURRENT_DATE() 
            AND enddate >= CURRENT_DATE() 
            LIMIT 0, 8"
            );

    if(count($fivesdrafts) > 0 ){
        
        //PC POPUPS Background Print
        if(wp_is_mobile() == FALSE){
            
            $jt_opacity = intval(get_option('JTPOP_bg_opacity'));
                if($jt_opacity != "") $jt_opacity = $jt_opacity / 100;
                else $jt_opacity = 0;
            
            $jt_opacity_ie8 = intval(get_option('JTPOP_bg_opacity'));
                if($jt_opacity_ie8 == "") $jt_opacity_ie8 = 0;
            
            $jt_html = '<div class="jtpop-page" style="opacity:'. $jt_opacity . '; filter:alpha(opacity='. $jt_opacity_ie8 .'); display:none;"></div>';
            
            echo $jt_html;
            
            //PC POPUPS Print
            JTPOP_show_html();
        }
        
    }
    JTPOP_show_script_style();
}


/*----------------------------------------------------------------------------
 * JT_POPUPS Show Popups List
 ---------------------------------------------------------------------------*/
function JTPOP_show_html(){

    global $wpdb;
      
    $popupstyle = get_option('JTPOP_popup_style') == "" ? "type1" : get_option('JTPOP_popup_style');
    
    $table = $wpdb -> prefix . "jt_popups";
    $fivesdrafts = $wpdb->get_results(
      "SELECT * FROM `" . $table . 
           "` WHERE startdate <= CURRENT_DATE() 
           AND enddate >= CURRENT_DATE() 
           LIMIT 0, 8"
           );

    foreach ($fivesdrafts as $key => $value) {
        if($value -> pcimg != 0){
            $pcpopupsize++;
        }
    }
    foreach ($fivesdrafts as $key => $value) {
        if($value -> pcimg != 0){
        $gallery_image_id = $value -> pcimg;
        $image_attributes = wp_get_attachment_image_src( $gallery_image_id, 'full');

        if($value->popuptop == "" && $value->popupleft == ""){
            echo '<article id=popup' . $value -> rowid . ' class="jtpop-bgs">';
        }else{
            echo '<article id=popup' . $value -> rowid . ' class="jtpop-bgs" style="top:' .$value->popuptop. 'px; left:'.$value->popupleft. 'px; z-index:99999'.$value->zindex.'">';
        }
        ?>
        <?php
            $popup_style = "style1";
            if(get_option('JTPOP_popup_style') == "type2") $popup_style = "style2";
            $popup_shadow = "shadow";
            if(get_option('JTPOP_popup_shadow') == "noshadow") $popup_shadow = "noshadow";
        ?>
            <div class="jtpop_bg_page <?php echo $popup_style. " " . $popup_shadow; ?>">
                <div class="popclose"><img src="
                <?php if($value->closecolor == "jtpop_black" && $popupstyle == "type1" || $value->closecolor == NULL && $popupstyle == "type1"){
                        echo Plugins_url('/img/close_pop.png', __FILE__); 
                      }
                      if($value->closecolor == "jtpop_white" && $popupstyle == "type1"){
                        echo Plugins_url('/img/close_pop_white.png', __FILE__);
                      }
                      if($popupstyle == "type2"){
                        echo Plugins_url('/img/close_pop_small2.png', __FILE__);
                      }
                ?>" alt="close" ?></div>
                <div class="jt-popup">
                    <div>
                        <?php if($value -> imglink != "" && $value -> imglink != "http://") echo '<a href="' . $value -> imglink . '">'; ?>
                        <img src="<?php echo $image_attributes[0]; ?>" alt="notice popup img" <?php if($value -> radius != NULL) echo 'class="img_radius_' . $value -> radius .'"'; ?> />
                        <?php if($value -> imglink != "" && $value -> imglink != "http://") echo '</a>'; ?>
                    </div>
                </div>
                <div class="popip_footer <?php if($value -> radius != NULL) echo 'footer_radius_' . $value -> radius; ?>">
                    <span class="notoday"><img src="
                        <?php if( $value->closecolor == "jtpop_black" && $popupstyle == "type1" || $value->closecolor == NULL && $popupstyle == "type1"){
                                  echo Plugins_url('/img/close_pop2.png', __FILE__); 
                              }else{
                                  echo Plugins_url('/img/close_pop2_white.png', __FILE__); 
                        }?>" alt="today close" title="오늘 하루 열지 않음"></span>
                </div>
            </div>
        </article>
        <?php
        }
    }
}


/*----------------------------------------------------------------------------
 * JT_POPUPS Show Popups Script & Stylesheet
 ---------------------------------------------------------------------------*/
function JTPOP_show_script_style(){
    wp_enqueue_script('JTPOP-jquery-cookie', Plugins_url('/js/jquery.cookie.js', __FILE__), array('jquery'), '1.4.0', true);
    wp_enqueue_script('JTPOP-show-script', Plugins_url('/js/jt-popup-show.js', __FILE__), array('jquery', 'JTPOP-jquery-cookie'), '1.0.0', true);  
    wp_enqueue_style('JTPOP-popup-show', Plugins_url('/css/jt-popup-show.css', __FILE__), false, '1.0.0');
    
    global $wpdb;

    $table = $wpdb -> prefix . "jt_popups";
    $fivesdrafts = $wpdb->get_results(
            "SELECT * FROM `" . $table . 
            "` WHERE startdate <= CURRENT_DATE() 
            AND enddate >= CURRENT_DATE() 
            LIMIT 0, 8"
            );

    //PC Popups Size
    foreach ($fivesdrafts as $key => $value) {
        if($value -> pcimg != 0){
            $pcpopupsize++;
        }
    }
    if($pcpopupsize == 1 && $value->popuptop == "" && $value->popupleft == ""){
        $pcpopupcenter = true;
    }
    
    //PARAMS
    $JTPOP_parmas_array = array(
    'pcpopupsize' => $pcpopupsize,
    'pcpopupcenter' => $pcpopupcenter
    );
    
    wp_localize_script( 'JTPOP-show-script', 'JTPOP_parmas', $JTPOP_parmas_array );
}

?>