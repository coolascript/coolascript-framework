<?php
namespace csfrsmework;
/**
 * Extra widget functionality. Provide an ability to esealy add new fields to your widget form.
 */
abstract class Widget extends \WP_Widget
{
	/**
	 * Widget fields
	 * @var array
	 */
	private $_fields = array();
	
	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() )
	{
		parent::__construct(
			$id_base,
			$name,
			$widget_options,
			$control_options
		);
		add_action( 'wp_ajax_file', array( $this, 'ajaxFile' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'addAssets' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'addAdminAssets' ), 100 );
		add_action( 'login_enqueue_scripts', array( $this, 'addLoginAssets' ), 100 );
	}

	/**
	 * Adds a new field. Possible field types: text, textarea, select, multiselect, checkbox, checkboxes, radio, file
	 * @param array $args Field settings. Key based array. Possible keys: type, name, label, default, values, filetype
	 */
	protected function _addField( $args )
	{
		if ( isset( $args['name'] ) && !isset( $this->_fields[$args['name']] ) ) {
			$this->_fields[$args['name']] = $args;
		}
		return $this;
	}

	function update( $new_instance, $instance ) {
		foreach ( $this->_fields as $name => $field ) {
			if ( in_array( $field['type'], array( 'multiselect', 'checkboxes' ) ) ) {
				$instance[$name] = isset( $new_instance[$name] ) ? ( array ) $new_instance[$name] : null;
			} else {
				$instance[$name] = isset( $new_instance[$name] ) ? ( string ) $new_instance[$name] : null;
			}
		}
		return $instance;
	}

	function form( $instance ) {
		?>
		<?php foreach ( $this->_fields as $name => $field ): ?>
			<div class="widget-field">
				<?php if ( $field['type'] == 'text' ): ?>
					<?php $value = empty( $instance[$name] ) ? ( isset( $field['default'] ) ? ( string ) $field['default'] : '' ) : ( string ) $instance[$name] ?>
					<?php if ( isset( $field['label'] ) ): ?>
						<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo apply_filters( 'csframework_widget_field_label', $field['label'] ); ?></label>
					<?php endif ?>
					<input id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>">
				<?php endif ?>
				<?php if ( $field['type'] == 'textarea' ): ?>
					<?php $value = empty( $instance[$name] ) ? ( isset( $field['default'] ) ? ( string ) $field['default'] : '' ) : ( string ) $instance[$name] ?>
					<?php if ( isset( $field['label'] ) ): ?>
						<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo apply_filters( 'csframework_widget_field_label', $field['label'] ); ?></label>
					<?php endif ?>
					<textarea id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
				<?php endif ?>
				<?php if ( $field['type'] == 'radio' ): ?>
					<?php $value = is_null( $instance[$name] ) ? ( isset( $field['default'] ) ? ( string ) $field['default'] : null ) : ( string ) $instance[$name] ?>
					<?php if ( $field['values'] && is_array( $field['values'] ) ): ?>
						<?php if ( isset( $field['label'] ) ): ?>
							<h4><?php echo apply_filters( 'csframework_widget_field_label', $field['label'] ); ?></h4>
						<?php endif ?>
						<ul class="options-list">
						<?php $i = 1; foreach ( $field['values'] as $val => $label ): ?>
							<li>
								<input type="radio" id="<?php echo esc_attr( $this->get_field_id( $name ) . '-' . $i ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" value="<?php echo esc_attr( $val ); ?>" <?php checked( ( string ) $val, ( string ) $value ); ?>>
								<label for="<?php echo esc_attr( $this->get_field_id( $name ) . '-' . $i ); ?>"><?php echo apply_filters( 'csframework_widget_field_radio_label', $label ); ?></label>
							</li>
						<?php $i++; endforeach ?>
						</ul>
					<?php endif ?>
				<?php endif ?>
				<?php if ( $field['type'] == 'select' ): ?>
					<?php $value = is_null( $instance[$name] ) ? ( isset( $field['default'] ) ? ( string ) $field['default'] : null ) : ( string ) $instance[$name] ?>
					<?php if ( $field['values'] && is_array( $field['values'] ) ): ?>
						<?php if ( isset( $field['label'] ) ): ?>
							<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo apply_filters( 'csframework_widget_field_label', $field['label'] ); ?></label>
						<?php endif ?>
						<select id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>">
						<?php foreach ( $field['values'] as $val => $label ): ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( ( string ) $val, ( string ) $value ); ?>><?php echo apply_filters( 'csframework_widget_field_option', $label ); ?></option>
						<?php endforeach ?>
						</select>
					<?php endif ?>
				<?php endif ?>
				<?php if ( $field['type'] == 'multiselect' ): ?>
					<?php $value = is_null( $instance[$name] ) ? ( $field['default'] ? ( array ) $field['default'] : array() ) : ( array ) $instance[$name] ?>
					<?php if ( $field['values'] && is_array( $field['values'] ) ): ?>
						<?php if ( isset( $field['label'] ) ): ?>
							<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo apply_filters( 'csframework_widget_field_label', $field['label'] ); ?></label>
						<?php endif ?>
						<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>[]" value="--no-value--">
						<select id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>[]" multiple>
						<?php $i = 1; foreach ( $field['values'] as $val => $label ): ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( true, in_array( ( string ) $val, $value, true ) ); ?>><?php echo apply_filters( 'csframework_widget_field_option', $label ); ?></option>
						<?php $i++; endforeach ?>
						</select>
					<?php endif ?>
				<?php endif ?>
				<?php if ( $field['type'] == 'checkbox' ): ?>
					<?php $value = ! isset( $instance[$name] ) ? ( $field['default'] ? (int) $field['default'] : 0 ) : ( int ) $instance[$name] ?>
					<input id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" type="checkbox" value="1" <?php checked( 1, $value ); ?>>
					<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo apply_filters( 'csframework_widget_field_checkbox_label', $field['label'] ); ?></label>
				<?php endif ?>
				<?php if ( $field['type'] == 'file' ): ?>
					<?php
					$value = !is_null( $instance[$name] ) ? ( int ) $instance[$name] : 0;
					$upload_link = esc_url( get_upload_iframe_src( $field['filetype'], null, 'type' ) );
					$file_src = wp_get_attachment_url( $value );
					$have_file = !empty( $file_src );
					?>
					<?php if ( isset( $field['label'] ) ): ?>
						<h4><?php echo apply_filters( 'csframework_widget_field_label', $field['label'] ); ?></h4>
					<?php endif ?>
					<div class="csframework-field csframework-field-file">
						<div class="csframework-file-field" data-type="<?php echo esc_attr( $field['filetype'] ); ?>">
							<div class="csframework-file-container">
								<?php if ( $have_file ) {
									switch ( $field['filetype'] ) {
										case 'image':
											?>
											<img src="<?php echo esc_attr( $file_src ); ?>" alt="" />
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
											<span class="file-name"><?php echo apply_filters( 'the_content', $filename ); ?></span>
											<?php
											break;
									}
								} 
								?>
							</div>
							<div class="file-error error-filetype hidden"><?php _e( 'Wrong file type', 'coolascript-framework' ) ?></div>
							<p class="hide-if-no-js">
								<a class="button add-file<?php echo esc_attr( $have_file ? ' hidden' : '' ); ?>" href="<?php echo esc_url( $upload_link ); ?>">
									<?php _e( 'Set file', 'coolascript-framework' ) ?>
								</a>
								<a class="button delete-file<?php echo ! $have_file ? ' hidden' : '' ?>" href="#">
									<?php _e( 'Remove file', 'coolascript-framework' ) ?>
								</a>
							</p>
							<input class="file-id" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" type="hidden" value="<?php echo esc_attr( $value ); ?>" />
						</div>
					</div>
				<?php endif ?>
				<?php if ( $field['type'] == 'checkboxes' ): ?>
					<?php $value = is_null( $instance[$name] ) ? ( $field['default'] ? ( array ) $field['default'] : array() ) : ( array ) $instance[$name] ?>
					<?php if ( $field['values'] && is_array( $field['values'] ) ): ?>
						<?php if ( isset( $field['label'] ) ): ?>
							<h4><?php echo apply_filters( 'csframework_widget_field_label', $field['label'] ); ?></h4>
						<?php endif ?>
						<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>[]" value="--no-value--">
						<?php $i = 1; foreach ( $field['values'] as $val => $label ): ?>
							<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( $name ) . '-' . $i ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>[]" value="<?php echo esc_attr( $val ); ?>" <?php checked( true, in_array( ( string ) $val, $value, true ) ); ?>>
							<label for="<?php echo esc_attr( $this->get_field_id( $name ) . '-' . $i ); ?>"><?php echo apply_filters( 'csframework_widget_field_checkbox_label', $label ); ?></label>
						<?php $i++; endforeach ?>
					<?php endif ?>
				<?php endif ?>
			</div>
		<?php endforeach ?>
		<?php
	}

	/**
	 * Override this function to your widget class to enqueue scripts and styles on frontend.
	 * Don't forget do parent::addAssets();
	 */
	public function addAssets() {}

	/**
	 * Override this function to your widget class to enqueue scripts and styles on backend.
	 * Don't forget do parent::addAdminAssets();
	 */
	public function addAdminAssets() {
		wp_enqueue_script( 'csframework-admin-upload' );
	}

	/**
	 * Override this function to your widget class to enqueue scripts and styles on login page.
	 * Don't forget do parent::addLoginAssets();
	 */
	public function addLoginAssets() {}

	/**
	 * Ajax action on file uploaded
	 */
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