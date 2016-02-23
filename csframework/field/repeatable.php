<?php
namespace csframework;
/**
* Fields prototype class
*/
class FieldRepeatable extends Field
{
	/**
	 * Fields in each new section
	 * @var array
	 */
	private $_fields;

	function __construct( $app, $args )
	{
		$fields = $args['fields'];
		unset( $args['fields'] );
		parent::__construct( $app, $args );
		$this->_setFields( $fields );
		add_action( 'wp_ajax_' . $this->getInputPath(), array( $this, 'ajaxRow' ) );
	}

	/*public function reInit()
	{
		foreach ( $this->_fields as &$field ) {
			$field->setParent( $this );
			if ( method_exists( $field, 'reInit' ) ) {
				$field->reInit();
			}
		}
		add_action( 'wp_ajax_' . $this->getInputPath(), array( $this, 'ajaxRow' ) );
	}*/

	public function addAdminAssets()
	{
		parent::addAdminAssets();
		wp_enqueue_style( 'csframework-repeatable-field' );
		wp_enqueue_script( 'csframework-repeatable-field' );
	}

	private function _setFields( $val )
	{
		if ( is_array( $val ) ) {
			foreach ( $val as $name => $field ) {
				if ( isset( $field['type'] ) ) {
					if ( $field['type'] != 'repeatable' ) {
						$field_class = 'csframework\Field' . ucfirst( $field['type'] );
						if ( class_exists( $field_class ) ) {
							$field['name'] = $name;
							$field['parent'] = $this;
							$this->_fields[$name] = new $field_class( $this->_app, $field );
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

	public function getFields()
	{
		return $this->_fields;
	}

	public function getField( $name )
	{
		return isset( $this->_fields[$name] ) ? $this->_fields[$name] : null;
	}

	public function render()
	{
		/*foreach ($this->_fields as &$field) {
			$field->setParent( $this );
			if ( method_exists( $field, 'reInit' ) ) {
				$field->reInit();
			}
		}*/
		?>
		<div class="csframework-field csframework-field-repeatable<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' );  ?>"<?php echo ( bool ) $this->_depend ? ' data-depend="' . esc_attr( implode( ';', $this->getDependecies() ) ) . '"' : '';  ?>>
			<?php if ( $this->_label && $this->_show_label ): ?>
				<h5 class="label"><?php echo apply_filters( 'the_title', $this->_label ); ?></h5>
			<?php endif ?>
			<div id="<?php echo esc_attr( $this->getInputId() ); ?>" class="csframework-subfields csframework-sortable" data-rows="<?php echo sizeof( $this->_value ); ?>">
			<?php if ( $this->_value ): ?>
				<?php foreach ( $this->_value as $indx => $val ): ?>
					<?php $this->setIndex( $indx )->_renderRow( $val ) ?>
				<?php endforeach ?>
			<?php endif ?>
			</div>
			<div class="csframework-field-row">
				<span class="spinner"></span>
				<a href="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=<?php echo esc_attr( $this->getInputPath() ); ?>" data-target="<?php echo esc_attr(  $this->getInputId() ); ?>" class="csframework-add-repeatable-row button"><?php _e( '+ Add', 'coolascript-framework' ) ?></a>
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
					<?php $rf_field->setValue( isset( $val[$rf_field->getName()] ) ? $val[$rf_field->getName()] : '' ) ?>
					<div class="csframework-field-row">
						<?php $rf_field->render() ?>
					</div>
				<?php endforeach ?>
				</div>
		<?php
	}

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

	public function ajaxRow()
	{
		/*foreach ($this->_fields as &$field) {
			$field->setParent( $this );
			if ( method_exists( $field, 'reInit' ) ) {
				$field->reInit();
			}
		}*/
		$indx = $_POST['indx'];
		$indexes = isset( $_POST['indexes'] ) ? array_reverse( $_POST['indexes'] ) : null;
		$this->setIndex( $indx )->_setIndexes( $indexes )->_renderRow();
		wp_die();
	}

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