<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Option_Type_Form_Builder_Item_Number extends FW_Option_Type_Form_Builder_Item {
	protected $number_regex = '/^\-?([\d]+)?[,\.]?([\d]+)?$/';

	public function get_type() {
		return 'number';
	}

	private function get_uri( $append = '' ) {
		return fw_get_framework_directory_uri(
			'/extensions/forms/includes/option-types/' .
			$this->get_builder_type() . '/items/' .
			$this->get_type() . $append
		);
	}

	public function get_thumbnails() {
		return array(
			array(
				'html' =>
					'<div class="item-type-icon-title" data-hover-tip="' . __( 'Add a Number field', 'fw' ) . '">' .
					'<div class="item-type-icon"><img src="' . esc_attr( $this->get_uri( '/static/images/icon.png' ) ) . '" /></div>' .
					'<div class="item-type-title">' . __( 'Number', 'fw' ) . '</div>' .
					'</div>'
			)
		);
	}

	public function enqueue_static() {
		wp_enqueue_style(
			'fw-builder-' . $this->get_builder_type() . '-item-' . $this->get_type(),
			$this->get_uri( '/static/css/styles.css' )
		);

		wp_enqueue_script(
			'fw-builder-' . $this->get_builder_type() . '-item-' . $this->get_type(),
			$this->get_uri( '/static/js/scripts.js' ),
			array(
				'fw-events',
			),
			false,
			true
		);

		fw()->backend->enqueue_options_static( $this->get_options() );
	}

	/**
	 * @since 1.0.2
	 */
	public function get_item_localization() {
		return array(
			'options'  => $this->get_options(),
			'l10n'     => array(
				'item_title'      => __( 'Number', 'fw' ),
				'label'           => __( 'Label', 'fw' ),
				'edit_label'      => __( 'Edit Label', 'fw' ),
				'toggle_required' => __( 'Toggle mandatory field', 'fw' ),
				'edit'            => __( 'Edit', 'fw' ),
				'delete'          => __( 'Delete', 'fw' )
			),
			'defaults' => array(
				'type'    => $this->get_type(),
				'width'   => fw_ext( 'forms' )->get_config( 'items/width' ),
				'options' => fw_get_options_values_from_input( $this->get_options(), array() )
			)
		);
	}

	private function get_options() {
		return array(
			array(
				'g1' => array(
					'type'    => 'group',
					'options' => array(
						array(
							'label' => array(
								'type'  => 'text',
								'label' => __( 'Label', 'fw' ),
								'desc'  => __( 'Enter field label (it will be displayed on the web site)', 'fw' ),
								'value' => __( 'Number', 'fw' ),
							)
						),
						array(
							'required' => array(
								'type'  => 'switch',
								'label' => __( 'Mandatory Field', 'fw' ),
								'desc'  => __( 'Make this field mandatory?', 'fw' ),
								'value' => true,
							)
						),
					)
				)
			),
			array(
				'g2' => array(
					'type'    => 'group',
					'options' => array(
						array(
							'placeholder' => array(
								'type'  => 'text',
								'label' => __( 'Placeholder', 'fw' ),
								'desc'  => __( 'This text will be used as field placeholder', 'fw' ),
							)
						),
						array(
							'default_value' => array(
								'type'  => 'text',
								'label' => __( 'Default Value', 'fw' ),
								'desc'  => __( 'This text will be used as field default value', 'fw' ),
							)
						)
					)
				)
			),
			array(
				'g3' => array(
					'type'    => 'group',
					'options' => array(
						array(
							'constraints' => array(
								'type'    => 'multi-picker',
								'label'   => false,
								'desc'    => false,
								'value'   => array(
									'constraint' => 'value',
								),
								'picker'  => array(
									'constraint' => array(
										'label'   => __( 'Restrictions', 'fw' ),
										'desc'    => __( 'Set digits or values restrictions of this field', 'fw' ),
										'type'    => 'radio',
										'inline'  => true,
										'choices' => array(
											'digits' => __( 'Digits', 'fw' ),
											'value'  => __( 'Value', 'fw' )
										),
									)
								),
								'choices' => array(
									'digits' => array(
										'min' => array(
											'type'  => 'short-text',
											'label' => __( 'Min', 'fw' ),
											'desc'  => __( 'Minim value', 'fw' ),
											'value' => 0
										),
										'max' => array(
											'type'  => 'short-text',
											'label' => __( 'Max', 'fw' ),
											'desc'  => __( 'Maxim value', 'fw' ),
											'value' => ''
										),
									),
									'value'  => array(
										'min' => array(
											'type'  => 'text',
											'label' => __( 'Min', 'fw' ),
											'desc'  => __( 'Minim value', 'fw' ),
											'value' => 0
										),
										'max' => array(
											'type'  => 'text',
											'label' => __( 'Max', 'fw' ),
											'desc'  => __( 'Maxim value', 'fw' ),
											'value' => ''
										),
									),
								),
							)
						),
					)
				)
			),
			array(
				'g4' => array(
					'type'    => 'group',
					'options' => array(
						array(
							'info' => array(
								'type'  => 'textarea',
								'label' => __( 'Instructions for Users', 'fw' ),
								'desc'  => __( 'The users will see these instructions in the tooltip near the field',
									'fw' ),
							)
						),
					)
				)
			),
			$this->get_extra_options()
		);
	}

	protected function get_fixed_attributes( $attributes ) {
		// do not allow sub items
		unset( $attributes['_items'] );

		$default_attributes = array(
			'type'      => $this->get_type(),
			'shortcode' => false, // the builder will generate new shortcode if this value will be empty()
			'width'     => '',
			'options'   => array()
		);

		// remove unknown attributes
		$attributes = array_intersect_key( $attributes, $default_attributes );

		$attributes = array_merge( $default_attributes, $attributes );

		/**
		 * Fix $attributes['options']
		 * Run the _get_value_from_input() method for each option
		 */
		{
			$only_options = array();

			foreach ( fw_extract_only_options( $this->get_options() ) as $option_id => $option ) {
				if ( array_key_exists( $option_id, $attributes['options'] ) ) {
					$option['value'] = $attributes['options'][ $option_id ];
				}
				$only_options[ $option_id ] = $option;
			}

			$attributes['options'] = fw_get_options_values_from_input( $only_options, array() );

			unset( $only_options, $option_id, $option );
		}

		{
			$constraints = $attributes['options']['constraints'];

			if ( ! empty( $constraints['constraint'] ) ) {
				$constraint      = $constraints['constraint'];
				$constraint_data = $constraints[ $constraint ];

				switch ( $constraint ) {
					case 'digits':
						if ( ! empty( $constraint_data['min'] ) ) {
							$constraint_data['min'] = intval( $constraint_data['min'] );

							if ( $constraint_data['min'] < 0 ) {
								$constraint_data['min'] = 0;
							}
						}

						if ( ! empty( $constraint_data['max'] ) ) {
							$constraint_data['max'] = intval( $constraint_data['max'] );

							if ( $constraint_data['max'] < 0 || $constraint_data['max'] < $constraint_data['min'] ) {
								$constraint_data['max'] = null;
							}
						}
						break;
					case 'value':
						if ( $constraint_data['min'] === '' || ! preg_match( $this->number_regex,
								$constraint_data['min'] )
						) {
							$constraint_data['min'] = null;
						} else {
							$constraint_data['min'] = doubleval( $constraint_data['min'] );
						}

						if ( $constraint_data['max'] === '' || ! preg_match( $this->number_regex,
								$constraint_data['max'] )
						) {
							$constraint_data['max'] = null;
						} else {
							$constraint_data['max'] = doubleval( $constraint_data['max'] );
						}

						if ( ! is_null( $constraint_data['max'] ) && ! is_null( $constraint_data['min'] ) ) {
							if ( $constraint_data['max'] < $constraint_data['min'] ) {
								$constraint_data['max'] = null;
							}
						}
						break;
					default:
						trigger_error( 'Invalid constraint: ' . $constraint, E_USER_WARNING );
						$attributes['options']['constraints']['constraint'] = '';
				}

				$attributes['options']['constraints'][ $constraint ] = $constraint_data;
			}
		}

		return $attributes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_value_from_attributes( $attributes ) {
		return $this->get_fixed_attributes( $attributes );
	}

	/**
	 * {@inheritdoc}
	 */
	public function frontend_render( array $item, $input_value ) {
		$options = $item['options'];

		// prepare attributes
		{
			$attr = array(
				'type'        => 'text',
				'name'        => $item['shortcode'],
				'placeholder' => $options['placeholder'],
				'value'       => is_null( $input_value ) ? $options['default_value'] : $input_value,
				'id'          => 'id-' . fw_unique_increment(),
			);

			if ( $options['required'] ) {
				$attr['required'] = 'required';
			}

			if ( ! empty( $options['constraints']['constraint'] ) ) {
				$constraint      = $options['constraints']['constraint'];
				$constraint_data = $options['constraints'][ $constraint ];

				switch ( $constraint ) {
					case 'digits':
						if ( $constraint_data['min'] || $constraint_data['max'] ) {
							$attr['data-constraint'] = json_encode( array(
								'type' => $constraint,
								'data' => $constraint_data
							) );
						}

						if ( $constraint_data['max'] ) {
							$attr['maxlength'] = $constraint_data['max'];
						}
						break;
					case 'value':
						if ( ! is_null( $constraint_data['min'] ) || ! is_null( $constraint_data['max'] ) ) {
							$attr['data-constraint'] = json_encode( array(
								'type' => $constraint,
								'data' => $constraint_data
							) );
						}
						break;
					default:
						trigger_error( 'Unknown constraint: ' . $constraint, E_USER_WARNING );
				}
			}
		}

		return fw_render_view(
			$this->locate_path( '/views/view.php', dirname( __FILE__ ) . '/view.php' ),
			array(
				'item' => $item,
				'attr' => $attr,
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function frontend_validate( array $item, $input_value ) {
		$options = $item['options'];

		$messages = array(
			'invalid'             => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must be a valid number', 'fw' )
			),
			'required'            => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field is required', 'fw' )
			),
			'digits_min_singular' => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must have minimum %d digit', 'fw' )
			),
			'digits_min_plural'   => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must have minimum %d digits', 'fw' )
			),
			'digits_max_singular' => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must have maximum %d digit', 'fw' )
			),
			'digits_max_plural'   => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must have maximum %d digits', 'fw' )
			),
			'value_min'           => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field minimum value must be %s', 'fw' )
			),
			'value_max'           => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field maximum value must be %s', 'fw' )
			),
		);

		if ( $options['required'] && ! fw_strlen( trim( $input_value ) ) ) {
			return $messages['required'];
		}

		if ( ! empty( $input_value ) && ! preg_match( $this->number_regex, $input_value ) ) {
			return $messages['invalid'];
		}

		if ( ! empty( $input_value ) && ! empty( $options['constraints']['constraint'] ) ) {
			$constraint      = $options['constraints']['constraint'];
			$constraint_data = $options['constraints'][ $constraint ];

			switch ( $constraint ) {
				case 'digits':
					$length = fw_strlen( str_replace( array( ',', '.', '-' ), '', $input_value ) );

					if ( $constraint_data['min'] && $length < $constraint_data['min'] ) {
						return sprintf( $messages[ 'digits_min_' . ( $constraint_data['min'] == 1 ? 'singular' : 'plural' ) ],
							$constraint_data['min']
						);
					}
					if ( $constraint_data['max'] && $length > $constraint_data['max'] ) {
						return sprintf( $messages[ 'digits_max_' . ( $constraint_data['max'] == 1 ? 'singular' : 'plural' ) ],
							$constraint_data['max']
						);
					}
					break;
				case 'value':
					$value = doubleval( $input_value );

					if ( ! is_null( $constraint_data['min'] ) && $value < $constraint_data['min'] ) {
						return sprintf( $messages['value_min'],
							$constraint_data['min'],
							$constraint_data['min'] == 1 ? '' : 's'
						);
					}
					if (
						! is_null( $constraint_data['max'] )
						&&
						$constraint_data['min'] !== $constraint_data['max']
						&&
						$value > $constraint_data['max']
					) {
						return sprintf( $messages['value_max'],
							$constraint_data['max'],
							$constraint_data['max'] == 1 ? '' : 's'
						);
					}
					break;
				default:
					return 'Unknown constraint: ' . $constraint;
			}
		}
	}
}

FW_Option_Type_Builder::register_item_type( 'FW_Option_Type_Form_Builder_Item_Number' );
