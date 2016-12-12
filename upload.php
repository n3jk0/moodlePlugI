<?php
/**
 * Created by PhpStorm.
 * User: Nejko
 * Date: 13. 08. 2016
 * Time: 23:48
 */
if (isset($_FILES['upload_file']['name'])) {
    $name = $_FILES['upload_file']['name'];
}


if( isset($_FILES['upload_file']['tmp_name'])){
    $tmp_name = $_FILES['upload_file']['tmp_name'];
}

if(isset($name)) {
    if (!empty($name)) {
        if (move_uploaded_file($tmp_name, 'local/Naloga1.java')) {
            header("Location: tester.php");
            echo "DONE";
        } else {
            echo "ERROR";
        }
    }
}

?>

<form action = "upload.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="upload_file">
    <br><br>
    <input type="submit" value="Preveri nalogo">
    <br><br>
<!--    <input name="upload[]" type="file" multiple="multiple" />-->
</form>