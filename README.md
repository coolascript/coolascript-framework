# coolascript-framework (Currently in develope)
Wordpress development framework

# Getting Started
1. Install coolascript-framework plugin to Wordpress.
2. Add next lines to your theme\plugin project:
```
namespace myapp;
if ( defined( 'CSFRAMEWORK_VERSION' ) ) {
	class myApp extends \csframework\Csframework {}
	myApp::getInstance()
		->setNamespace( 'myapp' )						// Your project PHP namespace for avoding conflictsv
		->setApppath( 'myapp', MYAPP_PLUGIN_DIR . '/' )	// Path to your project
		->setFieldsVar( 'myapp' )						// Key for all extra fields
		->run();
}
```
# Adding new post type
Create 'posttype\myposttype.php' file in your project path:
```
<?php
namespace myapp;
class PosttypeMyposttype extends \csframework\Posttype
{
	public static $post_type = 'myposttype_slug';

	protected function __construct()
	{
		if ( !post_type_exists( self::$post_type ) ) {
			parent::__construct( self::$post_type, array(				// 	same as for `register_post_type` parameters
				'label' => __( 'My post', 'mylocale' ),
				'labels' => array(
					'name' => __( 'My posts', 'mylocale' ),
					'singular_name' => __( 'My post', 'mylocale' ),
					'menu_name' => __( 'My posts' , 'mylocale' ),
					'all_items' => __( 'All My posts', 'mylocale' ),
					'name_admin_bar' => __( 'My post' , 'mylocale' ),
					'add_new' => __( 'Add New' , 'mylocale' ),
					'add_new_item' => __( 'Add New My post', 'mylocale' ),
					'edit_item' => __( 'Edit My post', 'mylocale' ),
					'new_item' => __( 'New My post', 'mylocale' ),
					'view_item' => __( 'View My post', 'mylocale' ),
					'search_items' => __( 'Search My posts', 'mylocale' ),
					'not_found' => __( 'No My posts found.', 'mylocale' ),
					'not_found_in_trash' => __( 'No My posts found in Trash', 'mylocale' ),
					'parent_item_colon' => __( 'Parent My post:', 'mylocale' ),
				),
				'description' => __( 'My post type description', 'mylocale' ),
				'public' => true,
				'supports' => array(
					'title',
					'editor',
					'excerpt',
					'thumbnail',
				),
			) );
		}
	}

	// Add metaboxes with custom fields
	// Attention: Metaboxes are creates only on backend
	public function addMetaboxes()
	{
		$fields = array();
		$fields['_myfield'] = array(					// Use `_` (underscore) for private meta fields
			'label' => __( 'My field', 'mylocale' ),
			'type' => 'text',							// Can be: text, textarea, reapeatable, sortable, wysiwyg, date, select, checkbox, checkboxes, color, email, file, multiselect, password, radio
			'class' => 'widefat',
			'sanitize' => 'int',
			'default' => '0',
		);
		$this->addMetabox( array(
			'name' => self::$post_type . '_mymetabox',
			'title' => __( 'My metabox' , 'mylocale' ),
			'fields' => $fields,
		) );
	}

	// Redefine if you need some extra fields to retriev
	public function load( $id )
	{
		parent::load( $id );

		$post = get_post( $id );
		if ( $post && get_post_type( $post ) == self::$post_type ) {
			$this->slug = $post->name;
		}

		$this->myfield = get_post_meta( $this->id, '_myfield', true );

		return $this;
	}
}
```