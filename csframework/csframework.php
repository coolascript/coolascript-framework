<?php
namespace csframework;
/**
 * Csframework main class which you would like to extend in your app
 *
 * @package wp-coolascript-framework
 * @version 1.0.0
 * @param array|null $config configuration array( '' )
 */
class Csframework
{
	/**
	 * Default text domain for translations
	 * @var string
	 */
	protected $_text_domain = 'csframework';
	/**
	 * Default key for fields that generated by your plugin (theme). To avoid collisions set it to uniqe in your plugin (theme) with setTextdomain() function.
	 * @var string
	 */
	protected $_fields_var = 'csframework_fields';
	/**
	 * Your app PHP namespace
	 * @var string
	 */
	protected $_app_namespace = 'csframework';
	/**
	 * Path to your app
	 * @var string
	 */
	protected $_app_path = 'csframework';
	/**
	 * Please add your app dir to it with addApppath function
	 * @var csframework\Autoloader
	 */
	protected $_autoloader = null;

	private function __construct()
	{
		require_once __DIR__ . '/autoloader.php';
		$this->_autoloader = new Autoloader;
	}
	private function __clone()
	{
		return self::getInstance();
	}
	private function __wakeup()
	{
		return self::getInstance();
	}

	/**
	 * Retrive an instance of class
	 * @return csframework\Csframework
	 */
	public static function getInstance()
	{
		static $instances = array();
		$calledClass = get_called_class();
		if ( !isset( $instances[$calledClass] ) )
		{
			$instances[$calledClass] = new $calledClass();
		}
		return $instances[$calledClass];
	}

	/**
	 * Adds the path to autoloader. Should contain the '/' on the end.
	 * @param string $handle handle by which you can get or remove this path
	 * @param string $path path to include
	 * @return csframework\Csframework
	 */
	public function setApppath( $handle, $path )
	{
		$this->_autoloader->addIncludePath( $handle, $path );
		$this->_app_path = ( string ) $path;
		return $this;
	}

	public function getApppath()
	{
		return ( string ) $this->_app_path;
	}

	/**
	 * Sets the lacale text domain
	 * @param string $text_domain
	 * @return csframework\Csframework
	 */
	public function setTextDomain( $text_domain )
	{
		$this->_text_domain = ( string ) $text_domain;
		load_theme_textdomain( $this->getTextDomain(), $this->getApppath() . '/languages' );
		return $this;
	}

	/**
	 * Retrive the lacale text domain
	 * @return string
	 */
	public function getTextDomain()
	{
		return ( string ) $this->_text_domain;
	}

	/**
	 * Sets key for fields that generated by your plugin (theme)
	 * @param string $fields_var
	 * @return csframework\Csframework
	 */
	public function setFieldsVar( $fields_var )
	{
		$this->_fields_var = ( string ) $fields_var;
		return $this;
	}

	/**
	 * Retrive the key for fields that generated by your plugin (theme)
	 * @return string
	 */
	public function getFieldsVar()
	{
		return ( string ) $this->_fields_var;
	}

	/**
	 * Sets prject PHP namespace
	 * @param string $app_namespace
	 * @return csframework\Csframework
	 */
	public function setNamespace( $app_namespace )
	{
		$this->_app_namespace = ( string ) $app_namespace;
		return $this;
	}

	/**
	 * Retrive prject PHP namespace
	 * @return string
	 */
	public function getNamespace()
	{
		return ( string ) $this->_app_namespace;
	}

	/**
	 * Automaticly load all your widgets
	 * @return csframework\Csframework
	 */
	private function _loadWidgets()
	{
		$dirs = $this->_autoloader->getIncludePaths();
		global $wp_widget_factory;
		foreach ( $dirs as $dir ) {
			if ( file_exists( $dir . 'widget' ) && $handle = opendir( $dir . 'widget' ) ) {
				while ( false !== ( $entry = readdir( $handle ) ) ) {
					if ( is_file( $dir . 'widget/' . $entry ) ) {
						$class_name = $this->getNamespace() . '\Widget' . ucfirst( basename( $entry, '.php' ) );
						if ( class_exists( $class_name ) ) {
							register_widget( $class_name );
							$wp_widget_factory->widgets[$class_name]->setApp( $this );
						}
					}
				}
				closedir( $handle );
			}
		}
		return $this;
	}

	/**
	 * Automaticly load all your taxonomies
	 * @return csframework\Csframework
	 */
	private function _loadTaxonomies()
	{
		$dirs = $this->_autoloader->getIncludePaths();
		foreach ( $dirs as $dir ) {
			if ( file_exists( $dir . 'taxonomy' ) && $handle = opendir( $dir . 'taxonomy' ) ) {
				while ( false !== ( $entry = readdir( $handle ) ) ) {
					if ( is_file( $dir . 'taxonomy/' . $entry ) ) {
						$class_name =  $this->getNamespace() . '\Taxonomy' . ucfirst( basename( $entry, '.php' ) );
						if ( class_exists( $class_name ) ) {
							$class_name::getInstance()->setApp( $this );
						}
					}
				}
				closedir( $handle );
			}
		}
		return $this;
	}

	/**
	 * Automaticly load all your post types
	 * @return csframework\Csframework
	 */
	private function _loadPosttypes()
	{
		$dirs = $this->_autoloader->getIncludePaths();
		foreach ( $dirs as $dir ) {
			if ( file_exists( $dir . 'taxonomy' ) && $handle = opendir( $dir . 'posttype' ) ) {
				while ( false !== ( $entry = readdir( $handle ) ) ) {
					if ( is_file( $dir . 'posttype/' . $entry ) ) {
						$class_name = $this->getNamespace() . '\Posttype' . ucfirst( basename( $entry, '.php' ) );
						if ( class_exists( $class_name ) ) {
							$class_name::getInstance()->setApp( $this );
						}
					}
				}
				closedir( $handle );
			}
		}
		return $this;
	}

	/**
	 * WP init action trigger
	 * @return void
	 */
	public function init()
	{
		$this->_loadTaxonomies();
		$this->_loadPosttypes();
	}

	/**
	 * WP widgets_init action trigger
	 * @return void
	 */
	public function widgets_init()
	{
		$this->_loadWidgets();
	}

	/**
	 * Runs the app
	 * @return csframework\Csframework
	 */
	public function run()
	{
		remove_action('init', 'wp_widgets_init', 1);
		add_action('init', 'wp_widgets_init', 15);
		add_action( 'init', array( $this, 'init' ), 100 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		return $this;
	}
}