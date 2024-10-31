<?php

/**
 * Class that handles booking form and calculations.
 *
 */
class Recurring_Bookings_For_Woocommerce_Main
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
     */
    public function __construct( $recurring_bookings_for_woocommerce, $version )
    {
        $this->recurring_bookings_for_woocommerce = $recurring_bookings_for_woocommerce;
        $this->version = $version;
    }
    
    /**
     * Decouple hooks from constructor.
     */
    public function init()
    {
        add_action( 'wp_loaded', array( $this, 'calculate_recurring_costs' ) );
        add_filter( 'booking_form_fields', array( $this, 'add_admin_recurrence_fields' ) );
        add_filter(
            'wc_get_template',
            array( $this, 'custom_booking_form_templates' ),
            20,
            5
        );
        //		Intercept emails that are for recurring bookings.
        add_filter(
            'woocommerce_email_recipient_new_booking',
            array( $this, 'intercept_recurring_booking_emails' ),
            99,
            2
        );
        add_filter(
            'woocommerce_email_recipient_booking_confirmed',
            array( $this, 'intercept_recurring_booking_emails' ),
            99,
            2
        );
        add_filter(
            'woocommerce_email_recipient_booking_reminder',
            array( $this, 'intercept_recurring_booking_emails' ),
            99,
            2
        );
        add_filter(
            'woocommerce_email_recipient_booking_cancelled',
            array( $this, 'intercept_recurring_booking_emails' ),
            99,
            2
        );
        add_filter(
            'woocommerce_email_recipient_admin_booking_cancelled',
            array( $this, 'intercept_recurring_booking_emails' ),
            99,
            2
        );
        add_filter(
            'woocommerce_email_recipient_booking_reminder',
            array( $this, 'intercept_recurring_booking_emails' ),
            99,
            2
        );
        add_filter(
            'woocommerce_email_recipient_booking_pending_confirmation',
            array( $this, 'intercept_recurring_booking_emails' ),
            99,
            2
        );
    }
    
    /**
     * Hook our ajax cost calculation function back in.
     */
    public function calculate_recurring_costs()
    {
        add_action( 'wp_ajax_wc_bookings_calculate_costs', array( $this, 'calculate_costs' ), 20 );
        add_action( 'wp_ajax_nopriv_wc_bookings_calculate_costs', array( $this, 'calculate_costs' ), 20 );
    }
    
    /**
     * Provide new templates for booking form input fields.
     *
     * @param $located
     * @param $template_name
     * @param $args
     * @param $template_path
     * @param $default_path
     *
     * @return mixed|string
     */
    public function custom_booking_form_templates(
        $located,
        $template_name,
        $args,
        $template_path,
        $default_path
    )
    {
        if ( 'booking-form/heading.php' == $template_name ) {
            $located = plugin_dir_path( __DIR__ ) . 'templates/heading.php';
        }
        if ( 'booking-form/radio.php' == $template_name ) {
            $located = plugin_dir_path( __DIR__ ) . 'templates/radio.php';
        }
        if ( 'booking-form/select-before.php' == $template_name ) {
            $located = plugin_dir_path( __DIR__ ) . 'templates/select-before.php';
        }
        if ( 'booking-form/select-month-rule.php' == $template_name ) {
            $located = plugin_dir_path( __DIR__ ) . 'templates/select-month-rule.php';
        }
        if ( 'booking-form/date.php' == $template_name ) {
            $located = plugin_dir_path( __DIR__ ) . 'templates/date.php';
        }
        return $located;
    }
    
    /**
     * Add a field to back-end booking form that allows admins (if permitted) to enter the number of bookings they want.
     *
     * @param $fields
     *
     * @return mixed
     */
    public function add_admin_recurrence_fields( $fields )
    {
        // This is a backend function. If on frontend, bail
        if ( rbwc_is_admin_request() == false ) {
            return $fields;
        }
        if ( get_current_screen()->base != 'wc_booking_page_create_recurring_booking' ) {
            return $fields;
        }
        // This is a backend function. If on frontend, bail
        if ( rbwc_is_admin_request() == false ) {
            return $fields;
        }
        $id = sanitize_text_field( $_POST['bookable_product_id'] );
        // If this product has a fixed length and period as set in the product settings page, bail
        if ( rbwc_is_fixed_length( $id ) == true && rbwc_is_fixed_period( $id ) == true ) {
            return $fields;
        }
        
        if ( rbwc_is_fixed_period( $id ) === false ) {
            $product = wc_get_product( $id );
            
            if ( $product->get_duration_unit() == 'month' ) {
                $default = 'month';
            } else {
                $default = 'day';
            }
            
            $fields['wc_bookings_field_recur_period'] = array(
                'name'    => 'wc_bookings_field_recur_period',
                'class'   => array(
                '0' => 'wc_bookings_field_recur_period',
                '1' => 'rbwc-series',
            ),
                'type'    => 'radio',
                'options' => array(
                'day'   => array(
                'periodicity' => esc_html__( 'Daily', 'recurring-bookings-for-woocommerce' ),
                'singular'    => esc_html__( 'day', 'recurring-bookings-for-woocommerce' ),
                'plural'      => esc_html__( 'days', 'recurring-bookings-for-woocommerce' ),
            ),
                'week'  => array(
                'periodicity' => esc_html__( 'Weekly', 'recurring-bookings-for-woocommerce' ),
                'singular'    => esc_html__( 'week', 'recurring-bookings-for-woocommerce' ),
                'plural'      => esc_html__( 'weeks', 'recurring-bookings-for-woocommerce' ),
            ),
                'month' => array(
                'periodicity' => esc_html__( 'Monthly', 'recurring-bookings-for-woocommerce' ),
                'singular'    => esc_html__( 'month', 'recurring-bookings-for-woocommerce' ),
                'plural'      => esc_html__( 'months', 'recurring-bookings-for-woocommerce' ),
            ),
            ),
                'default' => $default,
            );
        }
        
        if ( rbwc_is_date_defined( $id ) === false && rbwc_is_fixed_length( $id ) === false && rbwc_is_fixed_length_customer( $id ) === false ) {
            $fields['wc_bookings_field_recur_length'] = array(
                'name'  => 'wc_bookings_field_recur_length',
                'class' => array(
                '0' => 'wc_bookings_field_recur_length',
                '1' => 'rbwc-series',
                '2' => 'inline-field',
            ),
                'label' => esc_html__( 'For a total of', 'recurring-bookings-for-woocommerce' ),
                'after' => esc_html__( 'including the initial booking', 'recurring-bookings-for-woocommerce' ),
                'type'  => 'number',
                'step'  => 1,
                'min'   => 1,
                'max'   => rbwc_get_max_length( $id ),
            );
        }
        if ( rbwc_is_date_defined( $id ) === true ) {
            $fields['wc_booking_rbwc_recur_end_date'] = array(
                'name'  => 'wc_booking_rbwc_recur_end_date',
                'class' => array(
                '0' => 'wc_booking_rbwc_recur_end_date',
                '1' => 'rbwc-series',
            ),
                'label' => esc_html__( 'until', 'recurring-bookings-for-woocommerce' ),
                'type'  => 'date',
            );
        }
        return apply_filters( 'rbwc_booking_form_fields', $fields );
    }
    
    /**
     * Calculate costs (and thus availability) of a potential booking, and if recurring, get formatted data for each recurrence event.
     *
     * @throws Exception
     */
    public function calculate_costs()
    {
        $posted = array();
        parse_str( $_POST['form'], $posted );
        $booking_id = absint( $posted['add-to-cart'] );
        $customer_id = ( isset( $posted['customer_id'] ) ? absint( $posted['customer_id'] ) : null );
        $product = wc_get_product( $booking_id );
        $output_dates = '';
        $total = 0;
        $error = false;
        if ( !$product ) {
            wp_send_json( array(
                'result' => 'ERROR',
                'html'   => apply_filters(
                'woocommerce_bookings_calculated_booking_cost_error_output',
                '<span class="booking-error">' . __( 'This booking is unavailable.', 'recurring-bookings-for-woocommerce' ) . '</span>',
                null,
                null
            ),
            ) );
        }
        
        if ( !rbwc_is_recurrable( $booking_id ) || !rbwc_is_admin_request() && rbwc_fs()->is_not_paying() || !rbwc_is_admin_request() && !rbwc_is_public( $booking_id ) || rbwc_is_auto( $booking_id ) ) {
            $booking_data = wc_bookings_get_posted_data( $posted, $product );
            $cost = apply_filters(
                'rbwc_base_cost',
                WC_Bookings_Cost_Calculation::calculate_booking_cost( $booking_data, $product ),
                $booking_data,
                $product,
                $customer_id
            );
            if ( is_wp_error( $cost ) ) {
                wp_send_json( array(
                    'result' => 'ERROR',
                    'html'   => apply_filters(
                    'woocommerce_bookings_calculated_booking_cost_error_output',
                    '<span class="booking-error">' . $cost->get_error_message() . '</span>',
                    $cost,
                    $product
                ),
                ) );
            }
            $tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
            
            if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
                
                if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
                    $display_price = wc_get_price_including_tax( $product, array(
                        'price' => $cost,
                    ) );
                } else {
                    $display_price = $product->get_price_including_tax( 1, $cost );
                }
            
            } else {
                
                if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
                    $display_price = wc_get_price_excluding_tax( $product, array(
                        'price' => $cost,
                    ) );
                } else {
                    $display_price = $product->get_price_excluding_tax( 1, $cost );
                }
            
            }
            
            
            if ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
                $price_suffix = $product->get_price_suffix( $cost, 1 );
            } else {
                $price_suffix = $product->get_price_suffix();
            }
            
            // Build the output
            
            if ( rbwc_is_auto( $booking_id ) ) {
                $linked_subscription = rbwc_get_linked_subscription();
                
                if ( $linked_subscription == null ) {
                    wp_send_json( array(
                        'result' => 'ERROR',
                        'html'   => apply_filters( 'woocommerce_bookings_rbwc_subscription_error_output', '<span class="booking-error">' . __( 'We are unable to get your subscription information.', 'recurring-bookings-for-woocommerce' ) . '</span>' ),
                    ) );
                } else {
                    $output = apply_filters( 'woocommerce_bookings_booking_cost_string', __( 'Your initial booking is available. We will continue to renew your bookings as long as your subscription is active.', 'recurring-bookings-for-woocommerce' ), $product );
                    // Output the recurrence series formula for later use
                    $formula = rbwc_get_posted_series_formula( $posted );
                    foreach ( $formula as $idx => $value ) {
                        $name = htmlentities( $idx );
                        $value = htmlentities( $value );
                        $output .= '<input type="hidden" name="' . $name . '" value="' . $value . '">';
                    }
                    $output .= '<input type="hidden" name="rbwc_linked_subscription" value="' . $linked_subscription . '">';
                }
            
            } else {
                $output = apply_filters( 'woocommerce_bookings_booking_cost_string', __( 'Booking cost', 'recurring-bookings-for-woocommerce' ), $product ) . ': <strong>' . wc_price( $display_price ) . $price_suffix . '</strong>';
            }
            
            // Send the output
            wp_send_json( array(
                'result' => 'SUCCESS',
                'html'   => apply_filters(
                'woocommerce_bookings_calculated_booking_cost_success_output',
                $output,
                $display_price,
                $product
            ),
            ) );
        } else {
            $booking_data = wc_bookings_get_posted_data( $posted, $product );
            $cost = apply_filters(
                'rbwc_base_cost',
                WC_Bookings_Cost_Calculation::calculate_booking_cost( $booking_data, $product ),
                $booking_data,
                $product,
                $customer_id
            );
            
            if ( is_wp_error( $cost ) ) {
                $error = true;
                $output_dates .= '<li class="rbwc-invalid-date">';
                $output_dates .= rbwc_get_booking_string( $booking_data, $product );
                $output_dates .= ' - ' . $cost->get_error_message();
                $output_dates .= '</li>';
            } else {
                $output_dates .= '<li class="rbwc-valid-date">';
                $output_dates .= rbwc_get_booking_string( $booking_data, $product );
                $output_dates .= '</li>';
            }
            
            $recurrence_dates = rbwc_get_recurrences( $posted );
            foreach ( $recurrence_dates as $key => $recurrence_date ) {
                $booking_data_recurrable = wc_bookings_get_posted_data( $recurrence_date, $product );
                $recurrence_cost = apply_filters(
                    'rbwc_recurring_cost',
                    WC_Bookings_Cost_Calculation::calculate_booking_cost( $booking_data_recurrable, $product ),
                    $booking_data_recurrable,
                    $product,
                    $customer_id
                );
                
                if ( is_wp_error( $recurrence_cost ) ) {
                    $error = true;
                    $output_dates .= '<li class="rbwc-invalid-date">';
                    $output_dates .= rbwc_get_booking_string( $booking_data_recurrable, $product );
                    $output_dates .= ' - ' . $recurrence_cost->get_error_message();
                    $output_dates .= '</li>';
                } else {
                    $output_dates .= '<li class="rbwc-valid-date">';
                    $output_dates .= rbwc_get_booking_string( $booking_data_recurrable, $product );
                    $output_dates .= '</li>';
                    $output_dates .= rbwc_generate_post_data( $recurrence_date, $key );
                    $output_dates .= '<input type="hidden" name="rbwc_recurrence[' . $key . '][cost]" value="' . $recurrence_cost . '">';
                    $cost += $recurrence_cost;
                }
            
            }
            $tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
            
            if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
                
                if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
                    $display_price = wc_get_price_including_tax( $product, array(
                        'price' => $cost,
                    ) );
                } else {
                    $display_price = $product->get_price_including_tax( 1, $cost );
                }
            
            } else {
                
                if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
                    $display_price = wc_get_price_excluding_tax( $product, array(
                        'price' => $cost,
                    ) );
                } else {
                    $display_price = $product->get_price_excluding_tax( 1, $cost );
                }
            
            }
            
            
            if ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
                $price_suffix = $product->get_price_suffix( $cost, 1 );
            } else {
                $price_suffix = $product->get_price_suffix();
            }
            
            // Build the output
            $output = '<div id="rbwc-bookings-panel">';
            // Output the recurrence series formula for later use
            $formula = rbwc_get_posted_series_formula( $posted );
            if ( !empty($formula) ) {
                foreach ( $formula as $idx => $value ) {
                    $name = htmlentities( $idx );
                    $value = htmlentities( $value );
                    $output .= '<input type="hidden" name="' . $name . '" value="' . $value . '">';
                }
            }
            
            if ( $error == true ) {
                $output .= '<p>' . __( 'There was a problem with one or more of your bookings:', 'recurring-bookings-for-woocommerce' ) . '</p><ul>';
                $output .= $output_dates;
                $output .= '</ul>';
                $output .= '</div>';
                // Send the output
                wp_send_json( array(
                    'result' => 'ERROR',
                    'html'   => apply_filters(
                    'woocommerce_bookings_calculated_booking_cost_error_output',
                    $output,
                    $display_price,
                    $product
                ),
                ) );
            } else {
                $output .= '<p>' . __( 'The following bookings will be added:', 'recurring-bookings-for-woocommerce' ) . '</p><ul>';
                $output .= $output_dates;
                $output .= '</ul>';
                $output .= apply_filters( 'woocommerce_bookings_booking_cost_string', __( 'Total booking cost', 'recurring-bookings-for-woocommerce' ), $product ) . ': <strong>' . wc_price( $display_price ) . $price_suffix . '</strong>';
                $output .= apply_filters( 'rbwc_booking_panel_end', '', $product );
                $output .= '</div>';
                // Send the output
                wp_send_json( array(
                    'result' => 'SUCCESS',
                    'html'   => apply_filters(
                    'woocommerce_bookings_calculated_booking_cost_success_output',
                    $output,
                    $display_price,
                    $product
                ),
                ) );
            }
        
        }
    
    }
    
    /**
     * Remove recipient from (and therefore disable) emails that relate to recurring bookings. Prevents a flood of emails when recurring bookings are processed.
     *
     * @param $recipient
     * @param $booking
     *
     * @return mixed|string
     */
    public function intercept_recurring_booking_emails( $recipient, $booking )
    {
        $override = apply_filters( 'rbwc_disable_email_override', false );
        if ( $override === true ) {
            return $recipient;
        }
        if ( $booking ) {
            //  Check if Booking has an RBWC parent. If so, it must be a recurring booking.
            
            if ( !empty(get_post_meta( $booking->get_id(), '_rbwc_parent', false )) ) {
                $recipient = '';
            } else {
                return $recipient;
            }
        
        }
        return $recipient;
    }

}