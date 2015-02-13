<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Rate this course
 *
 * @package    block
 * @subpackage rate_course
 * @copyright  2009 Jenny Gray
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Code was Rewritten for Moodle 2.X By Atar + Plus LTD for Comverse LTD.
 * @copyright &copy; 2011 Comverse LTD.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once( '../../config.php' );

$courseid = required_param( 'courseid', PARAM_INT );
$course = get_course($courseid);
$context = context_course::instance($course->id);

$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/rate_course/rate.php', array('courseid' => $courseid));
$title = get_string('giverating', 'block_rate_course');
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();

//  Require user to be logged in to view this page.
if (!isloggedin()) {
    $msg = html_writer::tag('p', get_string('pleaselogin', 'block_rate_course'));
    echo html_writer::tag('div', $msg);
    echo $OUTPUT->footer();
    exit();
}

require_capability('block/rate_course:rate', $context);

echo html_writer::start_tag('div', array('style' => 'text-align:center'));

$block = block_instance('rate_course');
$block->display_rating($course->id);

$existinganswer = $DB->get_record('block_rate_course',
        array('course'=>$course->id, 'userid'=>$USER->id));
if ($existinganswer) {
    $ratetext = get_string('completed', 'block_rate_course');
} else {
    $ratetext = get_string('intro', 'block_rate_course');
}
$ratetext = html_writer::tag('p', $ratetext);
$ratetext = html_writer::tag('div', $ratetext);
echo $ratetext;

// Now output the form.
echo '<form method="post" action="'.
        $CFG->wwwroot.'/blocks/rate_course/update.php">
        <p><input name="id" type="hidden" value="'.$course->id.'" /></p><p>';

for ($i = 1; $i <= 5; $i++) {
    $checked = '';
    if (isset($existinganswer) && ($existinganswer !== false)) {
        if ($existinganswer->rating == $i) {
                $checked = 'checked="checked"';
        }
    }

    echo '<input type="radio" name="grade" ';
    if ($existinganswer) {
        echo 'disabled="disabled" ';
    }
    echo 'value="'.$i.'" '.$checked.' alt="Rating of '.$i.'"  />'.$i.' ';
}

echo '</p><p><input type="submit" value="'.get_string('submit', 'block_rate_course').'"';
if ($existinganswer) {
    echo 'disabled';
}
echo '/></p></form>';

echo html_writer::end_tag('div');

echo $OUTPUT->footer();
