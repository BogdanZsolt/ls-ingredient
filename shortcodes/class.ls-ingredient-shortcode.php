<?php

if( !class_exists( 'LS_Ingredient_Shortcode' ) ){
	class LS_Ingredient_Shortcode {
		public function __construct(){
			add_shortcode( 'ls_ingredients_list', array( $this, 'add_shortcode' ) );
		}

		public function add_shortcode( $atts = array(), $content = null, $tag = '' ){
			$atts = array_change_key_case( ( array ) $atts, CASE_LOWER );

			extract( shortcode_atts(
				array(
					'id'	=> '',
					'orderby' => 'title',
				),
				$atts,
				$tag,
			) );

			if( !empty( $id ) ){
				$id = array_map( 'absint', explode( ',', $id ) );
			}

			ob_start();
			require( LS_INGREDIENT_PATH . 'views/ls-ingredient_shortcode.php' );
			wp_enqueue_script( 'ls-ingredient-main-js' );
			wp_enqueue_style( 'ls-ingredient-main-css' );
			return ob_get_clean();
		}
	}
}