<?php
/**
 * The template used for select fields in the booking form, that have a before attribute.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$class   = $field['class'];
$label   = isset( $field['label'] ) ? $field['label'] : '';
$name    = $field['name'];
$options = $field['options'];
$before  = isset( $field['before'] ) ? $field['before'] : '';
?>
<div class="form-field select-before <?php echo esc_attr( implode( ' ', $class ) ); ?>">
    <label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
    <select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>">
		<?php foreach ( $options as $key => $value ) : ?>
            <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
		<?php endforeach; ?>
    </select>
	<?php if ( isset( $field['after'] ) && $field['after'] == true ) {
		echo '<span class="select-after-singular"></span><span class="select-after-plural"></span>';
	} ?>
</div>
