<?php
/**
 * The template used for date fields in the booking form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$after = isset( $field['after'] ) ? $field['after'] : null;
$class = $field['class'];
$label = $field['label'];
$name  = $field['name'];
?>
<p class="form-field form-field-wide rbwc-date-picker <?php echo esc_attr( implode( ' ', $class ) ); ?>">
    <?php do_action('rbwc_modify_date_field'); ?>
    <label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?>:</label>
    <input
            type="text"
            name="<?php echo esc_attr( $name ); ?>"
            id="<?php echo esc_attr( $name ); ?>"
    />
</p>
