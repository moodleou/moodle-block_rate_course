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
 * Handles clicking of the submit button in the Course Ratings block.
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

require_once("../../config.php");

$id        = required_param('id', PARAM_INT);                 // Course Module ID.
$grade     = required_param('grade', PARAM_INT);          // User selection.

if (! $course = $DB->get_record("course", array("id"=>$id))) {
    error("Course ID not found");
}

require_login($course, false);
if (!$context = context_course::instance($course->id)) {
    print_error('nocontext');
}

    require_capability('block/rate_course:rate', $context);
    global $USER;

if ($form = data_submitted()) {
    if ($DB->count_records('block_rate_course',
            array('course'=>$COURSE->id, 'userid'=>$USER->id))) {
        print_error('completed', 'block_rate_course', $CFG->wwwroot.'/course/view.php?id='.$COURSE->id);
    }

    $completion = new stdClass;
    $completion->course = $COURSE->id;
    $completion->userid = $USER->id;
    $completion->rating = $grade;
    $DB->insert_record( 'block_rate_course', $completion );

    redirect($CFG->wwwroot.'/course/view.php?id='.$COURSE->id);

}
