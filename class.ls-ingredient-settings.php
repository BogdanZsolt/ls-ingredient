<?php

if( !class_exists( 'LS_Ingredient_Settings' ) ){
	class LS_Ingredient_Settings {
		public static $options;

		public function __construct(){
			self::$options = get_option( 'ls_ingredient_options' );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
		}

		public function admin_init(){
			register_setting( 'ls_ingredient_group', 'ls_ingredient_options', array( $this, 'ls_ingredient_validate' ) );

			add_settings_section(
				'ls_ingredient_main_section',
				esc_html__( 'How does it work?', 'ls-ingredient' ),
				null,
				'ls_ingredient_page1',
			);

			add_settings_section(
				'ls_ingredient_second_section',
				esc_html__( 'Other Plugin Options', 'ls-ingredient' ),
				null,
				'ls_ingredient_page2',
			);

			add_settings_field(
				'ls_ingredient_shortcode',
				esc_html__( 'Shortcode', 'ls-ingredient' ),
				array( $this, 'ls_ingredient_shortcode_callback' ),
				'ls_ingredient_page1',
				'ls_ingredient_main_section',
			);

			add_settings_field(
				'ls_ingredient_filter',
				esc_html__( 'Display Letters Filter', 'ls-ingredient' ),
				array( $this, 'ls_ingredient_filter_callback' ),
				'ls_ingredient_page2',
				'ls_ingredient_second_section',
				array(
					'label_for'	=> 'ls_ingredient_filter',
				),
			);

			add_settings_field(
				'ls_ingredient_style',
				esc_html__( 'Ingredient Style', 'ls-ingredient' ),
				array( $this, 'ls_ingredient_style_callback' ),
				'ls_ingredient_page2',
				'ls_ingredient_second_section',
				array(
					'items'	=> array(
						'style-1',
						'style-2',
						'lasaphire'
					),
					'label_for'	=> 'ls_ingredient_style',
				),
			);
		}

		public function ls_ingredient_shortcode_callback(){
			?>
				<span><?php esc_html_e( 'Use the shortcode [ls_ingredients_list] to display the ingredients list in any page', 'ls-ingredient' ); ?></span>
			<?php
		}

		public function ls_ingredient_filter_callback( $args ){
			?>
				<input
					type="checkbox"
					name="ls_ingredient_options[ls_ingredient_filter]"
					id="ls_ingredient_filter"
					value="1"
					<?php
						if( isset( self::$options['ls_ingredient_filter'] ) ){
							checked( '1', self::$options['ls_ingredient_filter'], true );
						}
					?>
				>
				<label for="ls_ingredient_filter"><?php esc_html_e( 'Whether to display letters filter or not', 'ls-ingredient' ); ?></label>
			<?php
		}

		public function ls_ingredient_style_callback( $args ){
			?>
				<select
					id="ls_ingredient_style"
					name="ls_ingredient_options[ls_ingredient_style]"
				>
				<?php
					foreach( $args['items'] as $item ):
				?>
					<option value="<?php echo esc_attr( $item ); ?>"
					<?php
						isset( self::$options['ls_ingredient_style'] ) ? selected( $item, self::$options['ls_ingredient_style'], true ) : '';
					?>
					>
						<?php echo esc_html( ucfirst( $item ) ); ?>
					</option>
				<?php endforeach; ?>
				</select>
			<?php
		}

		public function ls_ingredient_validate( $input ){
			$new_input = array();
			foreach( $input as $key => $value ){
				$new_input[$key] = sanitize_text_field( $value );
			}
			return $new_input;
		}

	}
}