<?php
namespace csfrsmework;
/**
 * Creates a new widget
 *
 * @param string $object_type Post type slug for which you'd like to create taxonomy
 * @param array|null $args Arguments for your taxonomy
 */
abstract class Widget extends \WP_Widget
{

	private $_fields = array();
	
	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() )
	{
		parent::__construct(
			$id_base,
			$name,
			$widget_options,
			$control_options
		);
		add_action( 'wp_ajax_file', array( $this, 'ajaxFile' ) );
		$this->_addAssets();
	}

	protected function _addField( $args )
	{
		if ( isset( $args['name'] ) && !isset( $this->_fields[$args['name']] ) ) {
			$this->_fields[$args['name']] = $args;
		}
		return $this;
	}

	function update( $new_instance, $instance ) {
		foreach ($this->_fields as $name => $field) {
			$instance[$name] = isset( $new_instance[$name] ) ? $new_instance[$name] : '';
		}
		return $instance;
	}

	function form( $instance ) {
		?>
		<?php foreach ($this->_fields as $name => $field): ?>
			<div class="widget-field">
				<?php if ( $field['type'] == 'text' ): ?>
					<?php $value = empty( $instance[$name] ) ? ( isset(  $field['default'] ) ? ( string ) $field['default'] : '' ) : ( string ) $instance[$name] ?>
					<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>">
				<?php endif ?>
				<?php if ( $field['type'] == 'textarea' ): ?>
					<?php $value = empty( $instance[$name] ) ? ( isset(  $field['default'] ) ? ( string ) $field['default'] : '' ) : ( string ) $instance[$name] ?>
					<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
					<textarea id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
				<?php endif ?>
				<?php if ( $field['type'] == 'radio' ): ?>
					<?php $value = empty( $instance[$name] ) ? ( isset(  $field['default'] ) ? ( string ) $field['default'] : '' ) : ( string ) $instance[$name] ?>
					<?php if ( $field['values'] && is_array( $field['values'] ) ): ?>
						<h4><?php echo wp_kses_post( $field['label'] ); ?></h4>
						<ul class="options-list">
						<?php $i = 1; foreach ($field['values'] as $val => $label): ?>
							<li>
								<input type="radio" id="<?php echo esc_attr( $this->get_field_id( $name ) . '-' . $i ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" value="<?php echo esc_attr( $val ); ?>" <?php checked( $val, $value ); ?>>
								<label for="<?php echo esc_attr( $this->get_field_id( $name ) . '-' . $i ); ?>"><?php echo wp_kses_post( $label ); ?></label>
							</li>
						<?php $i++; endforeach ?>
						</ul>
					<?php endif ?>
				<?php endif ?>
				<?php if ( $field['type'] == 'select' ): ?>
					<?php $value = empty( $instance[$name] ) ? ( isset(  $field['default'] ) ? ( string ) $field['default'] : '' ) : ( string ) $instance[$name] ?>
					<?php if ( $field['values'] && is_array( $field['values'] ) ): ?>
						<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
						<select id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>">
						<?php $i = 1; foreach ($field['values'] as $val => $label): ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $val, $value); ?>><?php echo wp_kses_post( $label ); ?></option>
						<?php $i++; endforeach ?>
						</select>
					<?php endif ?>
				<?php endif ?>
				<?php if ( $field['type'] == 'mulltiselect' ): ?>
					<?php $value = ! $instance[$name] ? ( $field['default'] ? (array) $field['default'] : array() ) : $instance[$name] ?>
					<?php if ( $field['values'] && is_array( $field['values'] ) ): ?>
						<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
						<select id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" multiple>
						<?php $i = 1; foreach ($field['values'] as $val => $label): ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( true, in_array( $val, $value ) ); ?>><?php echo wp_kses_post( $label ); ?></option>
						<?php $i++; endforeach ?>
						</select>
					<?php endif ?>
				<?php endif ?>
				<?php if ( $field['type'] == 'checkbox' ): ?>
					<?php $value = ! isset( $instance[$name] ) ? ( $field['default'] ? (int) $field['default'] : 0 ) : ( int ) $instance[$name] ?>
					<input id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" type="checkbox" value="1" <?php checked( 1, $value ); ?>>
					<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
				<?php endif ?>
				<?php if ( $field['type'] == 'file' ): ?>
					<?php
					$value = isset( $instance[$name] ) ? $instance[$name] : 0;
					$upload_link = esc_url( get_upload_iframe_src( $field['filetype'], null, 'type' ) );
					$file_src = wp_get_attachment_url( $value );
					$have_file = !empty( $file_src );
					?>
					<h4><?php echo wp_kses_post( $field['label'] ); ?></h4>
					<div class="file-field" data-type="<?php echo esc_attr( $field['filetype'] ); ?>">
						<div class="file-container">
							<?php if ( $have_file ) {
								switch ( $field['filetype'] ) {
									case 'image':
										?>
										<img src="<?php echo esc_url( $file_src ); ?>" alt="" />
										<?php
										break;

									case 'audio':
										echo wp_audio_shortcode( array(
											'src' => $file_src
										) );
										break;

									case 'video':
										echo wp_video_shortcode( array(
											'src' => $file_src
										) );
										break;

									default:
										$filename = basename( get_attached_file( $value ) );
										echo wp_get_attachment_image( $value, array( 75, 75 ) );
										?>
										<span class="dashicons dashicons-media-default"></span>
										<span class="file-name"><?php echo wp_kses_post( $filename ); ?></span>
										<?php
										break;
								}
							} 
							?>
						</div>
						<div class="file-error error-filetype hidden"><?php _e( 'Wrong file type', Cstheme::getTextDomain() ) ?></div>
						<p class="hide-if-no-js">
							<a class="button add-file<?php echo esc_attr( $have_file ? ' hidden' : '' ); ?>" href="<?php echo esc_url( $upload_link ); ?>">
								<?php _e( 'Set file', Cstheme::getTextDomain() ) ?>
							</a>
							<a class="button delete-file<?php echo ! $have_file ? ' hidden' : '' ?>" href="#">
								<?php _e( 'Remove file', Cstheme::getTextDomain() ) ?>
							</a>
						</p>
						<input class="file-id" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" type="hidden" value="<?php echo esc_attr( $value ); ?>" />
					</div>
				<?php endif ?>
				<?php if ( $field['type'] == 'checkboxes' ): ?>
					<?php $value = ! $instance[$name] ? ( $field['default'] ? (array) $field['default'] : array() ) : $instance[$name] ?>
					<?php if ( $field['values'] && is_array( $field['values'] ) ): ?>
						<h4><?php echo wp_kses_post( $field['label'] ); ?></h4>
						<?php $i = 1; foreach ($field['values'] as $val => $label): ?>
							<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( $name ) . '-' . $i ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>[]" value="<?php echo esc_attr( $val ); ?>" <?php checked( true, in_array( $val, $value ) ); ?>>
							<label for="<?php echo esc_attr( $this->get_field_id( $name ) . '-' . $i ); ?>"><?php echo wp_kses_post( $label ); ?></label>
						<?php $i++; endforeach ?>
					<?php endif ?>
				<?php endif ?>
			</div>
		<?php endforeach ?>
		<?php
	}

	protected function _addAssets()
	{
		$theme = Cstheme::getInstance();
		$theme->scripts
			->setEnqueueMedia( true )
			->addScript( 'csframework-admin-upload', array(
				'url' => get_template_directory_uri() . '/assets/cstheme/js/admin-upload.js',
				'deps' => array( 'jquery', 'media-upload', 'thickbox' ),
				'ver' => '1.0.1',
				'load' => true,
				'load_check' => 'is_admin'
			) )
			->localizeScript( 'theme-admin-upload', 'theme_ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
	}

	public function ajaxFile()
	{
		$type = $_POST['type'];
		$id = $_POST['id'];
		$file_src = wp_get_attachment_url( $id );
		$have_file = !empty( $file_src );
		switch ( $type ) {
			case 'image':
				?>
				<img src="<?php echo esc_url( $file_src ); ?>" alt="" />
				<?php
				break;

			case 'audio':
				echo wp_audio_shortcode( array(
					'src' => $file_src
				) );
				break;

			case 'video':
				echo wp_video_shortcode( array(
					'src' => $file_src
				) );
				break;

			default:
				$filename = basename( get_attached_file( $id ) );
				?>
				<span class="dashicons dashicons-media-default"></span>
				<span class="file-name"><?php echo wp_kses_post( $filename ); ?></span>
				<?php
				break;
		}
		wp_die();
	}
}