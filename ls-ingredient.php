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

			$this->load_textdomain();

			add_action( 'admin_menu', array( $this, 'add_menu' ) );

			require_once( LS_INGREDIENT_PATH . 'post-types/class.ls-ingredient-cpt.php' );
			$LS_Ingredient_Post_Type = new LS_Ingredient_Post_Type();

			require_once( LS_INGREDIENT_PATH . 'class.ls-ingredient-settings.php' );
			$LS_Ingredient_Settings = new LS_Ingredient_Settings();

			require_once( LS_INGREDIENT_PATH . 'shortcodes/class.ls-ingredient-shortcode.php' );
			$LS_Ingredient_Shortcode = new LS_Ingredient_Shortcode();

			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 999 );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts') );
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
			delete_option( 'ls_ingredient_options');

			$posts = get_posts(
				array(
					'post_type'	=> 'ls-ingredient',
					'number_posts'	=> -1,
					'post_status' => 'any'
				)
			);

			foreach( $posts as $post ){
				wp_delete_post( $post->ID, true );
			}
		}

		public function load_textdomain(){
			load_plugin_textdomain(
				'ls-ingredient',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages/'
			);
		}

		public function add_menu(){
			add_menu_page(
				esc_html__( 'Ingredients Options', 'ls-ingredient' ),
				esc_html__( 'Ingredients', 'ls-ingredient' ),
				'manage_options',
				'ls_ingredient_admin',
				array( $this, 'ls_ingredient_settings_page' ),
				'dashicons-analytics',
			);

			add_submenu_page(
				'ls_ingredient_admin',
				esc_html__( 'Manage Ingredients', 'ls-ingredient' ),
				esc_html__( 'Manage Ingredients', 'ls-ingredient' ),
				'manage_options',
				'edit.php?post_type=ls-ingredient',
				null,
				null,
			);

			add_submenu_page(
				'ls_ingredient_admin',
				esc_html__( 'Add New Ingredient', 'ls-ingredient' ),
				esc_html__( 'Add New Ingredient', 'ls-ingredient' ),
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
				add_settings_error( 'ls_ingredient_options', 'ls_ingredient_message', esc_html__( 'Settings Saved ', 'ls-ingredient' ), 'success' );
			}
			settings_errors( 'ls_ingredient_options' );
			require( LS_INGREDIENT_PATH . 'views/settings-page.php' );
		}

		public function register_scripts(){
			wp_register_script( 'ls-ingredient-main-js', LS_INGREDIENT_URL . 'build/index.js', array(), LS_INGREDIENT_VERSION, true );
			wp_register_style( 'ls-ingredient-main-css', LS_INGREDIENT_URL . 'build/index.css', array(), LS_INGREDIENT_VERSION, 'all' );
		}

		public function register_admin_scripts(){
			global $typenow;
			if( $typenow == 'ls-ingredient'){
				wp_enqueue_style( 'ls-ingredient-admin', LS_INGREDIENT_URL . 'build/style-index.css' );
			}
		}
	}
}

if( class_exists( 'LS_Ingredient' ) ){
	register_activation_hook( __FILE__ , array( 'LS_Ingredient', 'activate' ) );
	register_deactivation_hook( __FILE__ , array( 'LS_Ingredient', 'deactivate' ) );
	register_uninstall_hook( __FILE__ , array( 'LS_Ingredient', 'uninstall' ) );
	$ls_ingredient = new LS_Ingredient();
}