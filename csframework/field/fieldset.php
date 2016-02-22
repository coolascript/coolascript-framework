<?php
/**
* Text field
*/

namespace csframework;
class FieldFieldset extends Field
{
	private $_fields = array();
	
	function __construct($args)
	{
		$fields = $args['fields'];
		unset( $args['fields'] );
		parent::__construct( $args );
		$this->setFields( $fields );
	}

	public function setFields($val)
	{
		if ( is_array( $val ) ) {
			foreach ($val as $name => $field) {
				if ( isset( $field['type'] ) ) {
					$field_class = 'rhtheme\Field' . ucfirst($field['type']);
					if ( class_exists( $field_class ) ) {
						$field['name'] = $name;
						$field['parent'] = &$this;
						$this->_fields[$name] = new $field_class( $field );
					} else {
						throw new \Exception( sprintf( __( "Unknown field type `%s`", 'csframework' ), $field['type'] ) );
					}
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
		return isset( $this->_fields[$name] ) ? $this->_fields[$name] : null;
	}

	public function render()
	{
		?>
		<div class="field field-fieldset<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<h3><?php echo wp_kses_post( $this->_label ); ?></h3>
			<?php endif ?>
			<div id="<?php echo esc_attr( $this->getInputId() ); ?>" class="fieldset-fields">
			<?php if ($this->_fields): ?>
				<?php foreach ($this->_fields as $sf_name => $sf_field): ?>
					<div class="field-row">
						<?php $sf_field->setValue( isset( $this->_value[0][$sf_field->getName()] ) ? $this->_value[0][$sf_field->getName()] : '' )->render() ?>
					</div>
				<?php endforeach ?>
			<?php endif ?>
			</div>
		</div>
		<?php
	}

	public function sanitize($val)
	{
		return is_array( $val ) ? $val : '';
	}
}