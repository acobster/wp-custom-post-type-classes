<?php

abstract class CustomPostType {

    /**
     * The post type name; the first argument to register_post_type():
     * http://codex.wordpress.org/Function_Reference/register_post_type
     * @var string
     */
    protected static $name;

    /**
     * Settings for the post type's behavior;
     * second argument to register_post_type():
     * http://codex.wordpress.org/Function_Reference/register_post_type
     * @var array
     */
    protected static $args;

    /**
     * The post_meta configuration for this custom post type
     * @var array
     */
    protected static $meta;

    /**
     * Data from a given post, including post_meta data, gets loaded into here
     * @var array
     */
    protected $data;

    /**
     * The actual WP_Post instance for this custom post
     * @var WP_Post
     */
    protected $wp_post;

    public static function fetch( $post, $meta=true ) {
        $post = get_post( $post );
        $data = get_post( $post, ARRAY_A );

        if( $meta ) {
            $metaData = get_post_meta( $post->ID );

            foreach( static::getMetaFieldNames() as $field ) {
                // We always use "custom" fields, so prefix with "_"
                // to avoid collisions
                $fieldName = "_$field";
                if( ! empty($metaData[$fieldName]) ) {
                    $data[$fieldName] = $metaData[$fieldName][0];
                }
            }
        }

        $class = get_called_class();
        $custom = new $class( $post, $data );
        return $custom;
    }

    /**
     * Constructor. Calling this directly is discouraged in favor of find()
     * @param $wp_post the WP_Post object
     */
    public function __construct( $wp_post, $data=array() ) {
        $this->wp_post = $wp_post;
        $this->post_id = $wp_post->ID;
        $this->data = $data;
    }

    /**
     * Register the custom post type along with all its meta boxes
     * @param  string $action which WordPress action to hook into
     */
    public static function init( $action = 'init' ) {
        // Use Late Static Binding to call register() on the subclass,
        // not the CustomPostType class
        add_action( $action, array( get_called_class(), 'register' ) );

        // Initialize meta boxes
        foreach( static::$meta as $id => $config ) {
            CustomPostMetaBox::init( $id, $config, static::$name );
        }
    }

    /**
     * Explicitly register the custom post type
     */
    public static function register() {
        register_post_type( static::$name, static::$args );
    }
}

