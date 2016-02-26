<?php
namespace csframework;
/**
* Form fieldset
*/
class FieldFieldset extends Field
{
	/**
	 * Fields in each section
	 * @var array
	 */
	private $_fields = array();
	
	/**
	 * Instantiate a class object
	 * @param csframework\Csframework $app  App instance
	 * @param array $args Field parameters
	 */
	function __construct( $app, $args )
	{
		$fields = array();
		if ( is_array( $args ) && isset( $args['fields'] ) ) {
			$fields = $args['fields'];
			unset( $args['fields'] );
		}
		parent::__construct( $app, $args );
		$this->setFields( $fields );
	}

	/**
	 * Set section fiels
	 * @param array $val Array of fields to add
	 */
	public function setFields( $val )
	{
		if ( is_array( $val ) ) {
			foreach ( $val as $name => $field ) {
				if ( isset( $field['type'] ) ) {
					$field_class = 'csframework\Field' . ucfirst( $field['type'] );
					if ( class_exists( $field_class ) ) {
						$field['name'] = $name;
						$field['parent'] = &$this;
						$this->_fields[$name] = new $field_class( $this->_app, $field );
					} else {
						throw new \Exception( sprintf( __( "csframework\FieldSortable: Unknown field type `%s`", 'coolascript-framework' ), $field['type'] ) );
					}
				}
			}
		}
	}

	/**
	 * Retriev fields
	 * @return array Array of all fields
	 */
	public function getFields()
	{
		return $this->_fields;
	}

	/**
	 * Retriev field by its name
	 * @param  string $name Name of the field
	 * @return csframework\Field|null       Field object or null if not exist
	 */
	public function getField( $name )
	{
		return isset( $this->_fields[$name] ) ? $this->_fields[$name] : null;
	}

	/**
	 * Add new field
	 * @param csframework\Field|array $field New field object or array with field parameters
	 */
	public function addField( $field )
	{
		if ( is_array( $field ) && isset( $field['name'], $field['type'] ) ) {
			$field_class = 'rhtheme\Field' . ucfirst( $field['type'] );
			if ( class_exists( $field_class ) ) {
				$field['parent'] = &$this;
				$this->_fields[$field['name']] = new $field_class( $field );
			} else {
				throw new \Exception( sprintf( __( "csframework\FieldSortable: Unknown field type `%s`", 'coolascript-framework' ), $field['type'] ) );
			}
		} elseif ( is_object( $field ) && method_exists( $field, 'getName' ) && method_exists( $field, 'getType' ) ) {
			$this->_fields[$field->getName()] = $field;
		}
	}

	/**
	 * Remove field by its name
	 * @param string $name Field name
	 */
	public function removeField( $name )
	{
		if ( isset( $this->_fields[$name] ) ) {
			unset( $this->_fields[$name] );
		}
	}

	/**
	 * Render a field HTML
	 * @return void
	 */
	public function render()
	{
		?>
		<fieldset class="csframework-field csframework-field-fieldset<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' ); ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' ); ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<legend><?php echo wp_kses_post( $this->_label ); ?></legend>
			<?php endif ?>
			<div id="<?php echo esc_attr( $this->getInputId() ); ?>" class="csframework-fieldset-fields">
			<?php if ($this->_fields): ?>
				<?php foreach ( $this->_fields as $sf_name => $sf_field ): ?>
					<div class="csframework-field-row">
						<?php $sf_field->setValue( isset( $this->_value[0][$sf_field->getName()] ) ? $this->_value[0][$sf_field->getName()] : '' )->render() ?>
					</div>
				<?php endforeach ?>
			<?php endif ?>
			</div>
		</fieldest>
		<?php
	}

	/**
	 * Sanitize field value
	 * @param  mixed $value Field value
	 * @return array        Sanitized value
	 */
	public function sanitize( $value )
	{
		$s_value = array();
		if ( $value = is_array( $value ) ? array_values( $value ) : array() ) {
			foreach ( $value as $indx => $val ) {
				$s_value[$indx] = array();
				foreach ( $this->_fields as $rf_name => $rf_field ) {
					$s_value[$indx][$rf_name] = $rf_field->sanitize( isset( $val[$rf_field->getName()] ) ? $val[$rf_field->getName()] : null );
				}
			}
		}
		return $s_value;
	}
}