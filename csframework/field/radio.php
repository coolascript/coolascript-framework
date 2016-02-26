<?php
namespace csframework;
/**
* Radio buttons field
*/
class FieldRadio extends Field
{
	/**
	 * Radio buttons
	 * @var array Key based array ( value => Label )
	 */
	protected $_values = array();
	
	/**
	 * Instantiate a class object
	 * @param csframework\Csframework $app  App instance
	 * @param array $args Field parameters
	 */
	function __construct( $app, $args )
	{
		parent::__construct( $app, $args );
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
		?>
		<div class="csframework-field csframework-field-radio<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' ); ?><?php echo esc_attr( $this->isRequired() ? ' csframework-required' : '' ); ?>"<?php echo ( bool ) $this->_depend ? ' data-depend="' . esc_attr( implode( ';', $this->getDependecies() ) ) . '"' : ''; ?>>
			<?php if ( $this->_label && $this->_show_label ): ?>
				<h5 class="csframework-label"><?php echo apply_filters( 'the_title', $this->_label ); ?>:<?php echo ( $this->isRequired() ? ' <em>*</em>' : '' ); ?></h5>
			<?php endif ?>
			<?php $i=0; foreach ( $this->_values as $value => $label ): ?>
				<label for="<?php echo esc_attr( $this->getInputId() . '-' . $i ); ?>" class="csframework-radio-label <?php echo esc_attr( $this->_class ); ?>">
					<input type="radio" name="<?php echo esc_attr( $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() . '-' . $i ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $value, !is_null( $this->_value ) && !empty( $this->_value ) ? $this->_value : $this->_default ); ?>>
					<?php echo apply_filters( 'the_title', $label ); ?>
				</label>
			<?php $i++; endforeach ?>
			<?php if ( $this->_description ): ?>
				<?php echo apply_filters( 'the_content', $this->_description ); ?>
			<?php endif ?>
		</div>
		<?php
	}
}