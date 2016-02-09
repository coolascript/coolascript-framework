<?php
/**
* sortable field
*/

namespace csframework;
class FieldSortable extends Field
{
	private $_fields;

	function __construct($args)
	{
		parent::__construct($args);
	}

	public function setFields($val)
	{
		if ( is_array( $val ) ) {
			foreach ($val as $name => $field) {
				if ( isset( $field['type'] ) ) {
					$field_class = 'rhtheme\Field' . ucfirst($field['type']);
					if ( class_exists( $field_class ) ) {
						$field['name'] = $name;
						$field['parent'] = &$this;
						$this->_fields[$name] = new $field_class( $field );
					} else {
						throw new \Exception( sprintf( __( "Unknown field type `%s`", Csframework::getTextDomain() ), $field['type'] ) );
					}
				}
			}
		}
	}

	public function render()
	{
		?>
		<div class="field field-sortable<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<h4><?php echo wp_kses_post( $field['label'] ); ?></h4>
			<?php endif ?>
			<div id="<?php echo esc_attr( $this->getInputId() ); ?>" class="sortable-fields sortable">
			<?php if ($this->_fields): ?>
				<?php $i = 0; foreach ($this->_fields as $sf_name => $sf_field): ?>
					<div class="sortable-field-row">
						<div class="sortable-row-controls">
							<span class="button sortable-handler dashicons dashicons-sort"></span>
						</div>
						<div class="field-row">
							<?php $sf_field->render() ?>
						</div>
					</div>
				<?php $i++; endforeach ?>
			<?php endif ?>
			</div>
		</div>
		<?php
	}

	public function sanitize($val)
	{
		return is_array( $val ) ? $val : '';
	}
}