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
class backup_rate_course_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {
        global $DB;

        // Define each element separated.
        $rate_course = new backup_nested_element('rate_course');
        $items = new backup_nested_element('items');
        $rate_course->add_child($items);

        // Build the tree.
        $item = new backup_nested_element('item', array('id'), array(
            'course',
            'userid',
            'rating',
        ));
        $items->add_child($item);

        $item->set_source_table('block_rate_course',
            array('course' => backup::VAR_COURSEID));

        $item->annotate_ids('user', 'userid');

        return $this->prepare_block_structure($rate_course);
    }
}
