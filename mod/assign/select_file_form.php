<?php

/**
 * Created by PhpStorm.
 * User: Nejko
 * Date: 3. 08. 2016
 * Time: 19:59
 */
defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');


require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

class select_file_form extends moodleform
{

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition()
    {
        // TODO: Implement definition() method.
        $mform = $this->_form;

        $mform->addElement('filepicker', 'userfile', get_string('file'), null,
            array('maxbytes' => 512, 'accepted_types' => '*'));

//        $content = $mform->get_file_content('userfile');
        $mform->addElement('submit', 'Preveri');
    }
}