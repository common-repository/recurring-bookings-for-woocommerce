<?php

class RBWC_Bookings_Create extends WC_Bookings_Create
{
    /**
     * Stores errors.
     *
     * @var array
     */
    private  $errors = array() ;
    /**
     * Output the form.
     *
     * @version  1.10.7
     */
    public function output()
    {
        $this->errors = array();
        $step = 1;
        try {
            if ( !empty($_POST) && !check_admin_referer( 'create_booking_notification' ) ) {
                throw new Exception( __( 'Error - please try again', 'recurring-bookings-for-woocommerce' ) );
            }
            
            if ( !empty($_POST['create_booking']) ) {
                $customer_id = ( isset( $_POST['customer_id'] ) ? absint( $_POST['customer_id'] ) : 0 );
                $bookable_product_id = absint( $_POST['bookable_product_id'] );
                $booking_order = wc_clean( $_POST['booking_order'] );
                if ( !$bookable_product_id ) {
                    throw new Exception( __( 'Please choose a bookable product', 'recurring-bookings-for-woocommerce' ) );
                }
                
                if ( 'existing' === $booking_order ) {
                    
                    if ( class_exists( 'WC_Seq_Order_Number_Pro' ) ) {
                        $order_id = WC_Seq_Order_Number_Pro::find_order_by_order_number( wc_clean( $_POST['booking_order_id'] ) );
                    } else {
                        $order_id = absint( $_POST['booking_order_id'] );
                    }
                    
                    $booking_order = $order_id;
                    if ( !$booking_order || get_post_type( $booking_order ) !== 'shop_order' ) {
                        throw new Exception( __( 'Invalid order ID provided', 'recurring-bookings-for-woocommerce' ) );
                    }
                }
                
                $step++;
                $product = wc_get_product( $bookable_product_id );
                $booking_form = new WC_Booking_Form( $product );
            } elseif ( !empty($_POST['create_booking_2']) ) {
                $customer_id = absint( $_POST['customer_id'] );
                $bookable_product_id = absint( $_POST['bookable_product_id'] );
                $booking_order = wc_clean( $_POST['booking_order'] );
                $product = wc_get_product( $bookable_product_id );
                $booking_data = wc_bookings_get_posted_data( $_POST, $product );
                $cost = apply_filters(
                    'rbwc_base_cost',
                    WC_Bookings_Cost_Calculation::calculate_booking_cost( $booking_data, $product ),
                    $booking_data,
                    $product,
                    $customer_id
                );
                $booking_cost = ( $cost && !is_wp_error( $cost ) ? number_format(
                    $cost,
                    2,
                    '.',
                    ''
                ) : 0 );
                $create_order = false;
                $order_id = 0;
                $item_id = 0;
                /**
                 * Now handle recurrences.
                 */
                // Start by checking we have any
                
                if ( !empty($_POST['rbwc_recurrence']) ) {
                    $recurrence_props = array();
                    foreach ( $_POST['rbwc_recurrence'] as $recurrence_date ) {
                        $recurrence_booking_data = wc_bookings_get_posted_data( $recurrence_date, $product );
                        $cost = apply_filters(
                            'rbwc_recurring_cost',
                            WC_Bookings_Cost_Calculation::calculate_booking_cost( $recurrence_booking_data, $product ),
                            $recurrence_booking_data,
                            $product,
                            $customer_id
                        );
                        $booking_cost_recurrence = ( $cost && !is_wp_error( $cost ) ? number_format(
                            $cost,
                            2,
                            '.',
                            ''
                        ) : 0 );
                        $recurrence_props[] = array(
                            'customer_id'   => $customer_id,
                            'product_id'    => ( is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id ),
                            'resource_id'   => ( isset( $recurrence_booking_data['_resource_id'] ) ? $recurrence_booking_data['_resource_id'] : '' ),
                            'person_counts' => $recurrence_booking_data['_persons'],
                            'cost'          => $booking_cost_recurrence,
                            'start'         => $recurrence_booking_data['_start_date'],
                            'end'           => $recurrence_booking_data['_end_date'],
                            'all_day'       => ( $recurrence_booking_data['_all_day'] ? true : false ),
                        );
                        $booking_cost += $booking_cost_recurrence;
                    }
                }
                
                
                if ( wc_prices_include_tax() ) {
                    $base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class() );
                    $base_taxes = WC_Tax::calc_tax( $booking_cost, $base_tax_rates, true );
                    $booking_cost = $booking_cost - array_sum( $base_taxes );
                }
                
                $props = array(
                    'customer_id'   => $customer_id,
                    'product_id'    => ( is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id ),
                    'resource_id'   => ( isset( $booking_data['_resource_id'] ) ? $booking_data['_resource_id'] : '' ),
                    'person_counts' => $booking_data['_persons'],
                    'cost'          => $booking_cost,
                    'start'         => $booking_data['_start_date'],
                    'end'           => $booking_data['_end_date'],
                    'all_day'       => ( $booking_data['_all_day'] ? true : false ),
                );
                
                if ( 'new' === $booking_order ) {
                    $create_order = true;
                    $order_id = $this->create_order( $booking_cost, $customer_id );
                    if ( !$order_id ) {
                        throw new Exception( __( 'Error: Could not create order', 'recurring-bookings-for-woocommerce' ) );
                    }
                } elseif ( $booking_order > 0 ) {
                    $order_id = absint( $booking_order );
                    if ( !$order_id || get_post_type( $order_id ) !== 'shop_order' ) {
                        throw new Exception( __( 'Invalid order ID provided', 'recurring-bookings-for-woocommerce' ) );
                    }
                    $order = new WC_Order( $order_id );
                    
                    if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
                        update_post_meta( $order_id, '_order_total', $order->get_total() + $booking_cost );
                    } else {
                        $order->set_total( $order->get_total( 'edit' ) + $booking_cost );
                        $order->save();
                    }
                    
                    do_action( 'woocommerce_bookings_create_booking_page_add_order_item', $order_id );
                }
                
                
                if ( $order_id ) {
                    $item_id = wc_add_order_item( $order_id, array(
                        'order_item_name' => $product->get_title(),
                        'order_item_type' => 'line_item',
                    ) );
                    if ( !$item_id ) {
                        throw new Exception( __( 'Error: Could not create item', 'recurring-bookings-for-woocommerce' ) );
                    }
                    
                    if ( !empty($customer_id) ) {
                        $order = wc_get_order( $order_id );
                        $keys = array(
                            'first_name',
                            'last_name',
                            'company',
                            'address_1',
                            'address_2',
                            'city',
                            'state',
                            'postcode',
                            'country'
                        );
                        $types = array( 'shipping', 'billing' );
                        foreach ( $types as $type ) {
                            $address = array();
                            foreach ( $keys as $key ) {
                                $address[$key] = (string) get_user_meta( $customer_id, $type . '_' . $key, true );
                            }
                            $order->set_address( $address, $type );
                        }
                    }
                    
                    // Add line item meta
                    wc_add_order_item_meta( $item_id, '_qty', 1 );
                    wc_add_order_item_meta( $item_id, '_tax_class', $product->get_tax_class() );
                    wc_add_order_item_meta( $item_id, '_product_id', $product->get_id() );
                    wc_add_order_item_meta( $item_id, '_variation_id', '' );
                    wc_add_order_item_meta( $item_id, '_line_subtotal', $booking_cost );
                    wc_add_order_item_meta( $item_id, '_line_total', $booking_cost );
                    wc_add_order_item_meta( $item_id, '_line_tax', 0 );
                    wc_add_order_item_meta( $item_id, '_line_subtotal_tax', 0 );
                    wc_add_order_item_meta( $item_id, '_product_id', $product->get_id() );
                    do_action( 'woocommerce_bookings_create_booking_page_add_order_item', $order_id );
                }
                
                // Calculate the order totals with taxes.
                $order = wc_get_order( $order_id );
                if ( is_a( $order, 'WC_Order' ) ) {
                    $order->calculate_totals( wc_tax_enabled() );
                }
                // Create the booking itself
                $new_booking = new WC_Booking( $props );
                $new_booking->set_order_id( $order_id );
                $new_booking->set_order_item_id( $item_id );
                $new_booking->set_status( ( $create_order ? 'unpaid' : 'confirmed' ) );
                $new_booking->save();
                $parent_id = $new_booking->get_id();
                // Create all our recurring bookings
                $recurring_booking_ids = null;
                
                if ( !empty($recurrence_props) ) {
                    $recurring_booking_ids = array();
                    foreach ( $recurrence_props as $recurrence_prop ) {
                        $new_booking = new WC_Booking( $recurrence_prop );
                        $new_booking->set_order_id( $order_id );
                        $new_booking->set_order_item_id( $item_id );
                        $new_booking->set_status( ( $create_order ? 'unpaid' : 'confirmed' ) );
                        $new_booking->save();
                        //	Add some meta
                        update_post_meta( $new_booking->get_id(), '_rbwc_parent', $parent_id );
                        update_post_meta( $new_booking->get_id(), '_rbwc_admin_created', 1 );
                        // add to array of recurring booking IDs
                        $recurring_booking_ids[] = $new_booking->get_id();
                    }
                }
                
                // Handle series formulas
                $rbwc_recur_period = null;
                $rbwc_recur_interval = null;
                $rbwc_recur_month_rule_0 = null;
                $rbwc_recur_month_rule_1 = null;
                $rbwc_recur_month_rule_2 = null;
                if ( isset( $_POST['rbwc_recur_period'] ) ) {
                    $rbwc_recur_period = sanitize_text_field( $_POST['rbwc_recur_period'] );
                }
                // Get interval
                if ( isset( $_POST['rbwc_recur_interval'] ) ) {
                    $rbwc_recur_interval = sanitize_text_field( $_POST['rbwc_recur_interval'] );
                }
                if ( $_POST['rbwc_recur_period'] == 'month' ) {
                    // Get month_rule_0
                    
                    if ( isset( $_POST['rbwc_recur_month_rule_0'] ) ) {
                        $rbwc_recur_month_rule_0 = sanitize_text_field( $_POST['rbwc_recur_month_rule_0'] );
                        
                        if ( $rbwc_recur_month_rule_0 == 'different' ) {
                            // Get month_rule_1
                            if ( isset( $_POST['rbwc_recur_month_rule_1'] ) ) {
                                $rbwc_recur_month_rule_1 = sanitize_text_field( $_POST['rbwc_recur_month_rule_1'] );
                            }
                            // Get month_rule_2
                            if ( isset( $_POST['rbwc_recur_month_rule_2'] ) ) {
                                $rbwc_recur_month_rule_2 = sanitize_text_field( $_POST['rbwc_recur_month_rule_2'] );
                            }
                        }
                    
                    }
                
                }
                do_action( 'woocommerce_bookings_created_manual_booking', $new_booking );
                wp_safe_redirect( admin_url( 'edit.php?post_type=wc_booking' ) );
                exit;
            }
        
        } catch ( Exception $e ) {
            $this->errors[] = $e->getMessage();
        }
        switch ( $step ) {
            case 1:
                include 'partials/html-create-booking-page.php';
                break;
            case 2:
                add_filter(
                    'wc_get_template',
                    array( $this, 'use_default_form_template' ),
                    10,
                    5
                );
                include 'partials/html-create-booking-page-2.php';
                remove_filter( 'wc_get_template', array( $this, 'use_default_form_template' ), 10 );
                break;
        }
    }
    
    /**
     * Create order.
     *
     * @param float $total
     * @param int $customer_id
     *
     * @return int
     */
    public function create_order( $total, $customer_id )
    {
        
        if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
            $order = wc_create_order( array(
                'customer_id' => absint( $customer_id ),
            ) );
            $order_id = $order->id;
            $order->set_total( $total );
            update_post_meta( $order->id, '_created_via', 'bookings' );
        } else {
            $order = new WC_Order();
            $order->set_customer_id( $customer_id );
            $order->set_total( $total );
            $order->set_created_via( 'bookings' );
            $order_id = $order->save();
        }
        
        do_action( 'woocommerce_new_booking_order', $order_id );
        return $order_id;
    }
    
    /**
     * Output any errors
     */
    public function show_errors()
    {
        foreach ( $this->errors as $error ) {
            echo  '<div class="error"><p>' . esc_html( $error ) . '</p></div>' ;
        }
    }
    
    /**
     * Use default template form from the extension.
     *
     * This prevents any overridden template via theme being used in
     * create booking screen.
     *
     * @since 1.9.11
     * @see https://github.com/woothemes/woocommerce-bookings/issues/773
     */
    public function use_default_form_template(
        $located,
        $template_name,
        $args,
        $template_path,
        $default_path
    )
    {
        if ( 'woocommerce-bookings' === $template_path ) {
            $located = $default_path . $template_name;
        }
        return $located;
    }
    
    /**
     * Add a series of information to an admin recurring booking form about that product.
     *
     * @param $id
     */
    public function display_recurrence_info( $id )
    {
        ?>
        <div id="rbwc-product-recurrence-information-panel">
            <h4><?php 
        esc_html_e( 'Product Information', 'recurring-bookings-for-woocommerce' );
        ?></h4>
            <h3><?php 
        echo  get_the_title( $id ) ;
        ?></h3>
            <p><?php 
        esc_html_e( 'This product:', 'recurring-bookings-for-woocommerce' );
        ?></p>
            <ul>
				<?php 
        
        if ( rbwc_is_fixed_length( $id ) ) {
            echo  '<li>' ;
            printf( esc_html__( 'has a fixed booking length (occurs %s times)', 'recurring-bookings-for-woocommerce' ), esc_html( rbwc_get_fixed_length( $id ) ) );
            echo  '</li>' ;
        } else {
            echo  '<li>' ;
            esc_html_e( 'has a variable booking length you may define below', 'recurring-bookings-for-woocommerce' );
            echo  '</li>' ;
        }
        
        
        if ( rbwc_is_fixed_period( $id ) ) {
            echo  '<li>' ;
            printf( esc_html__( 'has a fixed booking period (occurs every %s)', 'recurring-bookings-for-woocommerce' ), esc_html( rbwc_get_fixed_period( $id ) ) );
            echo  '</li>' ;
        } else {
            echo  '<li>' ;
            esc_html_e( 'has a booking period you may define below', 'recurring-bookings-for-woocommerce' );
            echo  '</li>' ;
        }
        
        ?>
            </ul>
        </div>
		<?php 
    }

}