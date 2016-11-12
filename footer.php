

		<footer>

		  <div class="footer">
			<div class="row main-center">
			  <div class="col-md-3">
			  <table width="135" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose GeoTrust SSL for secure e-commerce and confidential communications.">
              <tr>
              <td width="135" align="center" valign="top"><script type="text/javascript" src="https://seal.geotrust.com/getgeotrustsslseal?host_name=www.quinpool.com&amp;size=M&amp;lang=en"></script><br />
              <a href="http://www.geotrust.com/ssl/" target="_blank"  style="color:#000000; text-decoration:none; font:bold 7px verdana,sans-serif; letter-spacing:.5px; text-align:center; margin:0px; padding:0px;"></a></td>
              </tr>
              </table>
				<ul class="social">
					<?php
						$links = array(
							'fb'    => et_get_option("et_facebook_link"),
							'tw'    => et_get_option("et_twitter_account"),
							'gplus' => et_get_option("et_google_plus"),
							'rss'   => get_feed_link("rss2"),
							'mail'  => et_get_option("et_admin_email")
						)
					?>
					<?php if ( $links['fb'] ) { ?>
					<li class="fb">
						<a target="_blank" href="<?php echo $links['fb']; ?>">
							<?php _e('Facebook', ET_DOMAIN) ?>
						</a>
					</li>
					<?php }
					if ( $links['gplus'] ) { ?>
					<li class="gplus">
						<a target="_blank" href="<?php echo $links['gplus']; ?>">
							<?php _e('Google+', ET_DOMAIN) ?>
						</a>
					</li>
					<?php }
					if ( $links['tw'] ) { ?>
					<li class="tw">
						<a target="_blank" href="<?php echo $links['tw']; ?>">
							<?php _e('Twitter', ET_DOMAIN) ?>
						</a>
					</li>
					<?php } ?>
					<li class="rss">
						<a target="_blank" href="<?php echo $links['rss'] ?>">
							<?php _e('Rss', ET_DOMAIN) ?>
						</a>
					</li>
					<?php if(et_get_option("et_admin_email")){ ?>
					<li class="mail">
						<a target="_blank" href="mailto:<?php echo $links['mail'] ?>">
							<?php _e('Mail', ET_DOMAIN) ?>
						</a>
					</li>
					<?php } ?>
				</ul>
			  </div>
			  <div class="col-md-9 row">
				<div class="nav-wrap col-sm-6">
					<ul class="nav">
						<?php
							if(has_nav_menu('et_footer')){
								wp_nav_menu(array(
										'theme_location' => 'et_footer',
										'items_wrap' => '%3$s',
										'container' => ''
									));
							}
						?>
					</ul>
				</div>
				<div class="copyright-wrap col-sm-6">
					<ul class="nav fright">
					  <li class="copyright">
					  	<?php echo et_get_option("et_copyright") ?><br>
					  	<span>
			<a href="#" onclick='window.open("https://eclink.ca/%E5%B9%BF%E5%91%8A%E5%AE%A3%E8%A8%80", "_blank", "width=600,height=800");'>广告</a> |
<a href="#" onclick='window.open("https://eclink.ca/关于我们", "_blank", "width=600,height=800");'>关于我们</a> |
<a href="#" onclick='window.open("https://eclink.ca/联系我们", "_blank", "width=600,height=800");'>联系我们</a> |
<a href="#" onclick='window.open("https://eclink.ca/隐私权声明", "_blank", "width=600,height=800");'>隐私权声明</a> |

<a href="#" onclick='window.open("https://eclink.ca/服务条款", "_blank", "width=600,height=800");'>服务条款</a> |

<a href="#" onclick='window.open("https://eclink.ca/友情链接", "_blank", "width=600,height=800");'>友情链接</a></span></div>

					  	</span>
					  </li>
					</ul>
				</div>
			  </div>
			</div>
		  </div>
		</footer><!-- End Footer -->

		<!-- MODAL UPLOAD IMAGES -->
	    <?php
		    if(is_front_page() || is_singular( 'post' ) || is_tax()){
		    	get_template_part( 'template/modal', 'images' );
			}
		?>
		<!-- END MODAL UPLOAD IMAGES -->

		<!-- REPLY TEMPLATE -->
	    <?php
		    if( is_singular( 'post' ) ){
		    	get_template_part( 'template-js/reply', 'item' );
		    	get_template_part( 'template-js/child-reply', 'item' );
			}
		?>
		<!-- END REPLY TEMPLATE -->

		<!-- Modal Login -->
		<?php
			if(!is_user_logged_in()){
				get_template_part( 'template/modal', 'auth' );
			}
			else{
				get_template_part( 'template/modal', 'report' );
			}
		?>
		<!-- End Modal Login -->

		<!-- Modal Contact Form -->
		<?php
			if(is_author() || is_page_template('page-member.php' )){
				get_template_part( 'template/modal', 'contact' );
			}
		?>
		<!-- End Modal Contact Form -->
		<!-- REPLY TEMPLATE -->
		<script type="text/template" id="search_preview_template">

			<# _.each(threads, function(thread){ #>

			<div class="i-preview">
				<a href="{{= thread.permalink }}">
					<div class="i-preview-avatar">
						{{= (typeof(thread.et_avatar) === "object") ? thread.et_avatar.thumbnail : thread.et_avatar }}
					</div>
					<div class="i-preview-content">
						<span class="i-preview-title">{{= thread.post_title.replace( search_term, '<strong>' + search_term + "</strong>" ) }}</span>
						<span class="comment active">
							<span class="icon" data-icon="w"></span>{{= thread.et_replies_count }}
						</span>
						<span class="like active">
							<span class="icon" data-icon="k"></span>{{= thread.et_likes_count }}
						</span>
					</div>
				</a>
			</div>

			<# }); #>

			<div class="i-preview i-preview-showall">

				<# if ( total > 0 && pages > 1 ) { #>

				<a href="{{= search_link }}"><?php printf( __('View all %s results', ET_DOMAIN), '{{= total }}' ); ?></a>

				<# } else if ( pages == 1) { #>

				<a href="{{= search_link }}"><?php _e('View all results', ET_DOMAIN) ?></a>

				<# } else { #>

				<a> <?php _e('No results found', ET_DOMAIN) ?> </a>

				<# } #>

			</div>
		</script>
		<!-- REPLY TEMPLATE -->
		<!-- Default Wordpress Editor -->
		<div class="hide">
			<?php wp_editor( '' , 'temp_content', editor_settings() ); ?>
		</div>
		<!-- Default Wordpress Editor -->

		</div>
		<div class="mobile-menu">
			<ul class="mo-cat-list">
				<?php et_the_mobile_cat_list(); ?>
			</ul>
		</div>
	</div>
	<?php wp_footer(); ?>
	<!-- CHANGE DEFAULT SETTINGs UNDERSCORE  -->
	<!-- END CHANGE DEFAULT SETTINGs UNDERSCORE  -->
	<?php
		global $fe_confirm;
		if($fe_confirm == 1)
			echo '<script type="text/javascript">
	        jQuery(document).ready(function() {
	            pubsub.trigger("fe:showNotice", "'.__("Your account has been confirmed successfully!",ET_DOMAIN).'" , "success");
	        });
	    </script>';
	    //Show notification if user can't view this thread
	     if(isset($_REQUEST['error']) && $_REQUEST['error'] == 404){
	    	echo '<script type="text/javascript">
	        jQuery(document).ready(function() {
	             pubsub.trigger("fe:showNotice", "'.__("Please log into your  account to view this thread",ET_DOMAIN).'" , "warning");
	        });
	    </script>';
	    }
	?>
	<?php
		if(et_get_option('gplus_login', false)){
	?>
	<style type="text/css">
		iframe[src^="https://apis.google.com"] {
		  display: none;
		}
	</style>
	<?php } ?>
	<!-- Fix Padding Right In Thread Title -->
	<?php if(!is_user_logged_in() || !current_user_can( 'administrator' )){ ?>
	<style type="text/css">
		.f-floatright .title a {
			padding-right: 0;
		}
	</style>
	<?php } ?>
	<!-- Fix Padding Right In Thread Title -->
  	</body>
</html>