<?php
namespace csframework;
/**
* Fields prototype class
*/
abstract class Field extends Base
{
	/**
	 * Field label
	 * @var string
	 */
	protected $_label = '';
	/**
	 * Field description
	 * @var string
	 */
	protected $_description = '';
	/**
	 * Field name
	 * @var string
	 */
	protected $_name = '';
	/**
	 * Index of field in some srapper such as Fieldset
	 * @var int|null
	 */
	protected $_index = null;
	/**
	 * Field type
	 * @var string
	 */
	protected $_type = '';
	/**
	 * Show label or not
	 * @var boolean
	 */
	protected $_show_label = true;
	/**
	 * Field extra CSS class
	 * @var string
	 */
	protected $_class = '';
	/**
	 * Default field value
	 * @var mixed|null
	 */
	protected $_default = null;
	/**
	 * Field Value
	 * @var mixed|null
	 */
	protected $_value = null;
	/**
	 * Field display dependencies
	 * @var array
	 */
	protected $_depend = null;
	/**
	 * Field is required or not
	 * @var boolean
	 */
	protected $_required = false;
	/**
	 * Sanitaze field function
	 * @var string
	 */
	protected $_sanitize = 'text';
	/**
	 * Field parent class
	 * @var object|null
	 */
	protected $_parent = null;
	/**
	 * @var string
	 */
	protected $_base_name = 'csframework';

	/**
	 * Instantiate Field object
	 * @param string $base_name  App object
	 * @param array $args Field parameters
	 */
	function __construct( $base_name, $args )
	{
		if ( isset( $args['name'] ) && !empty( $args['name'] ) ) {
			parent::__construct();
			$this->_base_name = $base_name;
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
		}
	}

	/**
	 * Override this function in your class to enqueue scripts and styles on frontend.
	 * Don't forget do parent::addAssets();
	 */
	public function addAssets()
	{
		parent::addAssets();
		wp_enqueue_script( 'csframework-field' );
	}

	/**
	 * Sets Field index parameter
	 * @param int $val Index
	 */
	public function setIndex( $val )
	{
		$this->_index = ( int ) $val;
		return $this;
	}

	/**
	 * Retrieve Field index parameter
	 * @return int Index
	 */
	public function getIndex()
	{
		return $this->_index;
	}

	/**
	 * Sets Field label parameter
	 * @param string $val Label
	 */
	public function setLabel( $val )
	{
		$this->_label = $val;
		return $this;
	}

	/**
	 * Retrieve Field label
	 * @return string Label
	 */
	public function getLabel()
	{
		return $this->_label;
	}

	/**
	 * Sets Field description
	 * @param string $val Label
	 */
	public function setDescription( $val )
	{
		$this->_description = $val;
		return $this;
	}

	/**
	 * Retrieve Field description
	 * @return string Description
	 */
	public function getDescription()
	{
		return $this->_description;
	}

	/**
	 * Sets Field type
	 * @param string $val Type
	 */
	public function setType( $val )
	{
		$this->_type = $val;
		return $this;
	}

	/**
	 * Retrieve Field type
	 * @return string Type
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * Sets Show field label or not
	 * @param bool $val
	 */
	public function setShow_label( $val )
	{
		$this->_show_label = ( bool ) $val;
		return $this;
	}

	/**
	 * Sets required field or not
	 * @param bool $val
	 */
	public function setRequired( $val )
	{
		$this->_required = ( bool ) $val;
	}

	/**
	 * Retriev if field is required
	 * @return boolean Required or not
	 */
	public function isRequired()
	{
		return ( bool ) $this->_required;
	}

	/**
	 * Sets field extra CSS class
	 * @param string $val CSS class
	 */
	public function setClass( $val )
	{
		$this->_class = $val;
		return $this;
	}

	/**
	 * Sets field default value
	 * @param mixed $val Default value
	 */
	public function setDefault( $val )
	{
		$this->_default = $this->sanitize( $val );
		return $this;
	}

	/**
	 * Retrieve Field default value
	 * @return mixed Default value
	 */
	public function getDefault()
	{
		return $this->_default;
	}

	/**
	 * Sets field value
	 * @param mixed $val Field Value
	 */
	public function setValue( $val )
	{
		$this->_value = is_null( $val ) ? null : $this->sanitize( $val );
		return $this;
	}

	/**
	 * Retrieve field value
	 * @return mixed Value
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * Sets Field name parameter
	 * @param string $val Field name
	 */
	public function setName( $val )
	{
		$this->_name = esc_attr( $val );
		return $this;
	}

	/**
	 * Retrieve Field name
	 * @param string $val Field name
	 */
	public function getName()
	{
		return esc_attr( $this->_name );
	}

	/**
	 * Sets Field sanitize function. Possible values: email, title, url, html_class, user, js, html, attribute, int, bool, float, textarea, color.
	 * @param string $val Sunitize function
	 */
	public function setSanitize( $val )
	{
		$this->_sanitize = $val;
		return $this;
	}

	/**
	 * Retrieve Field sanitize parameter
	 * @return string Sanitize
	 */
	public function getSanitize()
	{
		return $this->_sanitize;
	}

	/**
	 * Get field show dependencies
	 * @return array Dependencies
	 */
	public function getDepend()
	{
		return $this->_depend;
	}

	/**
	 * Sets field show dependencies
	 * @param array $val Dependencies
	 */
	public function setDepend( $val )
	{
		$this->_depend = $val;
		return $this;
	}

	/**
	 * Sets Field parent object
	 * @param object|null $val Parent object inctance
	 */
	public function setParent( $val )
	{
		$this->_parent = $val;
		return $this;
	}

	/**
	 * Retrieve parent object instance
	 * @return Object Paren object
	 */
	public function getParent()
	{
		return $this->_parent;
	}

	/**
	 * Sanitize field value
	 * @param  mixed $val Field value
	 */
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
				return $val;
				break;
		}
	}

	/**
	 * Retrieve input HTML name attribute
	 * @param  Object $parent Field paren object
	 * @param  string $sep    Field name separator. Default: null (field[hello][world]). If not null - field{$sep}hello{$sep}world
	 * @return string         Field name attribute
	 */
	public function getInputName( $parent = null, $sep = null )
	{
		$open = '[';
		$close = ']';
		if ( !is_null( $sep ) ) {
			$open = $sep;
			$close = '';
		}
		$addBase = true;
		$input_name = '';
		if ( is_null( $parent ) ) {
			$parent = $this->getParent();
			$input_name = is_null( $parent ) ? $this->_name : $open . $this->_name . $close;
		}
		if ( method_exists( $parent, 'getParent' ) ) {
			$input_name = $this->getInputName( $parent->getParent(), $sep ) . ( method_exists( $parent, 'getName' ) ? $open . $parent->getName() . $close : '' ) . $open . ( int ) $parent->getIndex() . $close . $input_name;
			$addBase = false;
		} else {
			if ( method_exists( $parent, 'getIndex' ) ) {
				$input_name = ( method_exists( $parent, 'getName' ) ? $open . $parent->getName() . $close : '' ) . $open . ( int ) $parent->getIndex() . $close . $input_name;
			} else {
				$input_name = ( method_exists( $parent, 'getName' ) ? $open . $parent->getName() . $close : '' ) . $input_name;
			}
		}
		return esc_attr( ( $addBase ? $this->_base_name : '' ) . $input_name );
	}

	/**
	 * Retrieve input HTML id attribute
	 * @param  Object $parent Field paren object
	 * @return string         Field id attribute
	 */
	public function getInputId( $parent = null )
	{
		return $this->getInputName( $parent, '-' );
	}

	/**
	 * Retrieve input nesting path
	 * @param  Object $parent Field paren object
	 * @return string         Field nesting path
	 */
	public function getInputPath( $parent = null )
	{
		return $this->getInputName( $parent, '_' );
	}

	/**
	 * Retrieve Field dependencies list
	 * @return array Dependencies
	 */
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

	/**
	 * Render Field HTML code
	 */
	abstract public function render();
}