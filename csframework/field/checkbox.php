<?php
/**
* Checkbox field
*/

namespace csframework;
class FieldCheckbox extends Field
{
	
	function __construct($args)
	{
		$this->setSanitize( 'bool' );
		parent::__construct($args);
	}

	public function render()
	{
		?>
		<div class="field field-checkbox<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<label for="<?php echo esc_attr( $this->getInputId() ); ?>" class="checkbox-label">
				<input type="checkbox" name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" value="1" <?php checked( (bool) !is_null( $this->_value ) ? $this->_value : (bool) $this->_default, true ); ?> class="<?php echo esc_attr( $this->_class ); ?>" />
				<?php echo wp_kses_post( $this->_label ); ?>
			</label>
			<?php if ( $this->_description ): ?>
				<div class="field-description">
					<?php echo wp_kses_post( $this->_description ); ?>
				</div>
			<?php endif ?>
		</div>
		<?php
	}
}