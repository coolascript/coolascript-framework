<?php
namespace csframework;
/**
* Textarea form field
*/
class FieldTextarea extends Field
{
	
	/**
	 * Instantiate a class object
	 * @param csframework\Csframework $app  App instance
	 * @param array $args Field parameters
	 */
	function __construct( $app, $args )
	{
		$this->setSanitize( 'textarea' );
		parent::__construct( $app, $args );
	}

	/**
	 * Render a field HTML
	 * @return void
	 */
	public function render()
	{
		?>
		<div class="csframework-field csframework-field-textarea<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' );  ?>"<?php echo ( bool ) $this->_depend ? ' data-depend="' . esc_attr( implode( ';', $this->getDependecies() ) ) . '"' : '';  ?>>
			<?php if ( $this->_label && $this->_show_label ): ?>
				<label for="<?php echo esc_attr( $this->getInputId() ); ?>" class="label"><?php echo apply_filters( 'the_title', $this->_label ); ?>:</label>
			<?php endif ?>
			<textarea name="<?php echo esc_attr( $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" class="<?php echo esc_attr( $this->_class ); ?>" cols="40" rows="5"><?php echo esc_textarea( $this->_value ? $this->_value : $this->_default ); ?></textarea>
			<?php if ( $this->_description ): ?>
				<?php echo apply_filters( 'the_content', $this->_description ); ?>
			<?php endif ?>
		</div>
		<?php
	}
}