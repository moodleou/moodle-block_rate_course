<?php
/**
 * This PHP script is designed to display a graphical indication of the
 * rating.  It will be called from HTML exactly as if it were an image, and will return
 * an image to the browser with the correct headers.  The image will contain between one
 * and five stars
 *
 * Original Copyright of Moodle1.9 Block
 * @copyright &copy; 2008 The Open University
 * @author j.m.gray@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * Code was Rewritten for Moodle 2.X By Atar + Plus LTD for Comverse LTD.
 * @copyright &copy; 2011 Comverse LTD.
 * @author chysch@atarplpl.co.il 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once(dirname(__FILE__).'/../../../config.php');

$courseid = required_param('courseid',PARAM_INT);  //Course

@header('Content-Type: image/gif');
@header("Expires: ".gmdate("D, d M Y H:i:s") . " GMT" );
@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
@header("Cache-Control: no-store, no-cache, must-revalidate");
@header("Cache-Control: post-check=0, pre-check=0", false);
@header("Pragma: no-cache");

//Get average of marks given by users.
$block = block_instance('rate_course');
$avg = $block->get_rating($courseid);
if( $avg >= 0 ){
    echo file_get_contents( $CFG->dirroot.'/blocks/rate_course/graphic/star'.$avg.'.png' );
}
?>