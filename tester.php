<?php
/**
 * Created by PhpStorm.
 * User: Nejko
 * Date: 11. 07. 2016
 * Time: 18:48
 */
$FILE_NAME_JAVA = "local/Naloga1.java";
$FILE_NAME = "local/Naloga1";
$dir = 'local/test/';
$FILE_SOLUTIONS = array_diff(scandir('local/test/'), array('.', '..'));
//TODO: Uredi po parih

echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';
echo '<link rel="stylesheet" href="mod/assign/tester.css" type="text/css">', "\r\n";

$error = shell_exec("javac $FILE_NAME_JAVA 2>&1");
$total_points = 0;
$my_points = 0;
$j = 0;
foreach ($FILE_SOLUTIONS as $FILE_SOLUTION) {
    if (endsWith($FILE_SOLUTION, '.in.txt') or endsWith($FILE_SOLUTION, '.in')) {
        $div_text = 'solution_text_' . $j++;

        $executable = str_replace("\r", "", file_get_contents($dir . $FILE_SOLUTION));
        ?>
        <a href="javascript:toggle('<?php echo $div_text ?>');">Željen izpis:</a>
        <?php
        echo '<i>', $executable, '</i><br>';
        $output = shell_exec($executable);
        $output = str_replace("\r", "", $output);
        while (endsWith($output, "\n")) {
            $output = substr($output, 0, strlen($output) - 1);
        }
        continue;
    }
    $solution = str_replace("\r", "", file_get_contents($dir . $FILE_SOLUTION));
    $total_points++;

    ?>
    <div id="<?php echo $div_text ?>" style="display: none">
    <?php
    echo '<pre>', $solution, '</pre></div><br>';
    echo "Točke:";
    if ($error != null) {
        echo "0/1", '<br>';
        echo "Koda se ne prevede !! Izpis napake: ", '<br>';
        echo '<pre>', $error, '</pre><br>';
        break;
    } else {


        if ($output == $solution) {
            echo "1/1", '<br><br>';
            $my_points++;
        } else {
            echo "0/1", '<br>';
            $div_output = 'output_'.$j;
            ?>
            <a href="javascript:toggle('<?php echo $div_output ?>');">Moj izpis: </a><br>
            <div id="<?php echo $div_output ?>" style="display: none">
            <?php
            echo '<pre>', $output, '</pre></div><br>';
        }
    }
    echo "<hr>";
}
echo "<p>Skupno število točk: $my_points/$total_points</p><br>";
$myfile = fopen("$FILE_NAME_JAVA", "r") or die("Unable to open file!");
//echo "Izvorna koda: ";
echo '<button id = "hide">Izvorna koda</button>';
echo '<span id = "sourceCode" style="display: none;"><pre>';
$i = 1;
while (($line = fgets($myfile)) !== false) {
    echo $i, ": ", $line;
    $i++;
}
echo '</pre></span>';
?>

    <br><br>
    <form action="upload.php">
        <input type="submit" value="Nazaj">
    </form>
    <script>
        $(document).ready(function () {
            $("#hide").click(function () {
                $("#sourceCode").toggle();
            });
        });
    </script>
    <script language="javascript">
        function toggle(showHideDiv) {
            var ele = document.getElementById(showHideDiv);
            if (ele.style.display == "block") {
                ele.style.display = "none";
            }
            else {
                ele.style.display = "block";
            }
        }
    </script>
<?php


fclose($myfile);

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}