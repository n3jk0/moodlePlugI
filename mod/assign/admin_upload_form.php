<?php

/**
 * Created by PhpStorm.
 * User: Nejko
 * Date: 15. 08. 2016
 * Time: 21:44
 */
class admin_upload_form extends moodleform
{

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition()
    {
        $mform = $this->_form; // Don't forget the underscore!
        $mform->_attributes['action'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $mform->_attributes['enctype'] = "multipart/form-data";
        $assignid = $GLOBALS['cm']->instance;
        $testDir = "local/assign_$assignid/test/";
        $privateTestDir = "local/assign_$assignid/private_test/";

        //create public test directory in doesn't exist
        if (!file_exists("local/assign_$assignid/")) {
            mkdir("local/assign_$assignid/");
        }
        if (!file_exists($testDir)) {
            mkdir($testDir);
        }

        //create private test directory in doesn't exist
        if (!file_exists($privateTestDir)) {
            mkdir($privateTestDir);
        }

        //Move public tests
        if (isset($_FILES['upload_files_public']['name'])) {
            $name = $_FILES['upload_files_public']['name'];
            $count = count($name);
        }

        if (isset($_FILES['upload_files_public']['tmp_name'])) {
            $tmp_name = $_FILES['upload_files_public']['tmp_name'];
        }

        if (isset($tmp_name)) {
            for ($i = 0; $i < $count; $i++) {
                if (!empty($name[$i])) {
                    if (move_uploaded_file($tmp_name[$i], $testDir . $name[$i])) {

                    } else {
                        die();
                    }
                }
            }
            $myfile = fopen($testDir . "hw_name.txt", "w");
            fwrite($myfile, $_POST['hw_name']);
            header("Location: $_SERVER[PHP_SELF]?id=$assignid&action=view");
        }

        //Move private tests
        if (isset($_FILES['upload_files_private']['name'])) {
            $name = $_FILES['upload_files_private']['name'];
            $count = count($name);
        }

        if (isset($_FILES['upload_files_private']['tmp_name'])) {
            $tmp_name = $_FILES['upload_files_private']['tmp_name'];
        }

        if (isset($tmp_name)) {
            for ($i = 0; $i < $count; $i++) {
                if (!empty($name[$i])) {
                    if (move_uploaded_file($tmp_name[$i], $privateTestDir . $name[$i])) {

                    } else {
                        die();
                    }
                }
            }
            $myfile = fopen($testDir . "hw_name.txt", "w");
            fwrite($myfile, $_POST['hw_name']);
            header("Location: $_SERVER[PHP_SELF]?id=$_GET[id]&action=view");
        }

        $fileName = "";
        if (file_exists($testDir . "hw_name.txt")) {
            $fileName = file_get_contents($testDir . "hw_name.txt");
        }

        $mform->addElement('html', '<label for = "hw_name">Ime naloge (brez končnice)</label>');
        $mform->addElement('html', "<input name = \"hw_name\" type = \"text\" value=\"$fileName\">");
        $mform->addElement('html', '<hr>');
        $mform->addElement('html', "<p>Naloži javne teste: </p>");
        $mform->addElement('html', '<input name = "upload_files_public[]" type = "file" multiple = "multiple" />');
        $mform->addElement('html', '<br><hr>');
        $mform->addElement('html', "<p>Naloži skrite teste, ki se poženejo pri ocenjevanju: </p>");
        $mform->addElement('html', '<input name = "upload_files_private[]" type = "file" multiple = "multiple" />');
        $mform->addElement('html', '<br><hr>');
        $mform->addElement('html', '<input type = "submit" value = "Shrani resitve" >');
        $mform->addElement('html', '<br><br><br><br>');
        $mform->addElement('html', "<p>Testne datoteke, ki so že naložene: </p>");


        $FILE_SOLUTIONS = array_diff(scandir($testDir), array('.', '..', 'hw_name.txt'));
        $mform->addElement('html', "<p>Javni testi: </p>");
        foreach ($FILE_SOLUTIONS as $FILE_SOLUTION) {
            $mform->addElement('html', "<p id =$FILE_SOLUTION><a href=\"javascript:remove_file('$FILE_SOLUTION', '$assignid');\" style='color: red'>x</a> $FILE_SOLUTION </p>");
        }

        $FILE_SOLUTIONS = array_diff(scandir($privateTestDir), array('.', '..', 'hw_name.txt'));
        $mform->addElement('html', "<hr><p>Skriti testi: </p>");
        foreach ($FILE_SOLUTIONS as $FILE_SOLUTION) {
            $mform->addElement('html', "<p id =$FILE_SOLUTION><a href=\"javascript:remove_file('$FILE_SOLUTION', '$assignid');\" style='color: red'>x</a> $FILE_SOLUTION </p>");
        }

    }


}