<?php
namespace csframework;
/**
 * Makes it easy to modify comments for post types
 * and add custom fields to it.
 */
class Comment
{
	/**
	 * Post type slug
	 * @var string
	 */
	public static $post_type = 'posttype';
	/**
	 * Comment metaboxes
	 * @var array|null
	 */
	protected $_metaboxes = array();
	/**
	 * Fields base name
	 * @var string
	 */
	protected $_fields_base = 'csframework';

	/**
	 * Custom post type comment constructor
	 * @param string $slug Post type slug
	 * @param array $args Arguments for post type. See register_post_type (https://codex.wordpress.org/Function_Reference/register_post_type)
	 */
	protected function __construct( $slug )
	{
		if ( post_type_exists( $slug ) ) {
			self::$post_type = $slug;
			add_action( 'comment_post', array( $this, 'onAdd' ), 10, 3 );
			add_action( 'edit_comment', array( $this, 'onUpdate' ), 10 );
			add_action( 'wp_enqueue_scripts', array( $this, 'addAssets' ), 100 );
			add_action( 'admin_enqueue_scripts', array( $this, 'addAdminAssets' ), 100 );
			add_action( 'login_enqueue_scripts', array( $this, 'addLoginAssets' ), 100 );
		}
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
	 * Set Metabox fields base name
	 * @param string $fields_base Base name of all custom fields
	 * @return csframework\Posttype
	 */
	public function setFieldsBase( $fields_base )
	{
		$this->_fields_base = $fields_base;
		return $this;
	}

	/**
	 * Loads Post by slug or ID
	 * @param int $id Post ID
	 * @return csframework\Posttype
	 */
	public function load( $id )
	{
		$comment_class = get_called_class();
		
		$comment = get_comment( $id );
		if ( $comment && get_post_type( $comment->comment_post_ID ) == $comment_class::$post_type ) {
			$this->id = $comment->comment_ID;
			$this->content = $comment->comment_content;
		}
		return $this;
	}

	/**
	 * Adds metabox to post type create/edit page
	 * @param array $args
	 * @return csframework\Posttype
	 */
	public function addMetabox( $args )
	{
		$comment_class = get_called_class();
		$comment_id = ( int ) $_REQUEST['c'];
		$comment = get_comment( $comment_id );
		$post_type = get_post_type( $comment->comment_post_ID );
		if ( $comment_class::$post_type == $post_type && is_array( $args ) && isset( $args['name'] ) ) {
			$this->_metaboxes[$args['name']] = new CommentMetabox( $this->_fields_base, $args );
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
	 * @return self
	 */
	public function removeMetabox( $slug )
	{
		if ( isset( $this->_metaboxes[$slug] ) ) {
			unset( $this->_metaboxes[$slug] );
		}
		return $this;
	}

	/**
	 * Save custom fields on comment added
	 * @param  int $comment_id The comment ID
	 * @param  bool $approved   1 if the comment is approved, 0 if not, 'spam' if spam
	 * @param  array $commentdata   Comment data
	 * @return void
	 */
	public function onAdd( $comment_id, $approved, $commentdata )
	{
		$comment_class = get_called_class();
		$post_type = get_post_type();

		if ( $this->_metaboxes && $comment_class::$post_type == $post_type ) {
			foreach ( $this->_metaboxes as $box_slug => $metabox ) {
				foreach ( $metabox->getFields() as $field_name => $field ) {
					if ( isset( $_REQUEST[$field_name] ) ) {
						update_comment_meta( $comment_id, $field_name, $_REQUEST[$field_name] );
					}
				}
			}
		}
	}

	/**
	 * Saves comment custom fields after post updated
	 * @param int $comment_id
	 * @return void
	 */
	public function onUpdate( $comment_id )
	{
		$comment_class = get_called_class();
		$comment = get_comment( $comment_id );
		$post_type = get_post_type( $comment->comment_post_ID );

		if ( isset( $_REQUEST[$this->_fields_base] ) && $this->_metaboxes && $comment_class::$post_type == $post_type ) {
			foreach ( $this->_metaboxes as $box_slug => $metabox ) {
				foreach ( $metabox->getFields() as $field_name => $field ) {
					if ( isset( $_REQUEST[$this->_fields_base][$box_slug][$field_name] ) ) {
						update_comment_meta( $comment_id, $field_name, $_REQUEST[$this->_fields_base][$box_slug][$field_name] );
					} else {
						delete_comment_meta( $comment_id, $field_name );
					}
				}
			}
		}
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