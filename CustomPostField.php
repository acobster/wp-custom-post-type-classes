<?php
/**
 * A meta box may have any number of input fields of different types. This class
 * and its child classes are a way to abstract their behavior.
 * @author Coby Tamayo
 */
class CustomPostField {
    
    protected $name;
    protected $id;
    protected $type;
    protected $label;
    protected $labelClass;
    protected $labelAfter;
    protected $noLabel;
    protected $before;
    protected $after;
    protected $class;
    
    public static function create( $name, $info ) {
        switch( $info['type'] ) {
            case 'checkbox' :
                return new CheckboxField( $name, $info );
                break;
            case 'textarea' :
                return new TextareaField( $name, $info );
                break;
            default :
                return new CustomPostField( $name, $info ); 
        }
    }
    
    public function __construct( $name, $info ) {
        $this->name = $name;
                            
        $extra = array(
        	'type'          => 'text', 
            'label'         => '',
            'labelClass'	=> '',
            'labelAfter'    => false,
            'noLabel'	    => false,
            'before'        => '',
            'after'         => '',
            'class'         => '',
            'default'       => '',
        );
        
        foreach( $extra as $x => $default ) {
            $this->$x = isset( $info[$x] ) ? $info[$x] : $default;
        }
        
        $this->id = "{$this->name}_{$this->type}";
    }
    
    public function getSubmitted() {
        return $_POST[$this->name];
    }
    
    
    
    /************************ MODEL METHODS *************************/
    
    protected function getData( $postId ) {
        $value = maybe_unserialize( get_post_meta( $postId, "_{$this->name}" ) );
        return $value[0];
    }
    
    
    
    /************************ VIEW METHODS *************************/
    
    public function getMetaHtml( $postId ) {
        return $this->before
            . $this->maybeLabel( $this->getInputHtml( $postId ) )
            . $this->after;
    }
    
    protected function getInputHtml( $postId ) {
        $value = $this->getData( $postId );
        
        return
        	"<input id=\"{$this->id}\" type=\"{$this->type}\" "
            . "name=\"{$this->name}\" class=\"{$this->class}\" "
            . "value=\"$value\" />";
    }
    
    protected function getMetaLabel() {
        
        if( $this->noLabel ) {
            return '';
        } else {
            
            $label = empty( $this->label )
                ? ucfirst( $this->name )
                : $this->label;
            
            return "<label class=\"$label\" for=\"{$this->id}\">$label</label>";
        }
    }
    
    protected function maybeLabel( $input ) {
        $label = $this->getMetaLabel();
        
        return ( $this->labelAfter ? '' : "$label&nbsp;" )
            . $input
            . ( $this->labelAfter ? "&nbsp;$label" : '' );
    }
}



class CheckboxField extends CustomPostField {
    
    public function getInputHtml( $postId ) {

        $stored = $this->getData( $postId );
        $checked = empty( $stored )
            ? ''
            : 'checked';
        
        return 
        	"<input id=\"{$this->id}\" type=\"{$this->type}\" "
        	. "name=\"{$this->name}\" class=\"{$this->class}\" "
            . "value=\"1\" $checked />";
    }
    
    public function getSubmitted() {
        return isset( $_POST[$this->name] )
            ? 1
            : 0;
    }
}



class TextareaField extends CustomPostField {
    
    public function getInputHtml( $postId ) {
        
        $value = $this->getData( $postId );
        
        return 
        	"<textarea id=\"{$this->id}\" name=\"{$this->name}\" "
            . "class=\"{$this->class}\">$value</textarea>";
    }
}