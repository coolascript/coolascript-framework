<?php
namespace csframework;
/**
* Text form field
*/
class FieldText extends Field
{
	
	/**
	 * Instantiate a class
	 * @param csframework\Csframework $app  App instance
	 * @param array $args Field parameters
	 */
	function __construct( $app, $args )
	{
		parent::__construct( $app, $args );
	}

	/**
	 * Render a field HTML
	 * @return void
	 */
	public function render()
	{
		?>
		<div class="field field-text<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<label for="<?php echo esc_attr( $this->getInputId() ); ?>" class="label"><?php echo wp_kses_post( $this->_label ); ?>:</label>
			<?php endif ?>
			<input type="text" name="<?php echo esc_attr( $this->_app->getFieldsVar() . $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" value="<?php echo esc_attr( $this->_value ? $this->_value : $this->_default ); ?>" class="<?php echo esc_attr( $this->_class ); ?>" />
			<?php if ( $this->_description ): ?>
				<div class="field-description">
					<?php echo wp_kses_post( $this->_description ); ?>
				</div>
			<?php endif ?>
		</div>
		<?php
	}
}