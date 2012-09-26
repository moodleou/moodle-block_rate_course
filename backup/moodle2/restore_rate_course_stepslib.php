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
 * Rate course block backup
 *
 * @package    blocks
 * @subpackage rate_course
 * @copyright  2012 Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete structure for the backup, with file and id annotations
 */
class restore_rate_course_block_structure_step extends restore_structure_step {

    protected function define_structure() {
        $paths = array();

        $paths[] = new restore_path_element('block', '/block/rate_course/items');
        $paths[] = new restore_path_element('item', '/block/rate_course/items/item');

        return $paths;
    }

    public function process_block($data) {
    }

    public function process_item($item) {
        global $DB;

        $item['course'] = $this->task->get_courseid();
        $item['userid'] = $this->task->get_userid();

        $params = $item;
        unset($params['id']);

        $sql = 'SELECT id FROM {block_rate_course}
                WHERE course = :course
                AND userid  = :userid
        ';

        if ($existing = $DB->get_record_sql($sql, $params)) {
            $params['id'] = $existing->id;
            $DB->update_record('block_rate_course', $params);
        } else {
            $DB->insert_record('block_rate_course', $params);
        }
    }

}
