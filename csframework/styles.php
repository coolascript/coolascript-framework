<?php
/**
* Styles class
*/

namespace csframework;
class Styles extends Abstractive
{
	/*protected $_styles = array(
		'handler' => array(
			'url' => 'http://...',
			'deps' = array(),
			'ver' => '1.0',
			'media' = 'all',
			'load' = false,
			'inline' = false,
			'load_check' => array( callable, ... )
		)
	)*/
	protected $_styles = array();
	protected $_inline_styles = array();
	protected $_system = array();

	function __construct() {}

	public function addStyle( $handler, array $args )
	{
		if ( is_array( $args ) ) {
			if ( $args['inline'] = isset( $args['inline'] ) ? $args['inline'] : false ) {
				if ( isset( $this->_inline_styles[$handler] ) && isset( $args['load_check'] ) ) {
					$this->_inline_styles[$handler]['load_check'][] = $args['load_check'];
				} else {
					$args['load_check'] = isset( $args['load_check'] ) ? array( $args['load_check'] ) : array();
					$this->_inline_styles[$handler] = $args;
				}
			} else {
				if ( isset( $this->_styles[$handler] ) && isset( $args['load_check'] ) ) {
					$this->_styles[$handler]['load_check'][] = $args['load_check'];
				} else {
					$args['load_check'] = isset( $args['load_check'] ) ? array( $args['load_check'] ) : array();
					$this->_styles[$handler] = $args;
				}
			}
		} else {
			throw new \Exception( __( 'Styles::addStyle() : Second parameter should be array.', Csframework::getTextDomain() ) );
		}
		return $this;
	}

	public function addSystemStyle( $handler )
	{
		if ( !in_array( $handler, $this->_system ) ) {
			$this->_system[] = $handler;
		}
		return $this;
	}

	public function setLoadStyle( $name, bool $load )
	{
		if ( isset( $this->_styles[$name] ) ) {
			$this->_styles[$name]['load'] = $load;
		}
		return $this;
	}

	public function removeStyle( $handler )
	{
		if ( isset( $this->_styles[$handler] ) ) {
			unset( $this->_styles[$handler] );
		}
		return $this;
	}

	public function getStyle( $handler )
	{
		return isset( $this->_styles[$handler] ) ? $this->_styles[$handler] : false;
	}

	private function _registerStyle( $handler )
	{
		if ( isset( $this->_styles[$handler] ) ) {
			wp_register_style( $handler, $this->_styles[$handler]['url'], isset( $this->_styles[$handler]['deps'] ) && is_array( $this->_styles[$handler]['deps'] ) ? $this->_styles[$handler]['deps'] : array(), isset( $this->_styles[$handler]['ver'] ) ? ( string ) $this->_styles[$handler]['ver'] : false, isset( $this->_styles[$handler]['media'] ) ? ( string ) $this->_styles[$handler]['media'] : 'all' );
		}
		return $this;
	}

	private function _enqueueStyle( $handler )
	{
		if ( isset( $this->_styles[$handler]['load'] ) && $this->_styles[$handler]['load'] ) {
			if ( $this->_styles[$handler]['load_check'] ) {
				$load_check = false;
				foreach ($this->_styles[$handler]['load_check'] as $check) {
					if ( is_callable( $check ) && call_user_func( $check ) ) {
						$load_check = true;
						break;
					}
				}
				if ( $load_check ) {
					if ( $this->_styles[$handler]['inline'] ) {
						wp_add_inline_style( $handler, $this->_styles[$handler]['inline'] );
					} else {
						wp_enqueue_style( $handler );
					}
				}
			} else {
				if ( $this->_styles[$handler]['inline'] ) {
					wp_add_inline_style( $handler, $this->_styles[$handler]['inline'] );
				} else {
					wp_enqueue_style( $handler );
				}
			}
		}
		return $this;
	}

	private function _enqueueInlineStyle( $handler )
	{
		if ( isset( $this->_inline_styles[$handler]['load'] ) && $this->_inline_styles[$handler]['load'] ) {
			if ( $this->_inline_styles[$handler]['load_check'] ) {
				$load_check = false;
				foreach ($this->_inline_styles[$handler]['load_check'] as $check) {
					if ( is_callable( $check ) && call_user_func( $check ) ) {
						$load_check = true;
						break;
					}
				}
				if ( $load_check ) {
					wp_add_inline_style( $handler, $this->_inline_styles[$handler]['inline'] );
				}
			} else {
				wp_add_inline_style( $handler, $this->_inline_styles[$handler]['inline'] );
			}
		}
		return $this;
	}

	private function _registerStyles()
	{
		foreach ($this->_styles as $handler => $args) {
			if ( !$args['inline'] ) {
				$this->_registerStyle( $handler );
			}
		}
		return $this;
	}

	private function _enqueueStyles()
	{
		//$this->_styles = array_merge( $this->_styles, $this->_inline_styles );
		foreach ($this->_styles as $handler => $args) {
			$this->_enqueueStyle( $handler );
		}
		foreach ($this->_inline_styles as $handler => $args) {
			$this->_enqueueInlineStyle( $handler );
		}
		return $this;
	}

	private function _enqueueSystemStyles()
	{
		foreach ($this->_system as $handler) {
			wp_enqueue_style( $handler );
		}
		return $this;
	}

	public function enqueueAll()
	{
		$this
			->_registerStyles()
			->_enqueueSystemStyles()
			->_enqueueStyles();
	}

	public function load()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueAll' ), 1000 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAll' ) );
	}
}