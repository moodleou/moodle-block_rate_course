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
 * @package    block
 * @subpackage rate_course
 * @copyright  2009 Jenny Gray
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Code was Rewritten for Moodle 2.X By Atar + Plus LTD for Comverse LTD.
 * @copyright &copy; 2011 Comverse LTD.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

function xmldb_block_rate_course_upgrade($oldversion=0) {
    global $CFG, $THEME, $db, $DB;
    $result = true;
    if ($oldversion < 2009020307) {
        $oldblock = $DB->get_record('block', array('name'=>'rate_unit'));
        $newblock = $DB->get_record('block', array('name'=>'rate_course'));

        if ($oldblock) {
            // First migrate data from rate_unit.
            $ratings = $DB->get_recordset('rate_unit');
            if ($ratings) {
                while (!$ratings->EOF) {
                    $newrow = new stdClass();
                    $newrow->course = $ratings->fields['course'];
                    $newrow->userid = $ratings->fields['userid'];
                    $newrow->rating = $ratings->fields['course'];
                    $DB->insert_record('block_rate_course', $newrow);
                    $ratings->MoveNext();
                }
            }

            //  Swap the block instances over.
            $instances = $DB->get_records('block_instance',
                    array('blockid'=>$oldblock->id));
            if (!empty($instances)) {
                foreach ($instances as $instance) {
                    $instance->blockid = $newblock->id;
                    $DB->update_record('block_instance', $instance);
                }
            }
            $instances = $DB->get_records('block_pinned',
                    array('blockid'=>$oldblock->id));
            if (!empty($instances)) {
                foreach ($instances as $instance) {
                    $instance->blockid = $newblock->id;
                    $DB->update_record('block_pinned', $instance);
                }
            }

            // Delete the old block stuff.
            $DB->delete_records('block', array('id'=>$oldblock->id));
            $DB->drop_plugin_tables($oldblock->name,
                    "$CFG->dirroot/blocks/$oldblock->name/db/install.xml",
                    false); // Old obsoleted table names.
            $DB->drop_plugin_tables('block_'.$oldblock->name, "$CFG->dirroot/blocks/$oldblock->name/db/install.xml", false);
            capabilities_cleanup('block/'.$oldblock->name);
        }
    }
    return $result;
}
