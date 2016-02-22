<?php
namespace csframework;
/**
 * Autoloader helps to load classes.
 * If class is in path/to/class.php file and it named as PathToClass than it will be loaded automaticaly
 *
 * @param array|null $config configuration array( '' )
 */
class Autoloader
{
	/**
	 * @var include pathes array
	 */
	protected $_includePath = array();
	
	/**
	 * @static Handle of a system path in $_includePath array
	 */
	public static $sysPath = 'csframework';

	function __construct( array $config = null )
	{
		if ( $config ) {
			$this->setConfig( $config );
		}
		$this->_includePath[self::$sysPath] = CSFRAMEWORK_PLUGIN_DIR . self::$sysPath . '/';
		spl_autoload_register( array( $this, 'load' ) );
	}

	public function __set( $name, $value )
	{
		$method = 'set' . $name;
		if ( !method_exists( $this, $method ) ) {
			throw new \Exception( sprintf( __( "csframework\Autoloader: Invalid property `%s`.", 'coolascript-framework' ), $name ) );
		}
		$this->$method( $value );
	}
	
	public function __get( $name )
	{
		$method = 'get' . $name;
		if ( !method_exists( $this, $method ) ) {
			throw new \Exception( sprintf( __( "csframework\Autoloader: Invalid property `%s`.", 'coolascript-framework' ), $name ) );
		}
		return $this->$method();
	}

	/**
	 * Sets all Autoloader properies
	 * @param array|null $config configuration array( 'IncludePath' => 'path\to\include' )
	 * @return csframework\Autoloader
	 */
	public function setConfig( array $config = null )
	{
		if ( is_array( $config ) ) {
			$methods = get_class_methods( $this );
			foreach ( $config as $key => $value ) {
				$method = 'set' . ucfirst( ( string ) $key );
				if ( in_array( $method, $methods ) ) {
					$this->$method( $value );
				}
			}
		}
		return $this;
	}

	/**
	 * Sets csframework\Autoloader::$_includePath array
	 * @param array|null $includePath Paths to automaticaly load classes from. Should be 'handle' => 'path' structured
	 * @return csframework\Autoloader
	 */
	public function setIncludePath( $includePaths )
	{
		$this->_includePath = ( array ) $includePaths;
		return $this;
	}

	/**
	 * Add new path to autoloader
	 * @param string $handle handle by which you can get or remove this path
	 * @param string $includePath path to include ( 'pat\to\include' )
	 * @return csframework\Autoloader
	 */
	public function addIncludePath( $handle, $includePath )
	{
		if ( !isset( $this->_includePath[$handle] ) ) {
			$this->_includePath[$handle] = $includePath;
		} else {
			throw new \Exception( sprintf( __( "csframework\Autoloader: Path with handle `%s` allready exist.", 'coolascript-framework' ), $handle ) );
		}
		return $this;
	}

	/**
	 * Get include path by handle
	 * @param string $handle handle of a path
	 * @return csframework\Autoloader
	 */
	public function removeIncludePath( $handle )
	{
		if ( isset( $this->_includePath[$handle] ) && $handle != self::$sysPath ) {
			unset( $this->_includePath[$handle] );
		} else {
			throw new \Exception( sprintf( __( "csframework\Autoloader: You cant remove `%s` path as it does not exist or reserved by system.", 'coolascript-framework' ), $handle ) );
		}
		return $this;
	}

	/**
	 * Retrieve include path by handle
	 * @param string $handle handle of a path
	 * @return string|false
	 */
	public function getIncludePath( $handle )
	{
		if ( isset( $this->_includePath[$handle] ) && $handle != self::$sysPath ) {
			return $this->_includePath[$handle];
		}
		return false;
	}

	/**
	 * Retrieve all include pathes
	 * @return array
	 */
	public function getIncludePaths()
	{
		return $this->_includePath;
	}

	/**
	 * include class file
	 * @param string $className class name to autoload
	 * @return void
	 */
	public function load( $className )
	{
		if ( strpos( $className, '\\' ) !== false ) {
			list( $namespace, $className ) = explode( '\\', $className );
		}
		$pathArray = preg_split('/([[:upper:]][[:lower:]]+)/', $className, null, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
		$path = strtolower( is_array($pathArray) ? implode( '/', $pathArray ) : $pathArray ) . '.php';
		foreach ( $this->_includePath as $includePath ) {
			if ( file_exists($includePath . $path) ) {
				require_once $includePath . $path;
				return;
			}
		}
	}
}
?>