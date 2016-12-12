<?php
/**
 * Created by PhpStorm.
 * User: Nejko
 * Date: 13. 08. 2016
 * Time: 15:08
 */
//if ($mform->is_cancelled()) {
//    // CANCELLED
//    echo '<h1>Cancelled</h1>';
//    echo '<p>Handle form cancel operation, if cancel button is present on form<p>';
//} else if ($data = $mform->get_data()) {
$data = $_POST['form'];
// SUCCESS
echo '<h1>Success!</h1>';
echo '<p>In this case you process validated data. $mform->get_data() returns data posted in form.<p>';
echo "<p><center><a href='$CFG->wwwroot/local/filemanager'>Click here to return and see your File Manager!</a></center><p>";
// Save the files submitted
file_save_draft_area_files($draftitemid, $context->id, 'local_filemanager', 'attachment', $itemid, $filemanageropts);
// Just to show they are all there - output a list of submitted files
$fs = get_file_storage();
/** @var stored_file[] $files */
$files = $fs->get_area_files($context->id, 'local_filemanager', 'attachment', $itemid);
echo '<p>List of files:</p>';
echo '<ul>';
foreach ($files as $file) {
    $out = $file->get_filename();
    if ($file->is_directory()) {
        $out = $file->get_filepath();
    } else {
        $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
            $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        $out = html_writer::link($fileurl, $out);
    }
    echo html_writer::tag('li', $out);
}
echo '</ul>';
//} else {
//    // FAIL / DEFAULT
//    echo '<h1>Display form</h1>';
//    echo '<p>This is the form first display OR "errors"<p>';
//    $mform->display();
//}
