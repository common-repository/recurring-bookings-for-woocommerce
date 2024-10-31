<?php

/**
 * The file that defines the core plugin class
 *
 */
class Recurring_Bookings_For_Woocommerce
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Recurring_Bookings_For_Woocommerce_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected  $loader ;
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $recurring_bookings_for_woocommerce The string used to uniquely identify this plugin.
     */
    protected  $recurring_bookings_for_woocommerce ;
    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected  $version ;
    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->version = RBWC_VERSION;
        $this->recurring_bookings_for_woocommerce = 'recurring-bookings-for-woocommerce';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_main_hooks();
        require_once plugin_dir_path( dirname( __FILE__ ) ) . '/vendor/autoload.php';
        add_action( 'admin_init', array( 'PAnD', 'init' ) );
    }
    
    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Recurring_Bookings_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
     * - Recurring_Bookings_For_Woocommerce_i18n. Defines internationalization functionality.
     * - Recurring_Bookings_For_Woocommerce_Admin. Defines all hooks for the admin area.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recurring-bookings-for-woocommerce-loader.php';
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recurring-bookings-for-woocommerce-i18n.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-recurring-bookings-for-woocommerce-admin.php';
        /**
         * A set of general functions for use throughout the plugin and beyond.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recurring-bookings-for-woocommerce-main.php';
        /**
         * A set of general functions for use throughout the plugin and beyond.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/recurring-bookings-for-woocommerce-functions.php';
        $this->loader = new Recurring_Bookings_For_Woocommerce_Loader();
    }
    
    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Recurring_Bookings_For_Woocommerce_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Recurring_Bookings_For_Woocommerce_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }
    
    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Recurring_Bookings_For_Woocommerce_Admin( $this->get_recurring_bookings_for_woocommerce(), $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action(
            'admin_menu',
            $plugin_admin,
            'rbwc_create_submenu_page',
            999
        );
        $this->loader->add_action( 'admin_post_rbwc_form_response', $plugin_admin, 'the_form_response' );
        $this->loader->add_action( 'wp_ajax_rbwc_form_response', $plugin_admin, 'the_form_response' );
        $this->loader->add_action( 'wp_ajax_rbwc_resource_dropdown', $plugin_admin, 'rbwc_resource_dropdown' );
        $this->loader->add_action( 'wp_ajax_rbwc_persons_processor', $plugin_admin, 'rbwc_persons_processor' );
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'print_plugin_admin_notices' );
        $this->loader->add_filter( 'woocommerce_product_data_tabs', $plugin_admin, 'register_tab' );
        $this->loader->add_action( 'woocommerce_product_data_panels', $plugin_admin, 'booking_panels' );
        $this->loader->add_action(
            'woocommerce_process_product_meta',
            $plugin_admin,
            'save_product_meta',
            10,
            2
        );
        $this->loader->add_filter( 'product_type_options', $plugin_admin, 'add_recurrable_product_type' );
        $this->loader->add_action( 'woocommerce_process_product_meta_booking', $plugin_admin, 'save_post_product' );
        $this->loader->add_action( 'in_admin_header', $plugin_admin, 'add_custom_header' );
        $this->loader->add_action( 'admin_footer_text', $plugin_admin, 'admin_footer_text' );
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'backup_warning' );
        $this->loader->add_filter(
            'admin_url',
            $plugin_admin,
            'change_add_new_link',
            10,
            2
        );
    }
    
    /**
     * Register all of the hooks related to the overall functionality
     * of the plugin.
     */
    private function define_main_hooks()
    {
        $plugin_main = new Recurring_Bookings_For_Woocommerce_Main( $this->get_recurring_bookings_for_woocommerce(), $this->get_version() );
        $this->loader->add_action( 'init', $plugin_main, 'init' );
    }
    
    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }
    
    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_recurring_bookings_for_woocommerce()
    {
        return $this->recurring_bookings_for_woocommerce;
    }
    
    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Recurring_Bookings_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }
    
    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

}