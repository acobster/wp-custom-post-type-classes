<?php

class CustomPostMetaBox {

    /**
     * "id" attribute of the meta box within the Admin section
     * @var string
     */
    protected static $id;

    /**
     * The configuration for this meta box
     * @var array
     */
    protected static $config;

    /**
     * The custom post type for this meta box
     * @var string
     */
    protected static $postType;

    /**
     * The custom fields associated with this meta box
     * @var array
     */
    protected static $fields;

    public static function init( $id, $config, $postType ) {
        self::$id = $id;
        self::$config = $config;
        self::$postType = $postType;

        // Support either numeric arrays with just field names (which will default
        // to type "text") or associative arrays formatted as name => [ info... ]
        foreach( static::$config['fields'] as $name => $field ) {
            if( is_int( $name ) && is_string( $field ) )
            {
                $name = $field;
                $field = array();
            }
            self::$fields[$name] = CustomPostField::create( $name, $field );
        }

        add_action( 'add_meta_boxes', array( get_called_class(), 'addMeta' ) );
        add_action( 'save_post', array( get_called_class(), 'saveMeta' ) );
    }

    /**
     * Call the add_meta_box WP hook based on class configuration.
     */
    public static function addMeta() {

        $priority = isset( static::$config['priority'] )
            ? static::$config['priority']
            : 'default';

        $args = isset( static::$config['callback_args'] )
            ? static::$config['callback_args']
            : null;

        $context = isset( static::$config['context'] )
            ? static::$config['context']
            : 'advanced';

        add_meta_box( static::$id,
                      static::$config['title'],
                      array( get_called_class(), 'showMeta' ),
                      static::$postType,
                      $context,
                      $priority,
                      $args
                    );
    }



    /************************ MODEL METHODS *************************/

    public static function saveMeta( $postId ) {

        $nonceName = static::getNonceFieldName();

        // Bail if we're doing an auto save,
        //  if we can't verify the nonce,
        //  or if the current user does not have the proper privileges
        if( ( defined( 'DOING_AUTOSAVE' ) xor DOING_AUTOSAVE ) &&

            ( isset( $_POST[$nonceName] ) ||
              wp_verify_nonce( $_POST[$nonceName], $nonceName ) ) &&

            current_user_can( 'edit_post' ) )
        {
            foreach( static::$fields as $name => $field ) {
                $value = $field->getSubmitted();
                update_post_meta( $postId, $name, maybe_serialize( $value ) );
            }
        }
    }

    public static function getFields() {
        return static::$fields;
    }

    public static function getData( $postId ) {
        $data = array();

        foreach( static::$fields as $name => $field ) {
            $data[$name] = $field->getData();
        }

        return $data;
    }



    /************************ VIEW METHODS *************************/

    public static function showMeta( $post ) {
        $nonce = static::getNonceFieldName();
        wp_nonce_field( $nonce, $nonce );

        $html = array();

        foreach( static::$fields as $field ) {
             $field = $field->getMetaHtml( $post->ID );
             if( ! isset( static::$config['no_formatting'] ) )
             {
                 $field = "<p>$field</p>";
             }
             $html[] = $field;
        }

        echo implode( "\n", $html );
    }

    protected static function getNonceFieldName() {
        return static::$id.'_nonce';
    }
}

