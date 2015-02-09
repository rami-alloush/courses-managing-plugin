<?php
if(!class_exists('CoursePostTypeTemplate')) //###
{
    /**
     * A CoursePostTypeTemplate class that provides 3 additional meta fields
     */
    class CoursePostTypeTemplate //###
    {
        const PRODUCT_NAME = "course"; //###
        const CAT_NAME	   = "category"; //###
        const MENU_ICON	   = "dashicons-welcome-write-blog"; //###
        const PRODUCT_NO   = 1; //###
        private $_meta  = array(
            // 'meta_a',
            // 'meta_b',
            // 'meta_c',
        );
		
		/**
		 * The Constructor
		 */
		public function __construct()
		{
			// register actions
			add_action('init', array(&$this, 'init'));
			add_action('admin_init', array(&$this, 'admin_init'));
		} // END public function __construct()

		/**
		 * hook into WP's init action hook
		 */
		public function init()
		{
			/// Initialize Post Type
			$this->create_post_type();
			$this->create_post_taxonomies();
			
			include(sprintf("%s/../post-types/%s_acf_fields.php", dirname(__FILE__),self::PRODUCT_NAME));
			
			add_action('save_post', array(&$this, 'save_post'),10,2);

			add_filter('manage_'.self::PRODUCT_NAME.'_posts_columns', array(&$this,'add_new_product_columns'),10,2); //not with edit-
			add_action('manage_'.self::PRODUCT_NAME.'_posts_custom_column', array(&$this,'manage_product_columns'),10,2);//not with edit-
			add_filter('manage_edit-'.self::PRODUCT_NAME.'_sortable_columns', array(&$this,'sortable_product_columns'),10,2);
			add_action( 'pre_get_posts', array(&$this,'product_code_orderby' ),10,2);			

		} // END public function init()

		public function filter_post_data( $data , $postarr ) {
			// Change post title
			$data['post_title'] = $_POST['meta_a'];
			return $data;
		}

		/**
		 * Create the post type
		 */
		public function create_post_type()	{
		
			$labels = array(
				'name'               => __( sprintf('%ss', ucwords(str_replace("_", " ", self::PRODUCT_NAME))), 'cpm-plugin-textdomain' ),
				'singular_name'      => __( ucwords(str_replace("_", " ", self::PRODUCT_NAME)), 'cpm-plugin-textdomain' ),
				'menu_name'          => __( sprintf('%ss', ucwords(self::PRODUCT_NAME)), 'cpm-plugin-textdomain' ),
				'name_admin_bar'     => __( ucwords(self::PRODUCT_NAME), 'cpm-plugin-textdomain' ),
				'add_new'            => __( 'Add New', 'cpm-plugin-textdomain' ),
				'add_new_item'       => __( 'Add New '.ucwords(self::PRODUCT_NAME), 'cpm-plugin-textdomain' ),
				'new_item'           => __( 'New '.ucwords(self::PRODUCT_NAME), 'cpm-plugin-textdomain' ),
				'edit_item'          => __( 'Edit '.ucwords(self::PRODUCT_NAME), 'cpm-plugin-textdomain' ),
				'view_item'          => __( 'View '.ucwords(self::PRODUCT_NAME), 'cpm-plugin-textdomain' ),
				'all_items'          => __( 'All '.sprintf('%ss', ucwords(self::PRODUCT_NAME)), 'cpm-plugin-textdomain' ),
				'search_items'       => __( 'Search '.ucwords(self::PRODUCT_NAME), 'cpm-plugin-textdomain' ),
				'parent_item_colon'  => __( 'Parent '.ucwords(self::PRODUCT_NAME).':', 'cpm-plugin-textdomain' ),
				'not_found'          => __( 'No '.ucwords(self::PRODUCT_NAME).' found.', 'cpm-plugin-textdomain' ),
				'not_found_in_trash' => __( 'No '.ucwords(self::PRODUCT_NAME).' found in Trash.', 'cpm-plugin-textdomain' ),
			);

			$args = array(
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				// 'show_in_menu'       => 'edit.php?post_type=iso',
				'query_var'          => true,
				'rewrite'            => array( 'slug' => self::PRODUCT_NAME ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'menu_icon' 		 => self::MENU_ICON,
				'supports'           => array( 'title', 'editor', 'thumbnail') //,'author', 'excerpt', 'comments' )
			);

			register_post_type( self::PRODUCT_NAME , $args );
		}
		
		/// create taxonomies, Categories the post type "ISO"
		public function create_post_taxonomies() {
			
			/// Add new taxonomy, make it hierarchical (like categories)
			$labels = array(
				'name'               => __( sprintf('%ss', ucwords(self::CAT_NAME)), 'cpm-plugin-textdomain' ),
				'singular_name'      => __( ucwords(self::CAT_NAME), 'cpm-plugin-textdomain' ),
				'menu_name'          => __( sprintf('%ss', ucwords(self::CAT_NAME)), 'cpm-plugin-textdomain' ),
				'name_admin_bar'     => __( ucwords(self::CAT_NAME), 'cpm-plugin-textdomain' ),
				'add_new'            => __( 'Add New', 'cpm-plugin-textdomain' ),
				'add_new_item'       => __( 'Add New '.ucwords(self::CAT_NAME), 'cpm-plugin-textdomain' ),
				'new_item'           => __( 'New '.ucwords(self::CAT_NAME), 'cpm-plugin-textdomain' ),
				'edit_item'          => __( 'Edit '.ucwords(self::CAT_NAME), 'cpm-plugin-textdomain' ),
				'view_item'          => __( 'View '.ucwords(self::CAT_NAME), 'cpm-plugin-textdomain' ),
				'all_items'          => __( 'All '.sprintf('%ss', ucwords(self::CAT_NAME)), 'cpm-plugin-textdomain' ),
				'search_items'       => __( 'Search '.ucwords(self::CAT_NAME), 'cpm-plugin-textdomain' ),
				'parent_item_colon'  => __( 'Parent '.ucwords(self::CAT_NAME).':', 'cpm-plugin-textdomain' ),
				'not_found'          => __( 'No '.ucwords(self::CAT_NAME).' found.', 'cpm-plugin-textdomain' ),
				'not_found_in_trash' => __( 'No '.ucwords(self::CAT_NAME).' found in Trash.', 'cpm-plugin-textdomain' ),
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => self::CAT_NAME ),
			);

			register_taxonomy( self::CAT_NAME , self::PRODUCT_NAME, $args );
		}

		public function add_new_product_columns($columns) {
			 
			unset($columns['date']);
			// unset($columns['AttCustomTaxs']);
			// unset($columns['taxonomy-AttCustomTax']);
			
			return array_merge($columns,
            array('product_code' => __('Code'),
					'old_product_code' => __('Old Code'),
				  ));
		}
 
		public function manage_product_columns($column, $post_id) {
			switch ( $column ) {
			case 'product_code' :
				$terms = get_post_meta($post_id, 'product_code', true);
				if ( !empty( $terms ) )
					echo $terms;
				else
					_e( 'No Code Yet!', 'cpm-plugin-textdomain' );
				break;

			case 'old_product_code' :
				echo get_post_meta( $post_id , 'att_old_code' , true ); 
				break;
			}
		}
		
		public function sortable_product_columns( $columns ) {
			$columns['product_code'] = 'product_code';
		 
			//To make a column 'un-sortable' remove it from the array
			// unset($columns['title']);
		 
			return $columns;
		}
		
		public function product_code_orderby( $query ) {
			if( ! is_admin() )
				return;
		 
			$orderby = $query->get( 'orderby');
		 
			if( 'product_code' == $orderby ) {
				$query->set('meta_key','product_code');
				$query->set('orderby','meta_value');
			}
		}
				
		/**
		 * Save the metaboxes for this custom post type
		 */
		public function save_post($post_id, $post)
		{
			// verify if this is an auto save routine. 
			// If it is our form has not been submitted, so we don't want to do anything
			if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (isset($post->post_status) && 'auto-draft' == $post->post_status) )
			{
				return;
			}
			  
			if(current_user_can('edit_post', $post_id) && $post->post_type == self::PRODUCT_NAME)
			{
				foreach($this->_meta as $field_name)
				{
					// Update the post's meta field
					update_post_meta($post_id, $field_name, $_POST[$field_name]);
				}
				
				// Check if category chaned
				$old_cat_number = substr(get_post_meta($post_id,"product_code",true),-4,2);
				$taxonomies = get_the_terms($post_id, self::CAT_NAME);
				$cat 		= (is_array($taxonomies) ? array_shift($taxonomies) : $taxonomies);
				$cat_number = str_pad($cat->description, 2, "0", STR_PAD_LEFT);
				
				if ($old_cat_number != $cat_number || $_POST['product_code'] == 'PZXXYY') { // Cat changed or No Code is created yet!
					
					// Calculate PRODUCT_NAME Code
					$product_query  = new WP_Query( array(
														'post_type' => self::PRODUCT_NAME,
														'post_status'   => 'publish',
														'posts_per_page' => -1,
														'tax_query' => array(
																		array(
																		'taxonomy' => self::CAT_NAME,
																		'field' => 'id',
																		'terms' => array( $cat->term_id )
																		)
																	  )
												));
					$product_number = str_pad($product_query->post_count, 2, "0", STR_PAD_LEFT);
					$product_code  = 'P'. self::PRODUCT_NO . $cat_number . $product_number;
					
					/// Duplication Check
						//get all PRODUCT_NAME ids as an array
						$posts = get_posts(array('post_type' => self::PRODUCT_NAME, 'post_status' => 'publish', 'fields' => 'ids'));
						//loop over each post
						foreach($posts as $p){
							//get the meta you need form each post
							$existing_codes[] = get_post_meta($p,"product_code",true);
							//do whatever you want with it
						}
						
						if (is_array($existing_codes)) {
							while (in_array($product_code, $existing_codes)) {
								$code_int = preg_replace('/[^0-9]/i', '',$product_code);
								$new_code_int = (int)$code_int + 1;
								$product_code = 'P'.$new_code_int;
							}
						}
					
					update_post_meta($post_id, 'product_code', $product_code);
				}
			}
			else
			{
				return;
			}
		} // END public function save_post($post_id)
		
		/**
		 * hook into WP's admin_init action hook
		 */
		public function admin_init()
		{           
			// Add metaboxes
			add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
		} // END public function admin_init()
		
		/**
		 * hook into WP's add_meta_boxes action hook
		 */
		public function add_meta_boxes()
		{
			add_meta_box( //Code
				sprintf('cmp_%s_code_section', self::PRODUCT_NAME),						//ID
				sprintf('%s Code', ucwords(str_replace("_", " ", self::PRODUCT_NAME))),	//Title
				array($this, 'add_code_meta_boxes'),									//CallBack
				self::PRODUCT_NAME,														//PostType
				'side','core'															//Context (normal,advanced,side), Priority 
			);
		} // END public function add_meta_boxes()
				
		/**
		 * called off of the add meta box */				
		public function add_code_meta_boxes($post)
		{       
			// Render the job order metabox
			include(sprintf("%s/../templates/product_code.php", dirname(__FILE__)));         
		} // END public function add_code_meta_boxes($post)

    } // END class CoursePostTypeTemplate
} // END if(!class_exists('CoursePostTypeTemplate'))

// #####################################################################################

// Returns option HTML for ALL Courses Cats
if(!function_exists('cmp_return_courses_cats')) {
	function cmp_return_courses_cats() {
		$terms = get_terms( 'category', array(
			'orderby'    => 'alpha',
			'exclude'	=> 1,
			'hide_empty' => 0
		));
		
		echo "<ul>";
		foreach ( $terms as $term ) {
			$cmp_courses_cats .= '<option value="' . $term->term_id . '">' . $term->name .' ('.$term->count.') '. '</option>';
		}
		echo "</ul>";
		
		return $cmp_courses_cats;
	}
}

// Ajax Function to retrieve courses from certain category
add_action('wp_ajax_cmp_course_cat', 'cmp_ajax_action_callback');

function cmp_ajax_action_callback() {
	
	$course_cat = intval( $_POST['course_cat'] );
	$courses_ids = get_objects_in_term( $course_cat , 'category' );

	echo '<option value=" " disabled="disabled" selected="selected">Choose one</option>';

	foreach ( $courses_ids as $course_id ) {
		$course_title = get_the_title( $course_id );
		$course_code = get_field('product_code', $course_id);
		echo '<option value="'. $course_code . ':' . $course_title .'">'. $course_code .' | '. $course_title .'</option>';
	}
	die(); // this is required to return a proper result
}