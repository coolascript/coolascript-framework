<?php
/**
* Scripts class
*/

namespace csframework;
class Scripts extends Abstractive
{
	/*protected $_scripts = array(
		'handler' => array(
			'url' => 'http://...',
			'deps' = array(),
			'ver' => '1.0',
			'in_footer' = true,
			'load' = false,
			'load_check' => array( callable, ... )
		)
	)*/
	protected $_scripts = array();
	protected $_system = array();
	protected $_enqueue_media = false;

	function __construct() {}

	public function addScript( $handler, $args )
	{
		if ( is_array( $args ) ) {
			if ( isset( $this->_scripts[$handler] ) && isset( $args['load_check'] ) ) {
				$this->_scripts[$handler]['load_check'][] = $args['load_check'];
			} else {
				$args['load_check'] = isset( $args['load_check'] ) ? array( $args['load_check'] ) : array();
				$this->_scripts[$handler] = $args;
			}
		} else {
			throw new \Exception( __( 'Scripts::addScript() : Second parameter should be array.', Csframework::getTextDomain() ) );
		}
		return $this;
	}

	public function addSystemScript( $handler )
	{
		if ( !in_array( $handler, $this->_system ) ) {
			$this->_system[] = $handler;
		}
		return $this;
	}

	public function setLoadScript( $name, bool $load )
	{
		if ( isset( $this->_scripts[$name] ) ) {
			$this->_scripts[$name]['load'] = $load;
		}
	}

	public function removeScript( $handler )
	{
		if ( isset( $this->_scripts[$handler] ) ) {
			unset( $this->_scripts[$handler] );
		}
		return $this;
	}

	public function getScript( $handler )
	{
		return isset( $this->_scripts[$handler] ) ? $this->_scripts[$handler] : false;
	}

	private function _registerScript( $handler )
	{
		if ( isset( $this->_scripts[$handler] ) ) {
			if ( wp_script_is( $handler, 'registered' ) ) {
				wp_deregister_script( $handler );
			}
			wp_register_script(
				$handler,
				$this->_scripts[$handler]['url'],
				isset( $this->_scripts[$handler]['deps'] ) && is_array( $this->_scripts[$handler]['deps'] ) ? $this->_scripts[$handler]['deps'] : array(),
				isset( $this->_scripts[$handler]['ver'] ) ? ( string ) $this->_scripts[$handler]['ver'] : false,
				isset( $this->_scripts[$handler]['in_footer'] ) ? ( bool ) $this->_scripts[$handler]['in_footer'] : true );
		}
		return $this;
	}

	private function _enqueueScript( $handler )
	{
		if ( isset( $this->_scripts[$handler]['load'] ) && $this->_scripts[$handler]['load'] ) {
			if ( isset( $this->_scripts[$handler]['load_check'] ) && $this->_scripts[$handler]['load_check'] ) {
				$load_check = false;
				foreach ($this->_scripts[$handler]['load_check'] as $check) {
					if ( is_callable( $check ) && call_user_func( $check ) ) {
						$load_check = true;
						break;
					}
				}
				if ( $load_check ) {
					wp_enqueue_script( $handler );
				}
			} else {
				wp_enqueue_script( $handler );
			}
		}
		return $this;
	}

	public function localizeScript( $handler, $name, $data )
	{
		if ( isset( $this->_scripts[$handler] ) ) {
			$this->_scripts[$handler]['localize'] = array(
				'name' => $name,
				'data' => $data,
			);
		}
		return $this;
	}

	public function setEnqueueMedia( $val )
	{
		$this->_enqueue_media = ( bool ) $val;
		return $this;
	}

	private function _registerScripts()
	{
		foreach ($this->_scripts as $handler => $args) {
			$this->_registerScript( $handler );
		}
		return $this;
	}

	private function _enqueueSystemScripts()
	{
		foreach ($this->_system as $handler) {
			wp_enqueue_script( $handler );
		}
		return $this;
	}

	private function _enqueueScripts()
	{
		foreach ($this->_scripts as $handler => $args) {
			//if ( ( is_admin() && $args['admin'] ) || ( !is_admin() && !$args['admin'] ) ) {
				if ( isset( $args['localize'] ) && $args['localize'] ) {
					wp_localize_script( $handler, $args['localize']['name'], $args['localize']['data'] );
				}
				$this->_enqueueScript( $handler );
			//}
		}
		return $this;
	}

	public function enqueueAll()
	{
		$this
			->_registerScripts()
			->_enqueueSystemScripts()
			->_enqueueScripts();
	}

	public function enqueueAdmin()
	{
		if ( $this->_enqueue_media ) {
			wp_enqueue_media();
		}
		$this
			->_registerScripts()
			->_enqueueSystemScripts()
			->_enqueueScripts();
	}

	public function load()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueAll' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdmin' ) );
	}
}