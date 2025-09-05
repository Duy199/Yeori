<?php
/*
Plugin Name: Duy Dev
Description: DuyDev is a plugin that allows you to create and manage your website with ease.
Version: 1.0.0
Author: Duy Tran
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DuyDev {
    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        define('DUYDEV_PLUGIN_IMG', plugin_dir_url(__FILE__) . 'assets/images/');

        // Include the ajax-handlers.php file
        // require_once(__DIR__ . '/inc/ajax/ajax-handlers.php');
        // require_once(__DIR__ . '/inc/ajax/gravityforms.php');
        // require_once(__DIR__ . '/inc/shortcodes/thankyou-component.php');


        add_action('plugins_loaded', [$this, 'init']);
        add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'widget_scripts']);
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
    }

    public function init() {
        // Check if Elementor is installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }

        // Add your custom widget
        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
    }

    public function admin_notice_missing_elementor() {
        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'duydev'),
            '<strong>' . esc_html__('DuyDev Plugin', 'duydev') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'duydev') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    function add_elementor_widget_categories( $elements_manager ) {
        $elements_manager->add_category(
            'duydev-cat',
            [
                'title' => __('DuyDev Category', 'duydev'),
                'icon' => 'fa fa-plug',
            ]
        );

    }

    public function register_widgets() {
    require_once(__DIR__ . '/widgets/duydev.php');
    require_once(__DIR__ . '/widgets/yeori-slide.php');
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \DuyDev_Base());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Yeori_Slide_Widget());

    }

    public function register_scripts() {
        wp_enqueue_script('jquery');
        wp_register_style('swiper-css', plugins_url('assets/css/swiper-bundle.min.css', __FILE__));
        wp_register_style('duydev-css', plugins_url('assets/css/duydev.min.css', __FILE__));
        
        wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.6/gsap.min.js', array(), null, true);
        wp_enqueue_script('gsap-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.6/ScrollTrigger.min.js', array('gsap'), null, true);
        wp_enqueue_script('gsap-scrollto', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.6/ScrollToPlugin.min.js', array('gsap'), null, true);
        wp_enqueue_script('gsap-observer', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.6/Observer.min.js', array('gsap'), null, true);
        wp_enqueue_script('gsap-splittext','https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.6/SplitText.min.js',array('gsap'),null,true);

        
        // // Register AText animation
        // wp_register_style('atext-css', plugins_url('assets/css/atext.css', __FILE__));
        // wp_register_script('atext-js', plugins_url('assets/js/widgets/aText.js', __FILE__), array('gsap'), null, true);
        
        // wp_register_script('swiper-js', plugins_url('assets/js/swiper-bundle.min.js', __FILE__));
        wp_register_script('duydev-js', plugins_url('assets/js/duydev.min.js', __FILE__));

        wp_localize_script('duydev-js', 'duydev_data', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('duydev_nonce'),
        ]);

    }

    public function widget_scripts() {
        wp_enqueue_style('swiper-css');
        wp_enqueue_style('duydev-css');
        wp_enqueue_style('atext-css');
        // wp_enqueue_script('swiper-js');
        // Ensure GSAP and ScrollTrigger are loaded in Elementor editor/preview
        wp_enqueue_script('gsap');
        wp_enqueue_script('gsap-scrolltrigger');
        wp_enqueue_script('gsap-scrollto');
        wp_enqueue_script('gsap-observer');
        wp_enqueue_script('gsap-splittext');
        wp_enqueue_script('atext-js');
        wp_enqueue_script('duydev-js');
    }
}

// Initialize the plugin
DuyDev::instance();