<?php
namespace csframework;
/**
* Map markers field
* Free until exceeding 25,000 map loads per 24 hours for 90 consecutive days
*/
class FieldMap extends Field
{
	/**
	 * Google maps API key
	 * @var string
	 */
	protected $_gmaps_api_key = '***';
	
	/**
	 * Instantiate a class object
	 * @param csframework\Csframework $app  App instance
	 * @param array $args Field parameters
	 */
	function __construct( $app, $args )
	{
		parent::__construct( $app, $args );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function addAdminAssets()
	{
		wp_register_script(
			'csframework-google-maps-api',
			'https://maps.googleapis.com/maps/api/js?key=' . $this->_gmaps_api_key,
			array(),
			null,
			true
		);
		wp_enqueue_script(
			'csframework-map-field',
			CSFRAMEWORK_PLUGIN_URL . 'assets/js/map.js',
			array( 'csframework-google-maps-api' ),
			'1.0.0',
			true
		);
		wp_enqueue_style(
			'csframework-map-field',
			CSFRAMEWORK_PLUGIN_URL . 'assets/css/map.css',
			array(),
			'1.0.0'
		);
	}

	/**
	 * Render a field HTML
	 * @return void
	 */
	public function render()
	{
		?>
		<div class="csframework-field csframework-field-map<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' );  ?>"<?php echo ( bool ) $this->_depend ? ' data-depend="' . esc_attr( implode( ';', $this->getDependecies() ) ) . '"' : '';  ?>>
			<div class="csframework-map-field">
				<?php if ( $this->_label && $this->_show_label ): ?>
					<h5 class="label"><?php echo apply_filters( 'the_title', $this->_label ); ?>:</h5>
				<?php endif ?>
				<input type="hidden" name="<?php echo esc_attr( $this->getInputName() ); ?>[lat]" id="<?php echo esc_attr( $this->getInputId() ); ?>-lat" value="<?php echo esc_attr( $this->_value ? $this->_value['lat'] : $this->_default ); ?>" class="lat" />
				<input type="hidden" name="<?php echo esc_attr( $this->getInputName() ); ?>[lng]" id="<?php echo esc_attr( $this->getInputId() ); ?>-lng" value="<?php echo esc_attr( $this->_value ? $this->_value['lng'] : $this->_default['lng'] ); ?>" class="lng" />
				<label for="<?php echo esc_attr( $this->getInputId() ); ?>-title" class="label"><?php _e( 'Marker title', 'csframework' ); ?>:</label>
				<input type="text" name="<?php echo esc_attr( $this->getInputName() ); ?>[title]" id="<?php echo esc_attr( $this->getInputId() ); ?>-title" value="<?php echo esc_attr( $this->_value ? $this->_value['title'] : $this->_default['title'] ); ?>" class="title widefat" />
				<div class="csframework-field-content-map">
						<div class="csframework-field-map-canvas"></div>
				</div>
				<?php if ( $this->_description ): ?>
					<?php echo apply_filters( 'the_content', $this->_description ); ?>
				<?php endif ?>
			</div>
		</div>
		<?php
	}

	public function sanitize( $value )
	{
		if ( is_array( $value ) ) {
			if ( !isset( $value['lat'] ) ) {
				$value['lat'] = 0;
			}
			if ( !isset( $value['lng'] ) ) {
				$value['lng'] = 0;
			}
			if ( !isset( $value['title'] ) ) {
				$value['title'] = '';
			}
		} else {
			$value = array(
				'lat' => 0,
				'lng' => 0,
				'title' => '',
			);
		}
		return  $value;
	}
}