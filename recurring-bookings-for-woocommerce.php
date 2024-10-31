<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Recurring Bookings for WooCommerce
 * Plugin URI:        https://www.recurringbookings.com
 * Description:       Works with WooCommerce Bookings to create multiple or repeated bookings
 * Version:           2.0.0
 * Author:            Bouncingsprout Studio
 * Author URI:        https://www.bouncingsprout.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       recurring-bookings-for-woocommerce
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 5.2.0
 *
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( function_exists( 'rbwc_fs' ) ) {
    rbwc_fs()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( 'rbwc_fs' ) ) {
        // Create a helper function for easy SDK access.
        function rbwc_fs()
        {
            global  $rbwc_fs ;
            
            if ( !isset( $rbwc_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $rbwc_fs = fs_dynamic_init( array(
                    'id'             => '2407',
                    'slug'           => 'recurring-bookings-for-woocommerce',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_1b55179e2a4cca11ff9363e555d6b',
                    'is_premium'     => false,
                    'premium_suffix' => '',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                    'slug'    => 'recurring_bookings',
                    'support' => false,
                    'parent'  => array(
                    'slug' => 'edit.php?post_type=wc_booking',
                ),
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $rbwc_fs;
        }
        
        // Init Freemius.
        rbwc_fs();
        // Signal that SDK was initiated.
        do_action( 'rbwc_fs_loaded' );
    }
    
    function rbwc_fs_custom_connect_message(
        $message,
        $user_first_name,
        $plugin_title,
        $user_login,
        $site_link,
        $freemius_link
    )
    {
        return sprintf(
            __( 'Hey %1$s' ) . ',<br>' . __( 'never miss an important update -- opt-in to our security and feature updates notifications, and non-sensitive diagnostic tracking with freemius.com.', 'recurring-bookings-for-woocommerce' ),
            $user_first_name,
            '<b>' . $plugin_title . '</b>',
            '<b>' . $user_login . '</b>',
            $site_link,
            $freemius_link
        );
    }
    
    rbwc_fs()->add_filter(
        'connect_message',
        'rbwc_fs_custom_connect_message',
        10,
        6
    );
    /**
     * Currently plugin version.
     * Start at version 1.0.0 and use SemVer - https://semver.org
     * Rename this for your plugin and update it as you release new versions.
     */
    define( 'RBWC_VERSION', '2.0.0' );
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-recurring-bookings-for-woocommerce-activator.php
     */
    function activate_recurring_bookings_for_woocommerce()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-recurring-bookings-for-woocommerce-activator.php';
        // Let's check for both WooCommerce and WooCommerce Bookings and gracefully fail with a message if !exist
        
        if ( !class_exists( 'WooCommerce' ) || !class_exists( 'WC_Bookings' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'Please install and activate WooCommerce and WooCommerce Bookings.', 'recurring-bookings-for-woocommerce' ), 'Plugin dependency check', array(
                'back_link' => true,
            ) );
        } else {
            Recurring_Bookings_For_Woocommerce_Activator::activate();
        }
    
    }
    
    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-recurring-bookings-for-woocommerce-deactivator.php
     */
    function deactivate_recurring_bookings_for_woocommerce()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-recurring-bookings-for-woocommerce-deactivator.php';
        Recurring_Bookings_For_Woocommerce_Deactivator::deactivate();
    }
    
    /**
     * The code that runs during plugin uninstall.
     */
    function uninstall_recurring_bookings_for_woocommerce()
    {
        rbwc_fs()->add_action( 'after_uninstall', 'rbwc_fs_uninstall_cleanup' );
    }
    
    register_activation_hook( __FILE__, 'activate_recurring_bookings_for_woocommerce' );
    register_deactivation_hook( __FILE__, 'deactivate_recurring_bookings_for_woocommerce' );
    register_uninstall_hook( __FILE__, 'uninstall_recurring_bookings_for_woocommerce' );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-recurring-bookings-for-woocommerce.php';
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_recurring_bookings_for_woocommerce()
    {
        $plugin = new Recurring_Bookings_For_Woocommerce();
        $plugin->run();
    }
    
    function rbwc_remove_cart_manager_hooks()
    {
        remove_filter( 'woocommerce_add_cart_item_data', array( 'WC_Booking_Cart_Manager', 'add_cart_item_data' ), 10 );
    }
    
    add_action( 'wp_loaded', 'rbwc_remove_cart_manager_hooks' );
    function rbwc_remove_ajax_hooks()
    {
        remove_all_actions( 'wp_ajax_wc_bookings_calculate_costs' );
        remove_all_actions( 'wp_ajax_nopriv_wc_bookings_calculate_costs' );
    }
    
    add_action( 'wp_loaded', 'rbwc_remove_ajax_hooks', 1 );
    run_recurring_bookings_for_woocommerce();
}
