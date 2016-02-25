<?php
namespace csframework;
/**
* App settings page
*/
class Settings extends Base
{
	protected $_name = 'settings';
	protected $_title = '';
	protected $_capability = 'activate_plugins';
	protected $_parent = null;
	protected $_icon = '';
	protected $_position = null;
	protected $_sections = array();
	protected $_options = array();
	protected $_app = null;

	function __construct( $app, $title, $name, $capability = 'activate_plugins', $parent = null, $icon_url = '', $position = null ) {
		$this->_app = $app;
		$this->_name = sanitize_title( $name );
		$this->_title = apply_filters( 'the_title', $title );
		$this->_capability = ( string ) $capability;
		$this->_parent = $parent;
		$this->_icon = ( string ) $icon_url;
		$this->_position = $position;
	}

	public function addSettingsPage()
	{
		if ( !is_null( $this->_parent ) ) {
			add_submenu_page ( $this->_parent, $this->_title, $this->_title, $this->_capability, $this->_name, array( $this, 'showSettings' ) );
		} else {
			add_menu_page ( $this->_title, $this->_title, $this->_capability, $this->_name, array( $this, 'showSettings' ), $this->_icon, $this->_position );
		}
	}

	public function init()
	{
		register_setting( $this->_name, $this->_app->getFieldsVar(), array( $this, 'sanitize' ) );
		
		foreach ( $this->_sections as $slug => $section ) {
			add_settings_section( $slug, $section['name'], array( $this, 'renderSection' ), $this->_name );
			
			foreach ( $section['fields'] as $name ) {
				add_settings_field( 'option_' . $name, $this->_options[$name]->getLabel(), array( $this->_options[$name]->setValue( $this->getOptionValue( $name ) ), 'render' ), $this->_name, $slug );
			}
		}

	}

	public function sanitize( $val )
	{
		update_option( $this->_name, $val[$this->_name] );
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
				$field_class = 'csframework\Field' . ucfirst( $field_options['type'] );
				if ( class_exists( $field_class ) ) {
					$field_options['parent'] = $this;
					$this->_sections[$section]['fields'][] = $field_options['name'];
					$this->_options[$field_options['name']] = new $field_class( $this->_app, $field_options );
				} else {
					throw new \Exception( sprintf( __( "csframework\Settings: Unknown field type `%s`", 'coolascript-framework' ), $type ) );
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
			if ( $options = get_option( $this->_name, false ) ) {
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

	public function addAdminAssets()
	{
		wp_enqueue_script( 'csframework-accordion' );
	}

	public function renderSection() {}
	protected function _renderSection( $slug ) {
		?>
			<h3 class="csframewoork-settings-section-title csframewoork-section-<?php echo esc_attr( $slug ); ?>">
				<?php echo apply_filters( 'the_title', $this->_sections[$slug]['name'] ); ?>
			</h3>
			<div class="csframewoork-settings-section-fields csframewoork-section-<?php echo esc_attr( $slug ); ?>">
				<ul class="csframewoork-settings-fields">
				<?php foreach ($this->_sections[$slug]['fields'] as $field): ?>
					<li class="csframewoork-settings-field">
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
				<?php settings_fields( $this->_name ); ?>
				<h2>
					<?php echo get_admin_page_title(); ?>
				</h2>
				<div class="csframewoork-accordion">
					<?php foreach ($this->_sections as $slug => $section): ?>
						<?php $this->_renderSection( $slug ) ?>
					<?php endforeach ?>
				</div>
				<input name="Submit" type="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'coolascript-framework' ); ?>" />
			</form>
		</div>
		<?php
	}

	public function render()
	{
		add_action( 'admin_menu', array( $this, 'addSettingsPage' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
	}
}