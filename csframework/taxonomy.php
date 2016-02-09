<?php
namespace csframework;
/**
 * Creates a new taxonomy
 *
 * @param string $object_type Post type slug for which you'd like to create taxonomy
 * @param array|null $args Arguments for your taxonomy
 */
class Taxonomy
{
	/**
	 * Taxonomy slug
	 * @var string
	 */
	public static $taxonomy = null;
	/**
	 * Taxonomy custom fields
	 * @var array
	 */
	protected $_fields = array();
	/**
	 * Post type slug for which you'd like to create taxonomy
	 * @var string|null
	 */
	protected $_object_type = null;
	/**
	 * @var csframework\Csframework|null
	 */
	protected $_app = null;

	protected function __construct( $object_type, $args = null )
	{
		if ( !taxonomy_exists( static::$taxonomy ) ) {
			register_taxonomy( static::$taxonomy, $object_type, $args );
			$this->_object_type = $object_type;
		}
		add_action( static::$taxonomy . "_edit_form_fields", array( $this, 'renderEditFields' ) );
		add_action( static::$taxonomy . "_add_form_fields", array( $this, 'renderAddFields' ) );
		add_action( "create_" . static::$taxonomy, array( $this, 'onSave' ) );
		add_action( "edit_" . static::$taxonomy, array( $this, 'onSave' ) );
	}
	private function __clone()
	{
		return self::getInstance();
	}
	private function __wakeup()
	{
		return self::getInstance();
	}

	/**
	 * Retrive an instance of class
	 * @return csframework\Taxonomy
	 */
	public static function getInstance()
	{
		static $instances = array();
		$calledClass = get_called_class();
		if ( !isset( $instances[$calledClass] ) )
		{
			$instances[$calledClass] = new $calledClass();
		}
		return $instances[$calledClass];
	}

	/**
	 * Sets app from which post type was created
	 * @param csframework\Csframework $app Application main class instance
	 * @return csframework\Taxonomy
	 */
	public function setApp( $app )
	{
		$this->_app = $app;
		return $this;
	}

	/**
	 * Loads Term by slug or ID
	 * @param int|string $id Term ID or slug
	 * @return csframework\Taxonomy
	 */
	public function load( $id )
	{
		$term = false;
		if ( is_int( $id ) ) {
			$term = get_term_by( 'id', $id, self::$taxonomy );
		} elseif ( is_string( $id ) ) {
			$term = get_term_by( 'slug', $id, self::$taxonomy );
		}
		if ( $term ) {
			$term = get_term( $term, self::$taxonomy );
			$this->id = $term->term_id;
			$this->name = $term->name;
			$this->description = $term->description;

			foreach ($this->_fields as $name => $field) {
				$this->$name = $this->getFieldValue( $name );
			}
		}
		return $this;
	}

	/**
	 * Retrive Taxonomy slug
	 * @return string
	 */
	public function getSlug()
	{
		return ( string ) static::$taxonomy;
	}

	/**
	 * Retrive Taxonomy slug. Used by some fields
	 * @return string
	 */
	public function getName()
	{
		return $this->getSlug();
	}

	/**
	 * Adds a new field to taxonomy create/edit form
	 * @param array|object $args Field options
	 * @return csframework\Taxonomy
	 */
	public function addField( $args )
	{
		if ( is_array( $args ) ) {
			if ( isset( $args['type'] ) ) {
				$field_class = 'coolamenu\Field' . ucfirst($args['type']);
				if ( class_exists( $field_class ) ) {
					$args['parent'] = $this;
					$this->_fields[$args['name']] = new $field_class( $args );
				} else {
					throw new \Exception( sprintf( __( "Unknown field type `%s`", $this->_app->getTextDomain() ), $args['type'] ) );
					
				}
			}
		} elseif ( is_object( $args ) ) {
			$args->setParent( $this );
			$this->_fields[$args->getName()] = $args;
		}
		return $this;
	}

	/**
	 * Retrive taxonomy field by name
	 * @param string $name Field name
	 * @return object|false
	 */
	public function getField( $name )
	{
		return isset( $this->_fields[( string ) $name] ) ? $this->_fields[ ( string ) $name] : false;
	}

	/**
	 * Retrive taxonomy field value by name
	 * @param string $name Field name
	 * @return mixed
	 */
	public function getFieldValue( $name )
	{
		if ( isset( $this->_fields[( string ) $name] ) && $this->id ) {
			$field = $this->_fields[( string ) $name];
			return get_option( "tax_" . static::$taxonomy . "_{$this->id}_{$name}", $field->getDefault() );
		}
		return null;
	}

	/**
	 * Removes taxonomy field by name
	 * @param string $name Field name
	 * @return csframework\Taxonomy
	 */
	public function removeField( $name )
	{
		if ( isset( $this->_fields[( string ) $name] ) ) {
			unset( $this->_fields[( string ) $name] );
		}
		return $this;
	}

	/**
	 * Render taxonomy form fields HTML
	 * @param object $term Term WP object
	 * @return void
	 */
	public function renderEditFields( $term )
	{
		?>		
		<?php foreach ( $this->_fields as $name => $field ): ?>
			<?php
			$field
				->setShow_label( false )
				->setValue( get_option( "tax_" . static::$taxonomy . "_{$term->term_id}_{$name}", $field->getDefault() ) );
			?>
			<tr class="form-field term-name-wrap">
				<th scope="row">
					<?php if ( $field->getType() != 'checkbox' ): ?>
						<label for="<?php echo esc_attr( $field->getInputId() ); ?>"><?php echo wp_kses_post( $field->getLabel() ); ?></label>
					<?php endif ?>
				</th>
				<td>
					<?php $field->render() ?>
				</tr>
		<?php endforeach ?>
		<?php
	}

	/**
	 * Render new term form fields HTML
	 * @param object $slug Term WP object
	 * @return void
	 */
	public function renderAddFields( $slug )
	{
		?>
		<?php foreach ( $this->_fields as $name => $field ): ?>
			<?php
			$field
				->setShow_label( true );
			?>
			<div class="form-field term-<?php echo esc_attr( $name ); ?>-wrap">
				<?php $field->render() ?>
			</div>
		<?php endforeach ?>
		<?php
	}

	/**
	 * Saves term custom fields on term save
	 * @param int $term_id 
	 * @return void
	 */
	public function onSave( $term_id )
	{
		foreach ( $this->_fields as $name => $field ) {
			if ( isset( $_REQUEST[$this->_app->getFieldsVar()][static::$taxonomy][$name] ) ) {
				update_option( "tax_" . static::$taxonomy . "_{$term_id}_{$name}", $_REQUEST[$this->_app->getFieldsVar()][static::$taxonomy][$name] );
			} else {
				delete_option( "tax_" . static::$taxonomy . "_{$term_id}_{$name}" );
			}
		}
	}
}