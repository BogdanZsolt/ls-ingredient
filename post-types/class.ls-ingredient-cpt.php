<?php

if( !class_exists( 'LS_Ingredient_Post_type' ) ){
	class LS_Ingredient_Post_type {
		function __construct(){
			add_action( 'init', array( $this, 'create_post_type' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_post' ), 10, 1 );
			add_filter( 'manage_ls-ingredient_posts_columns', array( $this, 'ls_ingredient_cpt_columns' ) );
			add_action( 'manage_ls-ingredient_posts_custom_column', array( $this, 'ls_ingredient_custom_columns' ), 10, 2 );
			add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );
			add_filter( 'manage_edit-ls-ingredient_sortable_columns', array( $this, 'ls_ingredient_sortable_columns' ) );
		}

		public function create_post_type(){
			register_post_type(
				'ls-ingredient',
				array(
					'label'	=> esc_html__( 'Ingredient', 'ls-ingredient' ),
					'description'	=> esc_html__( 'Ingredients', 'ls-ingredient' ),
					'labels'	=> array(
						'name'	=> esc_html__( 'Ingredients', 'ls-ingredient' ),
						'singular_name'	=> esc_html__( 'Ingredient', 'ls-ingredient' ),
					),
					'public'	=> true,
					'supports'	=> array( 'title', 'editor', 'thumbnail' ),
					'hierarchical'	=> false,
					'show_ui' => true,
					'show_in_menu'	=> false,
					'menu_position' => 5,
					'show_in_admin_bar'	=> true,
					'show_in_nav_menus'	=> true,
					'can_export'	=> true,
					'has_archive'	=> false,
					'exclude_from_search'	=> false,
					'publicly_queryable'	=> true,
					'show_in_rest'	=> true,
					'menu_icon'	=>	'dashicons-analytics',
					// 'regiter_meta_box_cb' => array( $this, 'add_meta_boxes' ),
				)
			);
		}

		public function post_row_actions( $actions, $post ){
			if( $post->post_type === 'ls-ingredient' ){
				$actions['id'] = 'ID: ' . $post->ID;
			}
			return $actions;
		}

		public function ls_ingredient_custom_columns( $column, $post_id ){
			switch( $column ){
				case 'ls_ingredient_latin_name':
					echo esc_html( get_post_meta( $post_id, 'ls_ingredient_latin_name', true ) );
				break;
				case 'ls_ingredient_featured_image':
					the_post_thumbnail( array(50) );
				break;
			}
		}

		public function ls_ingredient_cpt_columns( $columns ){
			unset( $columns['date'] );
			$columns['ls_ingredient_latin_name'] = esc_html__( 'Latin Name', 'ls-ingredient' );
			$columns['ls_ingredient_featured_image'] = esc_html__( 'Featured image', 'ls-ingredient' );
			$columns['date'] = esc_html__( 'Date', 'ls-ingredient' );
			return $columns;
		}

		public function ls_ingredient_sortable_columns( $columns ){
			$columns['ls_ingredient_latin_name'] = 'ls_ingredient_latin_name';
			return $columns;
		}

		public function add_meta_boxes(){
			add_meta_box(
				'ls_ingredient_meta_box',
				esc_html__( 'Ingredient Options', 'ls-ingredient'),
				array( $this, 'add_inner_meta_boxes' ),
				'ls-ingredient',
				'normal',
				'high',
			);
		}

		public function add_inner_meta_boxes( $post ){
			require_once( LS_INGREDIENT_PATH . 'views/ls-ingredient_metabox.php');
		}

		public function save_post( $post_id ){
			if( isset( $_POST['ls_ingredient_nonce'] ) ){
				 if( !wp_verify_nonce( $_POST['ls_ingredient_nonce'], 'ls_ingredient_nonce' ) ){
						return;
					}
			}
			if( defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
				return;
			}

			if( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'ls-ingredient' ){
				if( !current_user_can( 'edit_page', $post_id ) ){
					return;
				} elseif( !current_user_can( 'edit_post', $post_id ) ){
					return;
				}
			}

			if( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ){
				$old_latin_name = get_post_meta( $post_id, 'ls_ingredient_latin_name', true );
				$new_latin_name = sanitize_text_field( $_POST['ls_ingredient_latin_name'] );

				if( empty( $new_latin_name ) ){
					update_post_meta( $post_id, 'ls_ingredient_latin_name', esc_html__( 'Add name', 'ls-ingredient') );
				} else {
					update_post_meta( $post_id, 'ls_ingredient_latin_name', $new_latin_name, $old_latin_name );
				}

			}
		}
	}
}