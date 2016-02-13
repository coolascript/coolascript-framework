<?php
namespace csframework;
/**
* Base class to avoid repeating functions
*/
class Base
{
	
	public function __construct()
	{
		add_action( 'wp_ajax_file', array( $this, 'ajaxFile' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'addAssets' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'addAdminAssets' ), 100 );
		add_action( 'login_enqueue_scripts', array( $this, 'addLoginAssets' ), 100 );
	}

	public function __set( $name, $value )
	{
		$method = 'set' . $name;
		if ( ( 'mapper' == $name ) || !method_exists( $this, $method ) ) {
			throw new \Exception( sprintf( __( "Unknown class method `%s`", 'csframework' ), $method ) );
		}
		$this->$method( $value );
	}
	public function __get( $name )
	{
		$method = 'get' . $name;
		if ( ( 'mapper' == $name ) || !method_exists( $this, $method ) ) {
			throw new \Exception( sprintf( __( "Unknown class method `%s`", 'csframework' ), $method ) );
		}
		return $this->$method();
	}

	/**
	 * Sets all class parameters if Setter is exist.
	 * @param array $options [description]
	 */
	public function setOptions( array $options )
	{
		$methods = get_class_methods( $this );
		foreach ( $options as $key => $value ) {
			$method = 'set' . ucfirst( $key );
			if ( in_array( $method, $methods ) ) {
				$this->$method( $value );
			}
		}
		return $this;
	}

	/**
	 * Override this function in your class to enqueue scripts and styles on frontend.
	 * Don't forget do parent::addAssets();
	 */
	public function addAssets() {}

	/**
	 * Override this function in your class to enqueue scripts and styles on backend.
	 * Don't forget do parent::addAdminAssets();
	 */
	public function addAdminAssets() {}

	/**
	 * Override this function in your class to enqueue scripts and styles on login page.
	 * Don't forget do parent::addLoginAssets();
	 */
	public function addLoginAssets() {}
}