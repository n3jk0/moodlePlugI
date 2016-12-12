<?php

/**
 * Created by PhpStorm.
 * User: Nejko
 * Date: 15. 08. 2016
 * Time: 15:34
 */

class tester_form extends moodleform
{

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition()
    {

        $mform = $this->_form; // Don't forget the underscore!
        $mform->_attributes['action'] = "http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?id=$_GET[id]&action=view";
        if ($this->_customdata['userid']) {
            $userid = $this->_customdata['userid'];
        } else {
            $userid = $_SESSION['USER']->id;
        }
        $assignid = $GLOBALS['cm']->instance;

        $dir = "local/assign_$assignid/test/";
        $java_dir = "local\\assign_$assignid\\user_$userid";
        $hw_name = file_get_contents($dir . "hw_name.txt");
        $FILE_NAME_JAVA = "local/assign_$assignid/user_$userid/$hw_name.java";
        $DOCKER_FILE_NAME = "/assign_$assignid/user_$userid/$hw_name.java";
        $DOCKER_IMAGE = "tester-moodle";

        $grade = fopen($java_dir . "/grade.txt", "w");


        if (!file_exists($dir)) {
            $mform->addElement('html', "<h4>Testnih primerov ni na voljo!</h4><p>Kontaktiraj profesorja.</p>");
            return;
        }
        //pravilen vrstni red datotek
        $FILE_SOLUTIONS = array_diff(scandir("$dir"), array('.', '..', 'hw_name.txt'));
        $not_test = 0;
        foreach ($FILE_SOLUTIONS as $FILE_SOLUTION) {
            if (!$this->startsWith($FILE_SOLUTION, "test") and !$this->startsWith($FILE_SOLUTION, "Test")) {
                $not_test++;
            }
        }

        //TODO: Docker
        $error = shell_exec("javac $FILE_NAME_JAVA 2>&1");
//        $output_status = shell_exec("sudo docker build --build-arg file1=$DOCKER_FILE_NAME --build-arg assign=assign_$assignid --no-cache -t $DOCKER_IMAGE .");
//        $error = null;
//        if ($output_status != 0) {
//            $error = shell_exec("javac $FILE_NAME_JAVA 2>&1");
//        }

        $total_points = (count($FILE_SOLUTIONS) - $not_test) / 2;
        $my_points = 0;
        $j = 0;
        $mform->addElement('html', "<hr>");
        if ($error != null) {
            fwrite($grade, "Koda se ne prevede ");
            $mform->addElement('html', "Koda se ne prevede !! Izpis napake: <br>");
            $mform->addElement('html', "<pre>$error</pre><br>");
        } else {
            foreach ($FILE_SOLUTIONS as $FILE_SOLUTION) {
                if (!$this->startsWith($FILE_SOLUTION, "test") and !$this->startsWith($FILE_SOLUTION, "Test")) {
                    $not_test++;
                    continue;
                }
                if ($this->endsWith($FILE_SOLUTION, '.in.txt') or $this->endsWith($FILE_SOLUTION, '.in')) {
                    $div_text = 'solution_text_' . $j++;

                    $input = str_replace("\r", "", file_get_contents($dir . $FILE_SOLUTION));
                    if ($this->startsWith($input, "<")) {
                        $input = str_replace("< ", "", $input);
                        $input = file_get_contents($dir . $input);
                    }
                    $executable = 'java -cp ' . $java_dir . " " . $hw_name . " " . $input;
                    //TODO: Docker

//                    $executable = "sudo docker run -i -t $DOCKER_IMAGE -cp assign_$assignid/user_$userid $hw_name $input";
                    //TODO: Uncomment this, when running on linux. And comment next line
//                    $output = shell_exec("timeout 10s ".$executable);
                    $output = shell_exec($executable);
                    $executable = explode(' ', $executable, 4);
                    $executable = $executable[3];
                    $mform->addElement('html', "<a href=\"javascript:toggle('$div_text');\">Željen izpis:</a>");
                    $mform->addElement('html', "<i>($executable)</i><br>");
                    $output = str_replace("\r", "", $output);
                    while ($this->endsWith($output, "\n")) {
                        $output = substr($output, 0, strlen($output) - 1);
                    }
                    continue;
                }
                $solution = str_replace("\r", "", file_get_contents($dir . $FILE_SOLUTION));
//            $total_points++;

                $mform->addElement('html', "<div id=\"$div_text\" style=\"display: none\">");
                $mform->addElement('html', "<pre> $solution </pre></div><br>");

                $mform->addElement('html', "Točke:");
                if ($output == $solution) {
                    $mform->addElement('html', "1/1 <br><br>");
                    fwrite($grade, "1 ");
                    $my_points++;
                } else {
                    $mform->addElement('html', "0/1<br>");
                    fwrite($grade, "0 ");
                    $div_output = 'output_' . $j;
                    $mform->addElement('html', "<a href=\"javascript:toggle('$div_output');\">Moj izpis: </a><br>");
                    $mform->addElement('html', "<div id=\"$div_output\" style=\"display: none\">");
                    $mform->addElement('html', "<pre>$output</pre></div><br>");
                }
                $mform->addElement('html', "<hr>");
            }
        }
        fwrite($grade, "<b>| sum:$my_points/$total_points</b>");
        $mform->addElement('html', "<h5>Skupno število točk: $my_points/$total_points</h5><br>");
        $myfile = fopen("$FILE_NAME_JAVA", "r") or die("Unable to open file!");

        $mform->addElement('html', '<a id = "hide" href="javascript:toggle(\'sourceCode\')">Izvorna koda</a>');
        $mform->addElement('html', '<span id = "sourceCode" style="display: none;"><pre>');
        $i = 1;
        while (($line = fgets($myfile)) !== false) {
            $mform->addElement('html', "$i : $line");
            $i++;
        }
        $mform->addElement('html', '</pre></span>');

        if (!$this->_customdata['userid']) {
            $mform->addElement('html', '<br><br>');
            $mform->addElement('html', '<input type="submit" value="Nazaj">');
            $mform->addElement('html', '</form>');
        } else {
            $mform->addElement('html', "<br><br><br><br>");
        }
        fclose($grade);
        fclose($myfile);


    }

    private function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    private function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }


}

