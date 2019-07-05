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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/blocks/rate_course/lib.php');

if ($ADMIN->fulltree) {

    $setting = new admin_setting_configtext('block_rate_course/customtitle',
        get_string('customtitle', 'block_rate_course'),
        null, '', PARAM_TEXT
    );
    $settings->add($setting);

    $setting = new admin_setting_configtextarea('block_rate_course/description',
        get_string('description', 'core'),
        null, '', PARAM_TEXT
    );
    $settings->add($setting);
}