<?php
namespace csframework;
/**
* Color field field
*/
class FieldColor extends Field
{
	
	/**
	 * Instantiate a class object
	 * @param string $fields_base_name  Field base name
	 * @param array $args Field parameters
	 */
	function __construct( $fields_base_name, $args )
	{
		$this->_sanitize = 'color';
		parent::__construct( $fields_base_name, $args );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function addAdminAssets()
	{
		wp_enqueue_style( 'iris' );
		wp_enqueue_script( 'csframework-color-field' );
	}

	/**
	 * Set acceped file type
	 * @param string $val File type: image, audio, video, file
	 */
	public function render()
	{
		?>
		<div class="csframework-field csframework-field-color<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' ); ?><?php echo esc_attr( $this->isRequired() ? ' csframework-required' : '' ); ?>"<?php echo ( bool ) $this->_depend ? ' data-depend="' . esc_attr( implode( ';', $this->getDependecies() ) ) . '"' : ''; ?>>
			<?php if ( $this->_label && $this->_show_label ): ?>
				<label for="<?php echo esc_attr( $this->getInputId() ); ?>" class="csframework-label"><?php echo apply_filters( 'the_title', $this->_label ); ?>:<?php echo ( $this->isRequired() ? ' <em>*</em>' : '' ); ?></label>
			<?php endif ?>
			<input type="text" name="<?php echo esc_attr( $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" value="<?php echo esc_attr( !is_null( $this->_value ) ? $this->_value : $this->_default ); ?>" class="csframework-color-field<?php echo esc_attr( $this->_class ? ' ' . $this->_class : '' ); ?><?php echo esc_attr( $this->isRequired() ? ' csframework-required-field' : '' ); ?>" />
			<?php if ( $this->_description ): ?>
				<?php echo apply_filters( 'the_content', $this->_description ); ?>
			<?php endif ?>
		</div>
		<?php
	}
}