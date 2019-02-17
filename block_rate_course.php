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
 * This block allows the user to give the course a rating, which
 * is displayed in a custom table (<prefix>_block_rate_course).
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

class block_rate_course extends block_list
{
    public function init() {
        $this->title = get_string('courserating', 'block_rate_course');
    }

    public function applicable_formats() {
        return array('all' => true, 'mod' => false, 'tag' => false, 'my' => false);
    }

    public function has_config() {
        return true; // Config only for review part.
    }

    private function rating_block_form($id_course, $id_user) {
        GLOBAL $DB, $CFG;
        // code adapted from rate.php
        $form = "<div style='text-align:center'>";
        if (!$DB->get_record('block_rate_course', array('course' => $id_course, 'userid' => $id_user))) {
            $form .='<form method="post" action="' . $CFG->wwwroot . '/blocks/rate_course/update.php"><p>'
                    . '  <input name="id" type="hidden" value="' . $id_course . '" /></p><p>';
            for ($i = 1; $i <= 5; $i++) {
                $form .= '<input type="radio" name="grade" value="' . $i . '" alt="Rating of ' . $i . '" />' . $i . ' ';
            }
            $form .= '</p><p><input type="submit" value="' . get_string('submit', 'block_rate_course') . '" /></p></form>';
        }
        $form .= '</div >';

        return $form;
    }

    public function get_content() {
        global $CFG, $COURSE, $USER, $DB, $OUTPUT, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();

        if (substr($PAGE->pagetype, 0, 11) == 'course-view') {
            $qmod = $DB->get_record('modules', array('name'=>'questionnaire'));
            if ($qmod && ($qmod->visible=='1') && !empty($CFG->block_rate_course_quest)) {
                // Get the Give a Review instance id.
                $questionnaire = $DB->get_record_sql(
                                "SELECT id,sid FROM {questionnaire} WHERE name = ? AND course = ?",
                                array($CFG->block_rate_course_quest, $COURSE->id));
                if ($questionnaire) {
                    $url = new moodle_url('/mod/questionnaire/report.php',
                                    array('instance'=>$questionnaire->id, 'sid'=>$questionnaire->sid));
                    $this->content->items[] = $OUTPUT->action_link($url, get_string('viewreview', 'block_rate_course'));
                    $this->content->icons[] = $OUTPUT->pix_icon('review', get_string('viewreview', 'block_rate_course'),
                                    'block_rate_course', array('class'=>'icon'));
                }
            }

            $this->content->items[] = $this->rating_block_form($COURSE->id, $USER->id);

            // Output current rating.
            $this->content->footer = '<div class="centered">'.
                            $this->display_rating($COURSE->id, true).'</div>';
        } else {
            if ($this->page->user_is_editing()) {
                $this->content->items[] = get_string('editingsitehome', 'block_rate_course');
            }
        }
        return $this->content;

    }

    /**
     * Checks whether any version of the course already exists.
     * @param int $courseid The ID of the course.
     * @return int  rating.
     */
    public function get_rating($courseid) {
        global $CFG, $DB;
        $sql = "SELECT AVG(rating) AS avg
        FROM {block_rate_course}
        WHERE course = $courseid";

        $avg = -1;
        if ($avgrec = $DB->get_record_sql($sql)) {
            $avg = $avgrec->avg * 2;  // Double it for half star scores.
            // Now round it up or down.
            $avg = round($avg);
        }
        return $avg;
    }

    /**
     * Outputs the current rating. Can be called outside the block.
     * @param int $courseid the ID of the course
     * @param bool $return return the string (true) or echo it immediately (false)
     * @return string the html to output graphic, alt text and number of ratings
     */
    public function display_rating($courseid, $return=false) {
        global $CFG, $DB, $OUTPUT;
        $count = $DB->count_records('block_rate_course', array('course'=>$courseid));
        $ratedby = '';
        if ($count > 0) {
            $ratedby = get_string ('rating_users', 'block_rate_course', $count);
        }

        $numstars = $this->get_rating( $courseid );
        if ($numstars == -1) {
            $alt = '';
        } else if ($numstars == 0) {
            $alt = get_string( 'rating_alt0', 'block_rate_course' );
        } else {
            $alt = get_string( 'rating_altnum', 'block_rate_course', $numstars/2 );
        }

        $avg = $this->get_rating($courseid);
        $res = '<img src="'.$OUTPUT->pix_url('star'.$avg, 'block_rate_course').'" alt="'.$alt.'"/><br/>'.$ratedby;

        if ($return) {
            return $res;
        }
        echo $res;
    }

    public function show_rating($courseid) {
        global $CFG, $DB;
        // Pinned block check once per session for performance.
        if (!isset($_SESSION['starsenabled'])) {
            $_SESSION['starsenabled'] = $DB->get_field('block', 'visible',
                            array('name'=>'rate_course'));
            if ($_SESSION['starsenabled'] && !isset($_SESSION['starspinned'])) {
                $_SESSION['starspinned'] = $DB->get_record_sql(
                                "SELECT * FROM {block_pinned} p
                                JOIN {block} b ON b.id = p.blockid
                                WHERE pagetype = ? AND p.visible = ? AND b.name = ?",
                                array('course-view', 1, 'rate_course'));
            }
        }
        if (!$_SESSION['starsenabled']) {
            return false;
        }
        if ($_SESSION['starspinned']) {
            return true;
        }

        return $DB->get_record_sql("SELECT * FROM {block_instance} i
                        JOIN {block} b ON b.id = i.blockid
                        WHERE pageid = ? and b.name = ?", array($courseid, 'rate_course'));
    }

}
