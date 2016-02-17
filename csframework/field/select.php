<?php
namespace csframework;
/**
* Select form field
*/
class FieldSelect extends Field
{
	/**
	 * Select options
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
	 * Set select field options
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
		<div class="csframework-field csframework-ield-select<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' );  ?>"<?php echo ( bool ) $this->_depend ? ' data-depend="' . esc_attr( implode( ';', $this->getDependecies() ) ) . '"' : '';  ?>>
			<?php if ( $this->_label && $this->_show_label ): ?>
				<label for="<?php echo esc_attr( $this->getInputId() ); ?>" class="label"><?php echo apply_filters( 'the_title', $this->_label ); ?>:</label>
			<?php endif ?>
			<select name="<?php echo esc_attr( $this->getInputName() ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" class="<?php echo esc_attr( $this->_class ); ?>">
			<?php foreach ( $this->_values as $value => $label ): ?>
				<?php if ( is_array( $label ) ): ?>
					<optgroup label="<?php echo esc_attr( $value ); ?>">
						<?php foreach ( $label as $group_item_val => $group_item_label ): ?>
							<option value="<?php echo esc_attr( $group_item_val ); ?>" <?php selected( $group_item_val, !is_null( $this->_value ) && !empty( $this->_value ) ? $this->_value : $this->_default ); ?>><?php echo apply_filters( 'the_title', $group_item_label ); ?></option>
						<?php endforeach ?>
					</optgroup>
				<?php else: ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, !is_null( $this->_value ) && !empty( $this->_value ) ? $this->_value : $this->_default ); ?>><?php echo apply_filters( 'the_title', $label ); ?></option>
				<?php endif ?>
			<?php endforeach ?>
			</select>
			<?php if ( $this->_description ): ?>
				<?php echo apply_filters( 'the_content', $this->_description ); ?>
			<?php endif ?>
		</div>
		<?php
	}
}