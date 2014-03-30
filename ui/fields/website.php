<?php
$attributes = array();

$type = 'text';

if ( 1 == pods_v( 'website_html5', $options ) ) {
	$type = 'url';
}

$attributes['type']     = $type;
$attributes['value']    = $value;
$attributes['tabindex'] = 2;
$attributes             = Pods_Form::merge_attributes( $attributes, $name, $form_field_type, $options );
?>
	<input<?php Pods_Form::attributes( $attributes, $name, $form_field_type, $options ); ?> />
<?php
Pods_Form::regex( $form_field_type, $options );