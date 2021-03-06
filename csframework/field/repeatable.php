<?php
namespace csframework;
/**
* Repeatable form field.
*/
class FieldRepeatable extends Field
{
	/**
	 * Fields in each new section
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
		add_action( 'wp_ajax_' . $this->getInputPath(), array( $this, 'ajaxRow' ) );
	}

	/**
	 * Enqueue scripts and styles on backend.
	 * Override this function in your class to enqueue scripts and styles on backend.
	 * Don't forget do parent::addAdminAssets();
	 */
	public function addAdminAssets()
	{
		parent::addAdminAssets();
		wp_enqueue_style( 'csframework-repeatable-field' );
		wp_enqueue_script( 'csframework-repeatable-field' );
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
					if ( $field['type'] != 'repeatable' ) {
						$field_class = 'csframework\Field' . ucfirst( $field['type'] );
						if ( class_exists( $field_class ) ) {
							$field['name'] = $name;
							$field['parent'] = $this;
							$this->_fields[$name] = new $field_class( $this->_base_name, $field );
						} else {
							throw new \Exception( sprintf( __( "csframework\FieldRepeatable: Unknown field type `%s`", 'coolascript-framework' ), $field['type'] ) );
						}
					} else {
						throw new \Exception( __( "csframework\FieldRepeatable: You can't add `repeatable` field as a child of another `repeatable` field. May be in the next version.", 'coolascript-framework' ) );
					}
				}
			}
		}
		return $this;
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
		<div class="csframework-field csframework-field-repeatable<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' ); ?><?php echo esc_attr( $this->isRequired() ? ' csframework-required' : '' ); ?>"<?php echo ( bool ) $this->_depend ? ' data-depend="' . esc_attr( implode( ';', $this->getDependecies() ) ) . '"' : ''; ?>>
			<?php if ( $this->_label && $this->_show_label ): ?>
				<h5 class="csframework-label"><?php echo apply_filters( 'the_title', $this->_label ); ?><?php echo ( $this->isRequired() ? ' <em>*</em>' : '' ); ?></h5>
			<?php endif ?>
			<div id="<?php echo esc_attr( $this->getInputId() ); ?>" class="csframework-subfields csframework-sortable" data-rows="<?php echo sizeof( $this->_value ); ?>">
			<?php if ( $this->_value ): ?>
				<?php foreach ( $this->_value as $indx => $val ): ?>
					<?php $this->setIndex( $indx )->_renderRow( $val ) ?>
				<?php endforeach ?>
			<?php endif ?>
			</div>
			<div class="csframework-field-row">
				<a href="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=<?php echo esc_attr( $this->getInputPath() ); ?>" data-target="<?php echo esc_attr( $this->getInputId() ); ?>" class="csframework-add-repeatable-row button"><?php _e( '+ Add', 'coolascript-framework' ) ?></a>
				<span class="spinner"></span>
			</div>
			<?php if ( $this->_description ): ?>
				<?php echo apply_filters( 'the_content', $this->_description ); ?>
			<?php endif ?>
		</div>
		<?php
	}

	private function _renderRow ( $val = array() )
	{
		?>
				<div class="csframework-repeatable-field-row">
					<div class="csframework-sortable-row-controls">
						<span class="button csframework-sortable-handler dashicons dashicons-sort"></span>
						<a href="#<?php echo esc_attr( $this->getInputId() ); ?>" class="csframework-remove-repeatable-row button dashicons dashicons-trash"></a>
					</div>
				<?php foreach ( $this->_fields as $rf_name => $rf_field ): ?>
					<div class="csframework-field-row">
						<?php $rf_field->setValue( isset( $val[$rf_field->getName()] ) ? $val[$rf_field->getName()] : '' )->render() ?>
					</div>
				<?php endforeach ?>
				</div>
		<?php
	}

	/**
	 * Set indexes to fields
	 * @param array $indexes Array of indexes
	 * @param object $parent  Field parent
	 */
	private function _setIndexes( $indexes, $parent = null )
	{
		if ( !$indexes ) {
			return $this;
		}
		if ( is_null( $parent ) ) {
			$parent = $this->getParent();
		}
		if ( $parent->getType() == $this->getType() ) {
			$indx = array_pop( $indexes );
			$parent->setIndex( $indx );
		}
		if ( $indexes ) {
			$this->_setIndexes( $indexes, $parent->getParent() );
		}
		return $this;
	}

	/**
	 * Ajax action to add new row
	 * @return void
	 */
	public function ajaxRow()
	{
		$indx = $_POST['indx'];
		$indexes = isset( $_POST['indexes'] ) ? array_reverse( $_POST['indexes'] ) : null;
		$this->setIndex( $indx )->_setIndexes( $indexes )->_renderRow();
		wp_die();
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