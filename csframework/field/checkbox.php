<?php
namespace csframework;
/**
* Checkbox form field
*/
class FieldCheckbox extends Field
{
	/**
	 * Instantiate a class object
	 * @param csframework\Csframework $app  App instance
	 * @param array $args Field parameters
	 */
	function __construct( $app, $args )
	{
		$this->setSanitize( 'bool' );
		parent::__construct( $app, $args );
	}

	/**
	 * Render a field HTML
	 * @return void
	 */
	public function render()
	{
		?>
		<div class="csframework-field csframework-field-checkbox<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' );  ?>"<?php echo ( bool ) $this->_depend ? ' data-depend="' . esc_attr( implode( ';', $this->getDependecies() ) ) . '"' : '';  ?>>
			<label for="<?php echo esc_attr( $this->getInputId() ); ?>" class="csframework-checkbox-label">
				<input type="checkbox" name="<?php echo esc_attr( $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" value="1" <?php checked( ( bool ) !is_null( $this->_value ) ? $this->_value : ( bool ) $this->_default, true ); ?> class="<?php echo esc_attr( $this->_class ); ?>" />
				<?php echo apply_filters( 'the_title', $this->_label ); ?>
			</label>
			<?php if ( $this->_description ): ?>
				<?php echo apply_filters( 'the_content', $this->_description ); ?>
			<?php endif ?>
		</div>
		<?php
	}
}