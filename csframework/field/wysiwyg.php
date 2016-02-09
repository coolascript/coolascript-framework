<?php
/**
* WYSIWYG field
*/

namespace csframework;
class FieldWysiwyg extends Field
{
	
	function __construct($args)
	{
		$this->setSanitize( 'textarea' );
		parent::__construct($args);
		$this->_addAssets();
	}

	private function _addAssets()
	{
		$theme = Csframework::getInstance();
		$theme->scripts
			->addScript( 'theme-wysiwyg', array(
				'url' => get_template_directory_uri() . '/assets/csframework/js/wysiwyg.js',
				'deps' => array( 'editor' ),
				'ver' => '1.0.0',
				'load' => true,
				'load_check' => 'is_admin',
			) );
	}

	public function render()
	{
		?>
		<div class="field field-wysiwyg<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<label for="<?php echo esc_attr( $this->getInputId() ); ?>" class="label"><?php echo wp_kses_post( $this->_label ); ?>:</label>
			<?php endif ?>

			<div class="wp-editor-container">
				<textarea name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" class="<?php echo esc_attr( $this->_class ); ?> wysiwyg-field"><?php echo esc_textarea( $this->_value ? $this->_value : $this->_default ); ?></textarea>
			</div>
			<?php if ( $this->_description ): ?>
				<div class="field-description">
					<?php echo wp_kses_post( $this->_description ); ?>
				</div>
			<?php endif ?>
		</div>
		<?php
	}
}