<?php
/**
* Email field
*/

namespace csframework;
class FieldEmail extends Field
{
	
	function __construct($args)
	{
		parent::__construct($args);
	}

	public function render()
	{
		?>
		<div class="field field-email<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<label for="<?php echo esc_attr( $this->getInputId() ); ?>" class="label"><?php echo wp_kses_post( $this->_label ); ?>:</label>
			<?php endif ?>
			<input type="email" name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" value="<?php echo esc_attr( $this->_value ? $this->_value : $this->_default ); ?>" class="<?php echo esc_attr( $this->_class ); ?>" />
			<?php if ( $this->_description ): ?>
				<div class="field-description">
					<?php echo wp_kses_post( $this->_description ); ?>
				</div>
			<?php endif ?>
		</div>
		<?php
	}
}