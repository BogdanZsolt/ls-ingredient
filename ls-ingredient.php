<?php

/**
	* Plugin Name: LS Ingredient
	* Plugin URI: https://lasaphire.hu/ingredients/
	* Description: A plugin for beauty ingredients
	* Version: 1.0
	* Requires at least: 5.6
	* Author: Zsolt Bogdán
	* Author URI: https://zsoltbogdan.hu
	* License: GPL v3 or later
	* License URI: https://www.gnu.org/licenses/gpl-3.0.html
	* Text Domain: ls-ingredient
	* Domain Path: /languages
*/

if( !defined( 'ABSPATH' ) ){
	exit;
}

if( !class_exists( 'LS_Ingredient' ) ){
	class LS_Ingredient {
		function __construct(){
			$this->define_constants();

			add_action( 'admin_menu', array( $this, 'add_menu' ) );

			require_once( LS_INGREDIENT_PATH . 'post-types/class.ls-ingredient-cpt.php' );
			$LS_Ingredient_Post_Type = new LS_Ingredient_Post_Type();

			require_once( LS_INGREDIENT_PATH . 'class.ls-ingredient-settings.php' );
			$LS_Ingredient_Settings = new LS_Ingredient_Settings();
		}

		public function define_constants(){
			define( 'LS_INGREDIENT_PATH', plugin_dir_path( __FILE__ ) );
			define( 'LS_INGREDIENT_URL', plugin_dir_url( __FILE__ ) );
			define( 'LS_INGREDIENT_VERSION', '1.0.0' );
		}

		public static function activate(){
			update_option( 'rewrite_rules', '' );
		}

		public static function deactivate(){
			flush_rewrite_rules();
			unregister_post_type( 'ls-ingredient' );
		}

		public static function uninstall(){

		}

		public function add_menu(){
			add_menu_page(
				'Ingredients Options',
				'Ingredients',
				'manage_options',
				'ls_ingredient_admin',
				array( $this, 'ls_ingredient_settings_page' ),
				'dashicons-analytics',
			);

			add_submenu_page(
				'ls_ingredient_admin',
				'Manage Ingredients',
				'Manage Ingredients',
				'manage_options',
				'edit.php?post_type=ls-ingredient',
				null,
				null,
			);

			add_submenu_page(
				'ls_ingredient_admin',
				'Add New Ingredient',
				'Add New Ingredient',
				'manage_options',
				'post-new.php?post_type=ls-ingredient',
				null,
				null,
			);
		}

		public function ls_ingredient_settings_page(){
			if( !current_user_can( 'manage_options' ) ){
				return;
			}
			if( isset( $_GET['settings-updated'] ) ){
				add_settings_error( 'ls_ingredient_options', 'ls_ingredient_message', 'Settings Saved', 'success' );
			}
			settings_errors( 'ls_ingredient_options' );
			require( LS_INGREDIENT_PATH . 'views/settings-page.php' );
		}
	}
}

if( class_exists( 'LS_Ingredient' ) ){
	register_activation_hook( __FILE__ , array( 'LS_Ingredient', 'activate' ) );
	register_deactivation_hook( __FILE__ , array( 'LS_Ingredient', 'deactivate' ) );
	register_uninstall_hook( __FILE__ , array( 'LS_Ingredient', 'uninstall' ) );
	$ls_ingredient = new LS_Ingredient();
}