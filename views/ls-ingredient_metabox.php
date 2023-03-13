<?php
	$latin_name = get_post_meta( $post->ID, 'ls_ingredient_latin_name', true );
	// var_dump( $meta );
?>
<table class="form-table ls-ingredient-metabox">
	<input type="hidden" name="ls_ingredient_nonce" value="<?php echo wp_create_nonce( 'ls_ingredient_nonce' ); ?>">
	<tr>
		<th>
			<label for="ls_ingredient_latin_name"><?php esc_html_e( 'Latin Name', 'ls-ingredient'); ?></label>
		</th>
		<td>
			<input
				type="text"
				name="ls_ingredient_latin_name"
				id="ls_ingredient_latin_name"
				class="regular-text latin-name"
				value="<?php echo ( isset( $latin_name ) ) ? esc_html( $latin_name ) : ''; ?>"
				required
			>
		</td>
	</tr>
</table>