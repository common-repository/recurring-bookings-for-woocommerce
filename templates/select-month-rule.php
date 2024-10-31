<?php
/**
 * The template used for select fields in the booking form, that have a before attribute.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$class   = $field['class'];
$label   = isset($field['label']) ? $field['label'] : '';
$name    = $field['name'];
$options = $field['options'];
$before  = isset($field['before']) ? $field['before'] : '';
$position = $field['position'];

if ( $position == 'start' ) {
	echo '<div class="rbwc-series-month-rules" data-RBWCdate="">';
}
?>
<p class="form-field select-before <?php echo esc_attr( implode( ' ', $class ) ); ?>">
    <label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
    <select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>">
		<?php foreach ( $options as $key => $value ) : ?>
            <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
		<?php endforeach; ?>
    </select>
	<?php
	if ( $position == 'start' ) { ?>
        <span class="wc_bookings_field_recur_month_rule_0_result_same">
		<?php esc_html_e( 'the', 'recurring-bookings-for-woocommerce' ) ?>&nbsp;<span></span>
            <?php esc_html_e( 'day', 'recurring-bookings-for-woocommerce' ) ?>&nbsp;
        </span>
        <span class="wc_bookings_field_recur_month_rule_0_result_different_the">
     <?php esc_html_e( 'the', 'recurring-bookings-for-woocommerce' ) ?>
 </span>
        <span class="wc_bookings_field_recur_month_rule_0_result_different_day">
            <?php esc_html_e( 'day', 'recurring-bookings-for-woocommerce' ) ?>
        </span>

		<?php
	} elseif ( $position == 'middle' ) {
		echo '<span class="wc_bookings_field_recur_month_rule_1_result"></span>';
	}
	?>
</p>

<?php
if ( $position == 'end' ) {
	echo '<p class="wc_bookings_field_recur_month_rule_2_result">' . esc_html__( 'of the month.', 'recurring-bookings-for-woocommerce' ) . '</p>';
	echo '</div>';
}
?>
