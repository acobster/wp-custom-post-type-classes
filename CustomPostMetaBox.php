<?php

class CustomPostMetaBox {
    
    protected $id;
    
    protected $postType;
    
    protected $fields;
    
    public function __construct( $id, $info, $postType ) {
        $this->id = $id;
        $this->postType = $postType;
        $this->info = $info;
        
        // Support either numeric arrays with just field names (which will default
        // to type "text") or associative arrays formatted as name => [ info... ]
        foreach( $info['fields'] as $name => $field ) {
            if( is_int( $name ) && is_string( $field ) )
            {
                $name = $field;
                $field = array();
            }
            $this->fields[$name] = CustomPostField::create( $name, $field );
        }
    }
    
    public function init() {
        add_action( 'add_meta_boxes', array( $this, 'addMeta' ) );
        add_action( 'save_post', array( $this, 'saveMeta' ) );
    }
    
    public function addMeta() {

        $priority = isset( $this->info['priority'] )
            ? $this->info['priority']
            : 'default';
            
        $args = isset( $this->info['callback_args'] )
            ? $this->info['callback_args']
            : null;
            
        $context = isset( $this->info['context'] )
            ? $this->info['context']
            : 'advanced';
        
        add_meta_box( $this->id,
                      $this->info['title'],
                      array( $this, 'showMeta' ),
                      $this->postType,
                      $context,
                      $priority,
                      $args
                    );
    }
    
    
    
    /************************ MODEL METHODS *************************/
    
    public function saveMeta( $postId ) {
        
        $nonceName = $this->getNonceFieldName();
        
        // Bail if we're doing an auto save, if we can't verify the nonce,
        // or if the current user does not have the proper privileges
        if( ( defined( 'DOING_AUTOSAVE' ) xor DOING_AUTOSAVE ) &&
        
            ( isset( $_POST[$nonceName] ) ||
              wp_verify_nonce( $_POST[$nonceName], $nonceName ) ) &&
              
            current_user_can( 'edit_post' ) )
        {
            foreach( $this->fields as $name => $field ) {
                $value = $field->getSubmitted();
                update_post_meta( $postId, "_$name", maybe_serialize( $value ) );
            }
        }
    }
    
    public function getData( $postId ) {
        $data = array();
        
        foreach( $this->$fields as $name => $field ) {
            $data[$name] = $field->getData();
        }
        
        return $data;
    }
    
    
    
    /************************ VIEW METHODS *************************/
    
    public function showMeta( $post ) {
        $nonce = $this->getNonceFieldName();        
        wp_nonce_field( $nonce, $nonce );
        
        $html = array();
        
        foreach( $this->fields as $field ) {
             $field = $field->getMetaHtml( $post->ID );
             if( ! isset( $this->info['no_formatting'] ) )
             {
                 $field = "<p>$field</p>";
             }
             $html[] = $field;
        }

        echo implode( "\n", $html );
    }
    
    protected function getNonceFieldName() {
        return "{$this->id}_nonce";
    }
}

