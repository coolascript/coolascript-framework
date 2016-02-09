<?php
/**
* 
*/

namespace csframework;
class Settings extends Abstractive
{
	protected $_name = 'settings';
	protected $_sections = array();
	protected $_options = array();
	protected $_theme = null;

	function __construct() {
		$this->_theme = Csframework::getInstance();
	}

	public function isActive()
	{
		return ( bool ) sizeof( $this->_options );
	}

	public function addSettingsPage()
	{
		add_theme_page( __( 'Theme Options', Csframework::getTextDomain() ), __( 'Theme Options', Csframework::getTextDomain() ), 'edit_theme_options', 'theme-options', array( $this, 'showSettings' ) );
	}

	public function init()
	{
		register_setting( 'theme_options', Csframework::getFieldsVar(), array( $this, 'sanitize' ) );
		
		foreach ($this->_sections as $slug => $section) {
			add_settings_section( $slug, $section['name'], array( $this, 'renderSection' ), 'theme-options' );
			
			foreach ($section['fields'] as $name) {
				add_settings_field( 'option_' . $name, $this->_options[$name]->getLabel(), array( $this->_options[$name]->setValue( $this->getOptionValue( $name ) ), 'render' ), 'theme-options', $slug );
			}
		}

	}

	public function sanitize( $val )
	{
		update_option( 'csframework', $val[$this->_name] );
		/*foreach ($val[$this->_name] as $key => $value) {
		}*/
	}

	public function addSection( $slug, $name )
	{
		if ( !isset( $this->_sections[$slug] ) ) {
			$this->_sections[$slug] = array(
				'name' => $name,
				'fields' => array(),
			);
		}
		return $this;
	}

	public function getSection( $slug )
	{
		return isset( $this->_sections[$slug] ) ? $this->_sections[$slug] : false;
	}

	public function addOption( $section, $field_options )
	{
		if ( is_array( $field_options ) ) {
			if ( isset( $this->_sections[$section] ) && !isset( $this->_options[$field_options['name']] ) ) {
				$field_class = 'rhtheme\Field' . ucfirst( $field_options['type'] );
				if ( class_exists( $field_class ) ) {
					$field_options['parent'] = $this;
					$this->_sections[$section]['fields'][] = $field_options['name'];
					$this->_options[$field_options['name']] = new $field_class( $field_options );
				} else {
					throw new \Exception( sprintf( __( "Unknown field type `%s`", Csframework::getTextDomain() ), $type ) );
				}
			}
		} elseif ( is_object( $field_options ) ) {
			if ( isset( $this->_sections[$section] ) && !isset( $this->_options[$field_options->getName()] ) ) {
				$field_options->setParent( $this );
				$this->_sections[$section]['fields'][] = $field_options->getName();
				$this->_options[$field_options->getName()] = $field_options;
			}
		}
		return $this;
	}

	public function getOption( $name )
	{
		return $this->_options[$name];
	}

	public function getOptionValue( $name )
	{
		if ( isset( $this->_options[$name] ) ) {
			if ( $options = get_option( 'csframework', false ) ) {
				return isset( $options[$name] ) ? $options[$name] : $this->_options[$name]->getDefault();
			} elseif ( $val = get_option( $name, $this->_options[$name]->getDefault() ) ) {
				return $val;
			}
		}
		return false;
	}

	public function getName()
	{
		return $this->_name;
	}

	private function _addAssets()
	{
		$this->_theme->scripts->addScript( 'theme-accordion-init', array(
			'url' => get_template_directory_uri() . '/assets/csframework/js/accordion-init.js',
			'deps' => array( 'jquery', 'jquery-ui-accordion' ),
			'ver' => '1.0.0',
			'load' => true,
			'load_check' => 'is_admin',
		) );
	}

	public function renderSection() {}
	protected function _renderSection( $slug ) {
		?>
			<h3 class="settings-section-title section-<?php echo esc_attr( $slug ); ?>">
				<?php echo wp_kses_post( $this->_sections[$slug]['name'] ); ?>
			</h3>
			<div class="settings-section-fields section-<?php echo esc_attr( $slug ); ?>">
				<ul class="settings-fields">
				<?php foreach ($this->_sections[$slug]['fields'] as $field): ?>
					<li class="settings-field">
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

	public function showSettings()
	{
		?>
		<div class="wrap">
			<form action="<?php echo admin_url( 'options.php' ); ?>" method="post" enctype="multipart/form-data">
				<?php settings_fields( 'theme_options' ); ?>
				<h2>
					<?php echo get_admin_page_title(); ?>
				</h2>
				<div class="theme-accordion">
					<?php foreach ($this->_sections as $slug => $section): ?>
						<?php $this->_renderSection( $slug ) ?>
					<?php endforeach ?>
				</div>
				<input name="Submit" type="submit" class="button button-primary" value="<?php _e( 'Save Changes', Csframework::getTextDomain() ); ?>" />
			</form>
		</div>
		<?php
	}

	public function render()
	{
		$this->_addAssets();
		add_action( 'admin_menu', array( $this, 'addSettingsPage' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
	}
}