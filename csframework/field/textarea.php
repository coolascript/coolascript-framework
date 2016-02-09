<?php
/**
* Textarea field
*/

namespace csframework;
class FieldTextarea extends Field
{
	
	function __construct($args)
	{
		$this->setSanitize( 'textarea' );
		parent::__construct($args);
	}

	public function render()
	{
		?>
		<div class="field field-textarea<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<label for="<?php echo esc_attr( $this->getInputId() ); ?>" class="label"><?php echo wp_kses_post( $this->_label ); ?>:</label>
			<?php endif ?>
			<textarea name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" class="<?php echo esc_attr( $this->_class ); ?>"><?php echo esc_textarea( $this->_value ? $this->_value : $this->_default ); ?></textarea>
			<?php if ( $this->_description ): ?>
				<div class="field-description">
					<?php echo wp_kses_post( $this->_description ); ?>
				</div>
			<?php endif ?>
		</div>
		<?php
	}
}