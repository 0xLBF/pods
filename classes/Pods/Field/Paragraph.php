<?php

/**
 * @package Pods\Fields
 */
class Pods_Field_Paragraph extends
	Pods_Field {

	/**
	 * Field Type Group
	 *
	 * @var string
	 * @since 2.0
	 */
	public static $group = 'Paragraph';

	/**
	 * Field Type Identifier
	 *
	 * @var string
	 * @since 2.0
	 */
	public static $type = 'paragraph';

	/**
	 * Field Type Label
	 *
	 * @var string
	 * @since 2.0
	 */
	public static $label = 'Plain Paragraph Text';

	/**
	 * Field Type Preparation
	 *
	 * @var string
	 * @since 2.0
	 */
	public static $prepare = '%s';

	/**
	 * {@inheritDocs}
	 */
	public function __construct() {

	}

	/**
	 * {@inheritDocs}
	 */
	public function options() {
		$options = array(
			self::$type . '_repeatable'        => array(
				'label'             => __( 'Repeatable Field', 'pods' ),
				'default'           => 0,
				'type'              => 'boolean',
				'help'              => __( 'Making a field repeatable will add controls next to the field which allows users to Add/Remove/Reorder additional values. These values are saved in the database as an array, so searching and filtering by them may require further adjustments".', 'pods' ),
				'boolean_yes_label' => '',
				'dependency'        => true,
				'developer_mode'    => true
			),
			'output_options'                   => array(
				'label' => __( 'Output Options', 'pods' ),
				'group' => array(
					self::$type . '_allow_html'      => array(
						'label'      => __( 'Allow HTML?', 'pods' ),
						'default'    => 1,
						'type'       => 'boolean',
						'dependency' => true
					),
					self::$type . '_oembed'          => array(
						'label'   => __( 'Enable oEmbed?', 'pods' ),
						'default' => 0,
						'type'    => 'boolean',
						'help'    => array(
							__( 'Embed videos, images, tweets, and other content.', 'pods' ),
							'http://codex.wordpress.org/Embeds'
						)
					),
					self::$type . '_wptexturize'     => array(
						'label'   => __( 'Enable wptexturize?', 'pods' ),
						'default' => 1,
						'type'    => 'boolean',
						'help'    => array(
							__( 'Transforms less-beautfiul text characters into stylized equivalents.', 'pods' ),
							'http://codex.wordpress.org/Function_Reference/wptexturize'
						)
					),
					self::$type . '_convert_chars'   => array(
						'label'   => __( 'Enable convert_chars?', 'pods' ),
						'default' => 1,
						'type'    => 'boolean',
						'help'    => array(
							__( 'Converts text into valid XHTML and Unicode', 'pods' ),
							'http://codex.wordpress.org/Function_Reference/convert_chars'
						)
					),
					self::$type . '_wpautop'         => array(
						'label'   => __( 'Enable wpautop?', 'pods' ),
						'default' => 1,
						'type'    => 'boolean',
						'help'    => array(
							__( 'Changes double line-breaks in the text into HTML paragraphs', 'pods' ),
							'http://codex.wordpress.org/Function_Reference/wpautop'
						)
					),
					self::$type . '_allow_shortcode' => array(
						'label'      => __( 'Allow Shortcodes?', 'pods' ),
						'default'    => 0,
						'type'       => 'boolean',
						'dependency' => true,
						'help'       => array(
							__( 'Embed [shortcodes] that help transform your static content into dynamic content.', 'pods' ),
							'http://codex.wordpress.org/Shortcode_API'
						)
					)
				)
			),
			self::$type . '_allowed_html_tags' => array(
				'label'      => __( 'Allowed HTML Tags', 'pods' ),
				'depends-on' => array( self::$type . '_allow_html' => true ),
				'default'    => 'strong em a ul ol li b i',
				'type'       => 'text',
				'help'       => __( 'Format: strong em a ul ol li b i', 'pods' )
			),
			self::$type . '_max_length'        => array(
				'label'   => __( 'Maximum Length', 'pods' ),
				'default' => - 1,
				'type'    => 'number',
				'help'    => __( 'Set to -1 for no limit', 'pods' )
			),
			self::$type . '_rows'              => array(
				'label'   => __( 'Rows', 'pods' ),
				'default' => 0,
				'type'    => 'number',
				'help'    => __( 'Customize how many rows the textarea will have.', 'pods' )
			)
		);

		return $options;
	}

	/**
	 * {@inheritDocs}
	 */
	public function schema( $options = null ) {
		$length = (int) pods_v( self::$type . '_max_length', $options, - 1, true );

		$schema = 'LONGTEXT';

		if ( 0 < $length ) {
			if ( $length <= 255 ) {
				$schema = 'VARCHAR(' . (int) $length . ')';
			} elseif ( $length <= 16777215 ) {
				$schema = 'MEDIUMTEXT';
			}
		}

		return $schema;
	}

	/**
	 * {@inheritDocs}
	 */
	public function display( $value = null, $name = null, $options = null, $pod = null, $id = null ) {
		$value = $this->strip_html( $value, $options );

		if ( 1 == pods_v( self::$type . '_oembed', $options, 0 ) ) {
			$embed = $GLOBALS['wp_embed'];
			$value = $embed->run_shortcode( $value );
			$value = $embed->autoembed( $value );
		}

		if ( 1 == pods_v( self::$type . '_wptexturize', $options, 1 ) ) {
			$value = wptexturize( $value );
		}

		if ( 1 == pods_v( self::$type . '_convert_chars', $options, 1 ) ) {
			$value = convert_chars( $value );
		}

		if ( 1 == pods_v( self::$type . '_wpautop', $options, 1 ) ) {
			$value = wpautop( $value );
		}

		if ( 1 == pods_v( self::$type . '_allow_shortcode', $options, 0 ) ) {
			if ( 1 == pods_v( self::$type . '_wpautop', $options, 1 ) ) {
				$value = shortcode_unautop( $value );
			}

			$value = do_shortcode( $value );
		}

		return $value;
	}

	/**
	 * {@inheritDocs}
	 */
	public function input( $name, $value = null, $options = null, $pod = null, $id = null ) {
		$form_field_type = Pods_Form::$field_type;

		if ( is_array( $value ) ) {
			$value = implode( "\n", $value );
		}

		if ( isset( $options['name'] ) && false === Pods_Form::permission( self::$type, $options['name'], $options, null, $pod, $id ) ) {
			if ( pods_v( 'read_only', $options, false ) ) {
				$options['readonly'] = true;
			} else {
				return;
			}
		} elseif ( ! pods_has_permissions( $options ) && pods_v( 'read_only', $options, false ) ) {
			$options['readonly'] = true;
		}

		pods_view( PODS_DIR . 'ui/fields/textarea.php', compact( array_keys( get_defined_vars() ) ) );
	}

	/**
	 * {@inheritDocs}
	 */
	public function pre_save( $value, $id = null, $name = null, $options = null, $fields = null, $pod = null, $params = null ) {
		$value = $this->strip_html( $value, $options );

		$length = (int) pods_v( self::$type . '_max_length', $options, - 1, true );

		if ( 0 < $length ) {
			$value = substr( $value, 0, $length );
		}

		return $value;
	}

	/**
	 * {@inheritDocs}
	 */
	public function ui( $id, $value, $name = null, $options = null, $fields = null, $pod = null ) {
		$value = $this->strip_html( $value, $options );

		$value = wp_trim_words( $value );

		return $value;
	}

	/**
	 * Strip HTML based on options
	 *
	 * @param string $value
	 * @param array  $options
	 *
	 * @return string
	 */
	public function strip_html( $value, $options = null ) {
		if ( is_array( $value ) ) {
			$value = @implode( ' ', $value );
		}

		$value = trim( $value );

		if ( empty( $value ) ) {
			return $value;
		}

		if ( 0 == pods_v( self::$type . '_allow_html', $options ) ) {
			$value = strip_tags( $value );
		} elseif ( 0 < strlen( pods_v( self::$type . '_allowed_html_tags', $options ) ) ) {
			$allowed_tags = pods_v( self::$type . '_allowed_html_tags', $options );
			$allowed_tags = trim( preg_replace( '/[^\<\>\/\,]/', ' ', $allowed_tags ) );
			$allowed_tags = explode( ' ', $allowed_tags );

			// Handle issue with self-closing tags in strip_tags
			// @link http://www.php.net/strip_tags#88991
			if ( in_array( 'br', $allowed_tags ) ) {
				$allowed_tags[] = 'br /';
			}

			if ( in_array( 'hr', $allowed_tags ) ) {
				$allowed_tags[] = 'hr /';
			}

			$allowed_tags = array_unique( array_filter( $allowed_tags ) );

			if ( ! empty( $allowed_tags ) ) {
				$allowed_tags = '<' . implode( '><', $allowed_tags ) . '>';

				$value = strip_tags( $value, $allowed_tags );
			}
		}

		return $value;
	}
}
