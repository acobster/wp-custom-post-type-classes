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

    public function __construct() {
        $this->data = array();
    }

    public static function init( $action = 'init' ) {
        // Use Late Static Binding to call register() on the subclass,
        // not the CustomPostType class
        add_action( $action, array( get_called_class(), 'register' ) );

        // Initialize meta boxes
        foreach( static::$meta as $id => $info ) {
            $meta = new CustomPostMetaBox( $id, $info, static::$name );
            $meta->init();
        }
    }

    public static function register() {
        register_post_type( static::$name, static::$args );
    }
}

