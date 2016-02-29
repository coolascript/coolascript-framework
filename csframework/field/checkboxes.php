<?php
namespace csframework;
/**
* Checkboxes form field
*/
class FieldCheckboxes extends Field
{
	/**
	 * Radio buttons
	 * @var array Key based array ( value => Label )
	 */
	protected $_values = array();

	/**
	 * Instantiate a class object
	 * @param string $fields_base_name  Field base name
	 * @param array $args Field parameters
	 */
	function __construct( $fields_base_name, $args )
	{
		$this->setSanitize( 'bool' );
		parent::__construct( $fields_base_name, $args );
	}

	/**
	 * Set radio buttons
	 * @param array $val Field options. Key based array ( value => Label )
	 */
	public function setValues( $val )
	{
		$this->_values = is_array( $val ) ? $val : array();
		return $this;
	}

	/**
	 * Retrieve field value
	 * @return array Value
	 */
	public function getValue()
	{
		$key = array_search( '--no-value--', $this->_value );
		unset( $this->_value[$key] );
		return $this->_value;
	}

	/**
	 * Retriev Field options
	 * @return array Key based array ( value => Label )
	 */
	public function getValues()
	{
		return $this->_values;
	}

	/**
	 * Render a field HTML
	 * @return void
	 */
	public function render()
	{
		$this->_default = is_array( $this->_default ) ? $this->_default : array();
		?>
		<div class="csframework-field csframework-field-checkboxes<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' ); ?><?php echo esc_attr( $this->isRequired() ? ' csframework-required' : '' ); ?>"<?php echo ( bool ) $this->_depend ? ' data-depend="' . esc_attr( implode( ';', $this->getDependecies() ) ) . '"' : ''; ?>>
			<?php if ( $this->_label && $this->_show_label ): ?>
				<h5 class="csframework-label"><?php echo apply_filters( 'the_title', $this->_label ); ?>:<?php echo esc_attr( $this->isRequired() ? ' csframework-required' : '' ); ?><?php echo ( $this->isRequired() ? ' <em>*</em>' : '' ); ?></h5>
			<?php endif ?>
			<input type="hidden" name="<?php echo esc_attr( $this->getInputName() ); ?>[]" value="--no-value--" />
			<?php $i=0; foreach ( $this->_values as $value => $label ): ?>
				<label for="<?php echo esc_attr( $this->getInputId() . '-' . $i ); ?>" class="checkbox-label <?php echo esc_attr( $this->_class ); ?>">
					<input type="checkbox" name="<?php echo esc_attr( $this->getInputName() ); ?>[]" id="<?php echo esc_attr( $this->getInputId() . '-' . $i ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( ( string ) $value, !is_null( $this->_value ) ? $this->_value : $this->_default, true ), true ); ?> class="<?php echo esc_attr( $this->_class ); ?>" />
					<?php echo apply_filters( 'the_title', $label ); ?>
				</label>
			<?php $i++; endforeach ?>
			<?php if ( $this->_description ): ?>
				<?php echo apply_filters( 'the_content', $this->_description ); ?>
			<?php endif ?>
		</div>
		<?php
	}

	/**
	 * Sinitize function override
	 * @param  mixed $val Input value
	 * @return array      sanitized value
	 */
	public function sanitize( $val )
	{
		return is_array( $val ) ? $val : array();
	}
}