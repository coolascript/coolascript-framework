<?php
/**
* Color field
*/

namespace csframework;
class FieldColor extends Field
{
	
	function __construct($args)
	{
		$this->_sanitize = 'color';
		parent::__construct($args);
		$this->_addAssets();
	}

	private function _addAssets()
	{
		$theme = Csframework::getInstance();
		$theme->styles->addStyle( 'theme-iris', array(
				'url' => get_template_directory_uri() . '/assets/csframework/css/iris.min.css',
				'ver' => '1.0.7',
				'load' => true,
			) );
		$theme->scripts
			->addScript( 'theme-iris', array(
				'url' => get_template_directory_uri() . '/assets/csframework/js/iris.min.js',
				'deps' => array( 'jquery-ui-draggable', 'jquery-ui-slider' ),
				'ver' => '1.0.7',
				'load' => false,
			) )->addScript( 'theme-colorpicker-init', array(
				'url' => get_template_directory_uri() . '/assets/csframework/js/colorpicker-init.js',
				'deps' => array( 'theme-iris' ),
				'ver' => '1.0.0',
				'load' => true,
			) );
	}

	public function render()
	{
		?>
		<div class="field field-color<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<label for="<?php echo esc_attr( $this->getInputId() ); ?>" class="label"><?php echo wp_kses_post( $this->_label ); ?>:</label>
			<?php endif ?>
			<input type="text" name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" value="<?php echo esc_attr( $this->_value ? $this->_value : $this->_default ); ?>" class="color-picker-field<?php echo esc_attr( $this->_class ? ' ' . $this->_class : '' ); ?>" />
			<?php if ( $this->_description ): ?>
				<div class="field-description">
					<?php echo wp_kses_post( $this->_description ); ?>
				</div>
			<?php endif ?>
		</div>
		<?php
	}
}