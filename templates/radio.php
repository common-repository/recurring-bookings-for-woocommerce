<?php
/**
 * The template used for radio fields in the booking series form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$class   = $field['class'];
$label   = isset($field['label']) ? $field['label'] : '';
$name    = $field['name'];
$options = $field['options'];
$default = $field['default'];

?>
<div class="form-field form-field-wide radio-tabs <?php echo esc_attr( implode( ' ', $class ) ); ?>">
		<?php foreach ( $options as $key => $value ) : ?>
			<input <?php echo $key == $default ? 'checked="checked"' : '' ?> type="radio" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" data-singular="<?php echo esc_attr( $value['singular'] ); ?>" data-plural="<?php echo esc_attr( $value['plural'] ); ?>">
            <label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value['periodicity'] ); ?></label>
		<?php endforeach; ?>
</div>
