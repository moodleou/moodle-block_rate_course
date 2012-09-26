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
 * This PHP script is designed to display a graphical indication of the
 * rating.  It will be called from HTML exactly as if it were an image, and will return
 * an image to the browser with the correct headers.  The image will contain between one
 * and five stars
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

require_once(dirname(__FILE__).'/../../../config.php');

$courseid = required_param('courseid', PARAM_INT); // Course.

@header('Content-Type: image/gif');
@header("Expires: ".gmdate("D, d M Y H:i:s") . " GMT" );
@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
@header("Cache-Control: no-store, no-cache, must-revalidate");
@header("Cache-Control: post-check=0, pre-check=0", false);
@header("Pragma: no-cache");

// Get average of marks given by users.
$block = block_instance('rate_course');
$avg = $block->get_rating($courseid);
if ($avg >= 0) {
    echo file_get_contents( $CFG->dirroot.'/blocks/rate_course/pix/star'.$avg.'.png' );
}
