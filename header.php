<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<!--[if lt IE 7]> <html class="ie ie6 oldie" lang="en"> <![endif]-->
	<!--[if IE 7]>    <html class="ie ie7 oldie" lang="en"> <![endif]-->
	<!--[if IE 8]>    <html class="ie ie8 oldie" lang="en"> <![endif]-->
	<!--[if gt IE 8]> <html class="ie ie9 newest" lang="en"> <![endif]-->
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<meta charset="utf-8">
	    <title>
            <?php wp_title( '|', true, 'right' ); ?>
	    </title>
		<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
		<!-- <meta name="thumbnail" content="<?php echo TEMPLATEURL . '/screenshot.png' ?>" /> -->
		<meta name="description" content="<?php echo et_get_option("blogdescription"); ?>" />
		<!-- Facebook Metas -->
		<meta property="og:image" content="<?php echo TEMPLATEURL . '/screenshot.png' ?>" />
		<!-- Facebook Metas / END -->
		<?php
			$favicon = et_get_option("et_mobile_icon") ? et_get_option("et_mobile_icon") : (TEMPLATEURL . '/img/fe-favicon.png');
		?>
		<link rel="shortcut icon" href="<?php echo $favicon ?>"/>
		<?php wp_head() ?>
		<link href="<?php echo TEMPLATEURL ?>/css/custom-ie.css" rel="stylesheet" media="screen">
		<!--[if lt IE 9]>
	      <script src="<?php echo TEMPLATEURL . '/js/libs/respond.min.js' ?>"></script>
	    <![endif]-->
	</head>
	<body <?php echo body_class() ?>>
	<div class="mp-qr-bar"><span class="arrow-down">扫扫我们的网站吧</span><br /><img src="/wp-content/uploads/2016/10/smallwebsitebarcode.png"></img></div>
	<div class="web-qr-bar"><span class="arrow-down">扫扫我们的公众号</span><br /><img src="/wp-content/uploads/2016/10/qrcode_for_gh_med.jpg"></img></div>
	<div class="site-container">
		<div class="cnt-container">
			<div class="header-top bg-header">
				<div class="main-center container">
					<div class="row header-info">
						<div class="col-md-2 logo-header">
							<a href="<?php echo home_url( ) ?>" class="logo">
								<img src="<?php echo fe_get_logo() ?>"/>
							</a>
						</div>
						<?php global $current_user,$et_query; ?>
						<div class="col-md-10">
							<div class="row">
								<div class="login col-md-2 col-sm-2 <?php if($current_user->ID){ echo 'collapse';} ?>">


				<a href="/register/" class="new-user-reg" style="margin-top:-8px;">新用户这里注册</a>
								<a href="/login-2/" class="old-user-join">老用户这里登录</a>

								</div>
								<div class="profile-account col-md-2 col-sm-2 <?php if(!$current_user->ID){ echo 'collapse';} ?>">
									<span class="name"><a href="javascript:void(0);"><?php echo $current_user->display_name; ?></a></span><span class="arrow"></span>
									<span class="img"><?php echo  et_get_avatar($current_user->ID) ?></span>
									<!-- <span class="number">8</span>   -->
									<div class="clearfix"></div>
								</div>
								<div class="search-header col-md-8  col-sm-6">

									<img src="/wp-content/uploads/2016/10/banner-adv.gif"></img>
								</div>
							</div>
							<div class="dropdown-profile">
								<span class="arrow-up"></span>
								<div class="content-profile">
									<div class="head"><span class="text">@<?php echo $current_user->user_login; ?></span></div>
									<ul class="list-profile">
										<li>
											<a href="<?php echo get_author_posts_url($current_user->ID); ?>">
												<span class="icon" data-icon="U"></span>
												<br />
												<span class="text"><?php _e('Profile',ET_DOMAIN); ?></span>
											</a>
										</li>
										<!-- <li>
											<a href="#">
												<span class="icon" data-icon="M"></span>
												<br />
												<span class="text">Inbox (8)</span>
											</a>
										</li> -->
										<li>
											<a href="<?php echo wp_logout_url( $_SERVER['REQUEST_URI'] ); ?>">
												<span class="icon" data-icon="Q"></span>
												<br />
												<span class="text"><?php _e('Logout',ET_DOMAIN); ?></span>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php if ( (!is_page_template( 'page-following.php' ) && !is_page_template( 'page-pending.php' ) && !is_search() && !is_front_page() && !is_tax('category') && !is_tax('fe_tag') && !is_page_template( 'page-threads.php' ) ))  { ?>

			<div class="header-bottom">
				<div class="main-center">
					<?php
					$breadcrumbs = get_the_breadcrumb(array(
						'class' 		=> 'breadcrumbs',
						'item_class' 	=> 'icon'
						));
					echo $breadcrumbs;
					?>
					<div class="menu-bar-social-bar">

                                            				<?php if(!$user_ID){?>



                                            				<a href="<?php echo et_get_page_link('login') ?>" class="fe-nav-btn fe-btn-profile"><span class="fe-sprite"></span></a>
                                            				<?php } ?>

                                            			</div>
				</div>


			</div>

			<?php } ?>