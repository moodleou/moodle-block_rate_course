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
 * 
 * Code was Rewritten for Moodle 3.4 and sup by Pierre Duverneix.
 * @copyright 2019 Pierre Duverneix.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class block_rate_course extends block_list {
    public function init() {
        $this->title = get_string('courserating', 'block_rate_course');
    }

    public function applicable_formats() {
        return array('all' => true, 'mod' => false, 'tag' => false, 'my' => false);
    }

    public function has_config() {
        return true; // Config only for review part.
    }

    public function get_content() {
        global $CFG, $COURSE, $USER, $DB, $OUTPUT, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();

        $form = new \block_rate_course\output\rateform($COURSE->id);
        $renderer = $this->page->get_renderer('block_rate_course');
        $this->content->items[] = $renderer->render($form);

        $rating = new \block_rate_course\output\rating($COURSE->id);
        $renderer = $this->page->get_renderer('block_rate_course');

        // Output current rating.
        $this->content->footer = '<div class="text-center">'.$renderer->render($rating).'</div>';
        
        return $this->content;

    }
}
