<?php

/**
 * This class defines a new post type, robot, and gives it some meta boxes
 * you can administrate to your heart's desire. As you can see, all it really
 * overrides are the member variables, allowing you to just worry about the
 * structure of your pages.
 * @author Coby Tamayo
 */
class RobotType extends CustomPostType {
    protected $name = 'robot';

    protected $args = array(
        'label'         => 'Robots',
        'description'   => 'I, for one, welcome our new Robot Overlords...',
        'public'		=> true,
        'supports'		=> array( 'title', 'editor', 'thumbnail', 'excerpt', ),
    );

    protected $meta = array(
        // The first meta box, #disposition, will capture info about the robot's
        // personality, if robots can be said to possess such things
        'disposition' => array (
            'title'     => 'This Robot\'s disposition',
            'fields'    => array(
                // For each input field in a given meta box, you can get away
                // with just a string, which will default to a text field
                // labelled with the name:
            	'mood',
                // You can get fancy and define different types of fields,
                // or define the HTML surrounding them:
                'evil'	=> array(
                    'type'	    => 'checkbox',
                    'before'	=> '<br/>',
                    'label'	    => 'This robot is evil&nbsp;',
                    'labelAfter' => true,
                ),
                'role_models'   => array(
                    'type'	    => 'textarea',
                    'before'	=> '<br/><br/><p>Who this robot admires, e.g. SkyNet:</p>',
                    'noLabel'	=> true,
                ),
            ),
        ),
    );
}