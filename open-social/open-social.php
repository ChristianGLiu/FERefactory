<?php
/**
 * Plugin Name: Open Social
 * Plugin URI: https://www.xiaomac.com/201311150.html
 * Description: Login or Share with social networks: QQ, WeiBo, Google, Microsoft, DouBan, XiaoMi, WeChat Open, WeChat MP, GitHub, Twitter, Facebook. No API! Single PHP!
 * Author: Afly
 * Author URI: https://www.xiaomac.com/
 * Version: 1.8.0
 * License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: open-social
 * Domain Path: /lang
 */

if(!session_id()) session_start();
$GLOBALS['osop'] = get_option('osop');

//init
add_action('init', 'open_init', 1);
function open_init() {
    $uri = $_SERVER['REQUEST_URL'];
    if(stristr($uri,'/xh')!=false){return;}
	load_plugin_textdomain( 'open-social', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	$GLOBALS['open_arr'] = array(
		'qq'=>__('QQ','open-social'),
		'sina'=>__('Sina','open-social'),
		'google'=>__('Google','open-social'),
		'live'=>__('Microsoft','open-social'),
		'douban'=>__('Douban','open-social'),
		'xiaomi'=>__('XiaoMi','open-social'),
		'oschina'=>__('OSChina','open-social'),
		'facebook'=>__('Facebook','open-social'),
		'twitter'=>__('Twitter','open-social'),
		'github'=>__('Github','open-social'),
		'wechat'=>__('WeChat','open-social'),
		'wechat_mp'=>__('WeChat.MP','open-social')
	);
	$GLOBALS['open_share_arr'] = array(
		'weibo'=>array(__('Share with Weibo','open-social'),"http://v.t.sina.com.cn/share/share.php?url=%URL%&title=%TITLE%&pic=%PIC%&appkey=".osop('SINA_AKEY')."&ralateUid=".osop('share_sina_user')."&language=zh_cn&searchPic=true"),
		'qqzone'=>array(__('Share with QQZone','open-social'),"http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=%URL%&title=%TITLE%&desc=&summary=&site=&pics=%PIC%"),
		'wechat'=>array(__('Share with WeChat','open-social'),"QRCODE"),
		'twitter'=>array(__('Share with Twitter','open-social'),"http://twitter.com/home/?status=%TITLE%:%URL%"),
		'facebook'=>array(__('Share with Facebook','open-social'),"http://www.facebook.com/sharer.php?u=%URL%&amp;t=%TITLE%")
	);
	if(isset($_GET['error_description']) && isset($_SESSION['state'])){
		open_check_callback($_GET,'login','code');
	}
	define('OPEN_CBURL', osop('extend_callback_url') ? osop('extend_callback_url') : home_url('/'));

	if(count($_GET)==2  && !isset($_GET['action']) && !isset($_GET['client_id']) && isset($_GET['code']) && isset($_GET['state']) && !isset($_SESSION['back'])) {
	echo "test".var_dump($_GET).' '.var_dump($_SESSION);
                    if(!isset($_GET['code']) || isset($_GET['error']) || isset($_GET['error_code']) || isset($_GET['error_description'])){
                        open_next(OPEN_CBURL);
                    }
                    define('OPEN_TYPE','WECHAT');
                    $os = new WECHAT_CLASS();
                    $os -> open_callback($_GET['code']);
                    open_action( $os -> open_new_user() );
        } else if (isset($_GET['connect']) || (isset($_GET['code']) && isset($_GET['state']) && isset($_SESSION['state']))) {
		$action = isset($_GET['action']) ? $_GET['action'] : '';
		if(!isset($_GET['connect'])){
		  // if(!defined('OPEN_TYPE')) {
			foreach ($GLOBALS['open_arr'] as $k => $v){
				if(osop(strtoupper($k)) && $_GET['state'] == md5($k.$_SESSION['state']) && $k != 'WECHAT'){
					$action = 'callback';
					define('OPEN_TYPE',$k);
					break;
				}
			}
		//	} else {
		//	  $action = 'callback';
		//	}
		}else{
			if(in_array($_GET['connect'],array_keys($GLOBALS['open_arr'])) && osop(strtoupper($_GET['connect']))){
				define('OPEN_TYPE',$_GET['connect']);
			}
		}
		if(!defined('OPEN_TYPE')) exit();
		$open_class = strtoupper(OPEN_TYPE).'_CLASS';
		//echo $open_class;
		$os = new $open_class();
		if(isset($_GET['back'])) $_SESSION['back'] = $_GET['back'];


		if ($action == 'login') {
			$_SESSION['state'] = uniqid(rand(), true);
			$os -> open_login(md5(OPEN_TYPE.$_SESSION['state']));
		} else if ($action == 'callback') {
			if(!isset($_GET['code']) || isset($_GET['error']) || isset($_GET['error_code']) || isset($_GET['error_description'])){
				open_next(OPEN_CBURL);
			}
			$os -> open_callback($_GET['code']);
			open_action( $os -> open_new_user() );
		} else if ($action == 'bind') {
			open_action($_POST);
		} else if ($action == 'unbind') {
			open_unbind();
		}
	} 
} 

register_activation_hook( __FILE__, 'open_social_activation' );
function open_social_activation(){
	if(!$GLOBALS['osop']) update_option('osop', array(
		'show_login_page'	    => 0,
		'show_login_form'	    => 1,
		'show_share_content'    => 0,
		'extend_show_nickname'	=> 1,
		'extend_comment_email'	=> 1,
		'extend_change_name'	=> 0,
		'extend_hide_user_bar'	=> 0,
		'delete_setting'	    => 0
	));
}

register_uninstall_hook( __FILE__, 'open_social_uninstall' );
function open_social_uninstall(){
	if( osop('delete_setting',1) ) delete_option('osop');
}

function osop($osop_key,$osop_val=NULL){
	if(isset($GLOBALS['osop']) && isset($GLOBALS['osop'][$osop_key])){
		return isset($osop_val) ? $GLOBALS['osop'][$osop_key]==$osop_val : $GLOBALS['osop'][$osop_key];
	}
	return '';
}

function osin($str, $find){
	return stripos($str, $find) !== false;
}

//CLASSES
class QQ_CLASS {
	function open_login($state) {
		$params=array(
			'response_type'=>'code',
			'client_id'=>osop('QQ_AKEY'),
			'scope'=>'get_user_info',
			'redirect_uri'=>OPEN_CBURL,
			'state'=>$state
		);
		open_next('https://graph.qq.com/oauth2.0/authorize?'.http_build_query($params));
	} 
	function open_callback($code) {
		$params=array(
			'grant_type'=>'authorization_code',
			'code'=>$code,
			'client_id'=>osop('QQ_AKEY'),
			'client_secret'=>osop('QQ_SKEY'),
			'redirect_uri'=>OPEN_CBURL
		);
		$str = open_connect_http('https://graph.qq.com/oauth2.0/token?'.http_build_query($params));
		open_check_callback($str,$code,'access_token');
		$_SESSION['access_token'] = $str['access_token'];
		$str = open_connect_http("https://graph.qq.com/oauth2.0/me?access_token=".$_SESSION['access_token']);
		$str_r = json_decode(trim(trim(trim($str),'callback('),');'), true);
		open_check_callback($str_r,$_SESSION['access_token'],'openid');
		$_SESSION['open_id'] = $str_r['openid'];
	} 
	function open_new_user(){
		$user = open_connect_http('https://graph.qq.com/user/get_user_info?access_token='.$_SESSION['access_token'].'&oauth_consumer_key='.osop('QQ_AKEY').'&openid='.$_SESSION['open_id']);
		open_check_callback($user,$_SESSION['open_id'],'nickname');
		$_SESSION['open_img'] = isset($user['figureurl_qq_2']) ? $user['figureurl_qq_2'] : $user['figureurl_qq_1'];
		$name = isset($user['nickname']) ? $user['nickname'] : 'Q'.time();
		return array(
			'nickname' => $name
		);
	}
} 

class SINA_CLASS {
	function open_login($state) {
	    //echo $state;
		$params=array(
			'response_type'=>'code',
			'client_id'=>osop('SINA_AKEY'),
			//'forcelogin'=>'true',
			'redirect_uri'=>OPEN_CBURL,
			'state'=>$state
		);
		open_next('https://api.weibo.com/oauth2/authorize?'.http_build_query($params));
	} 
	function open_callback($code) {
		$params=array(
			'grant_type'=>'authorization_code',
			'code'=>$code,
			'client_id'=>osop('SINA_AKEY'),
			'client_secret'=>osop('SINA_SKEY'),
			'redirect_uri'=>OPEN_CBURL
		);
		$str = open_connect_http('https://api.weibo.com/oauth2/access_token', http_build_query($params), 'POST');

		open_check_callback($str,$code,'access_token');
		$_SESSION['access_token'] = $str['access_token'];
		$_SESSION['open_id'] = $str['uid'];
	}
	function open_new_user(){
		$user = open_connect_http("https://api.weibo.com/2/users/show.json?access_token=".$_SESSION['access_token']."&uid=".$_SESSION['open_id']);
		open_check_callback($user,$_SESSION['access_token'],'screen_name');
		$_SESSION['open_img'] = isset($user['avatar_large']) ? $user['avatar_large'] : $user['profile_image_url'];
		return array(
			'nickname' => $user['screen_name'],
			'user_url' => 'http://weibo.com/'.$user['profile_url']
		);
	} 
} 

class GOOGLE_CLASS {
	function open_login($state) {
		$params=array(
			'response_type'=>'code',
			'client_id'=>osop('GOOGLE_AKEY'),
			'scope'=>'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
			'redirect_uri'=> OPEN_CBURL,
			'access_type'=>'offline',
			'state'=>$state
		);
		open_next('https://accounts.google.com/o/oauth2/auth?'.http_build_query($params));
	} 
	function open_callback($code) {
		$params=array(
			'grant_type'=>'authorization_code',
			'code'=>$code,
			'client_id'=>osop('GOOGLE_AKEY'),
			'client_secret'=>osop('GOOGLE_SKEY'),
			'redirect_uri'=>OPEN_CBURL
		);
		$str = open_connect_http('https://accounts.google.com/o/oauth2/token', http_build_query($params), 'POST');
		open_check_callback($str,$code,'access_token');
		$_SESSION['access_token'] = $str['access_token'];
	}
	function open_new_user(){
		$user = open_connect_http('https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$_SESSION['access_token']);
		open_check_callback($user,$_SESSION['access_token'],'id');
		$_SESSION['open_id'] = $user['id'];
		$_SESSION['open_img'] = $user['picture'];
		return array(
			'nickname' => $user['name'],
			'user_url' => $user['link'],
			'user_email' => $user['email']
		);
	}
} 

class LIVE_CLASS {
	function open_login($state) {
		$params=array(
			'response_type'=>'code',
			'client_id'=>osop('LIVE_AKEY'),
			'redirect_uri'=>OPEN_CBURL,
			'scope'=>'wl.signin wl.basic wl.emails',
			'state'=>$state
		);
		open_next('https://login.live.com/oauth20_authorize.srf?'.http_build_query($params));
	} 
	function open_callback($code) {
		$params=array(
			'grant_type'=>'authorization_code',
			'code'=>$code,
			'client_id'=>osop('LIVE_AKEY'),
			'client_secret'=>osop('LIVE_SKEY'),
			'redirect_uri'=>OPEN_CBURL
		);
		$str = open_connect_http('https://login.live.com/oauth20_token.srf', http_build_query($params), 'POST');
		open_check_callback($str,$code,'access_token');
		$_SESSION['access_token'] = $str['access_token'];
	}
	function open_new_user(){
		$user = open_connect_http('https://apis.live.net/v5.0/me');
		open_check_callback($user,$_SESSION['access_token'],'id');
		$_SESSION['open_id'] = $user['id'];
		$_SESSION['open_img'] = 'https://apis.live.net/v5.0/'.$_SESSION['open_id'].'/picture';
		return array(
			'nickname' => $user['name'],
			'user_url' => 'https://profile.live.com/cid-'.$_SESSION['open_id'],
			'user_email' => $user['emails']['preferred']
		);
	}
} 

class DOUBAN_CLASS {
	function open_login($state) {
		$params=array(
			'response_type'=>'code',
			'client_id'=>osop('DOUBAN_AKEY'),
			'redirect_uri'=>OPEN_CBURL,
			'scope'=>'shuo_basic_r,shuo_basic_w,douban_basic_common',
			'state'=>$state
		);
		open_next('https://www.douban.com/service/auth2/auth?'.http_build_query($params));
	} 
	function open_callback($code) {
		$params=array(
			'grant_type'=>'authorization_code',
			'code'=>$code,
			'client_id'=>osop('DOUBAN_AKEY'),
			'client_secret'=>osop('DOUBAN_SKEY'),
			'redirect_uri'=>OPEN_CBURL
		);
		$str = open_connect_http('https://www.douban.com/service/auth2/token', http_build_query($params), 'POST');
		open_check_callback($str,$code,'access_token');
		$_SESSION['access_token'] = $str['access_token'];
		$_SESSION['open_id'] = $str['douban_user_id'];
	}
	function open_new_user(){
		$user = open_connect_http('https://api.douban.com/v2/user/~me?access_token='.$_SESSION['access_token']);
		open_check_callback($user,$_SESSION['access_token'],'name');
		$_SESSION['open_img'] = isset($user['large_avatar']) ? $user['large_avatar'] : $user['avatar'];
		return array(
			'nickname' => $user['name'],
			'user_url' => $user['alt']
		);
	}
} 

class XIAOMI_CLASS {
	function open_login($state) {
		$params=array(
			'response_type'=>'code',
			'client_id'=>osop('XIAOMI_AKEY'),
			'redirect_uri'=>OPEN_CBURL,
			'scope'=>'',
			'state'=>$state
		);
		open_next('https://account.xiaomi.com/oauth2/authorize?'.http_build_query($params));
	} 
	function open_callback($code) {
		$params=array(
			'grant_type'=>'authorization_code',
			'code'=>$code,
			'client_id'=>osop('XIAOMI_AKEY'),
			'client_secret'=>osop('XIAOMI_SKEY'),
			'redirect_uri'=>OPEN_CBURL,
			'token_type'=>'mac'
		);
		$str = open_connect_http('https://account.xiaomi.com/oauth2/token?'.http_build_query($params));
		open_check_callback($str,$code,'access_token');
		$_SESSION['access_token'] = $str['access_token'];
		$_SESSION['mac_key'] = $str['mac_key'];
	}
	function open_new_user(){
		list($usec, $sec) = explode(' ', microtime());
		$nonce = (float)mt_rand();
		$minutes = (int)($sec / 60);
		$nonce = $nonce.":".$minutes;
		$base = $nonce."\nGET\nopen.account.xiaomi.com\n/user/profile\nclientId=".osop('XIAOMI_AKEY')."&token=".$_SESSION['access_token']."\n";
		$sign = urlencode(base64_encode(hash_hmac('sha1', $base, $_SESSION['mac_key'], true)));
		$head = array('Authorization:MAC access_token="'.$_SESSION['access_token'].'", nonce="'.$nonce.'",mac="'.$sign.'"');
		$user = open_connect_http('https://open.account.xiaomi.com/user/profile?clientId='.osop('XIAOMI_AKEY').'&token='.$_SESSION['access_token'],'','GET',$head);
		open_check_callback($user,$_SESSION['access_token'],'data');
		$_SESSION['open_id'] = $user['data']['userId'];
		$_SESSION['open_img'] = $user['data']['miliaoIcon_120'];
		unset($_SESSION['mac_key']);
		return array(
			'nickname' => isset($user['data']['aliasNick']) ? $user['data']['aliasNick'] : $user['data']['miliaoNick'],
			'user_url' => 'http://www.miui.com/space-uid-'.$_SESSION['open_id'].'.html'
		);
	}
} 

class OSCHINA_CLASS {
	function open_login($state) {
		$params=array(
			'response_type'=>'code',
			'client_id'=>osop('OSCHINA_AKEY'),
			'redirect_uri'=>OPEN_CBURL,
			'state'=>$state
		);
		open_next('https://www.oschina.net/action/oauth2/authorize?'.http_build_query($params));
	} 
	function open_callback($code) {
		$params=array(
			'grant_type'=>'authorization_code',
			'code'=>$code,
			'client_id'=>osop('OSCHINA_AKEY'),
			'client_secret'=>osop('OSCHINA_SKEY'),
			'redirect_uri'=>OPEN_CBURL,
			'dataType'=>'json'
		);
		$str = open_connect_http('https://www.oschina.net/action/openapi/token', http_build_query($params), 'POST');
		open_check_callback($str,$code,'access_token');
		$_SESSION['access_token'] = $str['access_token'];
	}
	function open_new_user(){
		$user = open_connect_http('https://www.oschina.net/action/openapi/user?access_token='.$_SESSION['access_token']);
		open_check_callback($user,$_SESSION['access_token'],'avatar');
		$_SESSION['open_id'] = $user['id'];
		$_SESSION['open_img'] = $user['avatar'];
		return array(
			'nickname' => $user['name'],
			'user_url' => $user['url'],
			'user_email' => $user['email']
		);
	} 
} 

class FACEBOOK_CLASS {
	function open_login($state) {
		$params=array(
			'response_type'=>'code',
			'client_id'=>osop('FACEBOOK_AKEY'),
			'redirect_uri'=>OPEN_CBURL,
			'state'=>md5(uniqid(rand(), true)),
			'display'=>'page',
			'auth_type'=>'reauthenticate',
			'state'=>$state
			//'scope'=>'basic_info,email'
		);
		open_next('https://www.facebook.com/dialog/oauth?'.http_build_query($params));
	} 
	function open_callback($code) {
		$params=array(
			'code'=>$code,
			'client_id'=>osop('FACEBOOK_AKEY'),
			'client_secret'=>osop('FACEBOOK_SKEY'),
			'redirect_uri'=>OPEN_CBURL
		);
		$str = open_connect_http('https://graph.facebook.com/oauth/access_token?'.http_build_query($params));
		open_check_callback($str,$code,'access_token');
		$_SESSION['access_token'] = $str['access_token'];
	}
	function open_new_user(){
		$user_img = open_connect_http('https://graph.facebook.com/me/picture?redirect=false&height=100&type=small&width=100');
		open_check_callback($user_img,$_SESSION['access_token'],'data');
		$_SESSION['open_img'] = $user_img['data']['url'];
		$user = open_connect_http('https://graph.facebook.com/me?access_token='.$_SESSION['access_token']);
		open_check_callback($user,$_SESSION['access_token'],'id');
		$_SESSION['open_id'] = $user['id'];
		return array(
			'nickname' => $user['name'],
			'user_url' => $user['link'],
			'user_email' => $user['email']
		);
	} 
} 
  
class TWITTER_CLASS {
	function open_login($state) {
		$str = '';
		$params=array(
			'oauth_callback'=>add_query_arg(array('code'=>'twitter_fixer','state'=>$state),OPEN_CBURL),//fix no code return
			'oauth_consumer_key'=>osop('TWITTER_AKEY'),
			'oauth_nonce'=>md5(microtime().mt_rand()),
			'oauth_signature_method'=>'HMAC-SHA1',
			'oauth_timestamp'=>time(),
			'oauth_version'=>'1.0'
		);
		foreach ($params as $key => $val) { $str .= '&'.$key.'='.rawurlencode($val); }
		$base = 'GET&'.rawurlencode('https://api.twitter.com/oauth/request_token').'&'.rawurlencode(trim($str, '&'));
		$params['oauth_signature'] = base64_encode(hash_hmac('sha1', $base, osop('TWITTER_SKEY').'&', true));
		$str = '';
		foreach ($params as $key => $val) { $str .= ' '.$key.'="'.rawurlencode($val).'", '; }
		$head = array('Authorization: OAuth '.trim($str,', '));
		$token = open_connect_http('https://api.twitter.com/oauth/request_token','','',$head);
		open_check_callback($token,$str,'oauth_token');
		$_SESSION['oauth_token'] = $token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $token['oauth_token_secret'];
		open_next('https://api.twitter.com/oauth/authenticate?force_login=false&oauth_token='.$_SESSION['oauth_token']);
	} 
	function open_callback($code) {
		$str = '';
		$params=array(
			'oauth_consumer_key'=>osop('TWITTER_AKEY'),
			'oauth_nonce'=>md5(microtime().mt_rand()),
			'oauth_signature_method'=>'HMAC-SHA1',
			'oauth_timestamp'=>time(),
			'oauth_token'=>$_SESSION['oauth_token'],
			'oauth_version'=>'1.0'
		);
		foreach ($params as $key => $val) { $str .= '&'.$key.'='.rawurlencode($val); }
		$base = 'POST&'.rawurlencode('https://api.twitter.com/oauth/access_token').'&'.rawurlencode(trim($str, '&'));
		$params['oauth_signature'] = base64_encode(hash_hmac('sha1', $base, osop('TWITTER_SKEY').'&'.$_SESSION['oauth_token_secret'], true));
		unset($_SESSION['oauth_token']);
		unset($_SESSION['oauth_token_secret']);
		$str = '';
		foreach ($params as $key => $val) { $str .= ' '.$key.'="'.rawurlencode($val).'", '; }
		$head = array('Authorization: OAuth '.trim($str,', '));
		$token = open_connect_http('https://api.twitter.com/oauth/access_token','oauth_verifier='.$_GET['oauth_verifier'],'POST',$head);
		open_check_callback($token,$code,'oauth_token');
		$_SESSION['access_token'] = $token['oauth_token'];
		$_SESSION['open_id'] = $token['user_id'];
		$_SESSION['open_name'] = $token['screen_name'];
		$params['oauth_token'] = $_SESSION['access_token'];
		$str = '';
		unset($params['oauth_signature']);
		foreach ($params as $key => $val) { $str .= '&'.$key.'='.rawurlencode($val); }
		$base = 'GET&'.rawurlencode('https://api.twitter.com/1.1/account/verify_credentials.json').'&'.rawurlencode(trim($str, '&'));
		$params['oauth_signature'] = base64_encode(hash_hmac('sha1', $base, osop('TWITTER_SKEY').'&'.$token['oauth_token_secret'], true));
		$str = '';
		foreach ($params as $key => $val) { $str .= ' '.$key.'="'.rawurlencode($val).'", '; }
		$head = array('Authorization: OAuth '.trim($str,', '));
		$user_img = open_connect_http('https://api.twitter.com/1.1/account/verify_credentials.json','','',$head);
		open_check_callback($user_img,$str,'profile_image_url_https');
		$_SESSION['open_img'] = str_replace('_normal','_200x200',$user_img['profile_image_url_https']);
		$_SESSION['nick_name'] = $user_img['name'];
		if(strlen($_SESSION['open_id'])<6 || strlen($_SESSION['access_token'])<6) open_next('./');//Twitter: Something is technically wrong
	}
	function open_new_user(){
		$twnu = array(
			'nickname' => isset($_SESSION['nick_name']) ? $_SESSION['nick_name'] : $_SESSION['open_name'],
			'user_url' => 'https://twitter.com/'.$_SESSION['open_name']
		);
		unset($_SESSION['open_name']);
		unset($_SESSION['nick_name']);
		return $twnu;
	} 
} 

class GITHUB_CLASS {
	function open_login($state) {
		$params=array(
			'client_id'=>osop('GITHUB_AKEY'),
			'redirect_uri'=>OPEN_CBURL,
			'scope'=>'user',
			'state'=>$state
		);
		open_next('https://github.com/login/oauth/authorize?'.http_build_query($params));
	} 
	function open_callback($code) {
		$params=array(
			'code'=>$code,
			'client_id'=>osop('GITHUB_AKEY'),
			'client_secret'=>osop('GITHUB_SKEY'),
			'redirect_uri'=>OPEN_CBURL
		);
		$str = open_connect_http('https://github.com/login/oauth/access_token', http_build_query($params), 'POST');
		open_check_callback($str,$code,'access_token');
		$_SESSION['access_token'] = $str['access_token'];
	}
	function open_new_user(){
		$user = open_connect_http('https://api.github.com/user?access_token='.$_SESSION['access_token']);
		open_check_callback($user,$_SESSION['access_token'],'avatar_url');
		$_SESSION['open_id'] = $user['id'];
		$_SESSION['open_img'] = $user['avatar_url'];
		return array(
			'nickname' => $user['login'],
			'user_url' => 'https://github.com/'.$user['login']
		);
	} 
} 

class WECHAT_CLASS {
	function open_login($state) {
//	echo '<script>var obj = new WxLogin({id:"wc-login-qr",appid:"'.osop("WECHAT_AKEY").'",scope: "snsapi_login",redirect_uri: "'.OPEN_CBURL.'",state:"'..'", style: "black"});</script>';
/**
		$params=array(
			'appid'=>osop('WECHAT_AKEY'),
			'redirect_uri'=>OPEN_CBURL,
			'response_type'=>'code',
			'scope'=>'snsapi_login',
			'state'=>$state
		);
		open_next('https://open.weixin.qq.com/connect/qrconnect?'.http_build_query($params).'#wechat_redirect');
	**/

}
	function open_callback($code) {
		$params=array(
			'appid'=>osop('WECHAT_AKEY'),
			'secret'=>osop('WECHAT_SKEY'),
			'code'=>$code,
			'grant_type'=>'authorization_code'
		);
		$str = open_connect_http('https://api.weixin.qq.com/sns/oauth2/access_token', http_build_query($params), 'POST');
		open_check_callback($str,$code,'access_token');
		$_SESSION['access_token'] = $str['access_token'];
		$_SESSION['open_id'] = $str['openid'];
		//echo $_SESSION['access_token']. ' '.$_SESSION['open_id']."<br />";
	}
	function open_new_user(){
		$user = open_connect_http('https://api.weixin.qq.com/sns/userinfo?access_token='.$_SESSION['access_token'].'&openid='.$_SESSION['open_id']."&lang=zh_CN");
		open_check_callback($user,$_SESSION['open_id'],'headimgurl');
		//echo $user['headimgurl']."<br />";
		$_SESSION['open_img'] = $user['headimgurl'];
		if(isset($user['unionid'])) $_SESSION['unionid'] = $user['unionid'];
		return array(
			'nickname' => $user['nickname']
		);
	} 
} 

class WECHAT_MP_CLASS {
	function open_login($state) {
		$params=array(
			'appid'=>osop('WECHAT_MP_AKEY'),
			'redirect_uri'=>OPEN_CBURL,
			'response_type'=>'code',
			'scope'=>'snsapi_userinfo',//snsapi_base,snsapi_userinfo
			'state'=>$state
		);
		open_next('https://open.weixin.qq.com/connect/oauth2/authorize?'.http_build_query($params).'#wechat_redirect');
	} 
	function open_callback($code) {
		$params=array(
			'appid'=>osop('WECHAT_MP_AKEY'),
			'secret'=>osop('WECHAT_MP_SKEY'),
			'code'=>$code,
			'grant_type'=>'authorization_code'
		);
		$str = open_connect_http('https://api.weixin.qq.com/sns/oauth2/access_token', http_build_query($params), 'POST');
		open_check_callback($str,$code,'access_token');
		$_SESSION['access_token'] = $str['access_token'];
		$_SESSION['open_id'] = $str['openid'];
	}
	function open_new_user(){
		$user = open_connect_http('https://api.weixin.qq.com/sns/userinfo?access_token='.$_SESSION['access_token'].'&openid='.$_SESSION['open_id'].'&lang=zh_CN');
		open_check_callback($user,$_SESSION['open_id'],'headimgurl');
		$_SESSION['open_img'] = $user['headimgurl'];
		if(isset($user['unionid'])) $_SESSION['unionid'] = $user['unionid'];
		return array(
			'nickname' => $user['nickname']
		);
	} 
} 

function open_get_var_dump($mixed = null) {
	ob_start();
	var_dump($mixed);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

function open_next($msg){
//echo "header: ".$msg;
	header('Location:'.$msg);
	exit();
}

function open_text($msg,$title=''){
	if(empty($title)) $title = __('OOPS!','open-social');
	if(!osin($msg, 'button')) $msg .= '<p><a class="button" href="'.(is_user_logged_in()?get_edit_profile_url():wp_login_url()).'">'.__('Back').'</a></p>';
	wp_die('<h1>'.$title.'</h1><p>'.$msg.'</p>', __('Open Social','open-social'), array('response'=>200,'back_link'=>false));
}

function open_check_callback($arr,$in,$out){
	$err = '';
	if(!is_array($arr)){
		$err .= '<h3>ERROR:</h3><p>' . $arr . '</p>';
	}else if(isset($arr['error']) || isset($arr['error_code'])){
		if(isset($arr['error'])) $err .= '<h3>ERROR:</h3><p>' . $arr['error'] . '</p>';
		if(isset($arr['error_code'])) $err .= '<h3>CODE :</h3><p>' . $arr['error_code'] . '</p>';
		if(isset($arr['error_description'])) $err .= '<h3>MSG  :</h3><p>' . $arr['error_description'] . '</p>';
	}else if(!isset($arr[$out])){
		$err .= '<h3>ERROR:</h3><p>' . $in . ' =>' . $out . '</p>';
	}
	if($err){
		if(defined('OPEN_TYPE')) $err = '<h3>LOGIN:</h3><p>' . OPEN_TYPE . '</p>' . $err;
		$err .= '<h3>RETURN:</h3><pre>' . open_get_var_dump($arr) . '</pre>';
		open_text($err);
	}
}

function open_isbind($open_type,$open_id){
	global $wpdb;
	$bid = $wpdb -> get_var($wpdb -> prepare("SELECT user_id FROM $wpdb->usermeta um WHERE um.meta_key='%s' AND um.meta_value='%s'", 'open_type_'.$open_type, $open_id));
	if(!$bid){//single era
		$bid = $wpdb -> get_var($wpdb -> prepare("SELECT um1.user_id FROM $wpdb->usermeta um1 INNER JOIN $wpdb->usermeta um2 ON um1.user_id = um2.user_id WHERE (um1.meta_key='open_type' AND um1.meta_value='%s' AND um2.meta_key='open_id' AND um2.meta_value='%s')", $open_type, $open_id));
		if($bid){
			update_user_meta($bid, 'open_type_'.$open_type, $_SESSION['open_id']);
			delete_user_meta($bid, 'open_id');
		}
	}
	return $bid;
} 

function open_unbind(){
	if(!is_user_logged_in()) return;
	$user = get_current_user_id();
	$user_email = get_userdata($user)->user_email;;
	$open_type = get_user_meta($user, 'open_type', true);
	if(OPEN_TYPE == trim($open_type,',') && preg_match('/@fake\.com/',$user_email)){
		if(!isset($_POST['confirm'])){
			$html = '<form method="post" action="'.OPEN_CBURL.'?'.http_build_query(array('connect'=>OPEN_TYPE,'action'=>'unbind')).'"><p>';
			$html .= '<p>'.__('Warning: Unbind the only social login will cause deletion of a user along with fake email. If this account have published some posts or you just want to keep it, please renew a valid email that can reset password to login with, then try again.','open-social').'</p><br/>';
			$html .= '<p><a class="button" href="'.get_edit_profile_url().'">'.__('Back').'</a> ';
			$html .= '<input class="button" name="confirm" type="submit" value="'.__('Delete Users').'">';
			$html .= '</form>';
			open_text($html, sprintf(__('Unbind with %s','open-social'), strtoupper(OPEN_TYPE)));
		}else{
			if(count_user_posts($user)>0) open_text(__('This account has published some posts so that it cannot be deleted.','open-social'));
			if(!function_exists('wp_delete_user')) include_once(ABSPATH.'wp-admin/includes/user.php');
			wp_delete_user($user);
			open_next(home_url());
		}
	}
	if(osin($open_type, OPEN_TYPE.',')){
		$open_type = str_replace(OPEN_TYPE.',','',rtrim($open_type,',').',');
		update_user_meta($user, 'open_type', $open_type);
		update_user_meta($user, 'open_img', '');
		delete_user_meta($user, 'open_type_'.OPEN_TYPE);
		if(osin(OPEN_TYPE, 'wechat') && !osin($open_type, 'wechat')) delete_user_meta($user, 'open_type_wechat_unionid');
	}
	open_next(get_edit_profile_url());
}

function open_username_emoji($n){
	$tmpName = json_encode($n);
	$tmpName = preg_replace("/(\\\u[ed][0-9a-f]{3})/i","",$tmpName);
	return empty($tmpName) ? 'Null' : sanitize_text_field(json_decode($tmpName));
}

function open_action($newuser){
//echo var_dump($newuser)."<br />";
//echo var_dump($_SESSION)."<br />";
	if(empty($_SESSION['open_id']) || empty($_SESSION['access_token']) || !defined('OPEN_TYPE')) return;
	$_SESSION['nickname'] = $newuser['nickname'];
	if(is_user_logged_in()){ //bind
		$wpuid = get_current_user_id();
		$wpuid_type = get_user_meta($wpuid, 'open_type', true);
		if(osin($wpuid_type, OPEN_TYPE.',')){
			open_text(__('This account has bound with a login of this type already.','open-social'));//same type
		}
		$wpuid_open = open_isbind(OPEN_TYPE,$_SESSION['open_id']);
		if(!$wpuid_open && isset($_SESSION['unionid'])) $wpuid_open = open_isbind('wechat_unionid',$_SESSION['unionid']);
		if($wpuid_open){
			if($wpuid == $wpuid_open){
				if(!osin(OPEN_TYPE, 'wechat')){//one wechat seen as two
					open_text(__('This account has bound with this login already.','open-social'));//rebind
				}
			}else{
				open_text(__('This account has been bound by other user already, please unbind first then try again.','open-social'));	
			}
		}
	}else{ //login
		$wpuid = open_isbind(OPEN_TYPE,$_SESSION['open_id']);
		if(!$wpuid){
			if(isset($_SESSION['unionid'])) $wpuid = open_isbind('wechat_unionid',$_SESSION['unionid']);
			if(!$wpuid) $wpuid = username_exists(strtoupper(OPEN_TYPE).$_SESSION['open_id']);
			if(!$wpuid){
				if(!isset($newuser['user_login'])) $newuser['user_login'] = $_SESSION['nickname'];
				if(!isset($newuser['user_email'])) $newuser['user_email'] = '';
				if(!isset($newuser['user_url'])) $newuser['user_url'] = '';
				$newuser['user_login'] = str_replace(array(' ','@'),'_',sanitize_user($newuser['user_login'],true));
				$newuser['user_email'] = sanitize_email($newuser['user_email']);
				$newuser['nickname'] = open_username_emoji(sanitize_text_field($_SESSION['nickname']));
				if(empty($newuser['user_login'])){
					$user_rnd = rand(10000,99998);
					$newname = 'ECLINK_'.$user_rnd;
					while(username_exists($newname)){
						$user_rnd++;
						$newname = 'ECLINK_'.$user_rnd;
					}
					$newuser['user_login'] = $newname;
				}
				$user_ok = 0;
				if(!username_exists($newuser['user_login'])) $user_ok = $user_ok + 1;
				if(is_email($newuser['user_email']) && !email_exists($newuser['user_email'])) $user_ok = $user_ok + 5;
				/**
				if($user_ok < 6 || !isset($_POST['user_login'])){ //first time confirm
					$user_ok_arr = array('border:2px solid red','border:2px solid green');
					$url = OPEN_CBURL.'?'.http_build_query(array('connect'=>OPEN_TYPE,'action'=>'bind'));
					$html = '<style>p label{line-height:280%;}input{padding:5px;margin:4px;}</style>';
					$html .= '<form method="post" action="'.$url.'"><p>';
					$html .= '<label><input size=30 type=text name=user_login placeholder="'.__('Username').'" value="'.esc_attr($newuser['user_login']).'" style="'.$user_ok_arr[$user_ok%5^0].'" autocapitalize="none" autocorrect="off" maxlength="60" aria-required="true" required> '.__('Username').'</label><br/>';
					$html .= '<label><input size=30 type=email name=user_email placeholder="'.__('Email').'" value="'.esc_attr($newuser['user_email']).'" style="'.$user_ok_arr[round($user_ok/10)].'" aria-required="true" required> '.__('Email').'</label><br/>';
					$html .= '<label><input size=30 type=text name=nickname placeholder="'.__('Nickname','open-social').'" value="'.esc_attr($newuser['nickname']).'" style="'.$user_ok_arr[!empty($newuser['nickname'])].'" required> '.__('Nickname','open-social').'</label><br/>';
					$html .= '<label><input size=45 type=url name=user_url placeholder="'.__('Website').'" value="'.esc_url($newuser['user_url']).'"></label><br/>';
					$html .= '<p><a class="button" href="'.wp_login_url().'">'.__('Back').'</a> ';
					$html .= '<input class="button" type="submit" value="'.esc_attr__('Create New User','open-social').'"> ';
					$html .= '<button class="button" onclick="location.href=\''.wp_login_url($url).'&'.http_build_query(array('bind'=>OPEN_TYPE)).'\';return false;">'.esc_attr__('Bind with Existing User','open-social').'</button>';
					$html .= '</form>';
					open_text($html, sprintf(__('Login with %s','open-social'), strtoupper(OPEN_TYPE)));
				}
				**/
				$newuser['display_name'] = $newuser['nickname'];
				$newuser['user_pass'] = wp_generate_password();
				if(osop('extend_hide_user_bar',1) && !preg_match('/ Mobile/', $_SERVER['HTTP_USER_AGENT'])) $newuser['show_admin_bar_front'] = 'false';
				if(!function_exists('wp_insert_user')) include_once(ABSPATH.WPINC.'/registration.php');
				$wpuid = wp_insert_user($newuser);
				if(!$wpuid) open_text(__('This account may contain some incompatible characters.','open-social'));
				update_user_meta($wpuid, 'open_user', 1);//mark as plugin register
				if(osop('extend_user_role',1)) wp_update_user(array('ID'=>$wpuid, 'role'=>'subscriber'));
				if(osop('extend_send_email',1) && !preg_match('/@fake\.com/',$newuser['user_email'])) wp_send_new_user_notifications($wpuid);
			}
		} 
	} 
	if($wpuid){
		$open_type_list = get_user_meta($wpuid, 'open_type', true);
		if($open_type_list) $open_type_list = trim($open_type_list,',').',';
		if(!osin($open_type_list, OPEN_TYPE.',')) update_user_meta($wpuid, 'open_type', $open_type_list.OPEN_TYPE.',');
		if(isset($_SESSION['open_img'])){//login via plugin, with avatar
			if(substr($_SESSION['open_img'],0,4) != 'http') $_SESSION['open_img'] = plugins_url('/images/gravatar.png', __FILE__);
			update_user_meta($wpuid, 'open_img', esc_url($_SESSION['open_img']));
			unset($_SESSION['open_img']); 
		}
		update_user_meta($wpuid, 'open_type_'.OPEN_TYPE, $_SESSION['open_id']);
		update_user_meta($wpuid, 'open_access_token', $_SESSION['access_token']);
		if(isset($_SESSION['unionid'])){
			update_user_meta($wpuid, 'open_type_wechat_unionid', $_SESSION['unionid']);//wechat unionid
			unset($_SESSION['unionid']);
		}
		if(OPEN_TYPE=='sina' && user_can($wpuid,'administrator')){//only admin
			$GLOBALS['osop']['share_sina_access_token'] = $_SESSION['access_token'];//for post
			$GLOBALS['osop']['share_sina_user'] = $_SESSION['open_id'];//for share
			update_option('osop',$GLOBALS['osop']);
		}
		wp_set_auth_cookie($wpuid, true, is_ssl());
		wp_set_current_user($wpuid);
		$user_info = get_userdata($wpuid);
		$login_cookie = $user_info->display_name.'|'.$user_info->user_email.'|'.get_avatar_url( $wpuid );
        setcookie('wp_open_social_login', $login_cookie, time()+3600*24*15, COOKIEPATH, COOKIE_DOMAIN, is_ssl());
	}
	unset($_SESSION['open_id']);
	unset($_SESSION['access_token']);
	unset($_SESSION['nickname']);
	unset($_SESSION['state']);
	$back = isset($_SESSION['back']) ? $_SESSION['back'] : home_url();
	unset($_SESSION['back']);
	open_next($back);
}

function open_connect_http($url, $postfields=NULL, $method='GET', $headers=array()){
	$ci = curl_init();
	if(osop('extend_proxy_server')) curl_setopt($ci, CURLOPT_PROXY, osop('extend_proxy_server'));
	curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ci, CURLOPT_HEADER, false);
	curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ci, CURLOPT_TIMEOUT, 30);
	if($method=='POST'){
		curl_setopt($ci, CURLOPT_POST, TRUE);
		if(!empty($postfields)) curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
	}
	if(!$headers && isset($_SESSION['access_token'])){
		$headers[]='Authorization: Bearer '.$_SESSION['access_token'];
	}
	$headers[] = 'User-Agent: Social Share login(eclink.ca)';
	$headers[] = 'Expect:';
	curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ci, CURLOPT_URL, $url);
	$response = curl_exec($ci);
	if($response===false) $response = curl_error($ci);
	curl_close($ci);
	$response = trim(trim($response),'&&&START&&&');
	$json_r = array();
	$json_r = json_decode($response, true);
	if(count($json_r)==0){
		parse_str($response,$json_r);
		if(count($json_r)==1 && current($json_r)==='') return $response;
	}
	return $json_r;
}

function open_login_page(){
	return osin($_SERVER["SCRIPT_NAME"], strrchr(wp_login_url(), '/'));
}

add_action('wp_login', 'open_social_inner_login', 10, 2);
function open_social_inner_login($user_login, $user) {
	update_user_meta($user->ID, 'open_img', '');//default gravatar
}

add_action('wp_logout','open_social_user_logout');
function open_social_user_logout(){
    setcookie('wp_open_social_login', '', time()-3600*24*360, COOKIEPATH, COOKIE_DOMAIN, is_ssl());
}

//admin setting
add_action( 'admin_init', 'open_social_admin_init' );
function open_social_admin_init() {
	register_setting( 'open_social_options_group', 'osop' );
}

add_filter("plugin_action_links_".plugin_basename(__FILE__), 'open_settings_link' );
function open_settings_link($links) {
	array_unshift($links, '<a href="options-general.php?page=open-social">'.__('Settings').'</a>');
	return $links;
}

add_action('admin_menu', 'open_options_add_page');
function open_options_add_page() {
	if(current_user_can('manage_options')){
		add_options_page(__('Open Social','open-social'), __('Open Social','open-social'), 'manage_options', 'open-social', 'open_options_page');
	}
    if(current_user_can('publish_posts') && osop('share_post_weibo',1)) add_action('post_submitbox_misc_actions', 'open_social_post_misc_action');
}

function open_social_post_misc_action() {
	$html = '<div class=misc-pub-section><label> <input name="open_social_post_weibo_check" type="checkbox" value="1" /> ';
	$html .= __('Post with Sina Weibo','open-social');
	$html .= '</label> ';
	if(osop('open_social_post_weibo_result')) $html .= ': '.osop('open_social_post_weibo_result');
	$html .= '</div>';
	echo $html;
}

function open_options_page() {
	if ( osop('extend_user_transfer',1) ) {
		if(file_exists(dirname(__FILE__).'/transfer.php')) include_once(dirname(__FILE__).'/transfer.php');
		if(function_exists('open_social_user_transfer')){
			open_social_user_transfer();
			$GLOBALS['osop']['extend_user_transfer'] = 0;
			update_option('osop',$GLOBALS['osop']);
			echo '<div class="updated fade"><p><strong>'.__('Users Data Transfer Complete','open-social').'</strong></p></div>';
		}
	}
	?> 
	<div class="wrap">
		<h2><?php _e('Open Social','open-social')?>
		<small style="font-size:14px;padding-left:8px;color:#666">
		<?php
			$plugin_data = get_plugin_data( __FILE__ );
			echo 'v'.$plugin_data['Version'];
		?>
		</small>
		</h2>
		<script>(function($){
		$('#screen-meta').after('<div id="screen-meta-links"><div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle"><a href="https://www.xiaomac.com/201311150.html" id="show-settings-link" class="button show-settings" target="_blank"><?php _e("Plugin Homepage","open-social")?></a></div></div>'
		);})(jQuery);</script>
		<form action="options.php" method="post">
		<?php
			settings_fields( 'open_social_options_group' );
		?>
		<table class="form-table">
		<tr valign="top">
		<th scope="row"><?php _e('Login','open-social')?></th>
		<td><fieldset>
			<label for="osop[show_login_page]"><input name="osop[show_login_page]" id="osop[show_login_page]" type="checkbox" value="1" <?php checked(osop('show_login_page'),1);?> /> <?php _e('Show in Login page','open-social')?></label><br/>
			<label for="osop[show_login_form]"><input name="osop[show_login_form]" id="osop[show_login_form]" type="checkbox" value="1" <?php checked(osop('show_login_form'),1);?> /> <?php _e('Show before comment form','open-social')?></label><br/>
			<label for="osop[show_inner_login]"><input name="osop[show_inner_login]" id="osop[show_inner_login]" type="checkbox" value="1" <?php checked(osop('show_inner_login'),1);?> /> <?php _e('Show button for Inner Login','open-social')?></label><br/>
			<label for="osop[show_only_enabled]"><input name="osop[show_only_enabled]" id="osop[show_only_enabled]" type="checkbox" value="1" <?php checked(osop('show_only_enabled'),1);?> /> <?php _e('Hide account setting that not enabled','open-social')?></label><br/>
            <label for="osop[show_login]"><input name="osop[show_login]" id="osop[show_login]" type="checkbox" value="1" <?php checked(osop('show_login'),1);?> /> <?php _e('Enable to customize login code','open-social')?></label> <br/>
            <textarea name="osop[show_login_html]" id="osop[show_login_html]" rows="4" cols="90" placeholder="&lt;a href=&quot;<?php echo add_query_arg(array('connect'=>'qq','action'=>'login'),OPEN_CBURL);?>&quot;&gt;QQ&lt;/a&gt;"><?php echo esc_textarea( osop('show_login_html') ) ?></textarea><br/>
            <textarea name="osop[show_profile_html]" id="osop[show_profile_html]" rows="4" cols="90" placeholder="&lt;i id=&quot;os_user_name&quot;&gt;&lt;/i&gt; &lt;i id=&quot;os_user_email&quot;&gt;&lt;/i&gt; &lt;i id=&quot;os_user_avatar&quot;&gt;&lt;/i&gt;"><?php echo esc_textarea( osop('show_profile_html') ) ?></textarea><br/>
			<p>Shortcode: <code>[os_login]</code> <code>[os_login show="qq,sina"]</code> PHP: <code>&lt;?php echo open_social_login_html();?&gt;</code></p>
			<p>Shortcode: <code>[os_profile]</code>  PHP: <code>&lt;?php echo open_social_profile_html();?&gt;</code></p>
			<p>Login Link: <code><?php echo add_query_arg(array('connect'=>'qq','action'=>'login'),OPEN_CBURL);?></code></p>
			<p>Profile Code: <code>&lt;i id="os_user_name"&gt;&lt;/i&gt; &lt;i id="os_user_email"&gt;&lt;/i&gt; &lt;i id="os_user_avatar"&gt;&lt;/i&gt;</code></p>
		</fieldset>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row"><?php _e('Share','open-social')?></th>
		<td><fieldset>
			<label for="osop[show_share_content]"><input name="osop[show_share_content]" id="osop[show_share_content]" type="checkbox" value="1" <?php checked(osop('show_share_content'),1);?> /> <?php _e('Show in Post pages','open-social')?></label> <br/>
			<?php
			foreach ($GLOBALS['open_share_arr'] as $k=>$v) {
				echo '<label for="osop[share_'.$k.']"><input name="osop[share_'.$k.']" id="osop[share_'.$k.']" type="checkbox" value="1" '.checked(osop('share_'.$k),1,false).' title="'.__('Enabled','open-social').'" /> '.$v[0].'</label> ';
				echo '<br/>'; 
			}?>
			<label for="osop[show_share_jssdk]"><input name="osop[show_share_jssdk]" id="osop[show_share_jssdk]" type="checkbox" value="1" <?php checked(osop('show_share_jssdk'),1);?> /> <?php _e('Enable WeChat JSSDK (require WeChat.MP account setting)','open-social')?></label> 
			<a href="http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html" target=_blank>?</a><br/>
			<label for="osop[share_post_weibo]"><input name="osop[share_post_weibo]" id="osop[share_post_weibo]" type="checkbox" value="1" <?php checked(osop('share_post_weibo'),1);?> /> <?php _e('Post to Weibo when publish a new blog (require Administrator binding with Sina Weibo)','open-social')?></label> <a href="http://open.weibo.com/wiki/2/statuses/upload" target=_blank>?</a> <br/>
            <label for="osop[show_share]"><input name="osop[show_share]" id="osop[show_share]" type="checkbox" value="1" <?php checked(osop('show_share'),1);?> /> <?php _e('Enable to customize share code','open-social')?></label> <br/>
            <textarea name="osop[show_share_html]" id="osop[show_share_html]" rows="4" cols="90" placeholder="SHARE HTML CODE..."><?php echo esc_textarea( osop('show_share_html') ) ?></textarea><br/>
			<p>Shortcode: <code>[os_share]</code>  PHP: <code>&lt;?php echo open_social_share_html();?&gt;</code></p>
			<input type="hidden" name="osop[share_sina_user]" id="osop[share_sina_user]" class="regular-text" value="<?php echo osop('share_sina_user')?>" />
			<input type="hidden" name="osop[share_sina_access_token]" id="osop[share_sina_access_token]" class="regular-text" value="<?php echo osop('share_sina_access_token')?>" />
			<input type="hidden" name="osop[open_social_post_weibo_check]" id="osop[open_social_post_weibo_check]" class="regular-text" value="<?php echo osop('open_social_post_weibo_check')?>" />
		</fieldset>
		</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Extensions','open-social')?></th>
		<td><fieldset>
			<label for="osop[extend_callback_url]"><input name="osop[extend_callback_url]" id="osop[extend_callback_url]" class="regular-text" placeholder="<?php echo home_url('/')?>" value="<?php echo osop('extend_callback_url')?>" /> <?php _e('Set new Callback URL when cant login','open-social')?></label><br/>
			<label for="osop[extend_proxy_server]"><input name="osop[extend_proxy_server]" id="osop[extend_proxy_server]" class="regular-text" placeholder="127.0.0.1:8087" value="<?php echo osop('extend_proxy_server')?>" /> <?php _e('Proxy for server request within plugin','open-social')?></label><br/>
			<label for="osop[extend_guest_comment]"><input name="osop[extend_guest_comment]" id="osop[extend_guest_comment]" class="regular-text" placeholder="/:\/\//" value="<?php echo osop('extend_guest_comment')?>" /> <?php _e('Regexp anti-spam for guest comment','open-social')?></label><br/>
			<label for="osop[extend_comment_email]"><input name="osop[extend_comment_email]" id="osop[extend_comment_email]" type="checkbox" value="1" <?php checked(osop('extend_comment_email'),1);?> /> <?php _e('Receive reply email notification','open-social')?></label> <br/>
			<label for="osop[extend_show_nickname]"><input name="osop[extend_show_nickname]" id="osop[extend_show_nickname]" type="checkbox" value="1" <?php checked(osop('extend_show_nickname'),1);?> /> <?php _e('Show nickname in users list and orderby registered time desc','open-social')?></label>
			<a href="<?php echo admin_url('users.php');?>">#<?php _e('Users');?></a><br/>
			<label for="osop[extend_hide_user_bar]"><input name="osop[extend_hide_user_bar]" id="osop[extend_hide_user_bar]" type="checkbox" value="1" <?php checked(osop('extend_hide_user_bar'),1);?> /> <?php _e('Hide user bar for new user','open-social')?></label>
			<a href="<?php echo admin_url('profile.php');?>#comment_shortcuts">#<?php _e('Profile');?></a><br/>
			<label for="osop[extend_user_role]"><input name="osop[extend_user_role]" id="osop[extend_user_role]" type="checkbox" value="1" <?php checked(osop('extend_user_role'),1);?> /> <?php _e('Use Subscriber role for new user or default role if uncheck','open-social')?>
			<a href="<?php echo admin_url('options-general.php');?>#users_can_register">#<?php _e('General Settings');?></a></label> <br/>
			<label for="osop[extend_gravatar_disabled]"><input name="osop[extend_gravatar_disabled]" id="osop[extend_gravatar_disabled]" type="checkbox" value="1" <?php checked(osop('extend_gravatar_disabled'),1);?> /> <?php _e('Disable Gravatar with a default blank avatar','open-social')?></label>
			<a href="<?php echo admin_url('options-discussion.php');?>#show_avatars">#<?php _e('Discussion Settings');?></a><br/>
			<label for="osop[extend_button_tooltip]"><input name="osop[extend_button_tooltip]" id="osop[extend_button_tooltip]" type="checkbox" value="1" <?php checked(osop('extend_button_tooltip'),1);?> /> <?php _e('Add jQuery.tooltip to the buttons','open-social')?></label>
			<a href="https://jqueryui.com/tooltip/" target="_blank">#jQuery.tooltip</a> <br/>
			<?php if(file_exists(dirname(__FILE__).'/transfer.php')) : ?>
				<label for="osop[extend_user_transfer]"><input name="osop[extend_user_transfer]" id="osop[extend_user_transfer]" type="checkbox" value="1" <?php checked(osop('extend_user_transfer'),1);?> /> <?php _e('Experimental: Transfer &ltwp-connect&gt users data to be compatible with','open-social')?></label> 
				<a href="https://wordpress.org/plugins/wp-connect/" target="_blank">wp-connect</a><br/>
			<?php endif; ?>
			<label for="osop[extend_send_email]"><input name="osop[extend_send_email]" id="osop[extend_send_email]" type="checkbox" value="1" <?php checked(osop('extend_send_email'),1);?> /> <?php _e('Send an email to user that can reset password and notify admin after registration','open-social')?></label> <br/>
			<label for="osop[extend_change_name]"><input name="osop[extend_change_name]" id="osop[extend_change_name]" type="checkbox" value="1" <?php checked(osop('extend_change_name'),1);?> /> <?php _e('Allow binding user change their [username] one time and only (CAREFULLY)','open-social')?></label> <br/>
			<label for="osop[delete_setting]"><input name="osop[delete_setting]" id="osop[delete_setting]" type="checkbox" value="1" <?php checked(osop('delete_setting'),1);?> /> <?php _e('Clear all configurations in this page after plugin deleted (NOT RECOMMENDED)','open-social')?></label> <br/>
			<pre>Shortcode: <code>[os_hide] XXX [/os_hide]</code></pre>
		</fieldset>
		<?php submit_button();?>
		</td>
		</tr>
		</table>
	</div>

	<div class="wrap">
		<h2><?php _e('Account Setting','open-social')?></h2>
		<table class="form-table">
		<?php
			$open_arr_link = array(
				'qq'=> array('http://connect.qq.com/','http://wiki.connect.qq.com/%E7%BD%91%E7%AB%99%E6%8E%A5%E5%85%A5%E6%B5%81%E7%A8%8B'),
				'sina'=> array('http://open.weibo.com/','http://open.weibo.com/authentication'),
				'google'=> array('https://cloud.google.com/console','https://developers.google.com/accounts/docs/OAuth2WebServer'),
				'live'=> array('https://account.live.com/developers/applications','http://msdn.microsoft.com/en-us/library/live/ff621314.aspx'),
				'douban'=> array('http://developers.douban.com/','http://developers.douban.com/wiki/?title=oauth2'),
				'xiaomi'=> array('http://dev.xiaomi.com/','http://dev.xiaomi.com/doc/'),
				'oschina'=> array('http://www.oschina.net/openapi/','http://www.oschina.net/openapi/docs'),
				'facebook'=> array('https://developers.facebook.com/','https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/'),
				'twitter'=> array('https://apps.twitter.com/','https://dev.twitter.com/web/sign-in/implementing'),
				'github'=> array('https://github.com/settings/applications','https://developer.github.com/v3/oauth/'),
				'wechat'=> array('https://open.weixin.qq.com/cgi-bin/index','https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316505&token=&lang=zh_CN'),
				'wechat_mp'=> array('https://mp.weixin.qq.com/cgi-bin/home','http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html')
			);
			foreach ($GLOBALS['open_arr'] as $k=>$v) {
				$K = strtoupper($k);
				echo '<tr'.((osop($K)!=1 && osop('show_only_enabled',1))?' style="display:none"':'').' valign="top"><th scope="row">
					<a href="'.(isset($open_arr_link[$k][0])?$open_arr_link[$k][0]:'#').'" target="_blank">'.$v.'</a>
					<a href="'.(isset($open_arr_link[$k][0])?$open_arr_link[$k][1]:'#').'" target="_blank">?</a> </th>
				<td><label for="osop['.$K.']">
					<input name="osop['.$K.']" id="osop['.$K.']" type="checkbox" value="1" '.checked(osop($K),1,false).' />'.__('Enabled','open-social').'</label><br/>
					<input name="osop['.$K.'_AKEY]" value="'.osop($K.'_AKEY').'" class="regular-text" /> App ID <br/>
					<input name="osop['.$K.'_SKEY]" value="'.osop($K.'_SKEY').'" class="regular-text" /> Secret KEY </td>
				</tr>';
			}
		?>
		<tr><th></th><td><?php submit_button();?></td></tr>
		</table>
		</form>
	</div>
	<?php
} 

//user setting
add_filter("get_avatar", "open_get_avatar",99999,5);
function open_get_avatar($avatar, $id_or_email, $size='80', $default, $alt) {
	global $comment;
	$comment_ip = '';
	if(is_object($id_or_email)){
		$comment_ID = $id_or_email->comment_ID;
		$id_or_email = $id_or_email->user_id;
		if(is_user_logged_in() && current_user_can('manage_options') && $comment_ID) $comment_ip = esc_attr(get_comment_author_IP($comment_ID));
	}elseif(is_email($id_or_email)){
		$user = get_user_by('email', $id_or_email);
		$id_or_email = $user->ID;
		$avatar_option = apply_filters('pre_option_show_avatars', '', 100);
	}
	if($id_or_email){
		$out = get_user_meta($id_or_email, 'open_img', true);
		if(preg_match('/\.(bdimg|sinaimg|douban|kaixin001|xiaonei|xnimg|xiaomi|csdn)\./', $out) && is_ssl()) unset($out);//https warning
	}
	if(!empty($avatar_option)) unset($out);
	if(empty($out) && preg_match('/gravatar\.com/', $avatar) && osop('extend_gravatar_disabled',1)){
		$out = plugins_url('/images/gravatar.png?s='.$size, __FILE__);
	}
	if(!empty($out)){
		$out = substr($out,stripos($out,'//'));
		$avatar = "<img alt='{$alt}' ip='{$comment_ip}' src='{$out}' class='avatar avatar-{$size}' width='{$size}' />";
	}
	return $avatar;
}

add_filter('comment_form_defaults','open_social_comment_note');
function open_social_comment_note($fields) {
	if(is_user_logged_in()){
		$user = wp_get_current_user();
		$open_img = get_user_meta($user->ID, 'open_img', true);
		if($open_img) $fields['logged_in_as'] = '<p class="logged-in-as"> <a href="'.get_edit_user_link($user->ID).'?from='.esc_url($_SERVER['REQUEST_URI']).'%23comment">'.get_avatar($user->ID,'80').'</a></p>';
	}elseif(get_option('comment_registration') && get_post_meta(get_the_ID(), 'os_guestbook', true)){
		add_filter('option_comment_registration', '__return_false');
		$fields['fields']['url'] = '';
	}
	return $fields;
}

if( get_option('comment_registration') ) add_action( 'pre_comment_on_post', 'open_social_guestbook', 10, 1 );
function open_social_guestbook( $comment_post_ID ){
	if(is_user_logged_in() || !get_post_meta($comment_post_ID, 'os_guestbook', true)) return;
	add_filter('option_comment_registration', '__return_false');
}

if( osop('extend_guest_comment') ) add_filter( 'preprocess_comment' , 'open_social_guest_comment' ); 
function open_social_guest_comment( $commentdata ) {
	if( !is_user_logged_in() && preg_match(osop('extend_guest_comment'),$commentdata['comment_content']) ) {
		open_text(__('<strong>ERROR</strong>: The comment could not be saved. Please try again later.'));
	}
	return $commentdata;
}

//login and share
if( osop('show_login_page',1) ) add_action('login_form', 'open_social_login_form');
if( osop('show_login_form',1) ) add_action('comment_form_top', 'open_social_login_form');
add_action('comment_form_must_log_in_after', 'open_social_login_form');
function open_social_login_form() {
	if(!is_user_logged_in()){
		$html = open_social_login_html();
		if(open_login_page() && isset($_GET['redirect_to']) && isset($_GET['bind']) && isset($_SESSION['open_id']) && isset($_SESSION['access_token'])){
			$html = '<p class="forgetmenot" style="float:inherit;line-height:250%"><label><input type="checkbox" checked="checked" disabled="disabled" /> ';
			$html .= sprintf(esc_attr__('Login and bind with %s','open-social'),strtoupper(esc_attr($_GET['bind']))).'</label></p>';
		}
		echo $html;
	}
} 

if( osop('show_share_content',1) ) add_filter('the_content', 'open_social_share_form');
function open_social_share_form($content) {
	if(is_single()) $content .= open_social_share_html();
	return $content;
}

function open_social_login_html($atts=array()) {
	if(osop('show_login',1) && osop('show_login_html')) return osop('show_login_html');
	$html = '<div class="open_social_box login_box">';
	$show = (isset($atts) && !empty($atts) && isset($atts['show'])) ? $atts['show'] : '';
	if( empty($show) && osop('show_inner_login',1) && !open_login_page() ) {
		$html .= open_login_button_show('wordpress',__('Inner Login','open-social'),wp_login_url(get_permalink()));
	}
	foreach ($GLOBALS['open_arr'] as $k=>$v){
		if($show && !osin($show.',', $k.',')) continue;
		if(osop(strtoupper($k))) $html .= open_login_button_show($k, sprintf(__('Login with %s','open-social'), $v), OPEN_CBURL);
	}
	$html .= '</div><script>!function(a,b){function d(a){var e,c=b.createElement("iframe"),d="https://open.weixin.qq.com/connect/qrconnect?appid="+a.appid+"&scope="+a.scope+"&redirect_uri="+a.redirect_uri+"&state="+a.state+"&login_type=jssdk";d+=a.style?"&style="+a.style:"",d+=a.href?"&href="+a.href:"",c.src=d,c.frameBorder="0",c.allowTransparency="true",c.scrolling="no",c.width="300px",c.height="400px",e=b.getElementById(a.id),e.innerHTML="",e.appendChild(c)}a.WxLogin=d}(window,document);</script><div id="wc-login-qr"></div>';
    $_SESSION['wechatstate'] = uniqid(rand(), true);
	$html .= '<script>var obj = new WxLogin({id:"wc-login-qr",appid:"'.osop("WECHAT_AKEY").'",scope: "snsapi_login",redirect_uri: "'.OPEN_CBURL.'",state:"'.md5('WECHAT'.$_SESSION['wechatstate']).'",style: "black"});</script>';

	return $html;
}

function open_social_share_html() {
	if(osop('show_share',1) && osop('show_share_html')) return osop('show_share_html');
	$html = '<div class="open_social_box share_box"><span>分享到微信微博:</span>';
	foreach ($GLOBALS['open_share_arr'] as $k=>$v) {
		if(osop('share_'.$k)) $html .= open_share_button_show($k,$v[0],$v[1]);
	}
	if(osop('share_wechat')) $html .= '<div class="open_social_qrcode" onclick="jQuery(this).hide();"></div>';
	$html .= '</div>';
	return $html;
}

function open_social_profile_html(){
	if(!is_user_logged_in()) return;
	if(osop('show_login',1) && osop('show_profile_html')) return osop('show_profile_html');
	$current_user = wp_get_current_user();
	$html = '<a title="'.$current_user->user_email.'" href="'.(current_user_can('manage_options')?admin_url():(get_edit_user_link()).'?from='.esc_url($_SERVER['REQUEST_URI'])).'">'.get_avatar($current_user->ID).'</a><br/>';
	$html .= $current_user->display_name;
	$html .= ' (<a href="'.wp_logout_url($_SERVER['REQUEST_URI']).'">'.__('Log Out').'</a>)';
	return $html;
}

//profile setting
add_action('personal_options_update', 'open_social_update_options',99,1);
function open_social_update_options($user_id) {
	global $wpdb;
	$user = wp_get_current_user();
	if( !isset($_POST['user_id']) || $user_id != $_POST['user_id'] || !current_user_can('edit_user', $user_id) ) return;
	if( isset($_POST['user_login']) ){
		$newname = sanitize_user( $_POST['user_login'] );
		$oldname = $user->user_login;
		if($newname == $oldname) return;
		if(strlen($newname)>=4 && strlen($newname)<=20 && preg_match('/^[a-zA-Z0-9]*$/', $newname)){
			if(!username_exists($newname)){
				$set_newname = $wpdb->prepare("UPDATE $wpdb->users SET user_login = %s WHERE user_login = %s", $newname, $oldname);
				if( false !== $wpdb->query( $set_newname ) ) {
					$newarray = array('ID' => $user_id, 'user_nicename' => sanitize_title($newname));
					if( $oldname == $user->display_name ) $newarray = array_merge($newarray,array('display_name' => $newname));
					update_user_meta($user_id, 'open_save', 1);
					wp_update_user($newarray);
					$result = '<div class="updated fade"><p><strong>'.sprintf( __( '%s is your new username' ), $newname).'</strong></p></div>';
				}else{
					$result = '<div class="error"><p><strong>'.$wpdb->last_error.'</strong></p></div>';
				}
			}else{
				$result = '<div class="error"><p><strong>'.__( 'Sorry, that username already exists!' ).'</strong></p></div>';
			}
		}else{
			$result = '<div class="error"><p><strong>'.__('Length of Username between 4 and 20, letters and numbers only; Or you already change it.','open-social').'</strong></p></div>';
		}
		$_SESSION['personal_options_update_return'] = $result;
	}
}

if(isset($_GET['updated']) || isset($_GET['from'])) add_action('admin_notices', 'open_social_edit_profile_note');
function open_social_edit_profile_note() {
	if( isset($_GET['from']) ) $_SESSION['from'] = $_GET['from'];
	$from = isset($_SESSION['from']) ? $_SESSION['from'] : home_url();
	echo '<div class="updated fade"><p><strong><a href="'.esc_url($from).'">'.__('&laquo; Back').': '.esc_url(substr($from,0,4)=='http'?$from:($_SERVER['SERVER_NAME'].$from)).'</a></strong></p></div>';
	if(isset($_SESSION['personal_options_update_return'])){
		echo $_SESSION['personal_options_update_return'];
		unset($_SESSION['personal_options_update_return']);
	}
}

if(osop('extend_change_name',1)) add_action('admin_head','open_social_hide_option');
function open_social_hide_option(){
	if(!is_user_logged_in()) return;
	$current_user = wp_get_current_user();
	$open_type = get_user_meta( $current_user->ID, 'open_type', true);
	$open_save = get_user_meta( $current_user->ID, 'open_save', true);
	if( $open_type && !$open_save && 'profile.php' == $GLOBALS['pagenow'] ){
		echo "<script>jQuery(document).ready(function(){jQuery('#user_login').attr('disabled',false).attr('maxlength',20);jQuery('#user_login').parent().find('.description').text('".__( 'Must be at least 4 characters, letters and numbers only. It cannot be changed, so choose carefully!' )."');});</script>";
	}
}

add_action('profile_personal_options', 'open_social_bind_options');
function open_social_bind_options( $user ) {
	$open_type = get_user_meta( $user->ID, 'open_type', true);
	$html = '<table class="form-table"><tr valign="top"><th scope="row">'.__('Open Social','open-social').'</th><td>';
	$html .= '<div id="open_social_login_box" class="open_social_box login_box">';
	foreach ($GLOBALS['open_arr'] as $k=>$v){
		if(osop(strtoupper($k))){
			if($open_type && osin($open_type, $k.',')){
				$html .= open_login_button_unbind($k,sprintf(__('Unbind with %s','open-social'),$v),OPEN_CBURL);
			}else{
				$html .= open_login_button_show($k,sprintf(__('Login with %s','open-social'),$v),OPEN_CBURL);
			}
		}
	}
	$html .= '</div>';
	$html .= '</td></tr></table>';
	echo $html;
} 

//post
if(osop('share_post_weibo',1)){
	add_action('publish_post', 'open_social_post_weibo', 10, 1);
	add_action('publish_page', 'open_social_post_weibo', 10, 1);
	add_action('future_post', 'open_social_future_post_weibo', 10, 1);
	if(defined('XMLRPC_REQUEST')) add_action('xmlrpc_publish_post', 'open_social_post_weibo', 10, 1);
	if(defined('APP_REQUEST')) add_action('app_publish_post', 'open_social_post_weibo', 10, 1);
}

function open_social_future_post_weibo($ID){
	$check = osop('open_social_post_weibo_check');
	if(!empty($_POST['open_social_post_weibo_check']) && !osin($check, ','.$ID.',')){
		$GLOBALS['osop']['open_social_post_weibo_check'] = rtrim($check,',').','.$ID.',';
		update_option('osop',$GLOBALS['osop']);//for cron
	}
}

function open_social_post_weibo($ID) {
	$check = osop('open_social_post_weibo_check');
	if(defined('DOING_CRON')){
		if(empty($check) || !osin($check, ','.$ID.',')) return;
		$check = str_replace(','.$ID.',',',',$check);
		$GLOBALS['osop']['open_social_post_weibo_check'] = $check;
	}else{
		if(empty($_POST['open_social_post_weibo_check'])) return;
	}
	$access_token = osop('share_sina_access_token');
	$str = open_connect_http('https://api.weibo.com/oauth2/get_token_info', http_build_query(array('access_token'=>$access_token)), 'POST');
	open_check_callback($str,$access_token,'expire_in');
	if( $str['create_at'] + $str['expire_in'] + 1*24*3600 < time() && current_user_can('administrator') ){ //token expire
		$url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : home_url($_SERVER['REQUEST_URI']);
		open_next(OPEN_CBURL.'?'.http_build_query(array('connect'=>'sina','action'=>'login','back'=>$url)));
	}
	if(empty($access_token)) return;
	$post = get_post($ID);
	$title = strip_tags($post->post_title);
	$content = apply_filters('the_content', $post->post_content);
	$status = $title . ' - ' . get_option('blogname') . "\r\n\r\n";//newline for weibo_vip
    $status .= mb_strimwidth(preg_replace('/\r|\n/i','',strip_tags($content)), 0,180,'...');
    $status .= get_permalink($ID);
    if(has_post_thumbnail($ID)){
        $timthumb_src = wp_get_attachment_image_src(get_post_thumbnail_id($ID),'full');
        $pic = $timthumb_src[0];
    }else{
        preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', $content, $matches);
		if($matches && count($matches)>1 && count($matches[1])>0) $pic = $matches[1][0];
    }
	$params = array('status'=>$status, 'access_token'=>$access_token);
	if(isset($pic)){
		$params['pic'] = preg_replace('/^https:\/\//i','http://',$pic);//weibo too low to https
        $boundary = uniqid('------------------');
        $multipart = '';
        foreach ($params as $param => $value) {
            if( $param == 'pic' ) {
                $content = file_get_contents( $value );
                $filename = explode('?', basename($value));
                $multipart .= '--'.$boundary . "\r\n";
                $multipart .= 'Content-Disposition: form-data; name="'. $param .'"; filename="'. $filename[0] .'"'."\r\n";
                $multipart .= "Content-Type: image/unknown\r\n\r\n";
                $multipart .= $content. "\r\n";
            } else {
                $multipart .= '--'.$boundary . "\r\n";
                $multipart .= 'content-disposition: form-data; name="' . $param . "\"\r\n\r\n";
                $multipart .= $value."\r\n";
            }
        }
        $multipart .= '--'.$boundary. '--';
		$result = open_connect_http('https://upload.api.weibo.com/2/statuses/upload.json', $multipart, 'POST', array('Content-Type:multipart/form-data;boundary='.$boundary));
	}else{
		$result = open_connect_http('https://api.weibo.com/2/statuses/update.json', http_build_query($params), 'POST');
	}
	if(isset($result['created_at'])){
		$GLOBALS['osop']['open_social_post_weibo_result'] = '<a title="'.$title.'" style="text-decoration:none" href="http://weibo.com/'.$result['user']['profile_url'].'" target="_blank"><span class="dashicons dashicons-yes"></span></a>';
	}else if(isset($result['error'])){
		$GLOBALS['osop']['open_social_post_weibo_result'] = '<a title="'.$title.'" style="text-decoration:none" href="http://open.weibo.com/wiki/Error_code?'.$result['error_code'].'='.$result['error'].'" target="_blank"><span class="dashicons dashicons-no-alt"></span></a>';
	}
	update_option('osop',$GLOBALS['osop']);
}

//script & style
add_action( 'wp_enqueue_scripts', 'open_social_style', 100 );
add_action( 'login_enqueue_scripts', 'open_social_style' );
add_action( 'admin_enqueue_scripts', 'open_social_style' );
function open_social_style() {
	wp_enqueue_style( 'open-social-style', plugins_url('/images/os.css', __FILE__) );
	if( osop('show_share_jssdk',1) && osop('WECHAT_MP',1) ){
		$jssdk_key = osop('show_share_jssdk_key');
		if( !is_array($jssdk_key) || $jssdk_key['time'] < time() ){
			$data = open_connect_http('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.osop('WECHAT_MP_AKEY').'&secret='.osop('WECHAT_MP_SKEY'));
			open_check_callback($data,osop('WECHAT_MP_AKEY'),'access_token');
			$token = $data['access_token'];
			$data = open_connect_http("https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$token");
			open_check_callback($data,$token,'ticket');
			$jssdk_key = array(
				'access_token' => $token,
				'ticket' => $data['ticket'],
				'time' => time() + 7000
			);
			$GLOBALS['osop']['show_share_jssdk_key'] = $jssdk_key;
			update_option('osop',$GLOBALS['osop']);
		}
		$jsapiTicket = $jssdk_key['ticket'];
		$protocol = is_ssl() ? 'https' : 'http';
		wp_enqueue_script('os-wechat-jssdk', "$protocol://res.wx.qq.com/open/js/jweixin-1.0.0.js");
		$url = "$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$timestamp = time();
		$nonceStr = md5(uniqid(rand(), true));
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		wp_localize_script( 'os-wechat-jssdk', 'os_wechat_init', array(
			'debug' => 0,
			'appId' => osop('WECHAT_MP_AKEY'),
			'timestamp' => $timestamp,
			'nonceStr' => $nonceStr,
			'signature' => sha1($string)
		));
	}
	wp_enqueue_script( 'open-social-script', plugins_url('/images/os.js', __FILE__), osop('extend_button_tooltip',1) ? array( 'jquery','jquery-ui-tooltip' ) : array(), '', true );
	if(osop('share_wechat')) wp_enqueue_script('jquery.qrcode', '//cdn.bootcss.com/jquery.qrcode/1.0/jquery.qrcode.min.js', array('jquery'));
}
function open_login_button_show($icon_type,$icon_title,$icon_link){
	return "<div class=\"one_login_box\"><div class=\"login_button login_icon_$icon_type\" onclick=\"login_button_click('$icon_type','$icon_link')\" title=\"$icon_title\"></div><div class=\"social-icon-title\">".$icon_title."</div></div>";
}
function open_login_button_unbind($icon_type,$icon_title,$icon_link){
	return "<div class=\"login_button login_button_unbind login_icon_$icon_type\" onclick=\"confirm('".__('Confirm')." ".sprintf(__('Unbind with %s','open-social'),strtoupper($icon_type))."?')&&login_button_unbind_click('$icon_type','$icon_link')\" title=\"$icon_title\"></div>";
}
function open_share_button_show($icon_type,$icon_title,$icon_link){
	return "<div class=\"share_button share_icon_$icon_type\" onclick=\"share_button_click('$icon_link',event)\" title=\"$icon_title\"></div>";
}

//shortcode
add_shortcode('os_login', 'open_social_login_html');
add_shortcode('os_share', 'open_social_share_html');
add_shortcode('os_profile', 'open_social_profile_html');
add_shortcode('os_hide', 'open_social_hide');
function open_social_hide($atts, $content=""){
	$output = '';
	if(is_user_logged_in()){
		$output .= '<span class="os_show"><p>'.trim($content).'</p></span>';
	}else{
		$output .= '<p class="os_hide">'.__('Login to check this hidden content out','open-social').'</p>';
	}
	return $output;
}

//email notification
add_filter( 'wp_mail', 'open_social_wp_mail_filter' );
function open_social_wp_mail_filter( $args ) {
	if(preg_match('/@fake\.com/', $args['to'])) $args['to'] = '';
	return $args;
}
if( osop('extend_comment_email',1) ) {
	add_action('wp_insert_comment','open_social_comment_email',99,2);
	function open_social_comment_email($comment_id, $comment_object) {
		if ($comment_object->comment_parent > 0) {
			$comment_parent = get_comment($comment_object->comment_parent);
			$user_id = $comment_parent->user_id;
			if(!$user_id)return;//user only
			$email = get_userdata($user_id)->user_email;
			$comment_email = $comment_parent->comment_author_email;
			if(preg_match('/@fake\.com/', $email) || (isset($comment_email) && preg_match('/@fake\.com/', $comment_email))) return;//no fake
			$content = __('Hello','open-social').' '.$comment_parent->comment_author.',<br><br>';
			$content .= $comment_object->comment_content . '<br><em>---- ';
			$content .= '<a href="'.esc_attr( $comment_object->comment_author_url ).'">'.$comment_object->comment_author . '</a>';
			$content .= '('.esc_attr( $comment_object->comment_author_email ).') # ';
			$content .= '<a href="'.get_permalink($comment_parent->comment_post_ID).'">'.get_the_title($comment_parent->comment_post_ID).'</a></em><br><br>';
			$content .= __('Go check it out','open-social').': <a href="'.get_comment_link( $comment_parent->comment_ID ).'">'.get_comment_link( $comment_parent->comment_ID ).'</a>';
			$headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n";
			wp_mail($email,'['.get_option('blogname').'] '.__('New reply to your comment','open-social'),$content,$headers);
		}
	}
}

//show nickname
if( osop('extend_show_nickname',1) ){
	add_filter('manage_users_columns', 'os_show_user_nickname_column');
	add_action('manage_users_custom_column', 'os_show_user_nickname_column_content', 20, 3);
	add_filter('manage_users_sortable_columns', 'os_user_sortable_columns');
	add_action('pre_user_query', 'os_user_order_query');
	function os_show_user_nickname_column($columns) {
		unset($columns['name']);
		$columns['nickname'] = __('Nickname');
		$columns['registered'] = __('Registered');
		return $columns;
	}
	function os_show_user_nickname_column_content($value, $column_name, $user_id) {
		$user = get_userdata($user_id);
		if('nickname' == $column_name) return $user->nickname;
		if('registered' == $column_name) return $user->user_registered;
		return $value;
	}
	function os_user_sortable_columns( $columns ) {
		$columns['nickname'] = 'name';
		$columns['registered'] = 'registered';
		return $columns;
	}
	function os_user_order_query($vars){
		if( !isset($_GET['orderby']) ) $vars->query_orderby = 'ORDER BY user_registered DESC';
	}
}

//widget
add_action('widgets_init', create_function('', 'return register_widget("open_social_login_widget");'));
//add_action( 'widgets_init', function(){register_widget( 'open_social_login_widget' );});//5.3
add_action('widgets_init', create_function('', 'return register_widget("open_social_share_widget");'));
add_action('widgets_init', create_function('', 'return register_widget("open_social_float_widget");'));

class open_social_login_widget extends WP_Widget {
	function __construct() {
		parent::__construct(false, __('Open Social Login', 'open-social'), array( 'description' => __('Display your Open Social login button', 'open-social'), ) );
	}
	function form($instance) {
		$title = $instance ? $instance['title'] : '';
		$html = '<p><label for="'.$this->get_field_id( 'title' ).'">'.__( 'Title:' ).'</label><input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="text" value="'.esc_attr( $title ).'" /></p>';
		echo $html;
	}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		if(!$title) $title = __('Howdy', 'open-social');
		$html = $before_widget;
		$html .= '<h3 class="widget-title">'.$title.'</h3>';
		$html .= '<div class="textwidget">';
		if(is_user_logged_in()){
			$html .= open_social_profile_html();
		}else{
			$html .= open_social_login_html();
		}
		$html .= '</div>';
		$html .= $after_widget;
		echo $html;
	}
}

class open_social_share_widget extends WP_Widget {
	function __construct() {
		parent::__construct(false, $name = __('Open Social Share', 'open-social'), array( 'description' => __('Display your Open Social share button', 'open-social'), ) );
	}
	function form($instance) {
		$title = $instance ? $instance['title'] : '';
		$html = '<p><label for="'.$this->get_field_id( 'title' ).'">'.__( 'Title:' ).'</label><input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="text" value="'.esc_attr( $title ).'" /></p>';
		echo $html;
	}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		if(!$title) $title = __('Connect', 'open-social');
		$html = $before_widget;
		$html .= '<h3 class="widget-title">'.$title.'</h3>';
		$html .= '<div class="textwidget">';
		$html .= open_social_share_html();
		$html .= '</div>';
		$html .= $after_widget;
		echo $html;
	}
}	

class open_social_float_widget extends WP_Widget {
	function __construct() {
		parent::__construct(false, $name = __('Floating Button', 'open-social'), array( 'description' => __('Some floating useful buttons', 'open-social'), ) );
	}
	function widget($args, $instance) {
		$html = '<div id="open_social_float_button">';
		$html .= '<div class="os_float_button float_icon_top" id="open_social_float_button_top"></div>';
		$html .= '<div class="os_float_button float_icon_comment" id="open_social_float_button_comment"></div>';
		$html .= '</div>';
		echo $html;
	}
}
?>