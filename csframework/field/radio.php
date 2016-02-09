<?php
/**
* Radio buttons field
*/

namespace csframework;
class FieldRadio extends Field
{
	protected $_values = array();
	
	function __construct($args)
	{
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
		<div class="field field-radio<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<h5 class="label"><?php echo wp_kses_post( $this->_label ); ?>:</h5>
			<?php endif ?>
			<?php $i=0; foreach ($this->_values as $value => $label): ?>
				<label for="<?php echo esc_attr( $this->getInputId() . '-' . $i ); ?>" class="radio-label <?php echo esc_attr( $this->_class ); ?>">
					<input type="radio" name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() . '-' . $i ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $value, !is_null( $this->_value ) && !empty( $this->_value ) ? $this->_value : $this->_default ); ?>>
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
}