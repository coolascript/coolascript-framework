<?php
/**
* Field
*/

namespace csframework;
abstract class Field extends Abstractive
{
	protected $_label = '';
	protected $_description = '';
	protected $_name = '';
	protected $_index = null;
	protected $_type = '';
	protected $_show_label = true;
	protected $_class = '';
	protected $_default = null;
	protected $_value = null;
	protected $_depend = null;

	protected $_sanitize = 'text';
	protected $_parent = null;

	function __construct( $args )
	{
		if ( isset( $args['name'] ) && !empty( $args['name'] ) ) {
			$parents = array();
			if ( isset( $args['parent'] ) ) {
				$parent = $args['parent'];
				while ( !is_null( $parent ) && get_class( $parent ) != 'PosttypeMetabox' ) {
					$parents[] = $parent->getName();
					$parent = method_exists( $parent, 'getParent' ) ? $parent->getParent() : null;
				}
				$parents = array_reverse( $parents );
			}
			$parents = implode( '-', $parents ) . ( count( $parents ) ? '-' : '' );
			$this->setOptions( $args );
			$this->_addAssets();
		}
	}

	private function _addAssets()
	{
		$theme = Csframework::getInstance();
		Csframework::getScripts()
			->addScript( 'theme-field', array(
				'url' => get_template_directory_uri() . '/assets/csframework/js/field.js',
				'deps' => array( 'jquery' ),
				'ver' => '1.0.0',
				'load' => true,
				'load_check' => 'is_admin',
			) );
	}

	public function setIndex( $val )
	{
		$this->_index = ( int ) $val;
		return $this;
	}

	public function getIndex()
	{
		return $this->_index;
	}

	public function setLabel( $val )
	{
		$this->_label = $val;
		return $this;
	}

	public function getLabel()
	{
		return $this->_label;
	}

	public function setDescription( $val )
	{
		$this->_description = $val;
		return $this;
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function setType( $val )
	{
		$this->_type = $val;
		return $this;
	}

	public function getType()
	{
		return $this->_type;
	}

	public function setShow_label( $val )
	{
		$this->_show_label = ( bool ) $val;
		return $this;
	}

	public function setClass( $val )
	{
		$this->_class = $val;
		return $this;
	}

	public function setDefault( $val )
	{
		$this->_default = $this->sanitize( $val );
		return $this;
	}

	public function getDefault()
	{
		return $this->_default;
	}

	public function setValue( $val )
	{
		$this->_value = is_null( $val ) ? null : $this->sanitize( $val );
		return $this;
	}

	public function getValue()
	{
		return $this->_value;
	}

	public function setName( $val )
	{
		$this->_name = esc_attr( $val );
		return $this;
	}

	public function getName()
	{
		return esc_attr( $this->_name );
	}

	public function setSanitize( $val )
	{
		$this->_sanitize = $val;
		return $this;
	}

	public function getSanitize()
	{
		return $this->_sanitize;
	}

	public function getDepend()
	{
		return $this->_depend;
	}

	public function setDepend( $val )
	{
		$this->_depend = $val;
		return $this;
	}

	public function setParent( $val )
	{
		$this->_parent = $val;
		return $this;
	}

	public function getParent()
	{
		return $this->_parent;
	}

	public function sanitize( $val )
	{
		switch ( $this->_sanitize ) {
			case 'email':
				return sanitize_email( $val );
				break;

			case 'title':
				return sanitize_title( $val );
				break;

			case 'url':
				return esc_url( $val );
				break;

			case 'html_class':
				return sanitize_html_class( $val );
				break;

			case 'user':
				return sanitize_user( $val );
				break;

			case 'js':
				return esc_js( $val );
				break;

			case 'html':
				return esc_html( $val );
				break;

			case 'attribute':
				return esc_attr( $val );
				break;

			case 'int':
				return intval( $val );
				break;

			case 'bool':
				return ( bool ) $val;
				break;

			case 'float':
				return floatval( $val );
				break;

			case 'textarea':
				return ( string ) $val;
				break;

			case 'color':
				if ( '' === $val )
					return '';

				// 3 or 6 hex digits, or the empty string.
				if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $val ) )
					return $val;
				break;
			
			default:
				return sanitize_text_field( $val );
				break;
		}
	}

	public function getInputName( $parent = null )
	{
		$input_name = '';
		if ( is_null( $parent ) ) {
			$parent = $this->getParent();
			$input_name = is_null( $parent ) ? $this->_name : '[' . $this->_name . ']';
		}
		if ( method_exists( $parent, 'getParent' ) ) {
			$input_name = $this->getInputName( $parent->getParent() ) . ( method_exists( $parent, 'getName' ) ? '[' . $parent->getName() . ']' : '' ) . '[' . ( int ) $parent->getIndex() . ']' . $input_name;
		} else {
			if ( method_exists( $parent, 'getIndex' ) ) {
				$input_name = ( method_exists( $parent, 'getName' ) ? '[' . $parent->getName() . ']' : '' ) . '[' . (int) $parent->getIndex() . ']' . $input_name;
			} else {
				$input_name = ( method_exists( $parent, 'getName' ) ? '[' . $parent->getName() . ']' : '' ) . $input_name;
			}
		}
		return esc_attr( $input_name );
	}

	public function getInputId( $parent = null )
	{
		$input_name = '';
		if ( is_null( $parent ) ) {
			$parent = $this->getParent();
			$input_name = is_null( $parent ) ? $this->_name : '-' . $this->_name;
		}
		if ( method_exists( $parent, 'getParent' ) ) {
			$input_name = $this->getInputId( $parent->getParent() ) . '-' . ( method_exists($parent, 'getName') ? $parent->getName() . '-' : '' ) . ( int ) $parent->getIndex() . $input_name;
		} else {
			if ( method_exists( $parent, 'getIndex' ) ) {
				$input_name = ( method_exists($parent, 'getName' ) ? $parent->getName() . '-' : '' ) . ( int ) $parent->getIndex() . $input_name;
			} else {
				$input_name = ( method_exists($parent, 'getName' ) ? $parent->getName() : '' ) . $input_name;
			}
		}
		return esc_attr( $input_name );
	}

	public function getInputPath( $parent = null )
	{
		$input_name = '';
		if ( is_null( $parent ) ) {
			$parent = $this->getParent();
			$input_name = is_null( $parent ) ? $this->_name : '_' . $this->_name;
		}
		if ( method_exists( $parent, 'getParent' ) ) {
			$input_name = $this->getInputPath( $parent->getParent() ) . '_' . ( method_exists($parent, 'getName') ? $parent->getName() . '_' : '' ) . $input_name;
		} else {
			if ( method_exists( $parent, 'getIndex' ) ) {
				$input_name = ( method_exists($parent, 'getName') ? $parent->getName() . '_' : '' ) . $input_name;
			} else {
				$input_name = ( method_exists($parent, 'getName') ? $parent->getName() : '' ) . $input_name;
			}
		}
		return esc_attr( $input_name );
	}

	public function getDependecies()
	{
		if ( $this->_depend ) {
			$depend_on_field = method_exists( $this->getParent(), 'getInputId') ? $this->getParent()->getInputId() : $this->getParent()->getName();
			if (method_exists($this->getParent(), 'getIndex')) {
				$depend_on_field .= '-' . ( int ) $this->getParent()->getIndex();
			}
			$dependencies = array();
			foreach ($this->_depend as $key => $value) {
				$values = is_array( $value ) ? implode( ',', $value ) : $value;
				$dependencies[] = ($depend_on_field ? $depend_on_field . '-' : '') . "$key:$values";
			}
		}
		return $dependencies;
	}

	abstract public function render();
}