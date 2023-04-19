<?php

function get_ingredient_product_list( $ingr ){
	$args = array(
		'post_type' 			=> 'product',
		'post_status'			=> 'publish',
		'ignore_sticky_posts'	=> 1,
		'posts_per_page' 		=> -1,
		'orderby'				=> 'title',
		'order'					=> 'ASC',
		'meta_query'			=> array(
			array(
				'key'				=> '_ingredients',
				'value'				=> 'yes',
				'compare'			=> '=',
			),
		),
	);
	$loop = new WP_Query( $args );
	$product_list = [];
	$index = 0;
	if( !empty( $loop->posts ) ){
		foreach( $loop->posts as $post){
			$selected_ingredients = get_post_meta( $post->ID, '_ingredient_select', true);
			if(!empty($selected_ingredients)){
				foreach($selected_ingredients as $selected_ingredient){
					if ($selected_ingredient == $ingr->ID)	{
						$product_list[$index] = $post->ID;
						$index++;
					}
				}
			}
		}
		wp_reset_postdata();
	}
	return $product_list;
}

if( LS_Ingredient_Settings::$options['ls_ingredient_filter'] ){
	$return_html = '';
	$return_html .= '
	<div id="ls-ingredient" class="' . ( isset( LS_Ingredient_Settings::$options['ls_ingredient_style'] ) ? esc_attr( LS_Ingredient_Settings::$options['ls_ingredient_style'] ) : 'style-1' ) . '">
		<ul class="ingredient-filter">
			<li>
				<a class="active" title="Display all components" data-filter="cat-*">'
					. esc_html__( 'All', 'ls-ingredient' ) .
				'</a>
			</li>
	';
	foreach( range('A', 'Z') as $letter ){
		$return_html .=
		'<li>
		<a class="disabled" title="Display the component that begins with a letter ' . $letter . '" data-filter="cat-' . strtolower( $letter ) . '">' . $letter . '</a>
		</li>';
	}
	$return_html .= '</ul>';
}

$args = array(
	'post_type'	=> 'ls-ingredient',
	'post_status' => 'publish',
	'posts_per_page'	=> -1,
	'orderby'	=> $orderby,
	'order'	=> 'ASC',
);

$my_query = new WP_Query( $args );

if( !empty( $my_query->posts ) ):
	foreach( $my_query->posts as $post ) :
		$latin_name = esc_html( get_post_meta( $post->ID, 'ls_ingredient_latin_name', true ) );
		$img = get_the_post_thumbnail( $post->ID, array( '75' ), array( 'class' => 'img-fluid' ) );
		$ing_products = get_ingredient_product_list( $post );
		$return_html .= '
				<div class="ingredient-wrapper onvisible">
					<div class="ingredient-wrapper--photo">
					' . $img . '
					</div>
					<div class="ingredient-wrapper--title">
						<h4>' . $post->post_title . '</h4>
						<p>' . $latin_name . '</p>
					</div>
					<div class="ingredient-wrapper--content">
						<p>' . $post->post_content . '</p>
					</div>
					<div class="ingredient-wrapper--products">
						<h6>' . __( 'Contains', 'ls-ingredient' ) . ': </h6>
							<ul class="found-in">';
							foreach($ing_products as $ing_product){
								$product = wc_get_product($ing_product);
								$return_html .= '<li><a href="' . $product->get_permalink() . '">' . $product->get_title() . '</a></li>';
							}
						$return_html .= '</ul>
					</div>
				</div>
				';
			endforeach;
			wp_reset_postdata();
		endif;
	$return_html .= '</div>';

echo $return_html;
?>