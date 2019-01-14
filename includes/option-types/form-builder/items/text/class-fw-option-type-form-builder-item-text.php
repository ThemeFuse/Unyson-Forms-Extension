<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Option_Type_Form_Builder_Item_Text extends FW_Option_Type_Form_Builder_Item {
	public function get_type() {
		return 'text';
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
					'<div class="item-type-icon-title" data-hover-tip="' . __( 'Add a Single Line Text', 'fw' ) . '">' .
					'<div class="item-type-icon"><img src="' . esc_attr( $this->get_uri( '/static/images/icon.png' ) ) . '" /></div>' .
					'<div class="item-type-title">' . __( 'Single Line Text', 'fw' ) . '</div>' .
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

	public function get_item_localization() {
		return array(
			'l10n'     => array(
				'item_title'      => __( 'Single Line Text', 'fw' ),
				'label'           => __( 'Label', 'fw' ),
				'toggle_required' => __( 'Toggle mandatory field', 'fw' ),
				'edit'            => __( 'Edit', 'fw' ),
				'delete'          => __( 'Delete', 'fw' ),
				'edit_label'      => __( 'Edit Label', 'fw' ),
			),
			'options'  => $this->get_options(),
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
								'value' => __( 'Single Line Text', 'fw' ),
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
						array(
							'autocomplete' => array(
								'label'   => __( 'Autocomplete', 'fw' ),
								'desc'    => sprintf( __( 'For a faster and more friendly user interface, you can set the autocomplete behavior for this field according to the %s', 'fw' ), '<a href="https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#attr-fe-autocomplete" target="_blank">spec</a>'),
								'type'    => 'select',
								'value'   => 'off',
								'choices' => array(
									'off' 		=> 'off', // default
									'on'			=> 'on', // automatic
									"name"		=> 'name', //Full name Free-form text, no newlines	Sir Timothy John Berners-Lee, OM, KBE, FRS, FREng, FRSA	Text
									"honorific-prefix" => "honorific-prefix", //Honorific prefix or title, (e.g. "Mr.", "Ms.", "Dr.", "Mlle")	Free-form text, no newlines	Sir	Text
									"given-name" => "given-name", //Given name, (in some Western cultures, also known as the first name)	Free-form text, no newlines	Timothy	Text
									"additional-name" => "additional-name", // Additional names, (in some Western cultures, also known as middle names, forenames other than the first name)	Free-form text, no newlines	John	Text
									"family-name" => "family-name", // Family name, //(in some Western cultures, also known as the last name or surname)	Free-form text, no newlines	Berners-Lee	Text
									"honorific-suffix" => "honorific-suffix", // Honorific suffix, (e.g. "Jr.", "B.Sc.", "MBASW", "II")	Free-form text, no newlines	OM, KBE, FRS, FREng, FRSA	Text
									"nickname" => "nickname", // Nickname, screen name, handle, a typically short name used instead of the full name	Free-form text, no newlines	Tim	Text
									"organization-title" => "organization-title", //Job title (e.g. "Software Engineer", "Senior Vice President", "Deputy Managing Director")	Free-form text, no newlines	Professor	Text
									"organization" => "organization", //Company name, corresponding to the person, address, or contact information in the other fields associated with this field	Free-form text, no newlines	World Wide Web Consortium	Text
									"address-line1"	=> "address-line1", // Street address (one line per field)	Free-form text, no newlines	32 Vassar Street	Text
									"address-line2"	=> "address-line2",  // Free-form text, no newlines	MIT Room 32-G524	Text
									"address-line3" => 	"address-line3", // Free-form text, no newlines Text
									"address-level4" => "address-level4", //most fine-grained administrative level, in addresses with four administrative levels	Free-form text, no newlines Text
									"address-level3" => "address-level3", //The third administrative level, in addresses with three or more administrative levels	Free-form text, no newlines Text
									"address-level2" => "address-level2", //The second administrative level, in addresses with two or more administrative levels; in the countries with two administrative levels, this would typically be the city, town, village, or other locality within which the relevant street address is found	Free-form text, no newlines	Cambridge	Text
									"address-level1" => "address-level1", //The broadest administrative level in the address, i.e. the province within which the locality is found; for example, in the US, this would be the state; in Switzerland it would be the canton; in the UK, the post town	Free-form text, no newlines	MA	Text
									"country" => "country", //	Country code Valid ISO 3166-1-alpha-2 country code [ISO3166]	US	Text
									"country-name" => "country-name", // Country name	//Free-form text, no newlines; derived from country in some cases	US	Text
									"postal-code"	=> "postal-code", // Postal code, post code, ZIP code, CEDEX code (if CEDEX, append "CEDEX", and the arrondissement, if relevant, to the address-level2 field)	Free-form text, no newlines	02139	Text
									"language" => "language", //Preferred language //	Valid BCP 47 language tag [BCP47]	en	Text
									"bday" => "bday", // Birthday	Valid date string	1955-06-08	Date SUITED FOR TEXT FIELD
									"sex"	=> "sex", // Gender identity (e.g. Female, Fa'afafine)	Free-form text, no newlines	Male	Text
									"tel"	=> "tel", // Full telephone number, including country code	ASCII digits and U+0020 SPACE characters, prefixed by a U+002B PLUS SIGN character (+)	+1 617 253 5702	Tel
									"tel-country-code" => "tel-country-code", // Country code component of the telephone number	ASCII digits prefixed by a U+002B PLUS SIGN character (+)	+1	Text
									"tel-national" => "tel-national", // Telephone number without the county code component, with a country-internal prefix applied if applicable	ASCII digits and U+0020 SPACE characters	617 253 5702	Text
									"tel-area-code"	=> "tel-area-code", // Area code component of the telephone number, with a country-internal prefix applied //if applicable	ASCII digits	617	Text
									"tel-local"	=> "tel-local", // Telephone number without the country code and area code components	ASCII digits	2535702	Text
									"tel-local-prefix" =>	"tel-local-prefix", // First part of the component of the telephone number that follows the area code, when that component is split into two components	ASCII digits	253	Text
									"tel-local-suffix" =>	"tel-local-suffix", // Second part of the component of the telephone number that follows the area code, when that component is split into two components	ASCII digits	5702	Text
									"tel-extension" => "tel-extension", //	Telephone number internal extension code	ASCII digits	1000	Text
									"impp" =>	"impp" // URL representing an instant messaging protocol endpoint //(for example, "aim:goim?screenname=example" or "xmpp:fred@example.net")	Valid URL string	irc://example.org/timbl,isuser	URL
								)
							)
						)
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
									'constraint' => 'characters',
								),
								'picker'  => array(
									'constraint' => array(
										'label'   => __( 'Restrictions', 'fw' ),
										'desc'    => __( 'Set characters or words restrictions for this field', 'fw' ),
										'type'    => 'radio',
										'inline'  => true,
										'choices' => array(
											'characters' => __( 'Characters', 'fw' ),
											'words'      => __( 'Words', 'fw' )
										),
									)
								),
								'choices' => array(
									'characters' => array(
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
									'words'      => array(
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
					case 'characters':
					case 'words':
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
				'autocomplete'=> $options['autocomplete'],
				'name'        => $item['shortcode'],
				'placeholder' => $options['placeholder'],
				'value'       => is_null( $input_value ) ? $options['default_value'] : $input_value,
				'id'          => 'id-' . fw_unique_increment(),
			);

			if ( $options['required'] ) {
				$attr['required'] = 'required';
			} else {
				$attr['aria-required'] = 'false';
			}

			if ( ! empty( $options['constraints']['constraint'] ) ) {
				$constraint      = $options['constraints']['constraint'];
				$constraint_data = $options['constraints'][ $constraint ];

				switch ( $constraint ) {
					case 'characters':
					case 'words':
						if ( $constraint_data['min'] || $constraint_data['max'] ) {
							$attr['data-constraint'] = json_encode( array(
								'type' => $constraint,
								'data' => $constraint_data
							) );
						}

						if ( $constraint == 'characters' && $constraint_data['max'] ) {
							$attr['maxlength'] = $constraint_data['max'];
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
			'required'                => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field is required', 'fw' )
			),
			'characters_min_singular' => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must contain minimum %d character', 'fw' )
			),
			'characters_min_plural'   => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must contain minimum %d characters', 'fw' )
			),
			'characters_max_singular' => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must contain maximum %d character', 'fw' )
			),
			'characters_max_plural'   => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must contain maximum %d characters', 'fw' )
			),
			'words_min_singular'      => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must contain minimum %d word', 'fw' )
			),
			'words_min_plural'        => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must contain minimum %d words', 'fw' )
			),
			'words_max_singular'      => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must contain maximum %d word', 'fw' )
			),
			'words_max_plural'        => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field must contain maximum %d words', 'fw' )
			),
		);

		if ( $options['required'] && ! fw_strlen( trim( $input_value ) ) ) {
			return $messages['required'];
		}

		$length = fw_strlen( $input_value );

		if ( $length && ! empty( $options['constraints']['constraint'] ) ) {
			$constraint      = $options['constraints']['constraint'];
			$constraint_data = $options['constraints'][ $constraint ];

			switch ( $constraint ) {
				case 'characters':
					if ( $constraint_data['min'] && $length < $constraint_data['min'] ) {
						return sprintf( $messages[ 'characters_min_' . ( $constraint_data['min'] == 1 ? 'singular' : 'plural' ) ],
							$constraint_data['min']
						);
					}
					if ( $constraint_data['max'] && $length > $constraint_data['max'] ) {
						return sprintf( $messages[ 'characters_max_' . ( $constraint_data['max'] == 1 ? 'singular' : 'plural' ) ],
							$constraint_data['max']
						);
					}
					break;
				case 'words':
					$words_length = count( preg_split( '/\s+/', $input_value ) );

					if ( $constraint_data['min'] && $words_length < $constraint_data['min'] ) {
						return sprintf( $messages[ 'words_min_' . ( $constraint_data['min'] == 1 ? 'singular' : 'plural' ) ],
							$constraint_data['min']
						);
					}
					if ( $constraint_data['max'] && $words_length > $constraint_data['max'] ) {
						return sprintf( $messages[ 'words_max_' . ( $constraint_data['max'] == 1 ? 'singular' : 'plural' ) ],
							$constraint_data['max']
						);
					}
					break;
				default:
					return 'Unknown constraint: ' . $constraint;
			}
		}
	}
}

FW_Option_Type_Builder::register_item_type( 'FW_Option_Type_Form_Builder_Item_Text' );
