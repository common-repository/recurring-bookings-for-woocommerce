<?php
/**
 * The template used for heading fields in the booking series form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$class   = $field['class'];
$label   = isset($field['label']) ? $field['label'] : '';
$name    = $field['name'];

?>
<p class="rbwc-series-header <?php echo esc_attr( implode( ' ', $class ) ); ?>"><?php echo esc_html( $label ); ?></p>
