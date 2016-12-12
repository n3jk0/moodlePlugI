<?php

/**
 * Created by PhpStorm.
 * User: Nejko
 * Date: 21. 07. 2016
 * Time: 20:12
 */
class upload_form extends moodleform
{

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition()
    {
        $mform = $this->_form; // Don't forget the underscore!
        $mform->_attributes['action'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $mform->_attributes['enctype'] = "multipart/form-data";
        $userid = $_SESSION['USER']->id;
        $assignid = $GLOBALS['cm']->instance;


        if (isset($_FILES['upload_file']['name'])) {
            $name = $_FILES['upload_file']['name'];
        }


        if (isset($_FILES['upload_file']['tmp_name'])) {
            $tmp_name = $_FILES['upload_file']['tmp_name'];
        }

        if (isset($name)) {
            if (!empty($name)) {
                if(!file_exists("local/assign_$assignid/")) {
                    mkdir("local/assign_$assignid/");
                }
                if(!file_exists("local/assign_$assignid/user_$userid/")) {
                    mkdir("local/assign_$assignid/user_$userid/");
                }
                $hw_name = file_get_contents("local/assign_$assignid/test/" . "hw_name.txt");
                if (move_uploaded_file($tmp_name, "local/assign_$assignid/user_$userid/$hw_name.java")) {
                    header("Location: $_SERVER[PHP_SELF]?id=$_GET[id]&action=tester");
                }
            } else {
                header("Location: $_SERVER[PHP_SELF]?id=$_GET[id]&action=tester");

            }
        }

//        $mform->addElement('hidden', 'id', $_GET['id']);
//        $mform->addElement('hidden', 'action', 'tester');

        $mform->addElement('html', '<h5>Izberite datoteko, ki jo želite preveriti: </h5>');
        $mform->addElement('html', '<input type="file" name="upload_file">');
        $mform->addElement('html', '<br><br>');
        $mform->addElement('html', '<input type="submit" value="Preveri nalogo">');
        $mform->addElement('html', '<br><br>');
    }
}