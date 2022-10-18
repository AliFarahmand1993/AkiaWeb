<?php
//setups
function moshaver_setup(){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(
        array(
            'main-menu'=>'منوی اصلی',
			'mobile-menu'=>'منوی موبایل',
            'footer-menu'=>' منوی فوتر',
    
        )
    );
}
add_action('after_setup_theme',"moshaver_setup");
//css&js support
function moshaver_scripts(){
    wp_enqueue_style('bootstrap',get_template_directory_uri() . '/assets/css/bootstrap.min.css');
    wp_enqueue_style('aos',get_template_directory_uri() . '/assets/css/aos.css');
    wp_enqueue_style('fontawesome',get_template_directory_uri() . '/assets/css/fontawesome.min.css');
    wp_enqueue_style('odometer',get_template_directory_uri() . '/assets/css/odometer.min.css');
    wp_enqueue_style('remixicon',get_template_directory_uri() . '/assets/css/remixicon.css');
    wp_enqueue_style('magnific',get_template_directory_uri() . '/assets/css/magnific-popup.min.css');
    wp_enqueue_style('meanmenu',get_template_directory_uri() . '/assets/css/meanmenu.min.css');
    wp_enqueue_style('swiper',get_template_directory_uri() . '/assets/css/swiper-bundle.min.css');
    wp_enqueue_style('owl.carousel',get_template_directory_uri() . '/assets/css/owl.carousel.min.css');
    wp_enqueue_style('owl.theme',get_template_directory_uri() . '/assets/css/owl.theme.default.min.css');
    wp_enqueue_style('stylesheet',get_template_directory_uri() . '/assets/css/style.css');
    wp_enqueue_style('style',get_template_directory_uri() . '/style.css', array(), '1.0.1');
    // 
    wp_enqueue_script('jquery', get_template_directory_uri().'/assets/js/jquery.min.js',array('jquery'),'1',true);
    wp_enqueue_script('bootstrap', get_template_directory_uri().'/assets/js/bootstrap.bundle.min.js',array('jquery'),'1',true);
    wp_enqueue_script('owl.carousel.min', get_template_directory_uri().'/assets/js/owl.carousel.min.js',array('jquery'),'1',true);
    wp_enqueue_script('swiper-bundle', get_template_directory_uri().'/assets/js/swiper-bundle.min.js',array('jquery'),'1',true);
    wp_enqueue_script('magnific-popup', get_template_directory_uri().'/assets/js/magnific-popup.min.js',array('jquery'),'1',true);
    wp_enqueue_script('meanmenu', get_template_directory_uri().'/assets/js/meanmenu.min.js',array('jquery'),'1',true);
    wp_enqueue_script('appear', get_template_directory_uri().'/assets/js/appear.min.js',array('jquery'),'1',true);
    wp_enqueue_script('odometer', get_template_directory_uri().'/assets/js/odometer.min.js',array('jquery'),'1',true);
    wp_enqueue_script('validator', get_template_directory_uri().'/assets/js/form-validator.min.js',array('jquery'),'1',true);
    wp_enqueue_script('contact-form-script', get_template_directory_uri().'/assets/js/contact-form-script.js',array('jquery'),'1',true);
    wp_enqueue_script('ajaxchimp', get_template_directory_uri().'/assets/js/ajaxchimp.min.js',array('jquery'),'1',true);
    wp_enqueue_script('aos', get_template_directory_uri().'/assets/js/aos.js',array('jquery'),'1',true);
    wp_enqueue_script('main', get_template_directory_uri().'/assets/js/main.js',array('jquery'),'1',true);
}
add_action('wp_enqueue_scripts','moshaver_scripts');
//cmb2
require_once dirname( __FILE__ ) . '/cmb2/init.php';
require_once dirname( __FILE__ ) . '/functions/cmb2-options.php';
//Remove editor
function remove_editor() {
    if (isset($_GET['post'])) {
        $id = $_GET['post'];
        $template = get_post_meta($id, '_wp_page_template', true);
        switch ($template) {
            case 'home.php':
            case 'contact_theme.php':
            // the below removes 'editor' support for 'pages'
            // if you want to remove for posts or custom post types as well
            // add this line for posts:
            // remove_post_type_support('post', 'editor');
            // add this line for custom post types and replace 
            // custom-post-type-name with the name of post type:
            // remove_post_type_support('custom-post-type-name', 'editor');
            remove_post_type_support('page', 'editor');
            break;
            default :
            // Don't remove any other template.
            break;
        }
    }
}
add_action('init', 'remove_editor');
// 