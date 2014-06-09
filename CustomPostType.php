<?php

abstract class CustomPostType {

    /**
     * The post type name; the first argument to register_post_type():
     * http://codex.wordpress.org/Function_Reference/register_post_type
     * @var string
     */
    protected $name;

    /**
     * Settings for the post type's behavior;
     * second argument to register_post_type():
     * http://codex.wordpress.org/Function_Reference/register_post_type
     * @var array
     */
    protected $args;

    /**
     *
     * @var array
     */
    protected $meta;

    protected $data;

    /**
     *
     */
    public function __construct() {
        $this->data = array();
    }

    public function init( $action = 'init' ) {
        add_action( $action, array( $this, 'register' ) );

        foreach( $this->meta as $id => $info ) {
            $meta = new CustomPostMetaBox( $id, $info, $this->name );
            $meta->init();
        }
    }

    public function register() {
        register_post_type( $this->name, $this->args );
    }
}

