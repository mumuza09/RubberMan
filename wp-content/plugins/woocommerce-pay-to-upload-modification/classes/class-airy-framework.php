<?php
/**
 * Airy_Framework class.
 */
 
/**
 * Changelog
 *
 * 1.1.0
 * - DEV: Added post type/taxonomy registration methods
 * - DEV: Enqueue/Admin Enqueue functions
 * - DEV: Renamed vital functions with generic names (ie. post_types, options_page, taxonomies...)
 * - DEV: Added class variables and more defaults (nonce, framework/plugin version, framework url, shortname)
 * - DEV: Added multiselect option's type
 * - BUG: Tab links on options 
 *
 * 1.0.1
 * - Commented PHP
 * - BUG: settings_load not loading from init action, calling from __construct manually works reliably
 * - BUG: tabs were not prefixed, all airy plugins had the same settings section name (general) so all settings showed on all plugin pages.
 * - DEV: admin styling
 *
 * 1.0.0
 * - Release
 *
 */
 
/**
 * Airy_Framework class.
 */
if( class_exists( 'Airy_Framework' ) ) return true;
class Airy_Framework {

	/**
	 * Public Variables
	 **/
	public	$shortname = 'airy_framework';
	public	$plugin_name = 'Airy Framework';
	public	$desc = 'A plugin built on the Airy Framework';
	public	$version = '0.0.0';
	public	$framework_version = '1.1.0';
	public	$framework_url = 'http://www.patrickgarman.com/wordpress-plugin-category/airy/';
	public	$capability = 'manage_options';
	public	$menu_location = 'plugins.php';
	public	$nonce = '_airy_nonce';
	public	$settings = array();
	public	$fields = array();

	/**
	 * Private Variables
	 **/
	private	$no_settings = array();

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct() {
		/**
		 * Actions
		 **/
		add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );
		add_action( 'admin_init', array( &$this, 'settings_init' ) );
		add_action( 'admin_init', array( &$this, 'admin_style' ) );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		
		/**
		 * Create full settings array
		 **/
		$this->fields = apply_filters( $this->shortname, array(
			$this->shortname . '_general' => array(
				'title' 	=> $this->plugin_name,
				'desc'		=> $this->desc,
				'settings'	=> $this->fields,
			),
		));
		
		/**
		 * Load the settings into $this
		 **/
		$this->settings_load();
		
		add_action( 'init', array( &$this, '_register_post_types' ) );
		add_action( 'init', array( &$this, '_register_taxonomies' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue' ) );
	}
	
	/**
	 * plugins_loaded function.
	 * 
	 * @access public
	 * @return void
	 */
	function plugins_loaded() {
		/**
		 * Localisation
		 **/
		load_plugin_textdomain( $this->shortname, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	/**
	 * admin_menu function.
	 * 
	 * @access public
	 * @return void
	 */
	function admin_menu() {
		/**
		 * Add submenu pages
		 * http://codex.wordpress.org/Function_Reference/add_submenu_page
		 **/
		add_submenu_page( $this->menu_location, $this->plugin_name, $this->plugin_name, $this->capability, $this->shortname, array( &$this, '_options_page' ) );
	}
	
	/**
	 * admin_style function.
	 * 
	 * @access public
	 * @return void
	 */
	function admin_style() {
	}
	
	/**
	 * settings_init function.
	 * 
	 * @access public
	 * @return void
	 */
	function settings_init() {
		
		foreach( $this->fields as $tab => $section ) {
		
			add_settings_section( $tab, $section['title'], array( &$this, 'section_desc' ), $tab );
			if( count( $section['settings'] ) > 0 ) {
				foreach( $section['settings'] as $setting => $option ) {
					$default = ( isset( $option['default'] ) ) ? $option['default'] : '';
					add_settings_field( $option['name'], $option['title'], array( &$this, 'settings_fields' ), $tab, $tab, $option );
					register_setting( $tab, $option['name'] );
					add_option( $option['name'], $default );
				}
			} else { $this->no_settings[] = $tab; }
		}
		$this->settings_load();
	}
	
	/**
	 * settings_load function.
	 * 
	 * @access public
	 * @return void
	 */
	function settings_load() {
		foreach( $this->fields as $section ) {
		 	foreach ( $section['settings'] as $option ) {
		 		if( isset( $option['name'] ) ) {
		 			$this->$option['name'] = get_option( $option['name'] );
		 		}
		 	}
		}
		$this->nonce = '_nonce_' . $this->shortname;
	}
		
	/**
	 * section_desc function.
	 * 
	 * @access public
	 * @param mixed $section
	 * @return void
	 */
	function section_desc($section) { echo wpautop( $this->fields[$section['id']]['desc'] ); }
	
	/**
	 * settings_fields function.
	 * 
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	function settings_fields($args) {
		extract($args);
		if(isset($type)) {
			switch($type) {
				case 'text':
					echo '<input type="text" name="'.$name.'" id="'.$name.'" value="'.$default.'" />';
					if(isset($desc) && $desc != '') echo '<span class="description">'.$desc.'</span>';
					break;
				case 'password':
					echo '<input type="password" name="'.$name.'" id="'.$name.'" value="'.$default.'" />';
					if(isset($desc) && $desc != '') echo '<span class="description">'.$desc.'</span>';
					break;
				case 'select':
					echo '<select name="'.$name.'" id="'.$name.'">';
					foreach($values as $key=>$title) {
						echo '<option value="'.$key.'" '.selected($default,$key,false).'>'.$title.'</option>';
					}
					echo '</select>';
					if(isset($desc) && $desc != '') echo '<span class="description">'.$desc.'</span>';
					break;
				case 'multiselect':
					echo '<select multiple="multiple" name="'.$name.'" id="'.$name.'">';
					foreach($values as $key=>$title) {
						echo '<option value="'.$key.'" '.selected($name,$key,true).'>'.$title.'</option>';
					}
					echo '</select>';
					if(isset($desc) && $desc != '') echo '<span class="description">'.$desc.'</span>';
					break;
				case 'textarea':
					echo '<textarea type="text" name="'.$name.'" id="'.$name.'">'.$default.'</textarea>';
					if(isset($desc) && $desc != '') echo '<span class="description">'.$desc.'</span>';
					break;
				case 'checkbox':
					echo '<input type="hidden" name="'.$name.'" id="'.$name.'" value="no" />';
					echo '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes" '.checked($this->$name,'yes',false).' />';
					if(isset($desc) && $desc != '') echo '<span class="description">'.$desc.'</span>';
					break;
			}
		}
	}
	
	/**
	 * _options_page function.
	 * 
	 * @access public
	 * @return void
	 */
	function _options_page() {
		echo '<div class="wrap"><div class="icon32" id="icon-options-general"><br /></div>';
			echo '<h2 class="nav-tab-wrapper">';
				$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : $this->shortname . '_general';
				foreach( $this->fields as $key => $data ) {
					$active = ( $key == $tab ) ? 'nav-tab-active' : '';
					echo '<a class="nav-tab ' . $active . '" href="' . add_query_arg( array( 'tab' => $key ) ) . '">' . $data['title'] . '</a>';
				}
			echo '</h2>';
			echo '<form method="post" id="mainform" action="options.php">';
				settings_fields( $tab );
				do_settings_sections( $tab );
				if( !in_array( $tab, $this->no_settings ) ) {
					echo '<p class="submit"><input type="submit" class="button-primary" value="'.__('Save Changes', $this->shortname ).'" /></p>';
				}
			echo '</form>';
			// By removing this line you forfeight all your internet, murder puppies, and love cats. I don't like cats... it's not like anyone but site admins see this anways!
			echo '<p style="border-top:1px solid #DFDFDF; padding:10px 0;"><a href="' . $this->framework_url . '" target="_blank">Airy Framework</a> Version: ' . $this->framework_version . '</p>';
		echo '</div>';
	}
	
	function assets_url( $name ) {
		return plugins_url( 'assets/' . $name, dirname(__FILE__) );
	}
	
	/**
	 * admin_enqueue function.
	 * 
	 * @access public
	 * @return void
	 */
	function admin_enqueue() {
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_style( 'jquery-ui-slider' );
	}
	
	/**
	 * enqueue function.
	 * 
	 * @access public
	 * @return void
	 */
	function enqueue() {
	}
	
	/**
	 * post_types function.
	 * 
	 * @access public
	 * @return void
	 */
	function _register_post_types() {
		
		$posts = array();
		foreach( apply_filters( $this->shortname . '_post_types', $posts ) as $name => $data ) {
		
			$menu_name = ( isset( $data['menu_name'] ) ) ? $data['menu_name'] : $data['plural'];
			
			$args = array(
				'label' => $data['plural'],
				'labels' => array(
					'name' 					=> $data['plural'],
					'singular_name' 		=> $data['single'],
					'add_new' 				=> 'Add New',
					'add_new_item' 			=> sprintf( 'Add New %s', $data['single'] ),
					'edit_item' 			=> sprintf( 'Edit %s', $data['single'] ),
					'new_item' 				=> sprintf( 'New %s', $data['single'] ),
					'all_items' 			=> sprintf( 'All %s', $data['plural'] ),
					'view_item' 			=> sprintf( 'View %s', $data['single'] ),
					'search_items' 			=> sprintf( 'Search %s', $data['plural'] ),
					'not_found' 			=> sprintf( 'No %s found', $data['plural'] ),
					'not_found_in_trash'	=> sprintf( 'No %s found in trash', $data['plural'] ), 
					'parent_item_colon'		=> '',
					'menu_name'				=> $menu_name,
				),
			);
			
			$keys = array( 'label', 'labels', 'description', 'public', 'exclude_from_search', 'show_ui', 'show_in_nav_menus', 'show_in_menu', 'show_in_admin_bar', 'menu_position', 'menu_icon', 'capability_type', 'capabilities', 'map_meta_cap', 'hierarchical', 'supports', 'register_meta_box_cb', 'taxonomies', 'has_archive', 'permalink_epmask', 'rewrite', 'query_var', 'can_export' );
			
			foreach( $keys as $key ) {
				if( isset( $data[ $key ] ) ) {
					$args[ $key ] = $data[ $key ];
				}
			}
			register_post_type( $name, $args );
			
		}
		flush_rewrite_rules();
	}
	
	/**
	 * taxonomies function.
	 * 
	 * @access public
	 * @return void
	 */
	function _register_taxonomies() {
		
		$taxonomies = apply_filters( $this->shortname . '_taxonomies', array() );
		foreach(  $taxonomies as $name => $data ) {
		
			if( isset( $data['post_types'] ) ) {
				$post_types = $data['post_types'];
			} else { continue; }
			
			$menu_name = ( isset( $data['menu_name'] ) ) ? $data['menu_name'] : $data['plural'];
			
			$args = array(
				'label' => $data['plural'],
				'labels' => array(
					'name' 					=> $data['plural'],
					'singular_name' 		=> $data['single'],
					'search_items' 			=> sprintf( 'Search %s', $data['plural'] ),
					'popular_items' 		=> sprintf( 'Popular %s', $data['plural'] ),
					'all_items' 			=> sprintf( 'All %s', $data['plural'] ),
					'add_new' 				=> 'Add New',
					'parent_item'			=> sprintf( 'Parent %s', $data['single'] ),
					'parent_item_colon'		=> sprintf( 'Parent %s:', $data['single'] ),
					'edit_item' 			=> sprintf( 'Edit %s', $data['single'] ),
					'update_item' 			=> sprintf( 'Update %s', $data['single'] ),
					'add_new_item' 			=> sprintf( 'Add New %s', $data['single'] ),
					'new_item_name'			=> sprintf( 'New %s Name', $data['single'] ),
					'separate_items_with_commas' => sprintf( 'Separate %s with commas', $data['plural'] ),
					'add_or_remove_items' 	=> sprintf( 'Add or remove %s', $data['plural'] ),
					'choose_from_most_used' => sprintf( 'Choose from the most used %s', $data['plural'] ),
					'menu_name'				=> $menu_name,
				),
			);
			
			$keys = array( 'label', 'labels', 'public', 'show_in_nav_menus', 'show_ui', 'show_tagcloud', 'hierarchical', 'update_count_callback', 'query_var', 'rewrite', 'capabilities' );
			
			foreach( $keys as $key ) {
				if( isset( $data[ $key ] ) ) {
					$args[ $key ] = $data[ $key ];
				}
			}
			register_taxonomy( $name, $post_types, $args );
			
		}
		flush_rewrite_rules();
	}

}
