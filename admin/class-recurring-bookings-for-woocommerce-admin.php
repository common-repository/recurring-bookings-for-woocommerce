<?php

/**
 * The admin-specific functionality of the plugin.
 *
 */
class Recurring_Bookings_For_Woocommerce_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $recurring_bookings_for_woocommerce The ID of this plugin.
     */
    private  $recurring_bookings_for_woocommerce ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @param string $recurring_bookings_for_woocommerce The name of this plugin.
     * @param string $version The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct( $recurring_bookings_for_woocommerce, $version )
    {
        $this->recurring_bookings_for_woocommerce = $recurring_bookings_for_woocommerce;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @param $hook
     *
     * @since    1.0.0
     */
    public function enqueue_styles( $hook )
    {
        if ( !in_array( $hook, [
            'wc_booking_page_create_recurring_booking',
            'wc_booking_page_recurring_bookings',
            'wc_booking_page_recurring_bookings-account',
            'wc_booking_page_recurring_bookings-contact',
            'wc_booking_page_recurring_bookings-pricing'
        ], true ) ) {
            return;
        }
        wp_enqueue_style( 'jquery-ui', '//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
        wp_enqueue_style(
            $this->recurring_bookings_for_woocommerce,
            plugin_dir_url( __FILE__ ) . 'css/recurring-bookings-for-woocommerce-admin.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            'jquery-ui-timepicker',
            plugin_dir_url( __FILE__ ) . 'css/jquery.timepicker.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            'wc-bookings-admin',
            plugins_url() . '/woocommerce-bookings/dist/css/admin.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            'wc-bookings-frontend',
            plugins_url() . '/woocommerce-bookings/dist/css/frontend.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            'select-2',
            plugin_dir_url( __FILE__ ) . 'css/select2.min.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            'jquery-ui-multidatespicker',
            plugin_dir_url( __FILE__ ) . 'css/jquery-ui.multidatespicker.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @param $hook
     *
     * @since    1.0.0
     */
    public function enqueue_scripts( $hook )
    {
        if ( !in_array( $hook, [
            'wc_booking_page_create_recurring_booking',
            'wc_booking_page_recurring_bookings',
            'post.php',
            'post-new.php'
        ], true ) ) {
            return;
        }
        wp_enqueue_script(
            'jquery-ui-timepicker-vega',
            plugin_dir_url( __FILE__ ) . 'js/jquery.timepicker.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        wp_enqueue_script(
            'jquery-ui-multidatespicker',
            plugin_dir_url( __FILE__ ) . 'js/jquery-ui.multidatespicker.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        wp_enqueue_script(
            'rbwc-main-js-advanced',
            plugin_dir_url( __FILE__ ) . 'js/recurring-bookings-for-woocommerce-admin-advanced.js',
            array( 'jquery', 'jquery-ui-datepicker' ),
            $this->version,
            false
        );
        wp_enqueue_script(
            'moments',
            plugin_dir_url( __FILE__ ) . 'js/moment.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        wp_enqueue_script(
            'select-2',
            plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js',
            null,
            $this->version,
            false
        );
        $ajax_params = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        );
        wp_enqueue_script(
            'rbwc_ajax_handle',
            plugin_dir_url( __FILE__ ) . 'js/rbwc-ajax-handler.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        wp_localize_script( 'rbwc_ajax_handle', 'params', $ajax_params );
    }
    
    /**
     * Add a checkbox for bookable products that allows toggling of recurrability.
     *
     * @param $product_type_options
     *
     * @return mixed
     */
    public function add_recurrable_product_type( $product_type_options )
    {
        $product_type_options["recurrable"] = [
            "id"            => "_recurrable",
            "wrapper_class" => "show_if_booking",
            "label"         => "Recurrable",
            "description"   => "Enable this if this bookable product can recur.",
        ];
        return $product_type_options;
    }
    
    /**
     * Save the recurrable booking product type.
     *
     * @param $post_ID
     */
    public function save_post_product( $post_id )
    {
        update_post_meta( $post_id, "_recurrable", ( isset( $_POST["_recurrable"] ) ? 'yes' : "no" ) );
    }
    
    /**
     * Output any errors
     */
    public function show_errors()
    {
        foreach ( $this->errors as $error ) {
            
            if ( !empty($error->get_error_data()) ) {
                $a = $error->get_error_data();
                $b = new DateTime( '@' . $a );
                $formatted = $b->format( 'l jS \\of F Y' );
                echo  '<div class="error"><p>Proposed booking for ' . $formatted . ' gave the following error: ' . $error->get_error_message() . '</p></div>' ;
            } else {
                echo  '<div class="error"><p>' . $error->get_error_message() . '</p></div>' ;
            }
        
        }
    }
    
    /**
     * Adds a new top-level menu to the bottom of the WooCommerce Bookings menu.
     *
     * If looking for the admin hook, see main class!
     *
     */
    public function rbwc_create_submenu_page()
    {
        remove_submenu_page( 'edit.php?post_type=wc_booking', 'create_booking' );
        add_submenu_page(
            'edit.php?post_type=wc_booking',
            // Position submenu page under the Bookings top menu
            'Add Booking',
            // The title to be displayed on the corresponding page for this menu
            'Add Booking',
            // The text to be displayed for this actual menu item
            'administrator',
            // Which type of users can see this menu
            'create_recurring_booking',
            // The unique ID - that is, the slug - for this menu item
            array( $this, 'display_create_recurring_booking_page' ),
            // The name of the function to call when rendering the menu for this page
            1
        );
        add_submenu_page(
            'edit.php?post_type=wc_booking',
            // Position submenu page under the Bookings top menu
            'Recurring Bookings',
            // The title to be displayed on the corresponding page for this menu
            'Recurring Bookings',
            // The text to be displayed for this actual menu item
            'administrator',
            // Which type of users can see this menu
            'recurring_bookings',
            // The unique ID - that is, the slug - for this menu item
            array( $this, 'display_submenu_page' )
        );
    }
    
    /**
     * Change the target of add new booking URLs to point to our page.
     *
     * @param $url
     * @param $path
     *
     * @return mixed|string
     */
    public function change_add_new_link( $url, $path )
    {
        if ( $path === 'edit.php?post_type=wc_booking&page=create_booking' ) {
            $url = 'edit.php?post_type=wc_booking&page=create_recurring_booking';
        }
        return $url;
    }
    
    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function display_submenu_page()
    {
        ?>

        <!-- Create a header in the default WordPress 'wrap' container -->
        <div class="wrap">

			<?php 
        include_once 'partials/recurring-bookings-for-woocommerce-admin-display.php';
        ?>

        </div><!-- /.wrap -->

		<?php 
    }
    
    /**
     * Render the create recurring booking page.
     */
    public function display_create_recurring_booking_page()
    {
        require_once 'class-recurring-bookings-for-woocommerce-create.php';
        $page = new RBWC_Bookings_Create();
        $page->output();
    }
    
    /**
     * Respond to change in product dropdown
     *
     * @since  1.2
     */
    public function rbwc_resource_dropdown()
    {
        
        if ( isset( $_POST['data'] ) ) {
            $selected_option = $_POST['data'];
            $product = wc_get_product( $selected_option );
            $resources = $product->get_resource_ids();
            $dropdown_html_resources = '<h3>Select resources</h3>';
            $dropdown_html_resources .= '<p>The selected product has one or more resources linked to it. You must choose a resource.</p>';
            $dropdown_html_resources .= '<select id="rbwc_resources_select" name="rbwc[resource_select]">
			<option value="">' . __( 'Select a resource', $this->recurring_bookings_for_woocommerce ) . '</option>';
            foreach ( $resources as $resource ) {
                $resource_object = get_post( $resource );
                $resource_name = $resource_object->post_title;
                $dropdown_html_resources .= '<option value="' . $resource . '">' . $resource_name . '</option>' . "\n";
            }
            $dropdown_html_resources .= '</select>';
        }
        
        ob_clean();
        echo  $dropdown_html_resources ;
        wp_die();
    }
    
    /**
     * Respond to change in product dropdown
     *
     * @since  1.3
     */
    public function rbwc_persons_processor()
    {
        
        if ( isset( $_POST['data'] ) ) {
            $selected_option = $_POST['data'];
            $product = wc_get_product( $selected_option );
            $max_persons = ( $product->get_max_persons() ? $product->get_max_persons() : '' );
            echo  '<h3>Select persons</h3>' ;
            
            if ( $product->has_person_types() ) {
                $person_types = $product->get_person_types();
                echo  '<p>This product has persons attached to it. You may choose the number of each person type displayed below.</p>' ;
                foreach ( $person_types as $person_type ) {
                    $min_person_type_persons = $person_type->get_min();
                    $max_person_type_persons = $person_type->get_max();
                    $min = ( is_numeric( $min_person_type_persons ) ? $min_person_type_persons : 0 );
                    $max = ( !empty($max_person_type_persons) ? absint( $max_person_type_persons ) : $max_persons );
                    $name = 'rbwc[persons_' . $person_type->get_id() . ']';
                    $label = $person_type->get_name();
                    $after = $person_type->get_description();
                    ?>
                    <label>
                        <input type="number" step="1" name="<?php 
                    echo  $name ;
                    ?>" value="" min="<?php 
                    echo  $min ;
                    ?>"
                               max="<?php 
                    echo  $max ;
                    ?>">
						<?php 
                    echo  $label ;
                    ?>
                    </label>
                    <p class="description"><?php 
                    echo  $after ;
                    ?></p>
					<?php 
                }
            } else {
                echo  '<p>This product has persons attached to it. You may choose the number of persons below.</p>' ;
                $min = $product->get_min_persons();
                $max = $max_persons;
                $name = 'rbwc[persons]';
                $label = __( 'Persons', 'woocommerce-bookings' );
                ?>
                <label>
                    <input type="number" step="1" name="<?php 
                echo  $name ;
                ?>" value="" min="<?php 
                echo  $min ;
                ?>"
                           max="<?php 
                echo  $max ;
                ?>">
					<?php 
                echo  $label ;
                ?>
                </label>
				<?php 
            }
            
            $resources_infobox = '<p>Person stuff</p>';
        }
        
        wp_die();
    }
    
    /**
     * Respond to the recurring booking form
     *
     * @since  1.0.0
     */
    public function the_form_response()
    {
        $errors = array();
        
        if ( isset( $_POST['rbwc_recurring_booking_nonce'] ) && wp_verify_nonce( $_POST['rbwc_recurring_booking_nonce'], 'rbwc_recurring_booking_form_nonce' ) ) {
            // Sanitize and declare
            $rbwc_user = get_user_by( 'login', $_POST['rbwc']['user_select'] );
            $rbwc_user_id = absint( $rbwc_user->ID );
            $rbwc_product_id = absint( $_POST['rbwc']['product_select'] );
            $product = wc_get_product( $rbwc_product_id );
            $mode = $_POST['rbwc']['mode_select'];
            $factor = wc_clean( $_POST['rbwc']['factor'] );
            $multiplier = ( isset( $_POST['rbwc']['multiplier'] ) ? absint( $_POST['rbwc']['multiplier'] ) : 1 );
            $rbwc_resource_id = absint( $_POST['rbwc']['resource_select'] );
            $rbwc_booking_time_start = filter_var( $_POST['rbwc']['advanced_start'], FILTER_SANITIZE_STRING );
            $rbwc_booking_time_finish = filter_var( $_POST['rbwc']['advanced_finish'], FILTER_SANITIZE_STRING );
            $rbwc_dates = explode( ", ", $_POST['rbwc']['advanced_dates'] );
            $error_handling = ( isset( $_POST['rbwc']['error_handling'] ) ? $_POST['rbwc']['error_handling'] : 'stop' );
            $faulty = false;
            $bug = array();
            // Check that there is actually a date
            
            if ( !empty($_POST['rbwc']['advanced_dates']) ) {
                // Next, conduct a dry run to check availability
                
                if ( 'freestyle' === $mode ) {
                    foreach ( $rbwc_dates as $date ) {
                        $booking_date = strtotime( $date );
                        $rbwc_booking_start = strtotime( $rbwc_booking_time_start, $booking_date );
                        $rbwc_booking_finish = strtotime( $rbwc_booking_time_finish, $booking_date );
                        $booking_data = $this->get_data(
                            $product,
                            $rbwc_user,
                            $rbwc_resource_id,
                            $rbwc_booking_start,
                            $rbwc_booking_finish,
                            $_POST
                        );
                        $cost = $this->calculate_booking_cost(
                            $product,
                            $rbwc_user,
                            $rbwc_resource_id,
                            $rbwc_booking_start,
                            $rbwc_booking_finish,
                            $_POST
                        );
                        
                        if ( is_wp_error( $cost ) ) {
                            $faulty = true;
                            $cost->add_data( $booking_date );
                            $this->errors[] = $cost;
                        }
                    
                    }
                } elseif ( 'fixed' === $mode ) {
                    for ( $i = 0 ;  $i < $multiplier ;  $i++ ) {
                        switch ( $factor ) {
                            case "day":
                                $modifier = '+ ' . $i . ' day';
                                break;
                            case "week":
                                $modifier = '+ ' . $i . ' week';
                                break;
                            case "month":
                                $modifier = '+ ' . $i . ' month';
                                break;
                        }
                        $booking_date = strtotime( $rbwc_dates[0] );
                        $rbwc_booking_start = strtotime( $rbwc_booking_time_start, $booking_date );
                        $rbwc_booking_finish = strtotime( $rbwc_booking_time_finish, $booking_date );
                        $modified_start_date = strtotime( $modifier, $rbwc_booking_start );
                        $modified_end_date = strtotime( $modifier, $rbwc_booking_finish );
                        $booking_data = $this->get_data(
                            $product,
                            $rbwc_user,
                            $rbwc_resource_id,
                            $modified_start_date,
                            $modified_end_date,
                            $_POST
                        );
                        $cost = $this->calculate_booking_cost(
                            $product,
                            $rbwc_user,
                            $rbwc_resource_id,
                            $modified_start_date,
                            $modified_end_date,
                            $_POST
                        );
                        
                        if ( is_wp_error( $cost ) ) {
                            $faulty = true;
                            $cost->add_data( $modified_start_date );
                            $this->errors[] = $cost;
                        }
                    
                    }
                }
            
            } else {
                // if no date, throw error and set flow to faulty
                $faulty = true;
                $this->errors[] = new WP_Error( 'Empty', __( 'Sorry, you need to select at least one day.', $this->recurring_bookings_for_woocommerce ) );
            }
            
            
            if ( $faulty == true ) {
                echo  '<h4 style="color: red;">Your bookings were processed, but the following errors were generated</h4><p>Depending on your error handling setting, bookings that were successful can be found in the <a href="/wp-admin/edit.php?post_type=wc_booking&page=booking_calendar" target="_blank">Calendar</a> or the <a href="/wp-admin/edit.php?post_type=wc_booking" target="_blank">List View</a>.</p>' ;
                $this->show_errors();
            } else {
                echo  '<h4 style="color: green;">Your bookings were processed succesfully</h4><p>You can view your new bookings in the <a href="/wp-admin/edit.php?post_type=wc_booking&page=booking_calendar" target="_blank">Calendar</a> or the <a href="/wp-admin/edit.php?post_type=wc_booking" target="_blank">List View</a>.</p>' ;
            }
            
            // If dry run was sucessful (every block of every booking was available) OR user allowed the available bookings to go ahead regardless, process the bookings
            if ( $faulty == false || $error_handling === 'continue' ) {
                // Process bookings
                
                if ( 'freestyle' === $mode ) {
                    foreach ( $rbwc_dates as $date ) {
                        $booking_date = strtotime( $date );
                        $rbwc_booking_start = strtotime( $rbwc_booking_time_start, $booking_date );
                        $rbwc_booking_finish = strtotime( $rbwc_booking_time_finish, $booking_date );
                        $booking_data = $this->get_data(
                            $product,
                            $rbwc_user,
                            $rbwc_resource_id,
                            $rbwc_booking_start,
                            $rbwc_booking_finish,
                            $_POST
                        );
                        $cost = $this->calculate_booking_cost(
                            $product,
                            $rbwc_user,
                            $rbwc_resource_id,
                            $rbwc_booking_start,
                            $rbwc_booking_finish,
                            $_POST
                        );
                        $booking_cost = ( $cost && !is_wp_error( $cost ) ? number_format(
                            $cost,
                            2,
                            '.',
                            ''
                        ) : 0 );
                        
                        if ( wc_prices_include_tax() ) {
                            $base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class() );
                            $base_taxes = WC_Tax::calc_tax( $booking_cost, $base_tax_rates, true );
                            $booking_cost = $booking_cost - array_sum( $base_taxes );
                        }
                        
                        $props = array(
                            'customer_id'   => $rbwc_user_id,
                            'product_id'    => $rbwc_product_id,
                            'resource_id'   => $booking_data['_resource_id'],
                            'person_counts' => $booking_data['_persons'],
                            'cost'          => ( isset( $booking_cost ) ? $booking_cost : 0 ),
                            'start'         => $rbwc_booking_start,
                            'end'           => $rbwc_booking_finish,
                        );
                        
                        if ( !is_wp_error( $cost ) ) {
                            // Create the booking itself
                            $new_booking = new WC_Booking( $props );
                            $new_booking->set_order_id( $order_id );
                            $new_booking->set_order_item_id( $item_id );
                            $new_booking->set_status( 'confirmed' );
                            // As this is an admin booking, we should assume it is confirmed
                            $new_booking->save();
                        }
                    
                    }
                } elseif ( 'fixed' === $mode ) {
                    for ( $i = 0 ;  $i < $multiplier ;  $i++ ) {
                        switch ( $factor ) {
                            case "day":
                                $modifier = '+ ' . $i . ' day';
                                break;
                            case "week":
                                $modifier = '+ ' . $i . ' week';
                                break;
                            case "month":
                                $modifier = '+ ' . $i . ' month';
                                break;
                        }
                        $booking_date = strtotime( $rbwc_dates[0] );
                        $rbwc_booking_start = strtotime( $rbwc_booking_time_start, $booking_date );
                        $rbwc_booking_finish = strtotime( $rbwc_booking_time_finish, $booking_date );
                        $modified_start_date = strtotime( $modifier, $rbwc_booking_start );
                        $modified_end_date = strtotime( $modifier, $rbwc_booking_finish );
                        $booking_data = $this->get_data(
                            $product,
                            $rbwc_user,
                            $rbwc_resource_id,
                            $modified_start_date,
                            $modified_end_date,
                            $_POST
                        );
                        $cost = $this->calculate_booking_cost(
                            $product,
                            $rbwc_user,
                            $rbwc_resource_id,
                            $modified_start_date,
                            $modified_end_date,
                            $_POST
                        );
                        $booking_cost = ( $cost && !is_wp_error( $cost ) ? number_format(
                            $cost,
                            2,
                            '.',
                            ''
                        ) : 0 );
                        
                        if ( wc_prices_include_tax() ) {
                            $base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class() );
                            $base_taxes = WC_Tax::calc_tax( $booking_cost, $base_tax_rates, true );
                            $booking_cost = $booking_cost - array_sum( $base_taxes );
                        }
                        
                        $props = array(
                            'customer_id'   => $rbwc_user_id,
                            'product_id'    => $rbwc_product_id,
                            'resource_id'   => $booking_data['_resource_id'],
                            'person_counts' => $booking_data['_persons'],
                            'cost'          => ( isset( $booking_cost ) ? $booking_cost : 0 ),
                            'start'         => $modified_start_date,
                            'end'           => $modified_end_date,
                        );
                        
                        if ( !is_wp_error( $cost ) ) {
                            // Create the booking itself
                            $new_booking = new WC_Booking( $props );
                            $new_booking->set_order_id( $order_id );
                            $new_booking->set_order_item_id( $item_id );
                            $new_booking->set_status( 'confirmed' );
                            // As this is an admin booking, we should assume it is confirmed
                            $new_booking->save();
                        }
                    
                    }
                }
            
            }
            // Server response if AJAX
            if ( isset( $_POST['ajaxrequest'] ) && $_POST['ajaxrequest'] === 'true' ) {
                // server response debug info
                // echo '<pre>';
                // print_r( $_POST );
                // print_r( $booking_data );
                // print_r( $data );
                // echo '</pre>';
                wp_die();
            }
            // Server response if AJAX not used
            // add the admin notice
            $admin_notice = "success";
            // redirect the user to the appropriate page
            $this->custom_redirect( $admin_notice, $_POST );
            exit;
        } else {
            wp_die( __( 'Invalid nonce specified', $this->recurring_bookings_for_woocommerce ), __( 'Error', $this->recurring_bookings_for_woocommerce ), array(
                'response'  => 403,
                'back_link' => 'edit.php?post_type=wc_booking&page=recurring_bookings',
            ) );
        }
    
    }
    
    /**
     * Get posted form data into a neat array
     * @return array
     */
    public function get_data(
        $product,
        $rbwc_user,
        $rbwc_resource_id,
        $rbwc_booking_start,
        $rbwc_booking_finish,
        $posted
    )
    {
        if ( empty($posted) ) {
            $posted = $_POST;
        }
        $this->product = $product;
        $data = array(
            '_year'    => '',
            '_month'   => '',
            '_day'     => '',
            '_persons' => array(),
        );
        $data['product'] = $product;
        // Get start date and time
        
        if ( !empty($rbwc_booking_start) ) {
            $date_time = new DateTime( '@' . $rbwc_booking_start );
            // Contains ISO 8061 formatted datetime
            $data['_year'] = $date_time->format( 'Y' );
            $data['_month'] = $date_time->format( 'm' );
            $data['_day'] = $date_time->format( 'd' );
            $data['_date'] = $data['_year'] . '-' . $data['_month'] . '-' . $data['_day'];
            $data['date'] = date_i18n( wc_date_format(), strtotime( $data['_date'] ) );
            $data['_time'] = $date_time->format( 'G:i' );
            $data['time'] = date_i18n( get_option( 'time_format' ), strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']} {$data['_time']}" ) );
        } else {
            $data['_time'] = '';
        }
        
        // Get finish date and time (date will always be the same in this version)
        
        if ( !empty($rbwc_booking_finish) ) {
            $date_time_end = new DateTime( '@' . $rbwc_booking_finish );
            // Contains ISO 8061 formatted datetime
        }
        
        // Quantity being booked
        $data['_qty'] = 1;
        // Work out persons
        
        if ( $this->product->has_persons() ) {
            
            if ( $this->product->has_person_types() ) {
                $person_types = $this->product->get_person_types();
                foreach ( $person_types as $person_type ) {
                    
                    if ( isset( $posted['rbwc']['persons_' . $person_type->ID] ) && absint( $posted['rbwc']['persons_' . $person_type->ID] ) > 0 ) {
                        $data[$person_type->post_title] = absint( $posted['rbwc']['persons_' . $person_type->ID] );
                        $data['_persons'][$person_type->ID] = $data[$person_type->post_title];
                    }
                
                }
            } elseif ( isset( $posted['rbwc']['persons'] ) ) {
                $data[__( 'Persons', 'woocommerce-bookings' )] = absint( $posted['rbwc']['persons'] );
                $data['_persons'][0] = absint( $posted['rbwc']['persons'] );
            }
            
            if ( $this->product->get_has_person_qty_multiplier() ) {
                $data['_qty'] = array_sum( $data['_persons'] );
            }
        }
        
        // Duration
        
        if ( 'customer' == $this->product->get_duration_type() ) {
            $booking_duration_raw = $date_time->diff( $date_time_end );
            $booking_duration_unit = $this->product->get_duration_unit();
            
            if ( $booking_duration_unit == 'hour' ) {
                $total_time = $booking_duration_raw->format( '%h' );
                // $total_duration = $booking_duration;
            } elseif ( $booking_duration_unit == 'minute' ) {
                $total_time = $booking_duration_raw->h * 60 + $booking_duration_raw->i;
                // $booking_duration = $total_duration;
                // $total_duration = $total_time / $this->product->get_duration();
            }
            
            $booking_duration = $total_time / $this->product->get_duration();
            $data['_duration_unit'] = $booking_duration_unit;
            $data['_duration'] = $booking_duration;
            $data['total_time'] = $total_time;
            // Get the duration * block duration
            $total_duration = $booking_duration * $this->product->get_duration();
            $data['total_duration'] = $total_duration;
            // Nice formatted version
            switch ( $booking_duration_unit ) {
                case 'hour':
                    $data['duration'] = $total_duration . ' ' . _n(
                        'hour',
                        'hours',
                        $total_duration,
                        'woocommerce-bookings'
                    );
                    break;
                case 'minute':
                    $data['duration'] = $total_duration . ' ' . _n(
                        'minute',
                        'minutes',
                        $total_duration,
                        'woocommerce-bookings'
                    );
                    break;
                default:
                    $data['duration'] = $total_duration;
                    break;
            }
        } else {
            // Fixed duration
            $booking_duration = $this->product->get_duration();
            $booking_duration_unit = $this->product->get_duration_unit();
            $total_duration = $booking_duration;
        }
        
        // Work out start and end dates/times
        
        if ( !empty($data['_time']) ) {
            $data['_start_date'] = strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']} {$data['_time']}" );
            $data['_end_date'] = strtotime( "+{$total_duration} {$booking_duration_unit}", $data['_start_date'] );
            $data['_all_day'] = 0;
        } else {
            $data['_start_date'] = strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']}" );
            // We need the following calculation to not add extra days (see #2147)
            $data['_end_date'] = strtotime( "+{$total_duration} {$booking_duration_unit} - 1 second", $data['_start_date'] );
            $data['_all_day'] = 1;
        }
        
        // Get posted resource or assign one for the date range
        if ( $this->product->has_resources() ) {
            
            if ( $this->product->is_resource_assignment_type( 'customer' ) ) {
                $resource = $this->product->get_resource( absint( $rbwc_resource_id ) );
                
                if ( !empty($rbwc_resource_id) && $resource ) {
                    $data['_resource_id'] = $resource->ID;
                    $data['type'] = $resource->post_title;
                } else {
                    $data['_resource_id'] = 0;
                }
            
            } else {
                // Assign an available resource automatically
                $available_bookings = wc_bookings_get_total_available_bookings_for_range(
                    $this->product,
                    $data['_start_date'],
                    $data['_end_date'],
                    0,
                    $data['_qty']
                );
                
                if ( is_array( $available_bookings ) ) {
                    $data['_resource_id'] = current( array_keys( $available_bookings ) );
                    $data['type'] = get_the_title( current( array_keys( $available_bookings ) ) );
                }
            
            }
        
        }
        return apply_filters(
            'woocommerce_booking_form_get_posted_data',
            $data,
            $this->product,
            $total_duration
        );
    }
    
    /**
     * Checks booking data is correctly set, and that the chosen blocks are indeed available.
     *
     * @param array $data
     *
     * @return bool|WP_Error on failure, true on success
     */
    public function is_bookable( $data )
    {
        $this->product = $data['product'];
        // Validate resources are set
        
        if ( $this->product->has_resources() && $this->product->is_resource_assignment_type( 'customer' ) ) {
            if ( empty($data['_resource_id']) ) {
                return new WP_Error( 'Error', __( 'Please choose a resource type', 'woocommerce-bookings' ) );
            }
        } elseif ( $this->product->has_resources() && $this->product->is_resource_assignment_type( 'automatic' ) ) {
            $data['_resource_id'] = 0;
        } else {
            $data['_resource_id'] = '';
        }
        
        // Validate customer set durations
        
        if ( $this->product->is_duration_type( 'customer' ) ) {
            if ( empty($data['_duration']) ) {
                return new WP_Error( 'Error', __( 'Invalid start or finish time - please double check and retry', 'woocommerce-bookings' ) );
            }
            if ( $data['_duration'] > $this->product->get_max_duration() ) {
                /* translators: 1: maximum duration */
                return new WP_Error( 'Error', sprintf( __( 'This product has a maximum duration - you have tried to book for more than that amount', 'woocommerce-bookings' ), $this->product->get_max_duration() ) );
            }
            if ( $data['_duration'] < $this->product->get_min_duration() ) {
                /* translators: 1: minimum duration */
                return new WP_Error( 'Error', sprintf( __( 'This product has a minimum duration - you have tried to book for less than that amount', 'woocommerce-bookings' ), $this->product->get_min_duration() ) );
            }
        }
        
        // Validate date and time
        if ( empty($data['date']) ) {
            return new WP_Error( 'Error', __( 'Date is required - please choose one above', 'woocommerce-bookings' ) );
        }
        if ( in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) && empty($data['time']) ) {
            return new WP_Error( 'Error', __( 'Time is required - please choose one above', 'woocommerce-bookings' ) );
        }
        if ( $data['_date'] && date( 'Ymd', strtotime( $data['_date'] ) ) < date( 'Ymd', current_time( 'timestamp' ) ) ) {
            return new WP_Error( 'Error', __( 'You must choose a future date and time.', 'woocommerce-bookings' ) );
        }
        if ( $data['_date'] && !empty($data['_time']) && date( 'YmdHi', strtotime( $data['_date'] . ' ' . $data['_time'] ) ) < date( 'YmdHi', current_time( 'timestamp' ) ) ) {
            return new WP_Error( 'Error', __( 'You must choose a future date and time.', 'woocommerce-bookings' ) );
        }
        // Validate min date and max date
        
        if ( in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
            $now = current_time( 'timestamp' );
        } elseif ( 'month' === $this->product->get_duration_unit() ) {
            $now = strtotime( 'midnight first day of this month', current_time( 'timestamp' ) );
        } else {
            $now = strtotime( 'midnight', current_time( 'timestamp' ) );
        }
        
        $min = $this->product->get_min_date();
        
        if ( $min ) {
            $min_date = wc_bookings_get_min_timestamp_for_day( strtotime( $data['_date'] ), $min['value'], $min['unit'] );
            if ( strtotime( $data['_date'] . ' ' . $data['_time'] ) < $min_date ) {
                /* translators: 1: minimum date */
                return new WP_Error( 'Error', sprintf( __( 'The earliest booking possible is currently %s.', 'woocommerce-bookings' ), date_i18n( wc_date_format() . ' ' . get_option( 'time_format' ), $min_date ) ) );
            }
        }
        
        $max = $this->product->get_max_date();
        
        if ( $max ) {
            $max_date = strtotime( "+{$max['value']} {$max['unit']}", $now );
            if ( strtotime( $data['_date'] . ' ' . $data['_time'] ) > $max_date ) {
                /* translators: 1: maximum date */
                return new WP_Error( 'Error', sprintf( __( 'The latest booking possible is currently %s.', 'woocommerce-bookings' ), date_i18n( wc_date_format() . ' ' . get_option( 'time_format' ), $max_date ) ) );
            }
        }
        
        // Check that the day of the week is not restricted.
        
        if ( $this->product->has_restricted_days() ) {
            $restricted_days = $this->product->get_restricted_days();
            if ( !in_array( date( 'w', $data['_start_date'] ), $restricted_days ) ) {
                return new WP_Error( 'Error', __( 'Sorry, bookings cannot start on this day.', 'woocommerce-bookings' ) );
            }
        }
        
        // Validate persons
        
        if ( $this->product->has_persons() ) {
            $persons = array_sum( $data['_persons'] );
            if ( $this->product->get_max_persons() && $persons > $this->product->get_max_persons() ) {
                /* translators: 1: maximum persons */
                return new WP_Error( 'Error', sprintf( __( 'The maximum persons per group is %d', 'woocommerce-bookings' ), $this->product->get_max_persons() ) );
            }
            if ( $persons < $this->product->get_min_persons() ) {
                /* translators: 1: minimum persons */
                return new WP_Error( 'Error', sprintf( __( 'The minimum persons per group is %d', 'woocommerce-bookings' ), $this->product->get_min_persons() ) );
            }
            
            if ( $this->product->has_person_types() ) {
                $person_types = $this->product->get_person_types();
                foreach ( $person_types as $person ) {
                    $person_max = $person->get_max();
                    if ( is_numeric( $person_max ) && isset( $data['_persons'][$person->get_id()] ) && $data['_persons'][$person->get_id()] > $person_max ) {
                        /* translators: 1: person name 2: maximum persons */
                        return new WP_Error( 'Error', sprintf( __( 'The maximum %1$s per group is %2$d', 'woocommerce-bookings' ), $person->post_title, $person_max ) );
                    }
                    $person_min = $person->get_min();
                    if ( is_numeric( $person_min ) && isset( $data['_persons'][$person->get_id()] ) && $data['_persons'][$person->get_id()] < $person_min ) {
                        /* translators: 1: person name 2: minimum persons */
                        return new WP_Error( 'Error', sprintf( __( 'The minimum %1$s per group is %2$d', 'woocommerce-bookings' ), $person->post_title, $person_min ) );
                    }
                }
            }
        
        }
        
        $base_interval = ( 'hour' === $this->product->get_duration_unit() ? $this->product->get_duration() * 60 : $this->product->get_duration() );
        $interval = $base_interval;
        if ( !empty($data['_duration']) ) {
            $interval = $base_interval * absint( $data['_duration'] );
        }
        $intervals = array( $interval, $base_interval );
        // Get availability for the dates
        $available_bookings = wc_bookings_get_total_available_bookings_for_range(
            $this->product,
            $data['_start_date'],
            $data['_end_date'],
            $data['_resource_id'],
            $data['_qty'],
            $intervals
        );
        if ( is_array( $available_bookings ) ) {
            $this->auto_assigned_resource_id = current( array_keys( $available_bookings ) );
        }
        
        if ( is_wp_error( $available_bookings ) ) {
            return $available_bookings;
        } elseif ( !$available_bookings ) {
            return new WP_Error( 'Error', __( 'Sorry, the selected block is not available', 'woocommerce-bookings' ) );
        }
        
        return true;
    }
    
    /**
     * Calculate costs from posted values
     *
     * @param array $posted
     *
     * @return string cost
     */
    public function calculate_booking_cost(
        $product,
        $rbwc_user,
        $rbwc_resource_id,
        $rbwc_booking_start,
        $rbwc_booking_finish,
        $posted
    )
    {
        if ( !empty($this->booking_cost) ) {
            return $this->booking_cost;
        }
        $this->product = $product;
        // Get costs
        $costs = $this->product->get_costs();
        // Get posted data
        $data = $this->get_data(
            $product,
            $rbwc_user,
            $rbwc_resource_id,
            $rbwc_booking_start,
            $rbwc_booking_finish,
            $posted
        );
        $validate = $this->is_bookable( $data );
        if ( is_wp_error( $validate ) ) {
            return $validate;
        }
        $bug = array();
        $base_cost = max( 0, $this->product->get_cost() );
        $base_block_cost = max( 0, $this->product->get_block_cost() );
        $total_block_cost = 0;
        $person_block_costs = 0;
        // See if we have an auto_assigned_resource_id
        if ( isset( $this->auto_assigned_resource_id ) ) {
            $data['_resource_id'] = $this->auto_assigned_resource_id;
        }
        // Get resource cost
        
        if ( isset( $data['_resource_id'] ) ) {
            $resource = $this->product->get_resource( $data['_resource_id'] );
            $base_block_cost += $resource->get_block_cost();
            $base_cost += $resource->get_base_cost();
        }
        
        // Potentially increase costs if dealing with persons
        if ( !empty($data['_persons']) ) {
            if ( $this->product->has_person_types() ) {
                foreach ( $data['_persons'] as $person_id => $person_count ) {
                    $person_type = new WC_Product_Booking_Person_Type( $person_id );
                    $person_cost = $person_type->get_cost();
                    $person_block_cost = $person_type->get_block_cost();
                    // Only a single cost - multiplication comes later if wc_booking_person_cost_multiplier is enabled.
                    
                    if ( $person_count > 0 ) {
                        if ( $person_cost > 0 ) {
                            $base_cost += $person_cost * $person_count;
                        }
                        if ( $person_block_cost > 0 ) {
                            $person_block_costs += $person_block_cost * $person_count;
                        }
                    }
                
                }
            }
        }
        $this->applied_cost_rules = array();
        $block_duration = $this->product->get_duration();
        $block_unit = $this->product->get_duration_unit();
        $blocks_booked = ( isset( $data['_duration'] ) ? absint( $data['_duration'] ) : $block_duration );
        $block_timestamp = $data['_start_date'];
        $bug['blocks_booked'] = $blocks_booked;
        $bug['block_duration'] = $block_duration;
        if ( $this->product->is_duration_type( 'fixed' ) ) {
            $blocks_booked = ceil( $blocks_booked / $block_duration );
        }
        $buffer_period = $this->product->get_buffer_period();
        if ( !empty($buffer_period) ) {
            // handle day buffers
            
            if ( !in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
                $buffer_days = WC_Bookings_Controller::find_buffer_day_blocks( $this->product );
                $contains_buffer_days = false;
                // Evaluate costs for each booked block
                for ( $block = 0 ;  $block < $blocks_booked ;  $block++ ) {
                    $block_start_time_offset = $block * $block_duration;
                    $block_end_time_offset = ($block + 1) * $block_duration - 1;
                    $block_start_time = date( 'Y-n-j', strtotime( "+{$block_start_time_offset} {$block_unit}", $block_timestamp ) );
                    $block_end_time = date( 'Y-n-j', strtotime( "+{$block_end_time_offset} {$block_unit}", $block_timestamp ) );
                    if ( in_array( $block_end_time, $buffer_days ) ) {
                        $contains_buffer_days = true;
                    }
                    if ( in_array( $block_start_time, $buffer_days ) ) {
                        $contains_buffer_days = true;
                    }
                }
                
                if ( $contains_buffer_days ) {
                    $block_duration_string = $block_duration;
                    if ( 'week' === $block_unit ) {
                        $block_duration_string = $block_duration * 7;
                    }
                    /* translators: 1: block duration days */
                    return new WP_Error( 'Error', sprintf( __( 'The duration of this booking must be at least %s days.', 'woocommerce-bookings' ), $block_duration_string ) );
                }
            
            }
        
        }
        $override_blocks = array();
        // Evaluate costs for each booked block
        for ( $block = 0 ;  $block < $blocks_booked ;  $block++ ) {
            $block_cost = $base_block_cost + $person_block_costs;
            $block_start_time_offset = $block * $block_duration;
            $block_end_time_offset = ($block + 1) * $block_duration;
            $block_start_time = $this->get_formatted_times( strtotime( "+{$block_start_time_offset} {$block_unit}", $block_timestamp ) );
            $block_end_time = $this->get_formatted_times( strtotime( "+{$block_end_time_offset} {$block_unit}", $block_timestamp ) );
            
            if ( in_array( $this->product->get_duration_unit(), array( 'night' ) ) ) {
                $block_start_time = $this->get_formatted_times( strtotime( "+{$block_start_time_offset} day", $block_timestamp ) );
                $block_end_time = $this->get_formatted_times( strtotime( "+{$block_end_time_offset} day", $block_timestamp ) );
            }
            
            foreach ( $costs as $rule_key => $rule ) {
                $type = $rule[0];
                $rules = $rule[1];
                
                if ( strrpos( $type, 'time' ) === 0 ) {
                    if ( !in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
                        continue;
                    }
                    
                    if ( 'time:range' === $type ) {
                        $year = date( 'Y', $block_start_time['timestamp'] );
                        $month = date( 'n', $block_start_time['timestamp'] );
                        $day = date( 'j', $block_start_time['timestamp'] );
                        if ( !isset( $rules[$year][$month][$day] ) ) {
                            continue;
                        }
                        $rule_val = $rules[$year][$month][$day]['rule'];
                        $from = $rules[$year][$month][$day]['from'];
                        $to = $rules[$year][$month][$day]['to'];
                    } else {
                        if ( !empty($rules['day']) ) {
                            if ( $rules['day'] != $block_start_time['day_of_week'] ) {
                                continue;
                            }
                        }
                        $rule_val = $rules['rule'];
                        $from = $rules['from'];
                        $to = $rules['to'];
                    }
                    
                    $rule_start_time_hi = date( 'YmdHi', strtotime( str_replace( ':', '', $from ), $block_start_time['timestamp'] ) );
                    $rule_end_time_hi = date( 'YmdHi', strtotime( str_replace( ':', '', $to ), $block_start_time['timestamp'] ) );
                    $matched = false;
                    // Reverse time rule - The end time is tomorrow e.g. 16:00 today - 12:00 tomorrow
                    
                    if ( $rule_end_time_hi <= $rule_start_time_hi ) {
                        if ( $block_end_time['time'] > $rule_start_time_hi ) {
                            $matched = true;
                        }
                        if ( $block_start_time['time'] >= $rule_start_time_hi && $block_end_time['time'] >= $rule_end_time_hi ) {
                            $matched = true;
                        }
                        if ( $block_start_time['time'] <= $rule_start_time_hi && $block_end_time['time'] <= $rule_end_time_hi ) {
                            $matched = true;
                        }
                    } else {
                        // Else Normal rule.
                        if ( $block_start_time['time'] >= $rule_start_time_hi && $block_end_time['time'] <= $rule_end_time_hi ) {
                            $matched = true;
                        }
                    }
                    
                    
                    if ( $matched ) {
                        $block_cost = $this->apply_cost( $block_cost, $rule_val['block'][0], $rule_val['block'][1] );
                        $base_cost = $this->apply_base_cost(
                            $base_cost,
                            $rule_val['base'][0],
                            $rule_val['base'][1],
                            $rule_key
                        );
                    }
                
                } else {
                    switch ( $type ) {
                        case 'months':
                        case 'weeks':
                        case 'days':
                            $check_date = $block_start_time['timestamp'];
                            while ( $check_date < $block_end_time['timestamp'] ) {
                                $checking_date = $this->get_formatted_times( $check_date );
                                $date_key = ( 'days' == $type ? 'day_of_week' : substr( $type, 0, -1 ) );
                                // cater to months beyond this year
                                
                                if ( 'month' === $date_key && intval( $checking_date['year'] ) > intval( date( 'Y' ) ) ) {
                                    $month_beyond_this_year = intval( $checking_date['month'] ) + 12;
                                    $checking_date['month'] = (string) ($month_beyond_this_year % 12);
                                    if ( '0' === $checking_date['month'] ) {
                                        $checking_date['month'] = '12';
                                    }
                                }
                                
                                
                                if ( isset( $rules[$checking_date[$date_key]] ) ) {
                                    $rule = $rules[$checking_date[$date_key]];
                                    $block_cost = $this->apply_cost( $block_cost, $rule['block'][0], $rule['block'][1] );
                                    $base_cost = $this->apply_base_cost(
                                        $base_cost,
                                        $rule['base'][0],
                                        $rule['base'][1],
                                        $rule_key
                                    );
                                    if ( $rule['override'] && empty($override_blocks[$check_date]) ) {
                                        $override_blocks[$check_date] = $rule['override'];
                                    }
                                }
                                
                                $check_date = strtotime( "+1 {$type}", $check_date );
                            }
                            break;
                        case 'custom':
                            $check_date = $block_start_time['timestamp'];
                            while ( $check_date < $block_end_time['timestamp'] ) {
                                $checking_date = $this->get_formatted_times( $check_date );
                                
                                if ( isset( $rules[$checking_date['year']][$checking_date['month']][$checking_date['day']] ) ) {
                                    $rule = $rules[$checking_date['year']][$checking_date['month']][$checking_date['day']];
                                    $block_cost = $this->apply_cost( $block_cost, $rule['block'][0], $rule['block'][1] );
                                    $base_cost = $this->apply_base_cost(
                                        $base_cost,
                                        $rule['base'][0],
                                        $rule['base'][1],
                                        $rule_key
                                    );
                                    if ( $rule['override'] && empty($override_blocks[$check_date]) ) {
                                        $override_blocks[$check_date] = $rule['override'];
                                    }
                                    /*
                                     * Why do we break?
                                     * See: Applying a cost rule to a booking block
                                     * from the DEVELOPER.md
                                     */
                                    break;
                                }
                                
                                $check_date = strtotime( '+1 day', $check_date );
                            }
                            break;
                        case 'persons':
                            if ( !empty($data['_persons']) ) {
                                
                                if ( $rules['from'] <= array_sum( $data['_persons'] ) && $rules['to'] >= array_sum( $data['_persons'] ) ) {
                                    $block_cost = $this->apply_cost( $block_cost, $rules['rule']['block'][0], $rules['rule']['block'][1] );
                                    $base_cost = $this->apply_base_cost(
                                        $base_cost,
                                        $rules['rule']['base'][0],
                                        $rules['rule']['base'][1],
                                        $rule_key
                                    );
                                }
                            
                            }
                            break;
                        case 'blocks':
                            if ( !empty($data['_duration']) ) {
                                
                                if ( $rules['from'] <= $data['_duration'] && $rules['to'] >= $data['_duration'] ) {
                                    $block_cost = $this->apply_cost( $block_cost, $rules['rule']['block'][0], $rules['rule']['block'][1] );
                                    $base_cost = $this->apply_base_cost(
                                        $base_cost,
                                        $rules['rule']['base'][0],
                                        $rules['rule']['base'][1],
                                        $rule_key
                                    );
                                }
                            
                            }
                            break;
                    }
                }
            
            }
            $total_block_cost += $block_cost;
        }
        foreach ( $override_blocks as $over_cost ) {
            $total_block_cost = $total_block_cost - $base_block_cost;
            $total_block_cost += $over_cost;
        }
        // Person multiplier multiplies all costs
        $booking_cost = max( 0, $total_block_cost + $base_cost );
        if ( !empty($data['_persons']) ) {
            if ( $this->product->get_has_person_cost_multiplier() ) {
                $booking_cost = $booking_cost * array_sum( $data['_persons'] );
            }
        }
        $bug['total_block_cost'] = $total_block_cost;
        $bug['base_block_cost'] = $base_block_cost;
        $bug['booking_cost'] = $booking_cost;
        // return apply_filters( 'booking_form_calculated_booking_cost', $this->booking_cost, $this );
        return $booking_cost;
    }
    
    /**
     * Get an array of formatted time values
     *
     * @param string $timestamp
     *
     * @return array
     */
    public function get_formatted_times( $timestamp )
    {
        return array(
            'timestamp'   => $timestamp,
            'year'        => intval( date( 'Y', $timestamp ) ),
            'month'       => intval( date( 'n', $timestamp ) ),
            'day'         => intval( date( 'j', $timestamp ) ),
            'week'        => intval( date( 'W', $timestamp ) ),
            'day_of_week' => intval( date( 'N', $timestamp ) ),
            'time'        => date( 'YmdHi', $timestamp ),
        );
    }
    
    /**
     * Apply a cost
     *
     * @param float $base
     * @param string $multiplier
     * @param float $cost
     *
     * @return float
     */
    private function apply_cost( $base, $multiplier, $cost )
    {
        $base = floatval( $base );
        $cost = floatval( $cost );
        switch ( $multiplier ) {
            case 'times':
                $new_cost = $base * $cost;
                break;
            case 'divide':
                $new_cost = $base / $cost;
                break;
            case 'minus':
                $new_cost = $base - $cost;
                break;
            case 'equals':
                $new_cost = $cost;
                break;
            default:
                $new_cost = $base + $cost;
                break;
        }
        return $new_cost;
    }
    
    /**
     * Apply a cost
     *
     * @param float $base
     * @param string $multiplier
     * @param float $cost
     * @param string $rule_key Cost to apply the rule to - used for * and /
     *
     * @return float
     */
    private function apply_base_cost(
        $base,
        $multiplier,
        $cost,
        $rule_key = ''
    )
    {
        if ( in_array( $rule_key, $this->applied_cost_rules ) ) {
            return $base;
        }
        $this->applied_cost_rules[] = $rule_key;
        return $this->apply_cost( $base, $multiplier, $cost );
    }
    
    /**
     * Redirect
     *
     * @since    1.0.0
     */
    public function custom_redirect( $admin_notice, $response )
    {
        wp_redirect( esc_url_raw( add_query_arg( array(
            'rbwc_admin_add_notice' => $admin_notice,
            'rbwc_response'         => $response,
        ), admin_url( 'edit.php?post_type=wc_booking&page=recurring_bookings' ) ) ) );
    }
    
    /**
     * Print Admin Notices
     *
     * @since    1.0.0
     */
    public function print_plugin_admin_notices()
    {
        
        if ( isset( $_REQUEST['rbwc_admin_add_notice'] ) ) {
            
            if ( $_REQUEST['rbwc_admin_add_notice'] === "success" ) {
                $html = '<div class="notice notice-success is-dismissible"><p><strong>Your bookings have been created</strong></p><br>';
                $html .= '<p>If you cannot see your new bookings, the most likely cause is incompatibility with the date and time constraints set within the product. Please head to the <a href="https://www.bouncingsprout.com/plugins/recurring-bookings-for-woocommerce/" target="_blank">plugin support page</a> for further guidance.</p>';
                // Uncomment this line to display the POST variables
                $html .= '<pre>' . htmlspecialchars( print_r( $_REQUEST['rbwc_response'], true ) ) . '</pre>';
                $html .= '</div>';
                echo  $html ;
            }
            
            // handle other types of form notices
        } else {
            return;
        }
    
    }
    
    /**
     * Add a tab to a WooCommerce bookable product.
     *
     * @param $tabs
     *
     * @return mixed
     */
    public function register_tab( $tabs )
    {
        $tabs['rbwc_bookings'] = array(
            'label'  => __( 'Recurring', 'recurring-bookings-for-woocommerce' ),
            'target' => 'rbwc_product_data',
            'class'  => array( 'show_if_booking' ),
        );
        return $tabs;
    }
    
    /**
     * Show the booking panels views.
     */
    public function booking_panels()
    {
        global  $post, $bookable_product ;
        if ( empty($bookable_product) || $bookable_product->get_id() !== $post->ID ) {
            $bookable_product = get_wc_product_booking( $post->ID );
        }
        $restricted_meta = $bookable_product->get_restricted_days();
        for ( $i = 0 ;  $i < 7 ;  $i++ ) {
            
            if ( $restricted_meta && in_array( $i, $restricted_meta ) ) {
                $restricted_days[$i] = $i;
            } else {
                $restricted_days[$i] = false;
            }
        
        }
        wp_enqueue_script( 'wc_bookings_admin_js' );
        include 'partials/html-recurring-booking-panel.php';
    }
    
    /**
     * Save any new product data.
     *
     * @param $id
     * @param $post
     */
    public function save_product_meta( $id, $post )
    {
        // update_post_meta( $id, '_wc_booking_rbwc_recur_always', $_POST['_wc_booking_rbwc_recur_always'] );
        update_post_meta( $id, '_wc_booking_rbwc_recur_length_fixed', $_POST['_wc_booking_rbwc_recur_length_fixed'] );
        update_post_meta( $id, '_wc_booking_rbwc_recur_length', $_POST['_wc_booking_rbwc_recur_length'] );
        update_post_meta( $id, '_wc_booking_rbwc_recur_period_fixed', $_POST['_wc_booking_rbwc_recur_period_fixed'] );
        update_post_meta( $id, '_wc_booking_rbwc_recur_period', $_POST['_wc_booking_rbwc_recur_period'] );
    }
    
    /**
     * Render our custom header - allows admin notices to render underneath it.
     */
    public function add_custom_header()
    {
        $screen = get_current_screen();
        if ( !in_array( $screen->id, [
            'wc_booking_page_create_recurring_booking',
            'wc_booking_page_recurring_bookings',
            'wc_booking_page_recurring_bookings-account',
            'wc_booking_page_recurring_bookings-contact',
            'wc_booking_page_recurring_bookings-pricing'
        ] ) ) {
            return;
        }
        echo  '<style>
    .woocommerce-layout__header {
        display: none;
    }
    .woocommerce-layout__activity-panel-tabs {
        display: none;
    }
    .woocommerce-layout__header-breadcrumbs {
        display: none;
    }
    .woocommerce-embed-page .woocommerce-layout__primary{
        display: none;
    }
    .woocommerce-embed-page #screen-meta, .woocommerce-embed-page #screen-meta-links{top:0;}
    </style>' ;
        $string = 'https://wordpress.org/support/plugin/recurring-bookings-for-woocommerce/';
        $suffix = '';
        ?>
        <div class="rbwc-admin-header">
            <div class="rbwc-title">
                <a href="<?php 
        echo  esc_url( 'https://www.recurringbookings.com' ) ;
        ?>"><img
                            src="<?php 
        echo  plugin_dir_url( __FILE__ ) . '/assets/logo.png' ;
        ?>" alt="">
                    <h1><?php 
        _e( 'Recurring Bookings for WooCommerce', 'recurring-bookings-for-woocommerce' );
        echo  $suffix ;
        ?></h1>
                </a>
            </div>
            <div class="rbwc-meta">
                <span class="rbwc-version"><?php 
        echo  'v' . RBWC_VERSION ;
        ?></span>
                <a target="_blank" class="button"
                   href="<?php 
        echo  esc_url( 'https://www.recurringbookings.com/support/' ) ;
        ?>"><?php 
        _e( 'Documentation', 'recurring-bookings-for-woocommerce' );
        ?></a>
                <a target="_blank" class="button button-primary rbwc-submit"
                   href="<?php 
        echo  $string ;
        ?>"><?php 
        _e( 'Get Support', 'recurring-bookings-for-woocommerce' );
        ?></a>
            </div>
            <h2 class="rbwc-notices-trigger"></h2>
        </div>
		<?php 
    }
    
    /**
     * Change the admin footer text on WooCommerce admin pages.
     *
     * @param string $footer_text text to be rendered in the footer.
     *
     * @return string
     */
    public function admin_footer_text( $footer_text )
    {
        if ( !current_user_can( 'manage_woocommerce' ) || !function_exists( 'wc_get_screen_ids' ) ) {
            return $footer_text;
        }
        $screen = get_current_screen();
        if ( !in_array( $screen->id, [ 'wc_booking_page_create_recurring_booking', 'wc_booking_page_recurring_bookings' ] ) ) {
            return;
        }
        $footer_text = sprintf(
            /* translators: 1: WooCommerce 2:: five stars */
            __( 'If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', 'recurring-bookings-for-woocommerce' ),
            sprintf( '<strong>%s</strong>', esc_html__( 'Recurring Bookings for WooCommerce', 'recurring-bookings-for-woocommerce' ) ),
            '<a href="https://wordpress.org/support/plugin/recurring-bookings-for-woocommerce/reviews?rate=5#new-post" target="_blank" class="wc-rating-link" aria-label="' . esc_attr__( 'five star', 'recurring-bookings-for-woocommerce' ) . '" data-rated="' . esc_attr__( 'Thanks :)', 'recurring-bookings-for-woocommerce' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
        );
        return $footer_text;
    }
    
    /**
     * Change the admin footer text on WooCommerce admin pages.
     *
     * @param string $footer_text text to be rendered in the footer.
     *
     * @return string
     */
    public function backup_warning()
    {
        $screen = get_current_screen();
        
        if ( !PAnD::is_admin_notice_active( 'disable-done-notice-forever' ) || !in_array( $screen->id, [ 'wc_booking_page_recurring_bookings' ] ) ) {
            return;
        } else {
            ?>
            <div data-dismissible="disable-done-notice-forever" class="notice notice-warning is-dismissible">
                <p><?php 
            _e( 'We strongly recommend carrying out a backup of the database before creating a large number of bookings.', 'recurring-bookings-for-woocommerce' );
            ?></p>
            </div>
		<?php 
        }
    
    }

}