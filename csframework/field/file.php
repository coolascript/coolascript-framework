<?php
namespace csframework;
/**
* File form field
*/
class FieldFile extends Field
{
	/**
	 * Type of accepted file: image, audio, video, file
	 * @var string
	 */
	protected $_filetype = 'image';

	/**
	 * Instantiate a class object
	 * @param csframework\Csframework $app  App instance
	 * @param array $args Field parameters
	 */
	function __construct( $app, $args )
	{
		parent::__construct( $app, $args );
		add_action( 'wp_ajax_file', array( $this, 'ajaxFile' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function addAdminAssets()
	{
		parent::addAdminAssets();
		wp_enqueue_media();
		wp_enqueue_script( 'csframework-admin-upload' );
	}

	/**
	 * Set acceped file type
	 * @param string $val File type: image, audio, video, file
	 */
	public function setFiletype( $val )
	{
		$this->_filetype = ( string ) $val;
		return $this;
	}

	/**
	 * Retriev accepted file type
	 * @return string file type
	 */
	public function getFiletype()
	{
		return $this->_filetype;
	}

	/**
	 * Render a field HTML
	 * @return void
	 */
	public function render()
	{
		$upload_link = esc_url( get_upload_iframe_src( $this->_filetype, null, 'type' ) );
		$file_src = wp_get_attachment_url( $this->_value );
		$have_file = !empty( $file_src );
		?>
		<div class="csframework-field csframework-field-file<?php echo esc_attr( $this->_depend ? ' csframework-depend-field' : '' ); ?><?php echo esc_attr( $this->isRequired() ? ' csframework-required' : '' ); ?>"<?php echo ( bool ) $this->_depend ? ' data-depend="' . esc_attr( implode( ';', $this->getDependecies() ) ) . '"' : ''; ?>>
			<div class="csframework-file-field" data-type="<?php echo esc_attr( $this->_filetype ); ?>">
				<?php if ( $this->_label && $this->_show_label ): ?>
					<h5 class="csframework-label"><?php echo apply_filters( 'the_title', $this->_label ); ?>:<?php echo ( $this->isRequired() ? ' <em>*</em>' : '' ); ?></h5>
				<?php endif ?>
				<div class="csframework-file-container">
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
				<input class="file-id<?php echo esc_attr( $this->_class ? ' ' . $this->_class : '' ); ?>" id="<?php echo esc_attr( $this->getInputId() ); ?>" name="<?php echo esc_attr( $this->getInputName() ); ?>" type="hidden" value="<?php echo esc_attr( $this->_value ); ?>" />
				<?php if ( $this->_description ): ?>
					<div class="field-description">
						<?php echo wp_kses_post( $this->_description ); ?>
					</div>
				<?php endif ?>
			</div>
		</div>
		<?php
	}

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