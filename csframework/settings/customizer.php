<?php
/**
* FrontendCustomizer class
*/

namespace csframework;
class SettingsCustomizer extends Settings
{
	private $_google_fonts = array();
	
	function __construct() {
		$this->_name = 'customizer';
		parent::__construct();
	}

	public function lessNotice()
	{
		?>
		<div class="error">
			<p><?php printf( __( 'For proper template work you should install and activate <a href="%s" class="thickbox" title="Install Less PHP Compiler">Less PHP Compiler</a> plugin!', Csframework::getTextDomain() ), network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=lessphp&TB_iframe=true&width=772&height=895' ) ); ?></p>
		</div>
		<?php
	}

	public function addSection( $slug, $name )
	{
		Csframework::getSettings()->addSection( $slug, $name );
		parent::addSection( $slug, $name );
		return $this;
	}

	public function addOption( $section, $field_options )
	{
		Csframework::getSettings()->addOption( $section, $field_options );
		parent::addOption( $section, $field_options );
		$this->_options[$field_options['name']]->setClass( 'customizer-field' );
		if ( $this->_options[$field_options['name']]->getSanitize() == 'google_font' ) {
			$this->_google_fonts[] = $this->getOptionValue( $field_options['name'] );
		}

		return $this;
	}

	public function getOptionValue( $name )
	{
		if ( get_option( 'is_demo', false ) ) {
			return isset( $_COOKIE[$name] ) ? $_COOKIE[$name] : $this->_options[$name]->getDefault();
		} else {
			return get_option( $name, $this->_options[$name]->getDefault() );
		}
	}

	protected function _addAssets()
	{
		if ( current_user_can( 'edit_theme_options' ) || Csframework::getSettings()->getOptionValue( 'is_demo' ) ) {
			Csframework::getStyles()->addStyle( 'theme-customizer', array(
				'url' => get_template_directory_uri() . '/assets/csframework/css/customizer.css',
				'deps' => array(),
				'ver' => '1.0',
				'load' => true,
			) );
			Csframework::getScripts()
				->addScript( 'theme-ajax-form', array(
					'url' => get_template_directory_uri() . '/assets/csframework/js/ajax-form.js',
					'deps' => array( 'jquery', 'jquery-form' ),
					'ver' => '1.0',
					'load' => false,
				) )
				->addScript( 'theme-less-options', array(
					'url' => get_template_directory_uri() . '/assets/csframework/js/less_options.js',
					'ver' => '1.0',
					'load' => false,
				) )
				->addScript( 'theme-less', array(
					'url' => get_template_directory_uri() . '/assets/csframework/js/less.min.js',
					'ver' => '2.3.1',
					'load' => false,
				) )
				->addScript( 'theme-accordion-init', array(
					'url' => get_template_directory_uri() . '/assets/csframework/js/accordion-init.js',
					'deps' => array( 'jquery', 'jquery-ui-accordion' ),
					'ver' => '1.0.0',
					'load' => false,
				) )
				->addScript( 'theme-customizer', array(
					'url' => get_template_directory_uri() . '/assets/csframework/js/customizer.js',
					'deps' => array( 'jquery', 'theme-ajax-form', 'theme-less-options', 'theme-less', 'theme-accordion-init' ),
					'ver' => '1.0',
					'load' => true,
				) );
		}
		if (class_exists('\Less_Cache')) {
			$less_files = array(
				get_template_directory() . '/assets/csframework/less/customizer.less' => get_template_directory_uri() . '/assets/csframework/css/cache/'
			);
			$options = array( 'cache_dir' => get_template_directory() . '/assets/csframework/css/cache', 'compress' => true );
			$variables = array();
			foreach ($this->_options as $name => $field) {
				$variables[$name] = $this->_getLessValue( $name );
			}
			$css_file_name = \Less_Cache::Get( $less_files, $options, $variables );
			Csframework::getStyles()->addStyle( 'theme-customizer-less', array(
				'url' => get_template_directory_uri() . '/assets/csframework/css/cache/' . $css_file_name,
				'ver' => '1.0',
				'load' => true,
				'load_check' => array( 'rhtheme\Csframework', 'is_frontend' ),
			) );
		}
		return $this;
	}

	public function loadFonts()
	{
		?>
		<?php foreach ($this->_google_fonts as $font): ?>
			<?php if ( array_key_exists( $font, CustomGooglefonts::getFonts() ) ): ?>
				<link href='http://fonts.googleapis.com/css?family=<?php echo str_replace( ' ', '+', $font ); ?>' rel='stylesheet' type='text/css'>
			<?php endif ?>
		<?php endforeach ?>
		<?php
	}

	public function loadLess()
	{
		?>
		<link rel="stylesheet/less" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/csframework/less/customizer.less" />
		<?php
	}

	private function _getLessValue( $name )
	{
		$value = $this->getOptionValue( $name );
		$field = $this->_options[$name];
		if ( $field->getSanitize() == 'google_font' ) {
			$value = explode( ':', $value );
			$value = $value[0];
		}
		if ( !in_array( $field->getSanitize(), array( 'int', 'float', 'color' ) ) ) {
			$value = "'" . $value . "'";
		}
		return $value;
	}

	protected function _renderSection( $slug )
	{
		?>
			<h3 class="customization-section-title section-<?php echo esc_attr( $slug ); ?>">
				<?php echo wp_kses_post( $this->_sections[$slug]['name'] ); ?>
			</h3>
			<div class="customization-section-fields section-<?php echo esc_attr( $slug ); ?>">
				<ul class="customization-fields">
				<?php foreach ($this->_sections[$slug]['fields'] as $field): ?>
					<li class="customization-field">
						<?php if ( $this->_options[$field]->getSanitize() == 'google_font' ): ?>
							<?php $values = $this->_options[$field]->getValues() ?>
							<?php $value = explode( ':', $this->getOptionValue( $field ) ) ?>
							<?php $value = array_search( $value[0], $values ) ?>
							<?php $this->_options[$field]->setValue( $value )->render() ?>
						<?php else: ?>
							<?php $this->_options[$field]->setValue( $this->getOptionValue( $field ) )->render() ?>
						<?php endif ?>
					</li>
				<?php endforeach ?>
				</ul>
			</div>
		<?php
	}

	public function showCustomizer()
	{
		?>
		<div class="frontend-customizer">
			<h3 class="customizer-title">
				<?php _e( 'Live Customizer', Csframework::getTextDomain() ) ?>
				<i class="loader fa fa-refresh fa-spin"></i>
			</h3>
			<div class="customizer-scroll">
				<div class="customizer-inner">
					<form class="csframework-ajax-form" action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post" enctype="multipart/form-data">
						<div class="theme-accordion">
						<?php foreach ($this->_sections as $slug => $section): ?>
							<?php $this->_renderSection( $slug ) ?>
						<?php endforeach ?>
						</div>
						<input type="hidden" name="action" value="<?php echo esc_attr( $this->_name . 'Update' );; ?>">
						<div class="form-response"></div>
						<div class="customizer-actions">
							<input type="submit" class="btn" value="<?php _e( 'Update', Csframework::getTextDomain() ) ?>">
						</div>
					</form>
				</div>
			</div>
			<a href="#live-customizer" class="customization-toggle"><i class="fa fa-gear"></i></a>
		</div>
		<?php
	}

	public function render()
	{
		if ( ! class_exists( 'Less_Cache' ) ) {
			add_action( 'admin_notices', array( $this, 'lessNotice' ) );
		}
		Csframework::getSettings()
			->addSection( $this->_name, __( 'Customizer', Csframework::getTextDomain() ) )
			->addOption( $this->_name, array(
				'name' => 'is_demo',
				'label' => __( 'Demo mode', Csframework::getTextDomain() ),
				'type' => 'radio',
				'values' => array(
					__( 'No', Csframework::getTextDomain() ),
					__( 'Yes', Csframework::getTextDomain() ),
				),
				'show_label' => true,
				'description' => __( 'prevent saving to database', Csframework::getTextDomain() ),
				'default' => 0,
			) );
		$this->_addAssets();
		add_action( 'wp_head', array( $this, 'loadFonts' ) );
		if ( current_user_can( 'edit_theme_options' ) || Csframework::getSettings()->getOptionValue( 'is_demo' ) ) {
			add_action( 'wp_head', array( $this, 'loadLess' ) );
			add_action( 'wp_footer', array( $this, 'showCustomizer' ) );
			add_action( 'wp_ajax_' . $this->_name . 'Update', array( $this, 'ajaxUpdate' ) );
			add_action( 'wp_ajax_nopriv_' . $this->_name . 'Update', array( $this, 'ajaxUpdate' ) );
		}
	}

	public function updateOption( $option, $value )
	{
		if ( get_option( 'is_demo', false ) ) {
			setcookie( $option, $value, time() + 60*60*24, COOKIEPATH );
		} else {
			update_option( $option, $value );
		}
		return $this;
	}

	public function ajaxUpdate()
	{
		$result = array(
			'message' => __( 'All Done!', Csframework::getTextDomain() ),
			'error' => false,
		);
		if ( isset( $_REQUEST[Csframework::getFieldsVar()][$this->_name] ) ) {
			$values = $_REQUEST[Csframework::getFieldsVar()][$this->_name];
			foreach ( $values as $key => $value ) {
				$this->updateOption( $key, $value );
			}
		}
		echo json_encode($result);
		wp_die();
	}
}