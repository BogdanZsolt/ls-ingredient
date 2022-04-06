<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">
	<?php
		settings_fields( 'ls_ingredient_group' );
		do_settings_sections( 'ls_ingredient_page1' );
		do_settings_sections( 'ls_ingredient_page2' );
		submit_button( 'Save Settings' );
	?>
	</form>
</div>