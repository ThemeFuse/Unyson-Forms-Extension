<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Option_Type_Form_Builder_Item_Select extends FW_Option_Type_Form_Builder_Item {
	public function get_type() {
		return 'select';
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
					'<div class="item-type-icon-title" data-hover-tip="' . __( 'Add a Dropdown', 'fw' ) . '">' .
					'<div class="item-type-icon">' .
					'<img src="' . esc_attr( $this->get_uri( '/static/images/icon.png' ) ) . '" />' .
					'</div>' .
					'<div class="item-type-title">' . __( 'Dropdown', 'fw' ) . '</div>' .
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
			'l10n'     => array(
				'item_title'      => __( 'Dropdown', 'fw' ),
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
								'value' => __( 'Dropdown', 'fw' ),
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
								'desc'    => sprintf( __( 'For a faster and more friendly user interface, you can set the autocomplete behavior according to the %s', 'fw' ), '<a href="https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#attr-fe-autocomplete" target="_blank">spec</a>'),
								'type'    => 'select',
								'value'   => 'off',
								'choices' => array(
									'off' 		=> 'off', // default
									'on'			=> 'on',
									"honorific-prefix" => "honorific-prefix", //Honorific prefix or title, (e.g. "Mr.", "Ms.", "Dr.", "Mlle")	Free-form text, no newlines	Sir	Text
									"honorific-suffix" => "honorific-suffix", // Honorific suffix, (e.g. "Jr.", "B.Sc.", "MBASW", "II")	Free-form text, no newlines	OM, KBE, FRS, FREng, FRSA	Text
									"organization-title" => "organization-title", //Job title (e.g. "Software Engineer", "Senior Vice President", "Deputy Managing Director")	Free-form text, no newlines	Professor	Text
									"country" => "country", //	Country code Valid ISO 3166-1-alpha-2 country code [ISO3166]	US	Text
									"country-name" => "country-name", // Country name	//Free-form text, no newlines; derived from country in some cases	US	Text
									"postal-code"	=> "postal-code", // Postal code, post code, ZIP code, CEDEX code (if CEDEX, append "CEDEX", and the arrondissement, if relevant, to the address-level2 field)	Free-form text, no newlines	02139	Text
									"language" => "language", //Preferred language //	Valid BCP 47 language tag [BCP47]	en	Text
									"bday-day" => "bday-day", // Day component of birthday	//Valid integer in the range 1..31	8	Numeric
									"bday-month" => "bday-month", // Month component of birthday	Valid integer in the range 1..12	6	Numeric
									"bday-year" => "bday-year", // Year component of birthday	Valid integer greater than zero	1955	Numeric
									"sex"	=> "sex", // Gender identity (e.g. Female, Fa'afafine)	Free-form text, no newlines	Male	Text
									"tel-country-code" => "tel-country-code", // Country code component of the telephone number	ASCII digits prefixed by a U+002B PLUS SIGN character (+)	+1	Text
									"tel-area-code"	=> "tel-area-code", // Area code component of the telephone number, with a country-internal prefix applied //if applicable	ASCII digits	617	Text
									"tel-local-prefix" =>	"tel-local-prefix", // First part of the component of the telephone number that follows the area code, when that component is split into two components	ASCII digits	253	Text
									"tel-local-suffix" =>	"tel-local-suffix", // Second part of the component of the telephone number that follows the area code, when that component is split into two components	ASCII digits	5702	Text
									"tel-extension" => "tel-extension", //	Telephone number internal extension code	ASCII digits	1000	Text
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
							'choices' => array(
								'type'   => 'addable-option',
								'label'  => __( 'Choices', 'fw' ),
								'desc'   => __( 'Add choice', 'fw' ),
								'option' => array(
									'type' => 'text',
								),
							)
						),
						array(
							'randomize' => array(
								'type'  => 'switch',
								'label' => __( 'Randomize', 'fw' ),
								'desc'  => __( 'Do you want choices to be displayed in random order?', 'fw' ),
								'value' => false,
							)
						),
					)
				)
			),
			array(
				'info' => array(
					'type'  => 'textarea',
					'label' => __( 'Instructions for Users', 'fw' ),
					'desc'  => __( 'The users will see these instructions in the tooltip near the field', 'fw' ),
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

		if ( empty( $attributes['options']['choices'] ) ) {
			$attributes['options']['choices'][] = __( 'Dropdown', 'fw' );
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

		$value = (string) $input_value;

		// prepare choices
		{
			$choices = array();

			foreach ( $options['choices'] as $choice ) {
				$attr = array(
					'value' => $choice,
				);

				if ( $choice === $value ) {
					$attr['selected'] = 'selected';
				}

				$choices[] = $attr;
			}

			if ( $options['randomize'] ) {
				shuffle( $choices );
			}
		}

		$required = array();
		if ( $options['required'] ) {
			$required['required'] = 'required';
		} else {
			$required['aria-required'] = 'false';
		}		

		// allow users to customize frontend static
		require ( $this->locate_path( '/static.php' , dirname( __FILE__ ) . '/static.php' ) );
		
		return fw_render_view(
			$this->locate_path( '/views/view.php', dirname( __FILE__ ) . '/view.php' ),
			array(
				'item'    => $item,
				'choices' => $choices,
				'value'   => $value,
				'attr'    => array_merge(array(
					'name' => $item['shortcode'],
					'autocomplete' => $options['autocomplete'],
					'id'   => 'id-' . fw_unique_increment(),
				), $required)
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function frontend_validate( array $item, $input_value ) {
		$options = $item['options'];

		$messages = array(
			'required'            => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( 'The {label} field is required', 'fw' )
			),
			'not_existing_choice' => str_replace(
				array( '{label}' ),
				array( $options['label'] ),
				__( '{label}: Submitted data contains not existing choice', 'fw' )
			),
		);

		if ( empty( $options['choices'] ) ) {
			// the item was not displayed in frontend
			return;
		}

		if ( $options['required'] && empty( $input_value ) ) {
			return $messages['required'];
		}

		// check if has not existing choices
		if ( ! empty( $input_value ) && ! in_array( $input_value, $options['choices'] ) ) {
			return $messages['not_existing_choice'];
		}
	}
}

FW_Option_Type_Builder::register_item_type( 'FW_Option_Type_Form_Builder_Item_Select' );
