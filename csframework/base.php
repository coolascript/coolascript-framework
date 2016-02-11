<?php
namespace csframework;
/**
* Base class to avoid repeating functions
*/
class Base
{
	
	private function __construct()
	{
		add_action( 'wp_ajax_file', array( $this, 'ajaxFile' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'addAssets' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'addAdminAssets' ), 100 );
		add_action( 'login_enqueue_scripts', array( $this, 'addLoginAssets' ), 100 );
	}

	/**
	 * Override this function in your class to enqueue scripts and styles on frontend.
	 * Don't forget do parent::addScript();
	 */
	public function addAssets() {}

	/**
	 * Override this function in your class to enqueue scripts and styles on backend.
	 * Don't forget do parent::addAdminScript();
	 */
	public function addAdminAssets() {}

	/**
	 * Override this function in your class to enqueue scripts and styles on login page.
	 * Don't forget do parent::addLoginScript();
	 */
	public function addLoginAssets() {}
}