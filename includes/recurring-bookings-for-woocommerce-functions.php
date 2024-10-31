<?php

/**
 * General functions for use throughout the plugin.
 */
/**
 * Checks a bookable product to see if it can be recurred. Defaults to true.
 *
 * @param int $id The product ID
 *
 * @return bool
 */
function rbwc_is_recurrable( $id )
{
    $recurrable = get_post_meta( $id, '_recurrable', true );
    
    if ( isset( $recurrable ) && $recurrable == 'yes' ) {
        return true;
    } else {
        return false;
    }

}

/**
 * Check if there is a fixed length for this product.
 *
 * @param $product_id
 *
 * @return bool
 */
function rbwc_is_fixed_length( $product_id )
{
    $fixed_length = get_post_meta( $product_id, '_wc_booking_rbwc_recur_length_fixed', true );
    
    if ( isset( $fixed_length ) && $fixed_length == 'yes' ) {
        return true;
    } else {
        return false;
    }

}

/**
 * Check if there is a date defined length for this product.
 *
 * @param $product_id
 *
 * @return bool
 */
function rbwc_is_date_defined( $product_id )
{
    $date_defined = get_post_meta( $product_id, '_wc_booking_rbwc_recur_length_date_defined', true );
    
    if ( isset( $date_defined ) && $date_defined == 'yes' ) {
        return true;
    } else {
        return false;
    }

}

/**
 * Check if there is a fixed period for this product.
 *
 * @param $product_id
 *
 * @return bool
 */
function rbwc_is_fixed_period( $product_id )
{
    $fixed_period = get_post_meta( $product_id, '_wc_booking_rbwc_recur_period_fixed', true );
    
    if ( isset( $fixed_period ) && $fixed_period == 'yes' ) {
        return true;
    } else {
        return false;
    }

}

/**
 * Check if customers are allowed to use intervals (non-consecutive days/weeks/months).
 *
 * @param $product_id
 *
 * @return bool
 */
function rbwc_are_intervals_allowed( $product_id )
{
    return false;
}

/**
 * Get fixed length for this product.
 *
 * @param $product_id
 *
 * @return bool
 */
function rbwc_get_fixed_length( $product_id )
{
    return get_post_meta( $product_id, '_wc_booking_rbwc_recur_length', true );
}

/**
 * Get fixed period for this product.
 *
 * @param $product_id
 *
 * @return bool
 */
function rbwc_get_fixed_period( $product_id )
{
    return get_post_meta( $product_id, '_wc_booking_rbwc_recur_period', true );
}

/**
 * Get the length of recurrence.
 *
 * @param $id
 *
 * @return mixed
 */
function rbwc_get_length( $id )
{
    // Is it globally fixed?
    if ( rbwc_is_fixed_length( $id ) ) {
        return rbwc_get_fixed_length( $id );
    }
}

/**
 * Get the recurrence period.
 *
 * @param $id
 *
 * @return mixed
 */
function rbwc_get_period( $id )
{
    // Is it globally fixed?
    if ( rbwc_is_fixed_period( $id ) ) {
        return rbwc_get_fixed_period( $id );
    }
}

/**
 * Get a set of recurrence dates based on WC $posted data.
 *
 * @param $posted array of data retrieved from a WC Bookings form.
 *
 * @return array|void
 * @throws Exception
 */
function rbwc_get_recurrences( $posted = array() )
{
    $id = sanitize_text_field( $posted['add-to-cart'] );
    $max_length = rbwc_get_max_length( $id );
    /**
     * Filter the absolute upper limit on recurrence generation, public and admin.
     * Increasing this may have performance implications.
     *
     * @param int
     */
    $hard_upper = apply_filters( 'rbwc_absolute_max_limit', 150 );
    $recurrence_dates = array();
    // Get recurrence data
    
    if ( isset( $posted['wc_bookings_field_recur_length'] ) ) {
        
        if ( $max_length && sanitize_text_field( $posted['wc_bookings_field_recur_length'] ) <= $max_length ) {
            $length = sanitize_text_field( $posted['wc_bookings_field_recur_length'] ) - 1;
        } elseif ( $max_length ) {
            $length = $max_length - 1;
        } else {
            $length = sanitize_text_field( $posted['wc_bookings_field_recur_length'] ) - 1;
        }
    
    } else {
        $length = rbwc_get_length( $id ) - 1;
    }
    
    
    if ( isset( $posted['wc_bookings_field_recur_period'] ) ) {
        $period = sanitize_text_field( $posted['wc_bookings_field_recur_period'] );
    } else {
        $period = rbwc_get_period( $id );
    }
    
    $end_date = false;
    $interval = 1;
    $recurrence_date = array();
    // For loop will always exist due to the length
    for ( $i = 1 ;  $i <= $length ;  $i++ ) {
        
        if ( $interval == 1 ) {
            $index = $i;
        } else {
            $index = $i * $interval;
        }
        
        
        if ( !empty($posted['wc_bookings_field_start_date_year']) && !empty($posted['wc_bookings_field_start_date_month']) && !empty($posted['wc_bookings_field_start_date_day']) && empty($posted['wc_bookings_field_start_date_time']) ) {
            $raw_dmy_date = new DateTime( $posted['wc_bookings_field_start_date_day'] . '-' . $posted['wc_bookings_field_start_date_month'] . '-' . $posted['wc_bookings_field_start_date_year'] );
            $raw_dmy_date->modify( '+' . $index . ' ' . $period );
            if ( !$end_date == false && $raw_dmy_date > $end_date ) {
                break;
            }
            $recurrence_date['wc_bookings_field_start_date_day'] = $raw_dmy_date->format( 'd' );
            $recurrence_date['wc_bookings_field_start_date_month'] = $raw_dmy_date->format( 'm' );
            $recurrence_date['wc_bookings_field_start_date_year'] = $raw_dmy_date->format( 'Y' );
        }
        
        
        if ( !empty($posted['wc_bookings_field_start_date_yearmonth']) ) {
            $raw_ym_date = new DateTime( $posted['wc_bookings_field_start_date_yearmonth'] );
            $raw_ym_date->modify( '+' . $index . ' ' . $period );
            if ( !$end_date == false && $raw_ym_date > $end_date ) {
                break;
            }
            $recurrence_date['wc_bookings_field_start_date_yearmonth'] = $raw_ym_date->format( 'Y-m' );
        }
        
        
        if ( !empty($posted['wc_bookings_field_start_date_time']) ) {
            $raw_datetime = new DateTime( $posted['wc_bookings_field_start_date_time'] );
            $raw_datetime->modify( '+' . $index . ' ' . $period );
            if ( !$end_date == false && $raw_datetime > $end_date ) {
                break;
            }
            $recurrence_date['wc_bookings_field_start_date_day'] = $raw_datetime->format( 'd' );
            $recurrence_date['wc_bookings_field_start_date_month'] = $raw_datetime->format( 'm' );
            $recurrence_date['wc_bookings_field_start_date_year'] = $raw_datetime->format( 'Y' );
            $recurrence_date['wc_bookings_field_start_date_time'] = $raw_datetime->format( DateTime::ATOM );
        }
        
        //  Handle persons, by finding a POST key that relates to persons, and then returning it
        foreach ( $posted as $key => $value ) {
            if ( strpos( $key, 'wc_bookings_field_persons' ) === 0 ) {
                // value starts with wc_bookings_field_persons
                $recurrence_date[$key] = $value;
            }
        }
        // Handle resources
        if ( isset( $posted['wc_bookings_field_resource'] ) ) {
            $recurrence_date['wc_bookings_field_resource'] = sanitize_text_field( $posted['wc_bookings_field_resource'] );
        }
        // Handle customer defined blocks
        if ( isset( $posted['wc_bookings_field_duration'] ) ) {
            $recurrence_date['wc_bookings_field_duration'] = sanitize_text_field( $posted['wc_bookings_field_duration'] );
        }
        // Handle timezones
        if ( isset( $posted['wc_bookings_field_start_date_local_timezone'] ) ) {
            $recurrence_date['wc_bookings_field_start_date_local_timezone'] = sanitize_text_field( $posted['wc_bookings_field_start_date_local_timezone'] );
        }
        $recurrence_date['add-to-cart'] = sanitize_text_field( $posted['add-to-cart'] );
        $recurrence_dates[] = $recurrence_date;
    }
    return $recurrence_dates;
}

/**
 * Get a recurrence series formula based on WC $posted data.
 * This should contain everything necessary to continue recurrences.
 *
 * @param $posted array of data retrieved from a WC Bookings form.
 *
 * @return array|void
 * @throws Exception
 */
function rbwc_get_posted_series_formula( $posted )
{
    $formula = array();
    // Get period
    if ( isset( $posted['wc_bookings_field_recur_period'] ) ) {
        $formula['rbwc_recur_period'] = sanitize_text_field( $posted['wc_bookings_field_recur_period'] );
    }
    // Get interval
    if ( isset( $posted['wc_bookings_field_recur_interval'] ) ) {
        $formula['rbwc_recur_interval'] = sanitize_text_field( $posted['wc_bookings_field_recur_interval'] );
    }
    // Get month_rule_0
    if ( isset( $posted['wc_bookings_field_recur_month_rule_0'] ) ) {
        $formula['rbwc_recur_month_rule_0'] = sanitize_text_field( $posted['wc_bookings_field_recur_month_rule_0'] );
    }
    // Get month_rule_1
    if ( isset( $posted['wc_bookings_field_recur_month_rule_1'] ) ) {
        $formula['rbwc_recur_month_rule_1'] = sanitize_text_field( $posted['wc_bookings_field_recur_month_rule_1'] );
    }
    // Get month_rule_2
    if ( isset( $posted['wc_bookings_field_recur_month_rule_2'] ) ) {
        $formula['rbwc_recur_month_rule_2'] = sanitize_text_field( $posted['wc_bookings_field_recur_month_rule_2'] );
    }
    return $formula;
}

/**
 * Takes an array of recurrence date data and creates a string of inputs for passing into a cart.
 *
 * @param $recurrence_date
 * @param $key
 *
 * @return string
 */
function rbwc_generate_post_data( $recurrence_date, $key )
{
    //	Start here: all posted data includes a product ID
    $values = '<input type="hidden" name="rbwc_recurrence[' . $key . '][add-to-cart]" value="' . $recurrence_date['add-to-cart'] . '">';
    
    if ( !empty($recurrence_date['wc_bookings_field_start_date_year']) && !empty($recurrence_date['wc_bookings_field_start_date_month']) && !empty($recurrence_date['wc_bookings_field_start_date_day']) ) {
        $values .= '<input type="hidden" name="rbwc_recurrence[' . $key . '][wc_bookings_field_start_date_day]" value="' . $recurrence_date['wc_bookings_field_start_date_day'] . '">';
        $values .= '<input type="hidden" name="rbwc_recurrence[' . $key . '][wc_bookings_field_start_date_month]" value="' . $recurrence_date['wc_bookings_field_start_date_month'] . '">';
        $values .= '<input type="hidden" name="rbwc_recurrence[' . $key . '][wc_bookings_field_start_date_year]" value="' . $recurrence_date['wc_bookings_field_start_date_year'] . '">';
    }
    
    if ( !empty($recurrence_date['wc_bookings_field_start_date_yearmonth']) ) {
        $values .= '<input type="hidden" name="rbwc_recurrence[' . $key . '][wc_bookings_field_start_date_yearmonth]" value="' . $recurrence_date['wc_bookings_field_start_date_yearmonth'] . '">';
    }
    if ( !empty($recurrence_date['wc_bookings_field_start_date_time']) ) {
        $values .= '<input type="hidden" name="rbwc_recurrence[' . $key . '][wc_bookings_field_start_date_time]" value="' . $recurrence_date['wc_bookings_field_start_date_time'] . '">';
    }
    //  Handle persons, by finding a POST key that relates to persons
    foreach ( $recurrence_date as $persons_key => $value ) {
        if ( strpos( $persons_key, 'wc_bookings_field_persons' ) === 0 ) {
            $values .= '<input type="hidden" name="rbwc_recurrence[' . $key . '][' . $persons_key . ']" value="' . $value . '">';
        }
    }
    // Handle resources
    if ( isset( $recurrence_date['wc_bookings_field_resource'] ) ) {
        $values .= '<input type="hidden" name="rbwc_recurrence[' . $key . '][wc_bookings_field_resource]" value="' . $recurrence_date['wc_bookings_field_resource'] . '">';
    }
    // Handle customer defined blocks
    if ( isset( $recurrence_date['wc_bookings_field_duration'] ) ) {
        $values .= '<input type="hidden" name="rbwc_recurrence[' . $key . '][wc_bookings_field_duration]" value="' . $recurrence_date['wc_bookings_field_duration'] . '">';
    }
    // Handle timezones
    if ( isset( $recurrence_date['wc_bookings_field_start_date_local_timezone'] ) ) {
        $values .= '<input type="hidden" name="rbwc_recurrence[' . $key . '][wc_bookings_field_start_date_local_timezone]" value="' . $recurrence_date['wc_bookings_field_start_date_local_timezone'] . '">';
    }
    return $values;
}

/**
 * Returns a formatted string for each recurring booking result.
 *
 * @param $data
 * @param null $product
 *
 * @return mixed|void
 */
function rbwc_get_booking_string( $data, $product = null )
{
    
    if ( !$product == null && $product->get_duration_unit() == 'day' && $product->get_duration() > 1 && $data['_all_day'] == 1 ) {
        $end = date( wc_bookings_date_format(), $data['_end_date'] );
        $string = $data['date'] . ' - ' . $end;
    } elseif ( $data['_all_day'] == 1 ) {
        $string = $data['date'];
    } else {
        // $string = $data['date'] . ' at ' . $data['time'];
        $string = sprintf(
            /* translators: 1: Booking date 2: Booking time */
            __( '%1$s at %2$s', 'recurring-bookings-for-woocommerce' ),
            $data['date'],
            $data['time']
        );
    }
    
    return apply_filters( 'rbwc_booking_string', $string );
}

/**
 * Check if there is a fixed length for customers trying to book this product.
 *
 * @param $product_id
 *
 * @return bool
 */
function rbwc_is_fixed_length_customer( $product_id )
{
    $fixed_length_customer = get_post_meta( $product_id, '_wc_booking_rbwc_recur_length_fixed_customer', true );
    
    if ( isset( $fixed_length_customer ) && $fixed_length_customer == 'yes' ) {
        return true;
    } else {
        return false;
    }

}

/**
 * Check if there is a fixed period for customers trying to book this product.
 *
 * @param $product_id
 *
 * @return bool
 */
function rbwc_is_fixed_period_customer( $product_id )
{
    $fixed_period_customer = get_post_meta( $product_id, '_wc_booking_rbwc_recur_period_fixed_customer', true );
    
    if ( isset( $fixed_period_customer ) && $fixed_period_customer == 'yes' ) {
        return true;
    } else {
        return false;
    }

}

/**
 * Get maximum number of bookings a customer can make.
 *
 * @param $id
 *
 * @return mixed
 */
function rbwc_get_max_length( $id )
{
    return false;
}

/**
 * Checks whether a bookable product is set to automatic mode.
 *
 * @param $id
 *
 * @return bool
 */
function rbwc_is_auto( $id )
{
    return false;
}

/**
 * Determine the error handling method.
 *
 * @param $id
 *
 * @return bool
 */
function rbwc_ignore_errors( $id )
{
    $ignore_errors = false;
    return $ignore_errors;
}

/**
 * Check if this is a request at the backend.
 *
 * @return bool true if is admin request, otherwise false.
 */
function rbwc_is_admin_request()
{
    /**
     * Get current URL.
     *
     * @link https://wordpress.stackexchange.com/a/126534
     */
    $current_url = home_url( add_query_arg( null, null ) );
    /**
     * Get admin URL and referrer.
     *
     * @link https://core.trac.wordpress.org/browser/tags/4.8/src/wp-includes/pluggable.php#L1076
     */
    $admin_url = strtolower( admin_url() );
    $referrer = strtolower( wp_get_referer() );
    /**
     * Check if this is a admin request. If true, it
     * could also be a AJAX request from the frontend.
     */
    
    if ( 0 === strpos( $current_url, $admin_url ) ) {
        /**
         * Check if the user comes from a admin page.
         */
        
        if ( 0 === strpos( $referrer, $admin_url ) ) {
            return true;
        } else {
            /**
             * Check for AJAX requests.
             *
             * @link https://gist.github.com/zitrusblau/58124d4b2c56d06b070573a99f33b9ed#file-lazy-load-responsive-images-php-L193
             */
            
            if ( function_exists( 'wp_doing_ajax' ) ) {
                return !wp_doing_ajax();
            } else {
                return !(defined( 'DOING_AJAX' ) && DOING_AJAX);
            }
        
        }
    
    } else {
        return false;
    }

}
