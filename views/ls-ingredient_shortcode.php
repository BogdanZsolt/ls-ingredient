<?php if( LS_Ingredient_Settings::$options['ls_ingredient_filter'] ){
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
					</div>
				</div>
				';
			endforeach;
			wp_reset_postdata();
		endif;
	$return_html .= '</div>';

echo $return_html;
?>