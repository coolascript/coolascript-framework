<?php
namespace csframework;
/**
 * Adds a metabox with extra fields to comment edit page
 */
class CommentMetabox extends Base
{
	/**
	 * HTML id attribute
	 * @var string|null
	 */
	private $_id = null;
	/**
	 * Metabox fields key name
	 * @var string|null
	 */
	private $_name = null;
	/**
	 * Metabox title
	 * @var string
	 */
	private $_title = '';
	/**
	 * Metabox context (See https://developer.wordpress.org/reference/functions/add_meta_box/)
	 * @var string
	 */
	private $_context = 'normal';
	/**
	 * Metabox priority (See https://developer.wordpress.org/reference/functions/add_meta_box/)
	 * @var string
	 */
	private $_priority = 'default';
	/**
	 * Metabox fields
	 * @var array
	 */
	private $_fields = array();
	/**
	 * Metabox fields base name
	 * @var string
	 */
	protected $_fields_base = null;
	
	/**
	 * Creates metabox instance
	 * @param string $post_type Post type slug
	 * @param string $fields_base_name Fields base name
	 * @param array $args       Metabox parameters
	 */
	function __construct( $fields_base_name, $args )
	{
		$this->_id = $args['name'];
		$this->_fields_base = $fields_base_name;
		$this->setOptions( $args );
		add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );
	}

	/**
	 * Sets metabox gitle
	 * @param string $val Title
	 */
	public function setTitle( $val )
	{
		$this->_title = $val;
		return $this;
	}

	/**
	 * Sets metabox name
	 * @param string $val Name
	 */
	public function setName( $val )
	{
		$this->_name = $val;
		return $this;
	}

	/**
	 * Retrieve metabox name. Used by fields.
	 * @return string Name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Sets metabox context
	 * @param string $val Context
	 */
	public function setContext( $val )
	{
		$this->_context = $val;
		return $this;
	}

	/**
	 * Sets metabox priority
	 * @param string $val Priority
	 */
	public function setPriority( $val )
	{
		$this->_priority = $val;
		return $this;
	}

	/**
	 * Sets metabox fields
	 * @param array $val Field list
	 */
	public function setFields( $val )
	{
		if ( is_array( $val ) ) {
			foreach ( $val as $name => $field ) {
				if ( is_array( $field ) ) {
					if ( isset( $field['type'] ) ) {
						$field_class = 'csframework\Field' . ucfirst( $field['type'] );
						if ( class_exists( $field_class ) ) {
							$field['name'] = $name;
							$field['parent'] = $this;
							$this->_fields[$name] = new $field_class( $this->_fields_base, $field );
						} else {
							throw new \Exception( sprintf( __( "csframework\PosttypeMetabox: Unknown field type `%s`", 'coolascript-framework' ), $field['type'] ) );
							
						}
					}
				} elseif ( is_object( $field ) ) {
					$field->setName( $name );
					$field->setParent( $this );
					$this->_fields[$name] = $field;
				}
			}
		}
		return $this;
	}

	/**
	 * Retrieve all metabox fields
	 * @return array Field list
	 */
	public function getFields()
	{
		return $this->_fields;
	}

	/**
	 * Retrieve field metabox field by name
	 * @param  string $name Field name
	 * @return csframework\Field       Field instance
	 */
	public function getField( $name )
	{
		return isset( $this->_fields[$name] ) ? $this->_fields[$name] : false;
	}

	/**
	 * WP add_meta_box function wrapper
	 * @return csframework\Metabox Self instance
	 */
	public function addMetaBox()
	{
		add_meta_box(
			$this->_id,										// box id
			$this->_title,									// box title
			array( $this, 'render' ),						// callback function
			'comment',										// screen
			$this->_context,								// context
			$this->_priority								// priority
		);
		return $this;
	}

	/**
	 * Render metabox HTML code
	 * @param  object $post   Post object
	 * @return void
	 */
	public function render( $comment )
	{
		?>
		<div class="csframework-metabox">
		<?php foreach ( $this->_fields as $name => $field ): ?>
			<?php $value = get_comment_meta( $comment->comment_ID, $name, true ) ?>
			<?php $field->setValue( $value ); ?>
			<div class="csframework-field-row">
				<?php $field->render(); ?>
			</div>
		<?php endforeach ?>
		</div>
		<?php
	}
}