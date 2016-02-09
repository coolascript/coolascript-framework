<?php
/**
* File field
*/

namespace csframework;
class FieldFile extends Field
{
	protected $_filetype = 'image';		// 'image', 'audio', 'video', 'file'
	
	function __construct( $args )
	{
		parent::__construct( $args );
		add_action( 'wp_ajax_file', array( $this, 'ajaxFile' ) );
		$this->_addAssets();
	}

	public function reInit()
	{
		add_action( 'wp_ajax_file', array( $this, 'ajaxFile' ) );
	}

	private function _addAssets()
	{
		$theme = Csframework::getInstance();
		$theme->scripts
			->setEnqueueMedia( true )
			->addScript( 'theme-admin-upload', array(
				'url' => get_template_directory_uri() . '/assets/csframework/js/admin-upload.js',
				'deps' => array( 'jquery', 'media-upload', 'thickbox' ),
				'ver' => '1.0.1',
				'load' => true,
				'load_check' => 'is_admin'
			) )
			->localizeScript( 'theme-admin-upload', 'theme_ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
	}

	public function setFiletype( $val )
	{
		$this->_filetype = ( string ) $val;
		return $this;
	}

	public function getFiletype()
	{
		return $this->_filetype;
	}

	public function render()
	{
		$upload_link = esc_url( get_upload_iframe_src( $this->_filetype, null, 'type' ) );
		$file_src = wp_get_attachment_url( $this->_value );
		$have_file = !empty( $file_src );
		?>
		<div class="field field-file<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<div class="file-field" data-type="<?php echo esc_attr( $this->_filetype ); ?>">
				<?php if ( $this->_label && $this->_show_label ): ?>
					<h5 class="label"><?php echo wp_kses_post( $this->_label ); ?>:</h5>
				<?php endif ?>
				<div class="file-container">
					<?php if ( $have_file ) {
						switch ( $this->_filetype ) {
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
								$filename = basename( get_attached_file( $this->_value ) );
								echo wp_get_attachment_image( $this->_value, array( 75, 75 ) );
								?>
								<span class="dashicons dashicons-media-default"></span>
								<span class="file-name"><?php echo wp_kses_post( $filename ); ?></span>
								<?php
								break;
						}
					} 
					?>
				</div>
				<div class="file-error error-filetype hidden"><?php _e( 'Wrong file type', Csframework::getTextDomain() ) ?></div>
				<p class="hide-if-no-js">
					<a class="button add-file<?php echo esc_attr( $have_file ? ' hidden' : '' ); ?>" href="<?php echo esc_url( $upload_link ); ?>">
						<?php _e( 'Set file', Csframework::getTextDomain() ) ?>
					</a>
					<a class="button delete-file<?php echo ! $have_file ? ' hidden' : '' ?>" href="#">
						<?php _e( 'Remove file', Csframework::getTextDomain() ) ?>
					</a>
				</p>
				<input class="file-id<?php echo esc_attr( $this->_class ? ' ' . $this->_class : '' ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" name="<?php echo esc_attr( Csframework::getFieldsVar() . $this->getInputName() ); ?>" type="hidden" value="<?php echo esc_attr( $this->_value ); ?>" />
				<?php if ( $this->_description ): ?>
					<div class="field-description">
						<?php echo wp_kses_post( $this->_description ); ?>
					</div>
				<?php endif ?>
			</div>
		</div>
		<?php
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