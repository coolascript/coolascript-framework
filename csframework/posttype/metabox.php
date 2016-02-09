<?php
/**
* Metabox
*/
namespace coolabook;
class PosttypeMetabox extends Abstractive
{
	private $_id = null;
	private $_name = null;
	private $_title = '';
	private $_post_type = '';
	private $_context = 'advanced';
	private $_priority = 'default';
	private $_fields = array();
	
	function __construct( $post_type, $args )
	{
		$this->_id = $post_type . '-' . $args['name'];
		$this->_post_type = $post_type;
		$this->setOptions( $args );
		add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );
	}

	public function setTitle( $val )
	{
		$this->_title = $val;
		return $this;
	}

	public function setName( $val )
	{
		$this->_name = $val;
		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setPost_type( $val )
	{
		$this->_post_type = $val;
		return $this;
	}

	public function setContext( $val )
	{
		$this->_context = $val;
		return $this;
	}

	public function setPriority( $val )
	{
		$this->_priority = $val;
		return $this;
	}

	public function setFields( $val )
	{
		if ( is_array( $val ) ) {
			foreach ($val as $name => $field) {
				if ( is_array( $field ) ) {
					if ( isset( $field['type'] ) ) {
						$field_class = 'coolabook\Field' . ucfirst($field['type']);
						if ( class_exists( $field_class ) ) {
							$field['name'] = $name;
							$field['parent'] = $this;
							$this->_fields[$name] = new $field_class( $field );
						} else {
							throw new \Exception( sprintf( __( "Unknown field type `%s`", Csplugin::getTextDomain() ), $field['type'] ) );
							
						}
					}
				}elseif ( is_object( $field )) {
					$field->setName( $name );
					$field->setParent( $this );
					$this->_fields[$name] = $field;
				}
			}
		}
		return $this;
	}

	public function getFields()
	{
		return $this->_fields;
	}

	public function getField( $name )
	{
		return isset( $this->_fields[$name] ) ? $this->_fields[$name] : false;
	}

	public function addMetaBox()
	{
		add_meta_box(
			$this->_id,										// box id
			$this->_title,									// box title
			array( $this, 'render' ),						// callback function
			$this->_post_type,								// post type
			$this->_context,								// context
			$this->_priority								// priority
			//$this->_fields									// fields
		);
		return $this;
	}

	public function render( $post, $fields )
	{
		?>
		<div class="Csplugin-box">
		<?php foreach ($this->_fields as $name => $field): ?>
			<?php $value = get_post_meta( $post->ID, $name, true ) ?>
			<?php $field->setValue( empty( $value ) ? null : $value ); ?>
			<div class="field-row">
				<?php $field->render(); ?>
			</div>
		<?php endforeach ?>
		</div>
		<?php
	}
}