<?php
/**
* Post field
*/

namespace csframework;
class FieldPost extends Field
{
	protected $_posttype = null;
	
	function __construct($args)
	{
		$args['posttype'] = isset( $args['posttype'] ) && $args['posttype'] ? $args['posttype'] : null;
		if ( is_array( $args['posttype'] ) && sizeof( $args['posttype'] == 1) ) {
			$args['posttype'] = ( string ) $args['posttype'][0];
		}
		parent::__construct($args);
		add_action( 'wp_ajax_' . $this->name, array( $this, 'ajaxPosttypePosts' ) );
	}

	public function setPosttype($val)
	{
		if ( is_array( $val ) ) {
			if ( sizeof( $val ) > 1 ) {
				foreach ($val as &$value) {
					$value = ( string ) $value;
				}
			} elseif( sizeof( $val ) ) {

			}
		} else {
			$val = ( string ) $val;
		}
		$this->_posttype = $val;
		return $this;
	}

	public function getPosttype()
	{
		return $this->_posttype;
	}

	public function render()
	{
		?>
		<div class="field field-post<?php echo esc_attr( $this->_depend ? ' depend-field' : '' );  ?>"<?php echo wp_kses_post( $this->_depend ? ' data-depend="' . implode( ';', $this->getDependecies() ) . '"' : '' );  ?>>
			<?php if ( !$this->_posttype ): ?>
				<?php if ($this->_label && $this->_show_label): ?>
					<h4 class="label"><?php echo wp_kses_post( $this->_label ); ?>:</h4>
				<?php endif ?>
				<?php
				$posttypes = get_post_types( array(
					'public' => true,
				), 'object' );
				$posttype_list = array();
				foreach ($posttypes as $posttype) {
					$posttype_list[$posttype->name] = $posttype->label;
				}
				$posttypes_field = new FieldSelect( array(
					'label' => __( 'Post type', Csframework::getTextDomain() ),
					'name' => $this->_name . '_posttype',
					'class' => 'widefat postfield-posttype',
					'values' => $posttype_list,
				) );
				$posttypes_field->render();
				?>
				<p class="post-select"></p>
			<?php elseif ( is_string( $this->_posttype ) ): ?>
				<?php
				$posts = get_posts( array(
					'posts_per_page' => -1,
					'post_type' => $this->_posttype,
				) );
				$post_list = array();
				foreach ($posts as $post) {
					$post_list[$post->ID] = $post->post_title;
				}
				$posts_field = new FieldSelect( array(
					'name' => $this->getInputName(),
					'label' => $this->_label,
					'class' => 'widefat',
					'values' => $post_list,
				) );
				$posts_field->render();
				?>
			<?php elseif ( is_array( $this->_posttype ) ): ?>
				<?php if ($this->_label && $this->_show_label): ?>
					<h4 class="label"><?php echo wp_kses_post( $this->_label ); ?>:</h4>
				<?php endif ?>
				<?php
				$posttypes = get_post_types( array(
					'public' => true,
				), 'object' );
				$posttype_list = array();
				foreach ($posttypes as $posttype) {
					if ( in_array( $posttype->name, $this->_posttype ) ) {
						$posttype_list[$posttype->name] = $posttype->label;
					}
				}
				$posttypes_field = new FieldSelect( array(
					'label' => __( 'Post type', Csframework::getTextDomain() ),
					'name' => $this->_name . '_posttype',
					'class' => 'widefat postfield-posttype',
					'values' => $posttype_list,
				) );
				$posttypes_field->render();
				?>
				<p class="post-select"></p>
			<?php endif ?>
		</div>
		<?php
	}

	public function ajaxPosttypePosts()
	{
		$posttype = ( string ) $_REQUEST['posttype'];
		$posts = get_posts( array(
			'posts_per_page' => -1,
			'post_type' => $posttype,
		) );
		$post_list = array();
		foreach ($posts as $post) {
			$post_list[$post->ID] = $post->post_title;
		}
		$posts_field = new FieldSelect( array(
			'label' => __( 'Post', Csframework::getTextDomain() ),
			'class' => 'widefat',
			'values' => $post_list,
		) );
		$posts_field->render();
		wp_die();
	}
}