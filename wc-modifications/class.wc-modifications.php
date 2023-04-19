<?php

/**
 * La Saphire WooCommerce Ingredients Product Type Modifications class.
*/

if(!class_exists('LS_Ingredient_wc_modifications')){
	class LS_Ingredient_wc_modifications {
		public function __construct(){
			add_filter( 'product_type_options', array($this, 'lasaphire_ingredients'), 10, 1);
			add_action( 'admin_head', array($this, 'wcpp_custom_style'));
			$this->product_actions();
			add_filter( 'woocommerce_product_data_tabs', array($this, 'lasaphire_product_data_tabs'));
			add_action( 'woocommerce_process_product_meta', array($this, 'ingredients_add_custom_general_fields_save'));
			add_action( 'woocommerce_product_data_panels', array($this, 'lasaphire_ingredients_product_data'));
			add_filter( 'woocommerce_product_tabs', array($this, 'lasaphire_ingredients_tab'));
		}

		public function lasaphire_ingredients($tabs){

			$tabs['ingredients'] = array(
				'id' => '_ingredients',
				'label' => esc_html__('Ingredients', 'ls-ingredient'),
				'description' => esc_html__('La Saphire provide access to the ingredient list of products.', 'ls-ingredient'),
				'wrapper_class' => 'show_if_simple show_if_variable',
				'default' => 'no',
			);
			$tabs['virtual']['wrapper_class'] = 'show_if_simple show_if_grouped';
			return $tabs;
		}

		public function wcpp_custom_style(){
			?>
<style>
#woocommerce-product-data ul.wc-tabs,
li.ingredients_options a:before {
  content: '\f175' !important;
}
</style>
<?php
		}

		public function product_actions() {
			// save the new product type meta value
			// 'woocommerce_process_product_meta_' . $product_type
			add_action('woocommerce_process_product_meta_variable', array($this, 'lasaphire_save_ingredients_product_data'), 10, 1);
			add_action('woocommerce_process_product_meta_simple', array($this, 'lasaphire_save_ingredients_product_data'), 10, 1);
		}

		public function lasaphire_save_ingredients_product_data($post_id){
			$enable_ingredients = isset( $_POST['_ingredients'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, '_ingredients', $enable_ingredients);
		}

		public function lasaphire_product_data_tabs( $tabs) {

			$tabs['ingredients'] = array(
				'label'			=> esc_html__('Ingredients', 'ls-ingredients'),
				'target'		=> 'ingredients_product_data',
				'class'  		=> array( 'show_if_ingredients' ),
				'priority'		=> 25,
			);
				return $tabs;
		}

		public function ingredients_add_custom_general_fields_save( $post_id ){

			if( !empty( $_POST['_ingredient_select'] ) ) {
				update_post_meta( $post_id, '_ingredient_select', $_POST['_ingredient_select'] );
			} else {
				update_post_meta( $post_id, '_ingredient_select',  [] );
			}
		}

		public function woocommerce_wp_select_multiple( $field ) {
			global $thepostid, $post;

			$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
			$field['class'] = isset( $field['class'] ) ? $field['class'] : 'select short';
			$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
			$field['name'] = isset( $field['name'] ) ? $field['name'] : $field['id'];
			$field['value'] = isset( $field['value'] ) ? $field['value'] : ( get_post_meta( $thepostid, $field['id'], true ) ? get_post_meta( $thepostid, $field['id'], true ) : array() );

			echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" multiple="multiple">';

			foreach ( $field['options'] as $key => $value ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . ( in_array( $key, $field['value'] ) ? 'selected="selected"' : '' ) . '>' . esc_html( $value ) . '</option>';
			}

			echo '</select> ';

			if ( ! empty( $field['description'] ) ) {
					if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
							echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
					} else {
							echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
					}
			}
			echo '</p>';
		}

		public function lasaphire_ingredients_product_data(){
		?>
<div id="ingredients_product_data" class="panel woocommerce_options_panel">
  <div class="woocommerce_product_tabs wc-metaboxes">
    <?php
			$arg = array(
				'post_type' 		=> 'ls-ingredient',
				'posts_per_page' 	=> -1,
				'orderby'			=> 'title',
				'order'				=> 'ASC'
			);
			$product = wc_get_product();
			// $options = array('' => 'Select Option');
			$ingr = new WP_Query($arg);
			if($ingr->have_posts()){
				// $i=2;
				while($ingr->have_posts()){
					$ingr->the_post();
					$options[get_the_ID()] = get_the_title();
					// $i++;
					wp_reset_postdata();
				}
			}
			$this->woocommerce_wp_select_multiple(
				array(
					'id' => '_ingredient_select',
					'name' => '_ingredient_select[]',
					'label' => esc_html__('Ingredients', 'ls-ingredients'),
					'options' => $options,
					'selected' => true,
					'value' => get_post_meta( $product->id, '_ingredient_select', true ),
					'desc_tip' => true,
					'description' => 'something super description',
					'wrapper_class' => 'form-field-wide',
				)
			);
		?>
  </div>
</div>
<?php
		}

		public function lasaphire_ingredients_product_tab_callback() {
			$product = wc_get_product();
			$html = '<div><h2 class="mb-3">' . esc_html__( 'Product Ingredients', 'ls-ingredients')  . '</h2>';
			$ingr_ids = get_post_meta( $product->id, '_ingredient_select', true);
			if(!empty($ingr_ids)){
				foreach( $ingr_ids as $ingr_id){
					$ingredient = get_post( $ingr_id );
					$latin_name = get_post_meta( $ingr_id, '_ingredient_latin_name', true );
					$html .= '
						<div class="row">
							<div class="col-md-4">
								<h4>' . $ingredient->post_title . '</h4>
								<p>' . $latin_name . '</p>
							</div>
							<div class="col-md-8">'
								. $ingredient->post_content .
							'</div>
						</div>
					';
				}
			} else {
				$html .= '<p class="mb-5">The ingredient list is empty</p>';
			}
			$html .= '</div>';
			echo $html;
		}


		public function lasaphire_ingredients_tab( $tabs ) {

			// Adds the new tab

			// $tabs['ingredients'] = array(
      //   'title' => __('Szállítás és kézbesítés', 'ls-ingredients'),
      //   'priority' => 50,
      //   'callback' => array($this, 'lasaphire_ingredients_tab_callback'),
    	// );
			$tabs['ingredients'] = array(
				'title' => __('Ingredients', 'ls-ingredients'),
				'priority' => 15,
				'callback' => array($this, 'lasaphire_ingredients_product_tab_callback'),
			);
    	return $tabs;

		}


	}
}