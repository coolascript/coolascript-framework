<?php
namespace csframework;
/**
 * Makes it easy to create new custom post types
 * and add custom fields to it.
 */
class Posttype
{
	/**
	 * Post type slug
	 * @var string
	 */
	public static $post_type = 'posttype';
	/**
	 * Post type metaboxes
	 * @var array|null
	 */
	protected $_metaboxes = array();
	/**
	 * @var csframework\Csframework|null
	 */
	protected $_app = null;

	/**
	 * Custom post type constructor
	 * @param string $slug Post type slug
	 * @param array $args Arguments for post type. See register_post_type (https://codex.wordpress.org/Function_Reference/register_post_type)
	 */
	protected function __construct( $slug, $args = null )
	{
		if ( is_null( $this->_app ) ) {
			$this->_app = Csframework::getInstance();
		}
		if ( !post_type_exists( $slug ) ) {
			if ( !is_wp_error( $post_type_object = register_post_type( $slug, $args ) ) ) {
				self::$post_type = $slug;
				add_action( 'save_post', array( $this, 'onSave' ) );
			} else {
				throw new \Exception( $post_type_object->get_error_message() );
			}
		} else {
			self::$post_type = $slug;
			add_action( 'save_post', array( $this, 'onSave' ) );
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'addAssets' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'addAdminAssets' ), 100 );
		add_action( 'login_enqueue_scripts', array( $this, 'addLoginAssets' ), 100 );
	}
	private function __clone()
	{
		return self::getInstance();
	}
	private function __wakeup()
	{
		return self::getInstance();
	}

	/**
	 * Retrieve an instance of class
	 * @return csframework\Posttype
	 */
	public static function getInstance()
	{
		static $instances = array();
		$calledClass = get_called_class();
		if ( !isset( $instances[$calledClass] ) )
		{
			$instances[$calledClass] = new $calledClass();
		}
		return $instances[$calledClass];
	}

	/**
	 * Sets app from which post type was created
	 * @param csframework\Csframework $app Application main class instance
	 * @return csframework\Csframework
	 */
	public function setApp( $app )
	{
		$this->_app = $app;
	}

	/**
	 * Loads Post by slug or ID
	 * @param int $id Post ID
	 * @return csframework\Posttype
	 */
	public function load( $id )
	{
		$post = get_post( $id );
		if ( $post && get_post_type( $post ) == self::$post_type ) {
			$this->id = $post->ID;
			$this->title = $post->post_title;
			$this->content = $post->post_content;
		}
		return $this;
	}

	/**
	 * Retrieve the Post type slug
	 * @return string
	 */
	public function getSlug()
	{
		return ( string ) self::$post_type;
	}

	/**
	 * Adds metabox to post type create/edit page
	 * @param array $args
	 * @return csframework\Posttype
	 */
	public function addMetabox( $args )
	{
		if ( is_array( $args ) && isset( $args['name'] ) ) {
			$this->_metaboxes[$args['name']] = new PosttypeMetabox( self::$post_type, $this->_app, $args );
		}
		return $this;
	}

	/**
	 * Override this function to add extra meta boxes and fields with addMetabox() function
	 * Attention: Metaboxes are creates only on backend
	 */
	public function addMetaboxes() {}

	/**
	 * Retrieve the Post type metabox by slug
	 * @param string $slug
	 * @return csframeworkPosttypeMetabox|false
	 */
	public function getMetabox( $slug )
	{
		return isset( $this->_metaboxes[( string ) $slug] ) ? $this->_metaboxes[( string ) $slug] : false;
	}

	/**
	 * Retrieve all Post type metaboxes
	 * @return array
	 */
	public function getMetaboxes()
	{
		return $this->_metaboxes;
	}

	/**
	 * Remove the Post type metabox by slug
	 * @param string $slug
	 * @return string
	 */
	public function removeMetabox( $slug )
	{
		if ( isset( $this->_metaboxes[$slug] ) ) {
			unset( $this->_metaboxes[$slug] );
		}
		return $this;
	}

	/**
	 * Saves post type custom fields on post save
	 * @param int $post_id
	 * @return void
	 */
	public function onSave( $post_id )
	{
		if ( ( isset( $_POST['post_type'] ) && self::$post_type == $_POST['post_type'] ) && ( !isset( $_POST['post_view'] ) || $_POST['post_view'] != 'list' ) ) {
			if ( isset( $_REQUEST[$this->_app->getFieldsVar()] ) ) {
				foreach ( $_REQUEST[$this->_app->getFieldsVar()] as $metabox => $fields ) {
					if ( isset( $this->_metaboxes[$metabox] ) ) {
						foreach ( $fields as $key => $value ) {
							if ( $field = $this->_metaboxes[$metabox]->getField( $key ) ) {
								$field->setValue( $value );
							}
						}
					}
				}
			}

			if ( $this->_metaboxes ) {
				foreach ( $this->_metaboxes as $box_slug => $metabox ) {
					foreach ( $metabox->getFields() as $field_name => $field ) {
						update_post_meta( $post_id, $field_name, $field->getValue() );
					}
				}
			}
		}
		return;
	}

	/**
	 * Override this function in your class to enqueue scripts and styles on frontend.
	 * Don't forget do parent::addAssets();
	 */
	public function addAssets() {}

	/**
	 * Override this function in your class to enqueue scripts and styles on backend.
	 * Don't forget do parent::addAdminAssets();
	 */
	public function addAdminAssets() {}

	/**
	 * Override this function in your class to enqueue scripts and styles on login page.
	 * Don't forget do parent::addLoginAssets();
	 */
	public function addLoginAssets() {}
}