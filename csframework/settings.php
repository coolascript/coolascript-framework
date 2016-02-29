<?php
namespace csframework;
/**
* App settings page
*/
class Settings extends Base
{
	/**
	 * Settings page slug, option group and option name
	 * @var string
	 */
	protected $_name = 'settings';
	/**
	 * Settings page and menu title
	 * @var string
	 */
	protected $_title = '';
	/**
	 * The capability required for this menu to be displayed to the user
	 * @var string
	 */
	protected $_capability = 'activate_plugins';
	/**
	 * Page menu parent. Will be added as submenu if not null.
	 * @var null|string
	 */
	protected $_parent = null;
	/**
	 * The URL to the icon to be used for this menu.
	 * Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme.
	 * This should begin with 'data:image/svg+xml;base64,'.
	 * Pass the name of a Dashicons helper class to use a font icon, e.g. 'dashicons-chart-pie'.
	 * * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS. 
	 * @var string
	 */
	protected $_icon = '';
	/**
	 * The position in the menu order this one should appear
	 * @var null
	 */
	protected $_position = null;
	/**
	 * Settings page sections
	 * @var array
	 */
	protected $_sections = array();
	/**
	 * Settings page options
	 * @var array
	 */
	protected $_options = array();
	/**
	 * Settings fields base name
	 * @var string
	 */
	protected $_fields_base = 'csframework';

	/**
	 * Instantiate Settings object
	 * @param string $fields_base_name        Settings fields base name
	 * @param string $title      Settings page and menu title
	 * @param string $name       Settings page slug, option group and option name
	 * @param string $capability The capability required for this menu to be displayed to the user
	 * @param string $parent     Parent menu slug to add as submenu
	 * @param string $icon_url   The URL to the icon to be used for this menu. <em> Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme. This should begin with 'data:image/svg+xml;base64,'. </em> Pass the name of a Dashicons helper class to use a font icon, e.g. 'dashicons-chart-pie'. * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.

	 * @param int $position   The position in the menu order this one should appear
	 */
	function __construct( $fields_base_name, $title, $name, $capability = 'activate_plugins', $parent = null, $icon_url = '', $position = null ) {
		$this->_fields_base = $fields_base_name;
		$this->_name = sanitize_title( $name );
		$this->_title = apply_filters( 'the_title', $title );
		$this->_capability = ( string ) $capability;
		$this->_parent = $parent;
		$this->_icon = ( string ) $icon_url;
		$this->_position = $position;
	}

	/**
	 * Add settings page and creates menu item for it
	 * Its an admin_menu action function and runs automaticaly on render
	 */
	public function addSettingsPage()
	{
		if ( !is_null( $this->_parent ) ) {
			add_submenu_page ( $this->_parent, $this->_title, $this->_title, $this->_capability, $this->_name, array( $this, 'showSettings' ) );
		} else {
			add_menu_page ( $this->_title, $this->_title, $this->_capability, $this->_name, array( $this, 'showSettings' ), $this->_icon, $this->_position );
		}
	}


	/**
	 * Register settings and options
	 * Its an admin_init action function and runs automaticaly on render
	 */
	public function init()
	{
		register_setting( $this->_name, $this->_fields_base, array( $this, 'sanitize' ) );
		
		foreach ( $this->_sections as $slug => $section ) {
			add_settings_section( $slug, $section['name'], array( $this, 'renderSection' ), $this->_name );
			
			foreach ( $section['fields'] as $name ) {
				add_settings_field( 'option_' . $name, $this->_options[$name]->getLabel(), array( $this->_options[$name]->setValue( $this->getOptionValue( $name ) ), 'render' ), $this->_name, $slug );
			}
		}

	}

	/**
	 * Sanitize the option values and save it to database on update action
	 * @param  mixed $val Input value
	 */
	public function sanitize( $val )
	{
		$errors = false;
		foreach ( $val[$this->_name] as $key => $value ) {
			if ( $this->_options[$key]->isRequired() && !$value ) {
				add_settings_error( $this->_name, $this->_name . '_error_' . $key . '_required', sprintf( __( 'Field `%s` is required!', 'coolascript-framework' ), $this->_options[$key]->getLabel() ), 'error' );
				$errors = true;
			} elseif ( $this->_options[$key]->getType() == 'email' && !empty( $value ) && !is_email( $value ) ) {
				add_settings_error( $this->_name, $this->_name . '_error_' . $key . '_wrong_email', sprintf( __( '`%s` is not valid email address!', 'coolascript-framework' ), $this->_options[$key]->getLabel() ), 'error' );
				$errors = true;
			}
		}
		if ( !$errors && ( update_option( $this->_name, $val[$this->_name] ) || get_option( $this->_name ) == $val[$this->_name] ) ) {
			add_settings_error( $this->_name, $this->_name . '_notice', __( 'Settings saved', 'coolascript-framework' ), 'updated' );
		} elseif( !$errors ) {
			add_settings_error( $this->_name, $this->_name . '_error', __( 'Something went wrong!', 'coolascript-framework' ), 'error' );
		}
	}

	/**
	 * Add new settings section
	 * @param string $slug Section unique slug
	 * @param string $name Section title
	 */
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

	/**
	 * Retriev section by slug
	 * @param  string $slug Section slug
	 * @return array       Array with section properies and fields
	 */
	public function getSection( $slug )
	{
		return isset( $this->_sections[$slug] ) ? $this->_sections[$slug] : false;
	}

	/**
	 * Add fields to the section
	 * @param string $section       Section slug
	 * @param array|object $field_options Field options array or field object
	 */
	public function addOption( $section, $field_options )
	{
		if ( is_array( $field_options ) ) {
			if ( isset( $this->_sections[$section] ) && !isset( $this->_options[$field_options['name']] ) ) {
				$field_class = 'csframework\Field' . ucfirst( $field_options['type'] );
				if ( class_exists( $field_class ) ) {
					$field_options['parent'] = $this;
					$this->_sections[$section]['fields'][] = $field_options['name'];
					$this->_options[$field_options['name']] = new $field_class( $this->_fields_base, $field_options );
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

	/**
	 * Retriev Field by name
	 * @param  string $name Field name
	 * @return csFramework\Field       Field object
	 */
	public function getOption( $name )
	{
		return $this->_options[$name];
	}

	/**
	 * Retriev field value by name
	 * @param  string $name Field name
	 * @return bool|string       Field value or false on error
	 */
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

	/**
	 * Retriev Settings name
	 * @return string Settings page slug, Settings group and option name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Enqueue scripts and styles to settings page
	 * Override this function in your class to enqueue scripts and styles on backend.
	 * Don't forget do parent::addAdminAssets();
	 */
	public function addAdminAssets()
	{
		wp_enqueue_script( 'csframework-accordion' );
	}

	public function renderSection() {}

	/**
	 * Render settings section HTML
	 * @param  string $slug Section slug
	 */
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

	/**
	 * Render Settings page HTML
	 * @return [type] [description]
	 */
	public function showSettings()
	{
		?>
		<div class="wrap">
			<form action="<?php echo admin_url( 'options.php' ); ?>" method="post" enctype="multipart/form-data">
				<h2>
					<?php echo get_admin_page_title(); ?>
				</h2>
				<?php settings_fields( $this->_name ); ?>
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

	/**
	 * Show settings page messeges
	 */
	public function settingsNotice()
	{
		settings_errors( $this->_name );
	}

	/**
	 * Generate Settings page and render page HTML if needed
	 */
	public function render()
	{
		//var_dump(get_option( $this->_name, false ));
		add_action( 'admin_menu', array( $this, 'addSettingsPage' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_notices', array( $this, 'settingsNotice' ) );
	}
}