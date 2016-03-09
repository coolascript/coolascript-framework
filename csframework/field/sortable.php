<?php
namespace csframework;
/**
* Sortable form field.
*/
class FieldSortable extends Field
{
	/**
	 * Fields in each section
	 * @var array
	 */
	private $_fields = array();
	
	/**
	 * Instantiate a class object
	 * @param string $fields_base_name  Field base name
	 * @param array $args Field parameters
	 */
	function __construct( $fields_base_name, $args )
	{
		$fields = array();
		if ( is_array( $args ) && isset( $args['fields'] ) ) {
			$fields = $args['fields'];
			unset( $args['fields'] );
		}
		parent::__construct( $fields_base_name, $args );
		$this->setFields( $fields );
	}

	/**
	 * Enqueue scripts and styles on backend.
	 * Override this function in your class to enqueue scripts and styles on backend.
	 * Don't forget do parent::addAdminAssets();
	 */
	public function addAdminAssets()
	{
		parent::addAdminAssets();
		wp_enqueue_style( 'csframework-sortable-field' );
		wp_enqueue_script( 'csframework-sortable-field' );
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
						$this->_fields[$name] = new $field_class( $this->_base_name, $field );
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
				$this->_fields[$field['name']] = new $field_class( $this->_base_name, $field );
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
		<div class="csframework-field csframework-field-sortable<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' ); ?>"<?php echo ( bool ) $this->_depend ? ' data-depend="' . esc_attr( implode( ';', $this->getDependecies() ) ) . '"' : ''; ?>>
			<?php if ( $this->_label && $this->_show_label ): ?>
				<h4 class="csframework-label"><?php echo apply_filters( 'the_title', $this->_label ); ?></h4>
			<?php endif ?>
			<div id="<?php echo esc_attr( $this->getInputId() ); ?>" class="csframework-sortable-fields csframework-sortable">
			<?php if ( $this->_fields ): ?>
				<?php $i = 0; foreach ( $this->_fields as $sf_name => $sf_field ): ?>
					<div class="csframework-sortable-field-row">
						<div class="csframework-sortable-row-controls">
							<span class="button csframework-sortable-handler dashicons dashicons-sort"></span>
						</div>
						<div class="csframework-field-row">
							<?php $sf_field->setValue( isset( $this->_value[0][$sf_field->getName()] ) ? $this->_value[0][$sf_field->getName()] : '' )->render() ?>
						</div>
					</div>
				<?php $i++; endforeach ?>
			<?php endif ?>
			</div>
			<?php if ( $this->_description ): ?>
				<?php echo apply_filters( 'the_content', $this->_description ); ?>
			<?php endif ?>
		</div>
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
		if ( $value = is_array( $value[0] ) ? array_values( $value[0] ) : array() ) {
			$indx = 0;
			foreach ( $this->_fields as $sf_name => $sf_field ) {
				$s_value[$sf_name] = $sf_field->sanitize( isset( $value[$indx] ) ? $value[$indx] : null );
				$indx++;
			}
		}
		return array( $s_value );
	}
}