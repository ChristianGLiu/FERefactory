<?php
if (WP_DEBUG && WP_DEBUG_DISPLAY)
{
   ini_set('error_reporting', E_ALL & ~E_STRICT & ~E_NOTICE);
}
if(is_admin()){
	/** Absolute path to the WordPress directory. */
	if ( !defined('ABSPATH') )
	    define('ABSPATH', dirname(__FILE__) . '/');

	define('CONCATENATE_SCRIPTS', false);
}
define("ET_UPDATE_PATH",    "//www.enginethemes.com/forums/?do=product-update");
define("ET_VERSION", '1.6.9');

if(!defined('ET_URL'))
	define('ET_URL', '//www.enginethemes.com/');

if(!defined('ET_CONTENT_DIR'))
	define('ET_CONTENT_DIR', WP_CONTENT_DIR.'/et-content/');

define ( 'TEMPLATEURL', get_bloginfo('template_url') );
define('THEME_NAME', 'forumengine');

define('THEME_CONTENT_DIR', WP_CONTENT_DIR . '/et-content' . '/' . THEME_NAME );
define('THEME_CONTENT_URL', content_url() . '/et-content' . '/' . THEME_NAME );

if(!defined('ET_LANGUAGE_PATH') )
	define('ET_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang');

if(!defined('ET_CSS_PATH') )
	define('ET_CSS_PATH', THEME_CONTENT_DIR . '/css');

require_once TEMPLATEPATH . '/includes/index.php';
//google captcha class
require_once TEMPLATEPATH . '/includes/google-captcha.php';

try {
	if ( is_admin() ){
		new ET_ForumAdmin();
	} else {
		new ET_ForumFront();
	}

} catch (Exception $e) {
	echo $e->getMessage();
}

add_theme_support( 'automatic-feed-links');
add_theme_support('post-thumbnails');

function et_prevent_user_access_wp_admin ()  {
	if(!current_user_can('manage_options') && !current_user_can('manage_product')) {
		//wp_redirect(home_url());
		exit;
	}
}

/// for test purpose
add_action( 'init', 'test_oauth' );
function test_oauth(){
	if ( isset($_GET['test']) && $_GET['test'] == 'twitter' ){
		require dirname(__FILE__) . '/auth.php';
		exit;
	}
}
function je_comment_template($comment, $args, $depth){
	$GLOBALS['comment'] = $comment;
?>
	<li class="et-comment" id="comment-<?php echo $comment->comment_ID ?>">
		<div class="et-comment-left">
			<div class="et-comment-thumbnail">
				<?php echo et_get_avatar($comment->user_id); ?>
			</div>
		</div>
		<div class="et-comment-right">
			<div class="et-comment-header">
				<a href="<?php comment_author_url() ?>"><strong class="et-comment-author"><?php comment_author() ?></strong></a>
				<span class="et-comment-time icon" data-icon="t"><?php comment_date() ?></span>
			</div>
			<div class="et-comment-content">
				<?php echo esc_attr( get_comment_text($comment->comment_ID) ) ?>
				<p class="et-comment-reply"><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></p>
			</div>
		</div>
		<div class="clearfix"></div>
<?php
}
/**
 *Check to load mobile version
 *
 *@return true if load mobile version / false if don't load
 *@since version 1.6.1
 */
if(!function_exists('et_load_mobile')) {
	function et_load_mobile() {
		global $isMobile;
		$detector = new ET_MobileDetect();
		$isMobile = $detector->isMobile() && ( ! $detector->isAndroidtablet() ) && ( ! $detector->isIpad() );
		$isMobile = apply_filters( 'et_is_mobile', $isMobile ? TRUE : FALSE );
		if ( $isMobile && ( ! isset( $_COOKIE[ 'mobile' ] ) || md5( 'disable' ) != $_COOKIE[ 'mobile' ] ) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
/**
 * Remove desktop style and script when load mobile version
 */


add_action('wp_head', 'et_wp_head');
function et_wp_head() {
	if(et_load_mobile()){
		return;
	}
}
add_action('wp_footer', 'et_wp_footer');
function et_wp_footer() {
	if(et_load_mobile()){
		return;
	}
}

//add_action('wp_head', 'et_get_wechat_login_qrcode');
//function et_get_wechat_login_qrcode() {
//	$wechat_login_qrcode_script = '<script>!function(a,b){function d(a){var e,c=b.createElement("iframe"),d="https://open.weixin.qq.com/connect/qrconnect?appid="+a.appid+"&scope="+a.scope+"&redirect_uri="+a.redirect_uri+"&state="+a.state+"&login_type=jssdk";d+=a.style?"&style="+a.style:"",d+=a.href?"&href="+a.href:"",c.src=d,c.frameBorder="0",c.allowTransparency="true",c.scrolling="no",c.width="300px",c.height="400px",e=b.getElementById(a.id),e.innerHTML="",e.appendChild(c)}a.WxLogin=d}(window,document);</script>';
   // $_SESSION['wechatstate'] = uniqid(rand(), true);
//    echo $wechat_login_qrcode_script;
//}

/**
 * Add closed status to search query.
 *
 * @param $query
 *
 * @return void
 *
 * @author nguyenvanduocit
 */
function add_status_to_search_query( $query ) {
	if ( $query->is_search() && $query->is_main_query() ) {
		$query->query_vars['post_status'] = apply_filters('fe_thread_status', array('publish', 'closed'));
	}
}
add_action( 'pre_get_posts', 'add_status_to_search_query' );

add_filter('et_is_mobile', 'disable_mobile');
function disable_mobile($mobile){
 return false;
}

if(!function_exists('bac_PostViews')) {
function bac_PostViews($post_ID) {

    //Set the name of the Posts Custom Field.
    $count_key = 'post_views_count';

    //Returns values of the custom field with the specified key from the specified post.
    $count = get_post_meta($post_ID, $count_key, true);

    //If the the Post Custom Field value is empty.
    if($count == ''){
        $count = 0; // set the counter to zero.

        //Delete all custom fields with the specified key from the specified post.
        delete_post_meta($post_ID, $count_key);

        //Add a custom (meta) field (Name/value)to the specified post.
        add_post_meta($post_ID, $count_key, '0');
        return $count . ' View';

    //If the the Post Custom Field value is NOT empty.
    }else{
        $count++; //increment the counter by 1.
        //Update the value of an existing meta key (custom field) for the specified post.
        update_post_meta($post_ID, $count_key, $count);

        //If statement, is just to have the singular form 'View' for the value '1'
        if($count == '1'){
        return $count . ' View';
        }
        //In all other cases return (count) Views
        else {
        return $count . ' Views';
        }
    }
}
}

function print_menu_shortcode($atts, $content = null) {
    extract(shortcode_atts(array( 'name' => null, ), $atts));
    return wp_nav_menu( array( 'menu' => $name, 'echo' => false ) );
}
add_shortcode('menu', 'print_menu_shortcode');

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);

function my_theme_wrapper_start() {
  echo '<div class="container main-center"><div class="row">';
  echo '<div class="header-bottom header-filter"><div class="main-center container">'.wp_nav_menu( array( 'menu' => '商铺', 'echo' => false ) ).'</div></div>';
  echo '<div class="col-md-9 col-sm-12 marginTop30">';
}

function my_theme_wrapper_end() {
  echo '</div><div class="col-md-3 col-sm-12 sidebar">';
  echo do_shortcode('[do_widget id=open_social_share_widget-4]');
  echo do_shortcode('[do_widget id=dc_product_vendors_list-3]');
  echo do_shortcode('[do_widget id=woocommerce_product_categories-3]');
  echo do_shortcode('[do_widget id=woocommerce_recently_viewed_products-2]');

  echo '</div></div></div>';
}
