.<?php
/**
* MultiSelect field
*/

namespace csframework;
class FieldMultiselect extends Field
{
	protected $_values = array();
	
	function __construct($args)
	{
		parent::__construct($args);
	}

	public function setValues($val)
	{
		$this->_values = is_array( $val ) ? $val : array();
		return $this;
	}

	public function getValues()
	{
		return $this->_values;
	}

	public function render()
	{
		?>
		<div class="field field-multiselect<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<label for="<?php echo esc_attr( $this->getInputId() ); ?>" class="label"><?php echo wp_kses_post( $this->_label ); ?>:</label>
			<?php endif ?>
			<select name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>[]" id="<?php echo esc_attr( $this->getInputId() ); ?>" class="<?php echo esc_attr( $this->_class ); ?>" multiple>
			<?php foreach ($this->_values as $value => $label): ?>
				<?php if ( is_array( $label ) ): ?>
					<optgroup label="<?php echo esc_attr( $value ); ?>">
						<?php foreach ($label as $group_item_val => $group_item_label): ?>
							<option value="<?php echo esc_attr( $group_item_val ); ?>" <?php selected( $group_item_val, !is_null( $this->_value ) ? $this->_value : $this->_default ); ?>><?php echo wp_kses_post( $group_item_label ); ?></option>
						<?php endforeach ?>
					</optgroup>
				<?php else: ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, !is_null( $this->_value ) ? $this->_value : $this->_default ); ?>><?php echo wp_kses_post( $label ); ?></option>
				<?php endif ?>
			<?php endforeach ?>
			</select>
			<?php if ( $this->_description ): ?>
				<div class="field-description">
					<?php echo wp_kses_post( $this->_description ); ?>
				</div>
			<?php endif ?>
		</div>
		<?php
	}

	public function sanitize($val)
	{
		return is_array( $val ) ? $val : array();
	}
}