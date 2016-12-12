<?php

/**
 * Created by PhpStorm.
 * User: Nejko
 * Date: 24. 08. 2016
 * Time: 20:30
 */
class custom_grade_form extends moodleform
{

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition()
    {
        $mform = $this->_form;
        //TODO change 8
        $assignid = $GLOBALS['cm']->instance;
        $test_dir = "local/assign_$assignid/test/";
        $private_test_dir = "local/assign_$assignid/private_test/";
        $DOCKER_IMAGE = "tester-moodle";

        $grade_all_file = fopen("local/assign_$assignid/grade_all.txt", "w");


        $mform->addElement('html', "<hr>");
        //get all users in course
        $users = $GLOBALS['DB']->get_records_sql("SELECT u.*  FROM mdl_user u 
                  INNER JOIN mdl_user_enrolments ue ON ue.userid = u.id 
                  INNER JOIN mdl_enrol e ON e.id = ue.enrolid 
                  INNER JOIN mdl_course c ON c.id = e.courseid 
                  WHERE c.id = ?", array($GLOBALS['course']->id));

        $FILE_SOLUTIONS = array_diff(scandir($test_dir), array('.', '..', 'hw_name.txt'));
        $not_test = 0;
        foreach ($FILE_SOLUTIONS as $FILE_SOLUTION) {
            if (!$this->startsWith($FILE_SOLUTION, "test")) {
                $not_test++;
            }
        }
        $test_total_points = (count($FILE_SOLUTIONS) - $not_test) / 2;

        $FILE_SOLUTIONS = array_diff(scandir($private_test_dir), array('.', '..'));
        $not_test = 0;
        foreach ($FILE_SOLUTIONS as $FILE_SOLUTION) {
            if (!$this->startsWith($FILE_SOLUTION, "test")) {
                $not_test++;
            }
        }
        $total_points = (count($FILE_SOLUTIONS) - $not_test) / 2;

//        $mform->addElement('html' , "<div id='grade_all'>");
//        $mform->addElement('html', "<h6>Ime Priimek (Vpisna številka)    | Javni test | Točke javnih testov &ensp; &ensp; &ensp; &ensp; // &ensp; &ensp; &ensp; &ensp;| Skriti testi | Točke skritih testov</h6>");
//        $mform->addElement('html' , "</div>");
        $mform->addElement('html', "<div class=\"no-overflow\">");
        $mform->addElement('html', "<table class=\"admintable generaltable\" id=\"grade_all\">");
        $mform->addElement('html', "<thead><tr>");
        $mform->addElement('html', "<th class=\"header c0 centeralign\" style=\"\" scope=\"col\">Ime Priimek (Vpisna številka)</th>
                <th class=\"header c1 centeralign\" style=\"\" scope=\"col\"> Javni test | Točke javnih testov </th>
                <th class=\"header c2 centeralign\" style=\"\" scope=\"col\"> Skriti testi | Točke skritih testov</th>
                <th class=\"header c6 lastcol\" style=\"\" scope=\"col\"></th>");
        $mform->addElement('html', "</tr></thead>");
//        $mform->addElement('html', "</div>");

        $mform->addElement('html', "<tbody>");
        foreach ($users as $user) {

            $user_public_grade = " <b>| sum:0/$test_total_points</b>";
            $private_grade = "";

            $java_dir = "local/assign_$assignid/user_" . $user->id . "/";
            $my_points = 0;
            if (file_exists($java_dir)) {
                $hw_name = file_get_contents($test_dir . "hw_name.txt");
                $DOCKER_FILE_NAME = "/assign_$assignid/user_$user->id/$hw_name.java";
                $FILE_NAME_JAVA = $java_dir . $hw_name . ".java";

                //TODO: Docker
                $error = shell_exec("javac $FILE_NAME_JAVA 2>&1");
//        $output_status = shell_exec("sudo docker build --build-arg file1=$DOCKER_FILE_NAME --build-arg assign=assign_$assignid --no-cache -t $DOCKER_IMAGE .");
//        $error = null;
//        if ($output_status != 0) {
//            $error = shell_exec("javac $FILE_NAME_JAVA 2>&1");
//        }
                if ($error == null) {
                    foreach ($FILE_SOLUTIONS as $FILE_SOLUTION) {
                        if (!$this->startsWith($FILE_SOLUTION, "test")) {
                            $not_test++;
                            continue;
                        }
                        if ($this->endsWith($FILE_SOLUTION, '.in.txt') or $this->endsWith($FILE_SOLUTION, '.in')) {
                            $input = str_replace("\r", "", file_get_contents($private_test_dir . $FILE_SOLUTION));
                            $executable = 'java -cp ' . $java_dir . " " . $hw_name . " " . $input;
                            //TODO: Docker
//                          $executable = "sudo docker run -i -t $DOCKER_IMAGE -cp assign_$assignid/user_$user->id $hw_name $input";
                            $output = shell_exec($executable);
                            $output = str_replace("\r", "", $output);
                            while ($this->endsWith($output, "\n")) {
                                $output = substr($output, 0, strlen($output) - 1);
                            }
                            continue;
                        }
                        $solution = str_replace("\r", "", file_get_contents($private_test_dir . $FILE_SOLUTION));

                        if ($output == $solution) {
//                            fwrite($grade,"1 ");
                            $private_grade .= "1 ";
                            $my_points++;
                        } else {
//                            fwrite($grade,"0 ");
                            $private_grade .= "0 ";
                        }
                    }
                }
            }
            $private_grade .= "<b>| sum_private: $my_points/$total_points</b>";

            if (file_exists($java_dir . "/grade.txt")) {
                $user_grade_path = $java_dir . "/grade.txt";
                $user_public_grade = file_get_contents($user_grade_path);
            }
//            $mform->addElement('html' , "<div id = \"user_$user->id\">");
//            $mform->addElement('html', "<pre>$user->firstname $user->lastname ($user->idnumber)    | $user_public_grade $private_grade</pre>");
//            $mform->addElement('html' , "</div>");

            $mform->addElement('html', "<tr class=\"\">");
            $mform->addElement('html', "<td class=\"centeralign cell c0\" style=\"\">$user->firstname $user->lastname ($user->idnumber) </td>
                    <td class=\"centeralign cell c1\" style=\"\"> | $user_public_grade </td>
                    <td class=\"centeraligncell c2\" style=\"\"> | $private_grade</td>
                    <td class=\"cell c6 lastcol\" style=\"\"></td>");

            $user_public_grade = str_replace("<b>", "", $user_public_grade);
            $user_public_grade = str_replace("</b>", "", $user_public_grade);

            $private_grade = str_replace("<b>", "", $private_grade);
            $private_grade = str_replace("</b>", "", $private_grade);

            $to_write = "$user->firstname $user->lastname ($user->idnumber) | $user_public_grade | $private_grade";
            fwrite($grade_all_file, $to_write . "\n");
            $mform->addElement('html', "</tr>");

        }
        $mform->addElement('html', "</tbody></table></div>");
        fclose($grade_all_file);
        $mform->addElement('html', "<a download =\"all_grade.txt\" href=\"local/assign_$assignid/grade_all.txt\"> Prenesi datoteko z rezultati</a>");

    }

    function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    function startsWith($haystack, $needle)
    {
        $haystack = strtolower($haystack);
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}