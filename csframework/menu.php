<?php
/**
* Menu navigation class
*/

namespace csframework;
class Menu
{
	protected $_slug = null;
	protected $_fields = array();
	
	function __construct( $slug, $descr )
	{
		register_nav_menu( $slug, $descr );
		$this->_slug = $slug;
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'setupItem' ) );
		add_action( 'nav_menu_item_fields', array( $this, 'renderFields' ) );
		add_action( 'wp_update_nav_menu_item', array( $this, 'onSave' ), 10, 3 );
	}

	public function getSlug()
	{
		return $this->_slug;
	}

	public function getName()
	{
		return $this->getSlug();
	}

	public function addField( $args )
	{
		if ( isset( $args['type'] ) && isset( $args['name'] ) && $args['name'] && !isset( $this->_fields[$args['name']] ) ) {
			$field_class = 'rhtheme\Field' . ucfirst($args['type']);
			if ( class_exists( $field_class ) ) {
				$args['parent'] = $this;
				$this->_fields[$args['name']] = new $field_class( $args );
			} else {
				throw new \Exception( sprintf( __( "Unknown field type `%s`", Csframework::getTextDomain() ), $args['type'] ) );
				
			}
		}
		return $this;
	}

	public function getField( $name )
	{
		return isset( $this->_fields[$name] ) ? $this->_fields[$name] : false;
	}

	public function renderFields( $item )
	{
		?>		
		<?php foreach ($this->_fields as $name => $field): ?>
			<p class="field-move hide-if-no-js description description-wide">
				<?php $field->setName( $name . '-' . $item->ID )->setValue( $item->$name )->render() ?>
			</p>
		<?php endforeach ?>
		<?php
	}

	public function onSave( $menu_id, $menu_item_db_id, $args )
	{
		if ( isset( $_REQUEST[Csframework::getFieldsVar()][$this->_slug] ) ) {
			foreach ($_REQUEST[Csframework::getFieldsVar()][$this->_slug] as $key => $value) {
				list( $option, $item_id ) = explode( '-', $key );
		        update_post_meta( $item_id, $option, $value );
			}
		}
	}

	public function setupItem( $menu_item )
	{
		foreach ($this->_fields as $key => $field) {
		    $menu_item->$key = get_post_meta( $menu_item->ID, $key, true );
		}
    	return $menu_item;
	}
}