<?php
/**
 * Created by PhpStorm.
 * User: Nejko
 * Date: 14. 08. 2016
 * Time: 14:01
 */

if (isset($_FILES['upload_files']['name'])) {
    $name = $_FILES['upload_files']['name'];
    $count = count($name);
}


if( isset($_FILES['upload_files']['tmp_name'])){
    $tmp_name = $_FILES['upload_files']['tmp_name'];
}

if(isset($name)) {
    for ($i = 0; $i< $count; $i++) {
        if (!empty($name[$i])) {
            if (move_uploaded_file($tmp_name[$i], 'local/test/' . $name[$i])) {
//                echo "DONE";
            } else {
                echo "ERROR";
            }
        }
    }
}

?>

<form action = "admin_upload.php" method="POST" enctype="multipart/form-data">
    <input name="upload_files[]" type="file" multiple="multiple" />
    <br><br>
    <input type="submit" value="Shrani resitve">
    <br><br>
</form>
