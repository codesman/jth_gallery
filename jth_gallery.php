<?php
/**
 * Plugin Name: JTH Gallery
 * Plugin URI:  http://wordpress.org/plugins
 * Description: Adds custom gallery shortcode
 * Version:     0.1.0
 * Author:      Tom Holland
 * Author URI:
 * License:     GPLv2+
 * Text Domain: jth
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2014 Tom Holland (email : tom@thpro.net)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 * version modified by theAverageDev (Luca Tumedei)
 * http://theaveragedev.com
 */

// Composer autoload
// modified to work with PHP 5.2 thanks to
// https://bitbucket.org/xrstf/composer-php52
include 'vendor/autoload_52.php';

/**
 * Activation and deactivation
 */
register_activation_hook( __FILE__, array( 'Jth_Gallery',
                                           'activate'
) );
register_deactivation_hook( __FILE__, array( 'Jth_Gallery',
                                             'deactivate'
) );

class Jth_Gallery
{
    public $version = null;
    public $path = null;
    public $uri = null;
    public $prefix = null;
    public $js_assets = null;
    public $css_assets = null;

    /**
     * An instance of the plugin main class, meant to be singleton.
     * @var Jth_Gallery
     */
    private static $instance = null;

    /**
     * The global functions adapter used to isolate the class.
     * @var tad_FunctionsAdapter or a mock object.
     */
    private $f = null;

    public function __construct( tad_FunctionsAdapterInterface $f = null )
    {
        if ( is_null( $f ) )
        {
            $f = new tad_FunctionsAdapter();
        }
        $this->f = $f;

        add_filter( 'post_gallery', array( __CLASS__,
                                           'my_gallery_shortcode'
        ), 10, 4 );
        add_shortcode( 'home_gallery_banner', array( __CLASS__,
                                                     'home_gallery_banner'
        ) );
        add_shortcode( 'home_video_banner', array( __CLASS__,
            'home_video_banner'
        ) );
    }

    public function my_gallery_shortcode( $output = '', $atts, $content = false, $tag = false )
    {
        $return = $output; // fallback
        $output = str_replace( '>', ' rel="gallery-gallery">', $output );
        // retrieve content of your own gallery function
        $my_result = $output;

        // boolean false = empty, see http://php.net/empty
        if ( !empty( $my_result ) )
        {
            $return = $my_result;
        }

        return $return;
    }

    private function banner_images()
    {
        static $gallery_id = 2176;

        $children_array = get_children( array( 'post_parent' => $gallery_id,
                                               'posts_per_page' => 9
        ) );

        foreach ( $children_array as $image_id )
        {
            echo wp_get_attachment_image( $image_id->ID, 'gallery-medium', 0, array( 'class' => 'nothickbox' ) );
        }

        return null;
    }

    private function get_banner_images($index = 0)
    {
        static $gallery_id = 2176;

        $children_array = get_children( array( 'post_parent' => $gallery_id,
                                               'posts_per_page' => 9
        ) );

        return $children_array[$index];
    }

    public function home_gallery_banner()
    {
        ob_start();

        ?><a href="gallery/" class="home_gallery"><?php print self::banner_images(); ?><h2>Click Here to see pictures of recent tours and our satisfied guests!</h2></a><?php

        return ob_get_clean();
    }

    public static function home_video_banner()
    {
        ob_start();

        ?><video id="9589b301" class="sublime" poster="https://43c06f8b102c8e410d10-4555f8d325a5c155a308e6d967cbf0cc.ssl.cf1.rackcdn.com/hjt_promo/hjt-promo-thumbnail.jpg" width="700" height="393" title="Hawaii Jeep Tours - Awesome!" data-uid="9589b301"  data-autoresize="fit" preload="none" data-google-analytics-enable='true'>
        <source src="https://43c06f8b102c8e410d10-4555f8d325a5c155a308e6d967cbf0cc.ssl.cf1.rackcdn.com/hjt_promo/2015-02-26_HJT-Promo-360.mp4" />
        <source src="https://43c06f8b102c8e410d10-4555f8d325a5c155a308e6d967cbf0cc.ssl.cf1.rackcdn.com/hjt_promo/2015-02-26_HJT-Promo-720.mp4" data-quality="hd" />
        <source src="https://43c06f8b102c8e410d10-4555f8d325a5c155a308e6d967cbf0cc.ssl.cf1.rackcdn.com/hjt_promo/2015-02-26_HJT-Promo-1080.mp4" />
    </video><?php

        return ob_get_clean();
    }

    /**
     * Do not allow writing access to properties.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set( $name, $value )
    {
        trigger_error( sprintf( 'Cannot set %s->%s property.', __CLASS__, $name ) );
    }

    public static function the( $key )
    {
        return self::$instance;
    }

    public static function get_instance()
    {
        return self::$instance;
    }

    private function init_vars()
    {
        $this->version = '0.1.0';
        $this->path = dirname( __FILE__ );
        $this->uri = $this->f->plugin_basename( __FILE__ );
        $this->prefix = "jth";
        $this->js_assets = $this->uri . '/assets/js';
        $this->css_assets = $this->uri . '/assets/css';
    }

    /*
     * Default initialization for the plugin:
     * - Initializes the plugin vars
     * - Hooks into actions and filters
     * - Registers the default textdomain.
     */
    public static function init()
    {
        if ( self::$instance == null )
        {
            self::$instance = new self();
        }
        self::$instance->init_vars();
        self::$instance->hook();

        $locale = apply_filters( 'plugin_locale', get_locale(), 'jth' );
        load_textdomain( 'jth', WP_LANG_DIR . '/jth/jth-' . $locale . '.mo' );
        load_plugin_textdomain( 'jth', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Hook into actions and filters here

     */
    public function hook()
    {
    }

    /**
     * Activate the plugin
     */
    public static function activate()
    {
        // do something here
    }

    /**
     * Deactivate the plugin
     * Uninstall routines should be in uninstall.php
     */
    public static function deactivate()
    {
        // do something here
    }
}

// Bootstrap the plugin main class
Jth_Gallery::init();
