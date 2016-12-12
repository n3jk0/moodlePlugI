<?php
/**
 * Created by PhpStorm.
 * User: Nejko
 * Date: 23. 08. 2016
 * Time: 22:28
 */
$file_name = $_GET['file'];
$assignid = $_GET['id'];
$dir = "local/assign_$assignid/";
$testDir = $dir."test/";
if (!file_exists($testDir.$file_name)) {
    $testDir = $dir."private_test/";
}

unlink($testDir.$file_name);