<?php
// Search widget Start
class es_search extends WP_Widget {
    // constructor
    function __construct() {
        parent::__construct(false, $name = __('Estatik Search', 'es-plugin') );
    }
    private function form_checkbox($title, $property_name, $property_value) {
        ?>
        <li class="wrap" style="margin-top: 15px">
            <label><?php echo $title; ?></label><br />
            <?php
            $value = (isset($property_value)) ? $property_value : '1';
            $field_name = $this->get_field_name($property_name);
            ?>
            <label style="margin-right:10px;">
                <input name="<?php echo $field_name; ?>"
                       type="radio" value="1"
                    <?php checked( '1', $value ); ?>/>
                <?php _e('Yes', 'es-plugin'); ?>
            </label>
            <label>
                <input name="<?php echo $field_name; ?>"
                       type="radio" value="0"
                    <?php checked( '0', $value ); ?>/>
                <?php _e('No', 'es-plugin'); ?>
            </label>
        </li>
        <?php
    }
    // widget form creation
    function form($instance) {
        $defaults = array(
            'search_title' => '',
            'search_address' => '0',
            'search_country' => '0',
            'search_state' => '0',
            'search_city' => '0',
            'search_price' => '0',
            'search_bedrooms' => '0',
            'search_bathrooms' => '0',
            'search_category' => '0',
            'search_type' => '0',
            'search_sqft' => '0',
            'search_lotsize' => '0',
            'search_agent' => '0',
            'search_keywords' => '0',
            'search_layout' => '',
            'search_page' => '',
            'es_search_page' => '',
            'show_on_pages' => '',
            'archive_page' => '',
            'single_page' => '',
            'category_page' => '',
            'author_page' => '',
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        // Check values
        if( $instance) {
            $search_title 		= esc_attr($instance['search_title']);
            $search_address 	= esc_attr($instance['search_address']);
            $search_country		= esc_attr($instance['search_country']);
            $search_state 		= esc_attr($instance['search_state']);
            $search_city 		= esc_attr($instance['search_city']);
            $search_price 		= esc_attr($instance['search_price']);
            $search_bedrooms 	= esc_attr($instance['search_bedrooms']);
            $search_bathrooms 	= esc_attr($instance['search_bathrooms']);
            $search_category 	= esc_attr($instance['search_category']);
            $search_type 		= esc_attr($instance['search_type']);
            $search_sqft 		= esc_attr($instance['search_sqft']);
            $search_lotsize 	= esc_attr($instance['search_lotsize']);
            $search_agent 		= esc_attr($instance['search_agent']);
            $search_keywords 	= esc_attr($instance['search_keywords']);
            $search_layout 		= esc_attr($instance['search_layout']);
            $search_page        = esc_attr($instance['search_page']);
            $es_search_page        = esc_attr($instance['es_search_page']);
            $show_on_pages 		= esc_attr($instance['show_on_pages']);
            $pages_args = array(
                'sort_order' => 'ASC',
                'sort_column' => 'post_title',
                'post_type' => 'page',
                'post_status' => 'publish'
            );
//			$pages = get_pages($pages_args);
//			foreach ($pages as $page ){
//				if ( isset($instance["page_field_".$page->ID]) ) {
//					esc_attr($instance["page_field_".$page->ID]);
//				}
//			}
            $archive_page 		= esc_attr($instance['archive_page']);
            $single_page 		= esc_attr($instance['single_page']);
            $category_page 		= esc_attr($instance['category_page']);
            $search_page 		= esc_attr($instance['search_page']);
            $author_page 		= esc_attr($instance['author_page']);
            $search_page_id = get_option('es_search_page');
        }
        ?>
        <ul>
            <li>
                <label><?php _e('Search Title:', 'es-plugin'); ?></label>
                <input class="widefat"
                       name="<?php echo $this->get_field_name('search_title'); ?>"
                       type="text"
                       value="<?php echo isset($search_title) ? $search_title : ''; ?>"/>
            </li>
            <?php
            $this->form_checkbox(__('Address:', 'es-plugin'),
                'search_address', $search_address);
            $this->form_checkbox(__('Country:', 'es-plugin'),
                'search_country', $search_country);
            $this->form_checkbox(__('State/Region:', 'es-plugin'),
                'search_state', $search_state);
            $this->form_checkbox(__('City:', 'es-plugin'),
                'search_city', $search_city);
            $this->form_checkbox(__('Price:', 'es-plugin'),
                'search_price', $search_price);
            $this->form_checkbox(__('Bedrooms:', 'es-plugin'),
                'search_bedrooms', $search_bedrooms);
            $this->form_checkbox(__('Bathrooms:', 'es-plugin'),
                'search_bathrooms', $search_bathrooms);
            $this->form_checkbox(__('Category:', 'es-plugin'),
                'search_category', $search_category);
            $this->form_checkbox(__('Type:', 'es-plugin'),
                'search_type', $search_type);
            $this->form_checkbox(__('Sqft:', 'es-plugin'),
                'search_sqft', $search_sqft);
            $this->form_checkbox(__('Lot size:', 'es-plugin'),
                'search_lotsize', $search_lotsize);
            $this->form_checkbox(__('Agent:', 'es-plugin'),
                'search_agent', $search_agent);
            $this->form_checkbox(__('Keywords:', 'es-plugin'),
                'search_keywords', $search_keywords);
            ?>
            <li class="wrap" style="margin-top: 15px">
                <label for="<?php echo $this->get_field_id('search_layout'); ?>">
                    <?php _e('Layout', 'es-plugin'); ?>
                </label>
                <?php $search_layout = (isset($search_layout)) ? $search_layout : 'horizontal'; ?>
                <select name="<?php echo $this->get_field_name('search_layout'); ?>"
                        id="<?php echo $this->get_field_id('search_layout'); ?>" class="widefat">
                    <?php
                    $options = array('horizontal', 'vertical');
                    foreach ($options as $option) {
                        echo '<option value="' . $option . '" id="' . $option . '"', $search_layout == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                    }
                    ?>
                </select>
            </li>
            <li class="wrap" style="margin-top: 15px">
                <label for="<?php echo 'search_page' ?>"><?php _e('Search page', 'es-plugin'); ?></label>
                <select name="<?php echo $this->get_field_name('es_search_page'); ?>" id="<?php echo $this->get_field_id('es_search_page'); ?>" class="widefat">
                    <?php
                    $pages = get_pages($pages_args);
                    foreach ($pages as $page ){ ?>
                        <option value="<?php echo $page->ID;  ?>" <?php selected($search_page_id, $page->ID); ?>><?php echo get_the_title($page->ID); ?></option>
                    <?php }  ?>
                </select>
            </li>
            <li class="wrap" style="margin-top: 15px">
                <label for="<?php echo $this->get_field_id('show_on_pages'); ?>"><?php _e('Show On Pages', 'es-plugin'); ?></label>
                <?php $show_on_pages = (isset($show_on_pages)) ? $show_on_pages : 'all_pages'; ?>
                <select name="<?php echo $this->get_field_name('show_on_pages'); ?>" id="<?php echo $this->get_field_id('show_on_pages'); ?>" class="widefat">
                    <?php
                    $options = array('all_pages','show_on_checked_pages', 'hide_on_checked_pages');
                    foreach ($options as $option) {
                        echo '<option value="' . $option . '" id="' . $option . '"', $show_on_pages == $option ? ' selected="selected"' : '', '>',  str_replace("_"," ",$option),  '</option>';
                    }
                    ?>
                </select>
            </li>
        </ul>
        <div style="height: 200px; overflow-x: hidden; margin-bottom:10px; overflow-y: scroll; padding-top: 2px;">
            <label><?php _e('Select Pages', 'es-plugin'); ?></label>
            <?php
            $pages_args = array(
                'sort_order' => 'ASC',
                'sort_column' => 'post_title',
                'post_type' => 'page',
                'post_status' => 'publish'
            );
            $pages = get_pages($pages_args);
            foreach ($pages as $page ){
                if ( !isset($instance["page_field_".$page->ID]) ) continue;
                $page_field = esc_attr(@$instance["page_field_".$page->ID]);
                $page_field_val = (isset($page_field)) ? $page_field : '';
                $page_title = $page->post_title;
                $page_field_name = "page_field_".$page->ID;
                ?>
                <div class="wrap">
                    <label><input name="<?php echo $this->get_field_name($page_field_name); ?>" type="checkbox" value="<?php echo $page->ID?>" <?php checked( $page->ID, $page_field_val ); ?>/><?php _e($page_title); ?></label>
                </div>
            <?php } ?>
            <div class="wrap">
                <?php $archive_page = (isset($archive_page)) ? $archive_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('archive_page'); ?>" type="checkbox" value="archive_page" <?php checked( 'archive_page', $archive_page ); ?>/><?php _e('Archive Page', 'es-plugin'); ?></label>
            </div>
            <div class="wrap">
                <?php $single_page = (isset($single_page)) ? $single_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('single_page'); ?>" type="checkbox" value="single_page" <?php checked( 'single_page', $single_page ); ?>/><?php _e('Single Page', 'es-plugin'); ?></label>
            </div>
            <div class="wrap">
                <?php $category_page = (isset($category_page)) ? $category_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('category_page'); ?>" type="checkbox" value="category_page" <?php checked( 'category_page', $category_page ); ?>/><?php _e('Category Page', 'es-plugin'); ?></label>
            </div>
            <div class="wrap">
                <?php $search_page = (isset($search_page)) ? $search_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('search_page'); ?>" type="checkbox" value="search_page" <?php checked( 'search_page', $search_page ); ?>/><?php _e('Search Page', 'es-plugin'); ?></label>
            </div>
            <div class="wrap">
                <?php $author_page = (isset($author_page)) ? $author_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('author_page'); ?>" type="checkbox" value="author_page" <?php checked( 'author_page', $author_page ); ?>/><?php _e('Author Page', 'es-plugin'); ?></label>
            </div>
        </div>
    <?php  }
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        // Fields
        $instance['search_title'] 		= strip_tags($new_instance['search_title']);
        $instance['search_address'] 	= strip_tags($new_instance['search_address']);
        $instance['search_country'] 	= strip_tags($new_instance['search_country']);
        $instance['search_state'] 		= strip_tags($new_instance['search_state']);
        $instance['search_city'] 		= strip_tags($new_instance['search_city']);
        $instance['search_price'] 		= strip_tags($new_instance['search_price']);
        $instance['search_bedrooms'] 	= strip_tags($new_instance['search_bedrooms']);
        $instance['search_bathrooms']	= strip_tags($new_instance['search_bathrooms']);
        $instance['search_category'] 	= strip_tags($new_instance['search_category']);
        $instance['search_type'] 		= strip_tags($new_instance['search_type']);
        $instance['search_sqft'] 		= strip_tags($new_instance['search_sqft']);
        $instance['search_lotsize'] 	= strip_tags($new_instance['search_lotsize']);
        $instance['search_agent'] 		= strip_tags($new_instance['search_agent']);
        $instance['search_keywords'] 	= strip_tags($new_instance['search_keywords']);
        $instance['search_layout'] 		= strip_tags($new_instance['search_layout']);
        $instance['search_page']        = strip_tags($new_instance['search_page']);
        $instance['es_search_page']        = strip_tags($new_instance['es_search_page']);
        update_option('es_search_page', $instance['es_search_page']);
        $instance['show_on_pages'] 		= strip_tags($new_instance['show_on_pages']);
        $pages_args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($pages_args);
        foreach ($pages as $page ){
            $instance["page_field_".$page->ID] = strip_tags($new_instance["page_field_".$page->ID]);
        }
        $instance['archive_page'] 		= strip_tags($new_instance['archive_page']);
        $instance['single_page'] 		= strip_tags($new_instance['single_page']);
        $instance['category_page'] 		= strip_tags($new_instance['category_page']);
        $instance['search_page'] 		= strip_tags($new_instance['search_page']);
        $instance['author_page'] 		= strip_tags($new_instance['author_page']);
        return $instance;
    }
    // display widget
    function widget($args, $instance) {
        extract( $args );
        // these are the widget options
        $search_title 		= esc_attr($instance['search_title']);
        $search_address 	= esc_attr($instance['search_address']);
        $search_country 	= esc_attr($instance['search_country']);
        $search_state 		= esc_attr($instance['search_state']);
        $search_city 		= esc_attr($instance['search_city']);
        $search_price 		= esc_attr($instance['search_price']);
        $search_bedrooms 	= esc_attr($instance['search_bedrooms']);
        $search_bathrooms 	= esc_attr($instance['search_bathrooms']);
        $search_category 	= esc_attr($instance['search_category']);
        $search_type 		= esc_attr($instance['search_type']);
        $search_sqft 		= esc_attr($instance['search_sqft']);
        $search_lotsize 	= esc_attr($instance['search_lotsize']);
        $search_agent 		= esc_attr($instance['search_agent']);
        $search_keywords 	= esc_attr($instance['search_keywords']);
        $search_layout 		= esc_attr($instance['search_layout']);
        $show_on_pages 		= esc_attr($instance['show_on_pages']);
        $pages_args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($pages_args);
        $choosed_pages = array();
        foreach ($pages as $page ){
            $page_index = "page_field_{$page->ID}";
            if ( isset($instance[$page_index]) ) {
                $choosed_pages[] = esc_attr($instance[$page_index]);
            }
        }
        $choosed_pages[]  	= esc_attr($instance['archive_page']);
        $choosed_pages[]  	= esc_attr($instance['single_page']);
        $choosed_pages[] 	= esc_attr($instance['category_page']);
        $choosed_pages[]   	= esc_attr($instance['search_page']);
        $choosed_pages[]   	= esc_attr($instance['author_page']);
        $widget_id 			= $args['widget_id'];
        $choosed_pages['widget_id'] = $widget_id;
        $before_widget 		= $args['before_widget'] = '<div class="widget '.$args['widget_id'].'">';
        $after_widget 		= $args['after_widget'] = '</div>';
        echo $before_widget;
        include(PATH_DIR.'front_templates/widgets/es_search.php');
        echo $after_widget;
    }
}
// register widget
add_action('widgets_init', create_function('', 'return register_widget("es_search");'));
// Request Info Form widget Start
class es_request_form extends WP_Widget {
    // constructor
    function __construct() {
        parent::__construct(false, $name = __('Estatik Request Form', 'es-plugin') );
    }
    // widget form creation
    function form($instance) {
        // Check values
        if( $instance) {
            $request_title 		= esc_attr($instance['request_title']);
            $request_message 	= esc_attr($instance['request_message']);
            $send_message_to 	= esc_attr($instance['send_message_to']);
        }
        ?>
        <p>
            <label><?php _e('Request Title:', 'es-plugin'); ?></label><br />
            <?php $request_title = (isset($request_title)) ? $request_title : __('Learn more about this property', 'es-plugin'); ?>
            <input class="widefat" name="<?php echo $this->get_field_name('request_title'); ?>" type="text" value="<?php echo $request_title?>"/>
        </p>
        <p>
            <label><?php _e('Request Message:', 'es-plugin'); ?></label><br />
            <?php $request_message = (isset($request_message)) ? $request_message : __('Hi, I`m interested in the property. Please send me more information about it. Thank you!', 'es-plugin'); ?>
            <textarea class="widefat" name="<?php echo $this->get_field_name('request_message'); ?>"><?php echo $request_message?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('send_message_to'); ?>"><?php _e('Send Message To', 'es-plugin'); ?></label>
            <?php $send_message_to = (isset($send_message_to)) ? $send_message_to : 'admin'; ?>
            <select name="<?php echo $this->get_field_name('send_message_to'); ?>" id="<?php echo $this->get_field_id('send_message_to'); ?>" class="widefat">
                <?php
                $options = array('admin', 'agent', 'admin_agent');
                foreach ($options as $option) {
                    echo '<option value="' . $option . '" id="' . $option . '"', $send_message_to == $option ? ' selected="selected"' : '', '>', str_replace("_"," & ",$option), '</option>';
                }
                ?>
            </select>
        </p>
    <?php  }
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        // Fields
        $instance['request_title'] 		= strip_tags($new_instance['request_title']);
        $instance['request_message'] 	= strip_tags($new_instance['request_message']);
        $instance['send_message_to'] 	= strip_tags($new_instance['send_message_to']);
        return $instance;
    }
    // display widget
    function widget($args, $instance) {
        extract( $args );
        // these are the widget options
        $request_title 		= (esc_attr($instance['request_title'])!="") 	? esc_attr($instance['request_title']) 		: __('Learn more about this property', 'es-plugin');
        $request_message 	= (esc_attr($instance['request_message'])!="") 	? esc_attr($instance['request_message']) 	: __('Hi, I`m interestedin the property. Please send me more information about it. Thank you!', 'es-plugin');
        $send_message_to 	= (esc_attr($instance['send_message_to'])!="") 	? esc_attr($instance['send_message_to']) 	: __('admin', 'es-plugin');
        $widget_id 			= $args['widget_id'];
        $before_widget 		= $args['before_widget'] = '<div class="widget '.$args['widget_id'].'">';
        $after_widget 		= $args['after_widget'] = '</div>';
        echo $before_widget;
        include(PATH_DIR.'front_templates/widgets/es_request_form.php');
        echo $after_widget;
    }
}
// register widget
add_action('widgets_init', create_function('', 'return register_widget("es_request_form");'));
// SlideShow widget Start
class es_slideshow extends WP_Widget {
    // constructor
    function __construct() {
        parent::__construct(false, $name = __('Estatik Slideshow', 'es-plugin') );
    }
    // widget form creation
    function form($instance) {
        global $wpdb;
        // Check values
        if( $instance) {
            $title 				= esc_attr($instance['title']);
            $show_arrows 		= esc_attr($instance['show_arrows']);
            $slide_effect 		= esc_attr($instance['slide_effect']);
            $only_featured 		= esc_attr($instance['only_featured']);
            $category 			= esc_attr($instance['category']);
            $type 				= esc_attr($instance['type']);
            $listing_ids 		= esc_attr($instance['listing_ids']);
            $images_height 		= esc_attr($instance['images_height']);
            $images_width 		= esc_attr($instance['images_width']);
            $number_of_images 	= esc_attr($instance['number_of_images']);
            $layout 			= esc_attr($instance['layout']);
            $show_on_pages 		= esc_attr($instance['show_on_pages']);
            $pages_args = array(
                'sort_order' => 'ASC',
                'sort_column' => 'post_title',
                'post_type' => 'page',
                'post_status' => 'publish'
            );
            $pages = get_pages($pages_args);
            foreach ($pages as $page ){
                if ( isset($instance["page_field_".$page->ID]) ) {
                    esc_attr($instance["page_field_".$page->ID]);
                }
            }
            $archive_page 		= esc_attr($instance['archive_page']);
            $single_page 		= esc_attr($instance['single_page']);
            $category_page 		= esc_attr($instance['category_page']);
            $search_page 		= esc_attr($instance['search_page']);
            $author_page 		= esc_attr($instance['author_page']);
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'es-plugin'); ?></label>
            <?php $title = (isset($title)) ? $title : 'Slideshow'; ?>
            <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title?>"/>
        </p>
        <p>
            <label><?php _e('Show Arrows:', 'es-plugin'); ?></label><br />
            <?php $show_arrows = (isset($show_arrows)) ? $show_arrows : '1'; ?>
            <label style="margin-right:10px;"><input name="<?php echo $this->get_field_name('show_arrows'); ?>" type="radio" value="1" <?php checked( '1', $show_arrows ); ?>/><?php _e('Yes', 'es-plugin'); ?></label>
            <label><input name="<?php echo $this->get_field_name('show_arrows'); ?>" type="radio" value="0" <?php checked( '0', $show_arrows ); ?>/><?php _e('No', 'es-plugin'); ?></label>
            <br /><?php _e('* When No selected images will be changed with fade effect.', 'es-plugin'); ?>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('slide_effect'); ?>"><?php _e('Slide Effect', 'es-plugin'); ?></label>
            <?php $slide_effect = (isset($slide_effect)) ? $slide_effect : 'horizontal'; ?>
            <select name="<?php echo $this->get_field_name('slide_effect'); ?>" id="<?php echo $this->get_field_id('slide_effect'); ?>" class="widefat">
                <?php
                $options = array('horizontal', 'vertical');
                foreach ($options as $option) {
                    echo '<option value="' . $option . '" id="' . $option . '"', $slide_effect == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                }
                ?>
            </select>
        </p>
        <p>
            <label><?php _e('Only Featured:', 'es-plugin'); ?></label><br />
            <?php $only_featured = (isset($only_featured)) ? $only_featured : '0'; ?>
            <label style="margin-right:10px;"><input name="<?php echo $this->get_field_name('only_featured'); ?>" type="radio" value="1" <?php checked( '1', $only_featured ); ?>/><?php _e('Yes', 'es-plugin'); ?></label>
            <label><input name="<?php echo $this->get_field_name('only_featured'); ?>" type="radio" value="0" <?php checked( '0', $only_featured ); ?>/><?php _e('No', 'es-plugin'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category', 'es-plugin'); ?></label>
            <?php $category = (isset($category)) ? $category : ''; ?>
            <select name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>" class="widefat">
                <option value=""></option>
                <?php $options = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_manager_categories' );
                if(!empty($options)) {
                    foreach ($options as $option) {
                        $selected = ($category == $option->cat_id) ? 'selected="selected"' : '';
                        echo '<option '.$selected.' value="'.$option->cat_id.'">'. $option->cat_title. '</option>';
                    }
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Type', 'es-plugin'); ?></label>
            <?php $type = (isset($type)) ? $type : ''; ?>
            <select name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>" class="widefat">
                <option value=""></option>
                <?php $options = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_manager_types' );
                if(!empty($options)) {
                    foreach ($options as $option) {
                        $selected = ($type == $option->type_id) ? 'selected="selected"' : '';
                        echo '<option '.$selected.' value="'.$option->type_id.'">'. $option->type_title. '</option>';
                    }
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('listing_ids'); ?>"><?php _e('Listing IDs', 'es-plugin'); ?></label>
            <?php $listing_ids = (isset($listing_ids)) ? $listing_ids : ''; ?>
            <input class="widefat" name="<?php echo $this->get_field_name('listing_ids'); ?>" type="text" value="<?php echo $listing_ids?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('images_height'); ?>"><?php _e('Images Height', 'es-plugin'); ?></label>
            <?php $images_height = (isset($images_height)) ? $images_height : ''; ?>
            <input class="widefat" name="<?php echo $this->get_field_name('images_height'); ?>" type="text" value="<?php echo $images_height?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('images_width'); ?>"><?php _e('Images Width', 'es-plugin'); ?></label>
            <?php $images_width = (isset($images_width)) ? $images_width : ''; ?>
            <input class="widefat" name="<?php echo $this->get_field_name('images_width'); ?>" type="text" value="<?php echo $images_width?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number_of_images'); ?>"><?php _e('Number of Images', 'es-plugin'); ?></label>
            <?php $number_of_images = (isset($number_of_images)) ? $number_of_images : '1'; ?>
            <input class="widefat" name="<?php echo $this->get_field_name('number_of_images'); ?>" type="text" value="<?php echo $number_of_images?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('layout'); ?>"><?php _e('Layout', 'es-plugin'); ?></label>
            <?php $layout = (isset($layout)) ? $layout : 'horizontal'; ?>
            <select name="<?php echo $this->get_field_name('layout'); ?>" id="<?php echo $this->get_field_id('layout'); ?>" class="widefat">
                <?php
                $options = array('horizontal', 'vertical');
                foreach ($options as $option) {
                    echo '<option value="' . $option . '" id="' . $option . '"', $layout == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('show_on_pages'); ?>"><?php _e('Show On Pages', 'es-plugin'); ?></label>
            <?php $show_on_pages = (isset($show_on_pages)) ? $show_on_pages : 'all_pages'; ?>
            <select name="<?php echo $this->get_field_name('show_on_pages'); ?>" id="<?php echo $this->get_field_id('show_on_pages'); ?>" class="widefat">
                <?php
                $options = array('all_pages','show_on_checked_pages', 'hide_on_checked_pages');
                foreach ($options as $option) {
                    echo '<option value="' . $option . '" id="' . $option . '"', $show_on_pages == $option ? ' selected="selected"' : '', '>',  str_replace("_"," ",$option),  '</optio>';
                }
                ?>
            </select>
        </p>
        <p>
            <label><?php _e('Select Pages', 'es-plugin'); ?></label>
        </p>
        <div style="height: 200px; overflow-x: hidden; margin-bottom:10px; overflow-y: scroll; padding-top: 2px;">
            <?php
            $pages_args = array(
                'sort_order' => 'ASC',
                'sort_column' => 'post_title',
                'post_type' => 'page',
                'post_status' => 'publish'
            );
            $pages = get_pages($pages_args);
            foreach ($pages as $page ){
                $page_field = esc_attr(@$instance["page_field_".$page->ID]);
                $page_field_val = (isset($page_field)) ? $page_field : '';
                $page_title = $page->post_title;
                $page_field_name = "page_field_".$page->ID;
                ?>
                <p>
                    <label><input name="<?php echo $this->get_field_name($page_field_name); ?>" type="checkbox" value="<?php echo $page->ID?>" <?php checked( $page->ID, $page_field_val ); ?>/><?php _e($page_title); ?></label>
                </p>
            <?php } ?>
            <p>
                <?php $archive_page = (isset($archive_page)) ? $archive_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('archive_page'); ?>" type="checkbox" value="archive_page" <?php checked( 'archive_page', $archive_page ); ?>/><?php _e('Archive Page', 'es-plugin'); ?></label>
            </p>
            <p>
                <?php $single_page = (isset($single_page)) ? $single_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('single_page'); ?>" type="checkbox" value="single_page" <?php checked( 'single_page', $single_page ); ?>/><?php _e('Single Page', 'es-plugin'); ?></label>
            </p>
            <p>
                <?php $category_page = (isset($category_page)) ? $category_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('category_page'); ?>" type="checkbox" value="category_page" <?php checked( 'category_page', $category_page ); ?>/><?php _e('Category Page', 'es-plugin'); ?></label>
            </p>
            <p>
                <?php $search_page = (isset($search_page)) ? $search_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('search_page'); ?>" type="checkbox" value="search_page" <?php checked( 'search_page', $search_page ); ?>/><?php _e('Search Page', 'es-plugin'); ?></label>
            </p>
            <p>
                <?php $author_page = (isset($author_page)) ? $author_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('author_page'); ?>" type="checkbox" value="author_page" <?php checked( 'author_page', $author_page ); ?>/><?php _e('Author Page', 'es-plugin'); ?></label>
            </p>
        </div>
    <?php  }
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        // Fields
        $instance['title'] 				= strip_tags($new_instance['title']);
        $instance['show_arrows'] 		= strip_tags($new_instance['show_arrows']);
        $instance['slide_effect'] 		= strip_tags($new_instance['slide_effect']);
        $instance['only_featured'] 		= strip_tags($new_instance['only_featured']);
        $instance['category'] 			= strip_tags($new_instance['category']);
        $instance['type'] 				= strip_tags($new_instance['type']);
        $instance['listing_ids'] 		= strip_tags($new_instance['listing_ids']);
        $instance['images_height'] 		= strip_tags($new_instance['images_height']);
        $instance['images_width'] 		= strip_tags($new_instance['images_width']);
        $instance['number_of_images'] 	= strip_tags($new_instance['number_of_images']);
        $instance['layout'] 			= strip_tags($new_instance['layout']);
        $instance['show_on_pages'] 		= strip_tags($new_instance['show_on_pages']);
        $pages_args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($pages_args);
        foreach ($pages as $page ){
            $instance["page_field_".$page->ID] = strip_tags($new_instance["page_field_".$page->ID]);
        }
        $instance['archive_page'] 		= strip_tags($new_instance['archive_page']);
        $instance['single_page'] 		= strip_tags($new_instance['single_page']);
        $instance['category_page'] 		= strip_tags($new_instance['category_page']);
        $instance['search_page'] 		= strip_tags($new_instance['search_page']);
        $instance['author_page'] 		= strip_tags($new_instance['author_page']);
        return $instance;
    }
    // display widget
    function widget($args, $instance) {
        extract( $args );
        // these are the widget options
        $title 				= esc_attr($instance['title']);
        $show_arrows 		= esc_attr($instance['show_arrows']);
        $slide_effect 		= esc_attr($instance['slide_effect']);
        $only_featured 		= esc_attr($instance['only_featured']);
        $category 			= esc_attr($instance['category']);
        $type 				= esc_attr($instance['type']);
        $listing_ids 		= esc_attr($instance['listing_ids']);
        $images_height 		= esc_attr($instance['images_height']);
        $images_width 		= esc_attr($instance['images_width']);
        $number_of_images 	= esc_attr($instance['number_of_images']);
        $layout 			= esc_attr($instance['layout']);
        $show_on_pages 		= esc_attr($instance['show_on_pages']);
        $pages_args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($pages_args);
        $choosed_pages = array();
        foreach ($pages as $page ){
            if ( isset($instance["page_field_".$page->ID]) ) {
                $choosed_pages[] = esc_attr($instance["page_field_".$page->ID]);
            }
        }
        $choosed_pages[]  	= esc_attr($instance['archive_page']);
        $choosed_pages[]  	= esc_attr($instance['single_page']);
        $choosed_pages[] 	= esc_attr($instance['category_page']);
        $choosed_pages[]   	= esc_attr($instance['search_page']);
        $choosed_pages[]   	= esc_attr($instance['author_page']);
        $widget_id 			= $args['widget_id'];
        $choosed_pages['widget_id'] = $widget_id;
        $before_widget 		= $args['before_widget'] = '<div class="widget '.$args['widget_id'].'">';
        $after_widget 		= $args['after_widget'] = '</div>';
        echo $before_widget;
        include(PATH_DIR.'front_templates/widgets/es_slideshow.php');
        echo $after_widget;
    }
}
// register widget
add_action('widgets_init', create_function('', 'return register_widget("es_slideshow");'));
// MapView widget Start
class es_mapview extends WP_Widget {
    // constructor
    /*function es_mapview() {
        parent::WP_Widget(false, $name = __('Estatik Map View', 'es-plugin') );
    }*/
    function __construct() {
        parent::__construct(
            'es_mapview', // Base ID
            __( 'Estatik Map View', 'es-plugin' )
        );
    }
    // widget form creation
    function form($instance) {
        global $wpdb;
        // Check values
        if( $instance) {
            $category 			= esc_attr($instance['category']);
            $map_icon_style 	= esc_attr($instance['map_icon_style']);
            $number_of_props 	= esc_attr($instance['number_of_props']);
            $map_zoom_level 	= esc_attr($instance['map_zoom_level']);
            $mapview_layout 	= esc_attr($instance['mapview_layout']);
            $show_on_pages 		= esc_attr($instance['show_on_pages']);
            $pages_args = array(
                'sort_order' => 'ASC',
                'sort_column' => 'post_title',
                'post_type' => 'page',
                'post_status' => 'publish'
            );
            $pages = get_pages($pages_args);
            foreach ($pages as $page ){
                esc_attr($instance["page_field_".$page->ID]);
            }
            $archive_page 		= esc_attr($instance['archive_page']);
            $single_page 		= esc_attr($instance['single_page']);
            $category_page 		= esc_attr($instance['category_page']);
            $search_page 		= esc_attr($instance['search_page']);
            $author_page 		= esc_attr($instance['author_page']);
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category', 'es-plugin'); ?></label>
            <?php $category = (isset($category)) ? $category : ''; ?>
            <select name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>" class="widefat">
                <option value="all">Show All</option>
                <?php $options = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_manager_categories' );
                if(!empty($options)) {
                    foreach ($options as $option) {
                        $selected = ($category == $option->cat_id) ? 'selected="selected"' : '';
                        echo '<option '.$selected.' value="'.$option->cat_id.'">'. $option->cat_title. '</option>';
                    }
                }
                ?>
            </select>
        </p>
        <p>
            <label><?php _e('Map Icon Style:', 'es-plugin'); ?></label><br />
            <?php $map_icon_style = (isset($map_icon_style)) ? $map_icon_style : '1'; ?>
            <label style="margin-right:10px;">
                <input name="<?php echo $this->get_field_name('map_icon_style'); ?>" type="radio" value="1" <?php checked( '1', $map_icon_style ); ?>/>
                <img src="<?php echo DIR_URL . 'front_templates/images/mapIconBlue1.png';?>" style="vertical-align:middle; margin-left:-6px;" alt="Map Icon Style Icon" />
                <img src="<?php echo DIR_URL . 'front_templates/images/mapIconRed1.png';?>" style="vertical-align:middle; margin-left:-8px;" alt="Map Icon Style Icon" />
            </label>
            <label style="margin-right:10px;">
                <input name="<?php echo $this->get_field_name('map_icon_style'); ?>" type="radio" value="2" <?php checked( '2', $map_icon_style ); ?>/>
                <img src="<?php echo DIR_URL . 'front_templates/images/mapIconBlue2.png';?>" style="vertical-align:middle; margin-left:-10px;" alt="Map Icon Style Icon" />
                <img src="<?php echo DIR_URL . 'front_templates/images/mapIconRed2.png';?>" style="vertical-align:middle; margin-left:-12px;" alt="Map Icon Style Icon" />
            </label>
            <br/>
            <label style="margin-right:10px;">
                <input name="<?php echo $this->get_field_name('map_icon_style'); ?>" type="radio" value="3" <?php checked( '3', $map_icon_style ); ?>/>
                <img src="<?php echo DIR_URL . 'front_templates/images/mapIconBlue3.png';?>" style="vertical-align:middle;" alt="Map Icon Style Icon" />
                <img src="<?php echo DIR_URL . 'front_templates/images/mapIconRed3.png';?>" style="vertical-align:middle; margin-left:-2px;" alt="Map Icon Style Icon" />
            </label>
            <label style="margin-right:10px;">
                <input name="<?php echo $this->get_field_name('map_icon_style'); ?>" type="radio" value="4" <?php checked( '4', $map_icon_style ); ?>/>
                <img src="<?php echo DIR_URL . 'front_templates/images/mapIconBlue4.png';?>" style="vertical-align:middle;" alt="Map Icon Style Icon" />
                <img src="<?php echo DIR_URL . 'front_templates/images/mapIconRed4.png';?>" style="vertical-align:middle; margin-left:-2px;" alt="Map Icon Style Icon" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number_of_props'); ?>"><?php _e('Number of Props at once', 'es-plugin'); ?></label>
            <?php $number_of_props = (isset($number_of_props)) ? $number_of_props : ''; ?>
            <input class="widefat" name="<?php echo $this->get_field_name('number_of_props'); ?>" type="text" value="<?php echo $number_of_props?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('map_zoom_level'); ?>"><?php _e('Map Zoom Level(default 10)', 'es-plugin'); ?></label>
            <?php $map_zoom_level = (isset($map_zoom_level)) ? $map_zoom_level : '10'; ?>
            <input class="widefat" name="<?php echo $this->get_field_name('map_zoom_level'); ?>" type="text" value="<?php echo $map_zoom_level?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('mapview_layout'); ?>"><?php _e('Layout', 'es-plugin'); ?></label>
            <?php $mapview_layout = (isset($mapview_layout)) ? $mapview_layout : 'horizontal'; ?>
            <select name="<?php echo $this->get_field_name('mapview_layout'); ?>" id="<?php echo $this->get_field_id('mapview_layout'); ?>" class="widefat">
                <?php
                $options = array('horizontal', 'vertical');
                foreach ($options as $option) {
                    echo '<option value="' . $option . '" id="' . $option . '"', $mapview_layout == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('show_on_pages'); ?>"><?php _e('Show On Pages', 'es-plugin'); ?></label>
            <?php $show_on_pages = (isset($show_on_pages)) ? $show_on_pages : 'all_pages'; ?>
            <select name="<?php echo $this->get_field_name('show_on_pages'); ?>" id="<?php echo $this->get_field_id('show_on_pages'); ?>" class="widefat">
                <?php
                $options = array('all_pages','show_on_checked_pages', 'hide_on_checked_pages');
                foreach ($options as $option) {
                    echo '<option value="' . $option . '" id="' . $option . '"', $show_on_pages == $option ? ' selected="selected"' : '', '>',  str_replace("_"," ",$option),  '</optio>';
                }
                ?>
            </select>
        </p>
        <p>
            <label><?php _e('Select Pages', 'es-plugin'); ?></label>
        </p>
        <div style="height: 200px; overflow-x: hidden; margin-bottom:10px; overflow-y: scroll; padding-top: 2px;">
            <?php
            $pages_args = array(
                'sort_order' => 'ASC',
                'sort_column' => 'post_title',
                'post_type' => 'page',
                'post_status' => 'publish'
            );
            $pages = get_pages($pages_args);
            foreach ($pages as $page ){
                $page_field = esc_attr(@$instance["page_field_".$page->ID]);
                $page_field_val = (isset($page_field)) ? $page_field : '';
                $page_title = $page->post_title;
                $page_field_name = "page_field_".$page->ID;
                ?>
                <p>
                    <label><input name="<?php echo $this->get_field_name($page_field_name); ?>" type="checkbox" value="<?php echo $page->ID?>" <?php checked( $page->ID, $page_field_val ); ?>/><?php _e($page_title); ?></label>
                </p>
            <?php } ?>
            <p>
                <?php $archive_page = (isset($archive_page)) ? $archive_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('archive_page'); ?>" type="checkbox" value="archive_page" <?php checked( 'archive_page', $archive_page ); ?>/><?php _e('Archive Page', 'es-plugin'); ?></label>
            </p>
            <p>
                <?php $single_page = (isset($single_page)) ? $single_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('single_page'); ?>" type="checkbox" value="single_page" <?php checked( 'single_page', $single_page ); ?>/><?php _e('Single Page', 'es-plugin'); ?></label>
            </p>
            <p>
                <?php $category_page = (isset($category_page)) ? $category_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('category_page'); ?>" type="checkbox" value="category_page" <?php checked( 'category_page', $category_page ); ?>/><?php _e('Category Page', 'es-plugin'); ?></label>
            </p>
            <p>
                <?php $search_page = (isset($search_page)) ? $search_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('search_page'); ?>" type="checkbox" value="search_page" <?php checked( 'search_page', $search_page ); ?>/><?php _e('Search Page', 'es-plugin'); ?></label>
            </p>
            <p>
                <?php $author_page = (isset($author_page)) ? $author_page : '0'; ?>
                <label><input name="<?php echo $this->get_field_name('author_page'); ?>" type="checkbox" value="author_page" <?php checked( 'author_page', $author_page ); ?>/><?php _e('Author Page', 'es-plugin'); ?></label>
            </p>
        </div>
    <?php  }
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        // Fields
        $instance['category'] 			= strip_tags($new_instance['category']);
        $instance['map_icon_style'] 	= strip_tags($new_instance['map_icon_style']);
        $instance['number_of_props'] 	= strip_tags($new_instance['number_of_props']);
        $instance['map_zoom_level'] 	= strip_tags($new_instance['map_zoom_level']);
        $instance['mapview_layout'] 	= strip_tags($new_instance['mapview_layout']);
        $instance['show_on_pages'] 		= strip_tags($new_instance['show_on_pages']);
        $pages_args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($pages_args);
        foreach ($pages as $page ){
            $instance["page_field_".$page->ID] = strip_tags($new_instance["page_field_".$page->ID]);
        }
        $instance['archive_page'] 		= strip_tags($new_instance['archive_page']);
        $instance['single_page'] 		= strip_tags($new_instance['single_page']);
        $instance['category_page'] 		= strip_tags($new_instance['category_page']);
        $instance['search_page'] 		= strip_tags($new_instance['search_page']);
        $instance['author_page'] 		= strip_tags($new_instance['author_page']);
        return $instance;
    }
    // display widget
    function widget($args, $instance) {
        extract( $args );
        // these are the widget options
        $category 				= esc_attr($instance['category']);
        $map_icon_style 		= esc_attr($instance['map_icon_style']);
        $number_of_props 		= esc_attr($instance['number_of_props']);
        $map_zoom_level 		= esc_attr($instance['map_zoom_level']);
        $mapview_layout 		= esc_attr($instance['mapview_layout']);
        $show_on_pages 		= esc_attr($instance['show_on_pages']);
        $pages_args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($pages_args);
        $choosed_pages = array();
        foreach ($pages as $page ){
            if ( isset($instance["page_field_".$page->ID]) ) {
                $choosed_pages[] = esc_attr($instance["page_field_".$page->ID]);
            }
        }
        $choosed_pages[]  	= esc_attr($instance['archive_page']);
        $choosed_pages[]  	= esc_attr($instance['single_page']);
        $choosed_pages[] 	= esc_attr($instance['category_page']);
        $choosed_pages[]   	= esc_attr($instance['search_page']);
        $choosed_pages[]   	= esc_attr($instance['author_page']);
        $widget_id 			= $args['widget_id'];
        $before_widget 		= $args['before_widget'] = '<div class="widget '.$args['widget_id'].'">';
        $after_widget 		= $args['after_widget'] = '</div>';
        echo $before_widget;
        include(PATH_DIR.'front_templates/widgets/es_mapview.php');
        echo $after_widget;
    }
}
// register widget
add_action('widgets_init', create_function('', 'return register_widget("es_mapview");')); 
