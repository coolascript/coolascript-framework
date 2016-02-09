<?php
/**
* repeatable field
*/

namespace csframework;
class FieldRepeatable extends Field
{
	private $_fields;

	function __construct( $args )
	{
		$fields = $args['fields'];
		unset( $args['fields'] );
		parent::__construct( $args );
		$this->_setFields( $fields );
		add_action( 'wp_ajax_' . $this->getInputPath(), array( $this, 'ajaxRow' ) );
		$this->_addAssets();
	}

	public function reInit()
	{
		foreach ($this->_fields as &$field) {
			$field->setParent( $this );
			if ( method_exists( $field, 'reInit' ) ) {
				$field->reInit();
			}
		}
		add_action( 'wp_ajax_' . $this->getInputPath(), array( $this, 'ajaxRow' ) );
	}

	private function _addAssets()
	{
		Csframework::getScripts()
			->addScript( 'theme-repeatable', array(
				'url' => get_template_directory_uri() . '/assets/csframework/js/repeatable.js',
				'deps' => array( 'jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-sortable' ),
				'ver' => '1.0.0',
				'load' => true,
				'load_check' => 'is_admin',
			) );
	}

	private function _setFields( $val )
	{
		if ( is_array( $val ) ) {
			foreach ($val as $name => $field) {
				if ( isset( $field['type'] ) ) {
					$field_class = 'rhtheme\Field' . ucfirst($field['type']);
					if ( class_exists( $field_class ) ) {
						$field['name'] = $name;
						$field['parent'] = $this;
						$this->_fields[$name] = new $field_class( $field );
					} else {
						throw new \Exception( sprintf( __( "Unknown field type `%s`", Csframework::getTextDomain() ), $field['type'] ) );
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
		foreach ($this->_fields as &$field) {
			$field->setParent( $this );
			if ( method_exists( $field, 'reInit' ) ) {
				$field->reInit();
			}
		}
		?>
		<div class="field field-repeatable<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ($this->_label && $this->_show_label): ?>
				<h5 class="label"><?php echo wp_kses_post( $this->_label ); ?></h5>
			<?php endif ?>
			<div id="<?php echo esc_attr( $this->getInputId() ); ?>" class="subfields sortable" data-rows="<?php echo esc_attr( sizeof( $this->_value ) ); ?>">
			<?php if ($this->_value): ?>
				<?php foreach ($this->_value as $indx => $val): ?>
					<?php $this->setIndex($indx)->_renderRow( $val ) ?>
				<?php endforeach ?>
			<?php endif ?>
			</div>
			<div class="field-row">
				<a href="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=<?php echo esc_attr( $this->getInputPath() ); ?>" data-target="<?php echo esc_attr(  $this->getInputId() ); ?>" class="sk-add-repeatable-row button"><?php _e( '+ Add', Csframework::getTextDomain() ) ?></a>
			</div>
			<?php if ( $this->_description ): ?>
				<div class="field-description">
					<?php echo wp_kses_post( $this->_description ); ?>
				</div>
			<?php endif ?>
		</div>
		<?php
	}

	private function _renderRow ($val = array() )
	{
		?>
				<div class="repeatable-field-row">
					<div class="sortable-row-controls">
						<span class="button sortable-handler dashicons dashicons-sort"></span>
						<a href="#<?php echo esc_attr( $this->getInputId() ); ?>" class="remove-repeatable-row button dashicons dashicons-trash"></a>
					</div>
				<?php foreach ($this->_fields as $rf_name => $rf_field): ?>
					<?php $rf_field->setValue( isset( $val[$rf_field->getName()] ) ? $val[$rf_field->getName()] : '' ) ?>
					<div class="field-row">
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
		foreach ($this->_fields as &$field) {
			$field->setParent( $this );
			if ( method_exists( $field, 'reInit' ) ) {
				$field->reInit();
			}
		}
		$indx = $_POST['indx'];
		$indexes = isset( $_POST['indexes'] ) ? array_reverse( $_POST['indexes'] ) : null;
		$this->setIndex( $indx )->_setIndexes( $indexes )->_renderRow();
		wp_die();
	}

	public function sanitize( $value )
	{
		$s_value = array();
		if ( $value = is_array( $value ) ? array_values( $value ) : array() ) {
			foreach ($value as $indx => $val) {
				$s_value[$indx] = array();
				foreach ($this->_fields as $rf_name => $rf_field) {
					$s_value[$indx][$rf_name] = $rf_field->sanitize( isset( $val[$rf_field->getName()] ) ? $val[$rf_field->getName()] : null );
				}
			}
		}
		return $s_value;
	}
}