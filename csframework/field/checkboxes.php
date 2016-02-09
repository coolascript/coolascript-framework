<?php
/**
* Checkboxes field
*/

namespace csframework;
class FieldCheckboxes extends Field
{
	protected $_values = array();
	protected $_default = array();
	
	function __construct($args)
	{
		$this->setSanitize( 'bool' );
		parent::__construct($args);
	}

	public function setValues($val)
	{
		$this->_values = is_array( $val ) ? $val : array();
		return $this;
	}

	public function getValues()
	{
		return $this->_values;
	}

	public function render()
	{
		?>
		<div class="field field-checkboxes<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<h5 class="label"><?php echo wp_kses_post( $this->_label ); ?>:</h5>
			<?php endif ?>
			<?php $i=0; foreach ($this->_values as $value => $label): ?>
			<label for="<?php echo esc_attr( $this->getInputId() . '-' . $i ); ?>" class="checkbox-label <?php echo esc_attr( $this->_class ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>[]" id="<?php echo esc_attr( $this->getInputId() . '-' . $i ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, is_array( $this->_value ) ? $this->_value : $this->_default ), true ); ?> class="<?php echo esc_attr( $this->_class ); ?>" />
				<?php echo wp_kses_post( $label ); ?>
			</label>
			<?php $i++; endforeach ?>
			<?php if ( $this->_description ): ?>
				<div class="field-description">
					<?php echo wp_kses_post( $this->_description ); ?>
				</div>
			<?php endif ?>
		</div>
		<?php
	}

	public function sanitize($val)
	{
		return is_array( $val ) ? $val : array();
	}
}