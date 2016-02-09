<?php
/**
* Text field
*/

namespace csframework;
class FieldMap extends Field
{
	
	function __construct($args)
	{
		parent::__construct($args);
		$this->_addAssets();
	}

	private function _addAssets()
	{
		$theme = Csframework::getInstance();
		$theme->scripts
			->addScript( 'theme-maps-api', array(
				'url' => 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDUO7A6BxRJdyXzbqpNymLiHWu_-C0UKEk',
				'load' => false,
				'load_check' => 'is_admin',
				'in_footer' => false,
			) )
			->addScript( 'theme-map-field', array(
				'url' => get_template_directory_uri() . '/assets/csframework/js/map.js',
				'deps' => array( 'theme-maps-api' ),
				'load' => true,
				'load_check' => 'is_admin',
			) );
	}

	public function render()
	{
		?>
		<div class="field field-map<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<div class="map-field">
				<?php if ($this->_label && $this->_show_label): ?>
					<h5 class="label"><?php echo wp_kses_post( $this->_label ); ?>:</h5>
				<?php endif ?>
				<input type="hidden" name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>[lat]" id="<?php echo esc_attr( $this->getInputId() ); ?>-lat" value="<?php echo esc_attr( $this->_value ? $this->_value['lat'] : $this->_default ); ?>" class="lat" />
				<input type="hidden" name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>[lng]" id="<?php echo esc_attr( $this->getInputId() ); ?>-lng" value="<?php echo esc_attr( $this->_value ? $this->_value['lng'] : $this->_default['lng'] ); ?>" class="lng" />
				<label for="<?php echo esc_attr( $this->getInputId() ); ?>-title" class="label"><?php _e( 'Marker title', Csframework::getTextDomain() ); ?>:</label>
				<input type="text" name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>[title]" id="<?php echo esc_attr( $this->getInputId() ); ?>-title" value="<?php echo esc_attr( $this->_value ? $this->_value['title'] : $this->_default['title'] ); ?>" class="title widefat" />
				<div class="field-content-map">
					<div class="field-google-map-api">
						<div class="field-map-canvas"></div>
					</div> 
				</div>
				<?php if ( $this->_description ): ?>
				<div class="field-description">
					<?php echo wp_kses_post( $this->_description ); ?>
				</div>
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