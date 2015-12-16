<?php
/**
 * Plugin Name: Pods - Custom Content Types and Fields
 * Plugin URI: http://pods.io/
 * Description: Pods is a framework for creating, managing, and deploying customized content types and fields
 * Version: 3.0.0-a-10
 * Author: Pods Framework Team
 * Author URI: http://pods.io/about/
 * Text Domain: pods
 * Domain Path: /languages/
 * GitHub Plugin URI: https://github.com/pods-framework/pods
 * GitHub Branch: release/3.0
 *
 * @package Pods
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html GPLv2+
 */

// Prevent conflicts with Pods 1.x
if ( defined( 'PODS_VERSION' ) || defined( 'PODS_DIR' ) ) {
    add_action( 'init', 'pods_deactivate_pods_1_x' );
    add_action( 'init', 'pods_deactivate_pods_ui' );
}
else {
    // Current version
    define( 'PODS_VERSION', '3.0.0-a-10' );

    // Version tracking between DB updates themselves
    define( 'PODS_DB_VERSION', '2.3.5' );

    if ( ! defined( 'PODS_WP_VERSION_MINIMUM' ) ) {
        define( 'PODS_WP_VERSION_MINIMUM', '4.0' );
    }

    if ( ! defined( 'PODS_PHP_VERSION_MINIMUM' ) ) {
        define( 'PODS_PHP_VERSION_MINIMUM', '5.2.4' );
    }

    if ( ! defined( 'PODS_MYSQL_VERSION_MINIMUM' ) ) {
        define( 'PODS_MYSQL_VERSION_MINIMUM', '5.0' );
    }

    define( 'PODS_SLUG', plugin_basename( __FILE__ ) );
    define( 'PODS_URL', plugin_dir_url( __FILE__ ) );
    define( 'PODS_DIR', plugin_dir_path( __FILE__ ) );

    /**
     * Path to the vendor directory in Pods
     *
     * @since 3.0.0
     */
    define( 'PODS_VENDOR_DIR', PODS_DIR . 'includes/vendor' );

    // Prevent conflicts with old Pods UI plugin
    if ( function_exists( 'pods_ui_manage' ) ) {
        add_action( 'init', 'pods_deactivate_pods_ui' );
    } else {
        global $pods, $pods_init, $pods_form;

        require_once( PODS_DIR . 'classes/Pods/ClassLoader.php' );

        $classLoader = new Pods_ClassLoader();
        $classLoader->addDirectory( PODS_DIR . 'classes' );
        $classLoader->addDirectory( PODS_DIR . 'deprecated/classes' );
        $classLoader->addAliases( array(
            'PodsAPI'             => 'Pods_API',
            'PodsAdmin'           => 'Pods_Admin',
            'PodsArray'           => 'Pods_Array',
            'PodsComponent'       => 'Pods_Component',
            'PodsComponents'      => 'Pods_Components',
            'PodsData'            => 'Pods_Data',
            'PodsField'           => 'Pods_Field',
            'PodsForm'            => 'Pods_Form',
            'PodsInit'            => 'Pods_Init',
            'PodsMeta'            => 'Pods_Meta',
            'PodsMigrate'         => 'Pods_Migrate',
            'PodsUI'              => 'Pods_UI',
            'PodsView'            => 'Pods_View',
            'PodsWidgetField'     => 'Pods_Widget_Field',
            'PodsWidgetForm'      => 'Pods_Widget_Form',
            'PodsWidgetList'      => 'Pods_Widget_List',
            'PodsWidgetSingle'    => 'Pods_Widget_Single',
            'PodsWidgetView'      => 'Pods_Widget_View',
            'PodsField_Avatar'    => 'Pods_Field_Avatar',
            'PodsField_Boolean'   => 'Pods_Field_Boolean',
            'PodsField_Code'      => 'Pods_Field_Code',
            'PodsField_Color'     => 'Pods_Field_Color',
            'PodsField_Currency'  => 'Pods_Field_Currency',
            'PodsField_Date'      => 'Pods_Field_Date',
            'PodsField_DateTime'  => 'Pods_Field_DateTime',
            'PodsField_Email'     => 'Pods_Field_Email',
            'PodsField_File'      => 'Pods_Field_File',
            'PodsField_HTML'      => 'Pods_Field_HTML',
            'PodsField_Number'    => 'Pods_Field_Number',
            'PodsField_Paragraph' => 'Pods_Field_Paragraph',
            'PodsField_Password'  => 'Pods_Field_Password',
            'PodsField_Phone'     => 'Pods_Field_Phone',
            'PodsField_Pick'      => 'Pods_Field_Pick',
            'PodsField_Slug'      => 'Pods_Field_Slug',
            'PodsField_Taxonomy'  => 'Pods_Field_Taxonomy',
            'PodsField_Text'      => 'Pods_Field_Text',
            'PodsField_Time'      => 'Pods_Field_Time',
            'PodsField_Website'   => 'Pods_Field_Website',
            'PodsField_WYSIWYG'   => 'Pods_Field_WYSIWYG'
        ) );
        $classLoader->register();

        require_once( PODS_DIR . 'includes/classes.php' );
        require_once( PODS_DIR . 'includes/data.php' );
        require_once( PODS_DIR . 'includes/general.php' );

        if ( ! defined( 'PODS_MEDIA' ) || PODS_MEDIA ) {
            require_once( PODS_DIR . 'includes/media.php' );
        }

        if ( ! defined( 'SHORTINIT' ) || ! SHORTINIT ) {
            if ( pods_allow_deprecated() ) {
                require_once( PODS_DIR . 'deprecated/deprecated.php' );
            }

            if ( false !== pods_compatibility_check() ) {
                $pods_form = pods_form();

                if ( ! is_network_admin() ) {
                    $pods_init = pods_init();
                }

            }

        }

    }

}



/**
 * Deactivate Pods 1.x or other Pods plugins
 */
function pods_deactivate_pods_1_x() {
    if ( defined( 'PODS_VERSION' ) && defined( 'PODS_DIR' ) && file_exists( untrailingslashit( PODS_DIR ) . '/init.php' ) ) {
        if ( !function_exists( 'deactivate_plugins' ) )
            include_once ABSPATH . 'wp-admin/includes/plugin.php';

        deactivate_plugins( realpath( untrailingslashit( PODS_DIR ) . '/init.php' ) );

        if ( !headers_sent() && ( !function_exists( 'pods_ui_manage' ) && !file_exists( WP_CONTENT_DIR . 'plugins/pods-ui/pods-ui.php' ) ) ) {
            wp_redirect( $_SERVER[ 'REQUEST_URI' ] );
            die();
        }
    }
}

/**
 * Deactivate Pods UI plugin
 */
function pods_deactivate_pods_ui() {
    if ( function_exists( 'pods_ui_manage' ) && file_exists( WP_CONTENT_DIR . 'plugins/pods-ui/pods-ui.php' ) ) {
        if ( !function_exists( 'deactivate_plugins' ) )
            include_once ABSPATH . 'wp-admin/includes/plugin.php';

        deactivate_plugins( realpath( WP_CONTENT_DIR . 'plugins/pods-ui/pods-ui.php' ) );

        if ( !headers_sent() ) {
            wp_redirect( $_SERVER[ 'REQUEST_URI' ] );
            die();
        }
    }
}
