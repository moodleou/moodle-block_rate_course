<?php
/**
 * Rate this course
 *
 * @copyright &copy; 2006 The Open University
 * @author j.e.c.brisland@open.ac.uk
 * @author j.m.gray@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once( '../../config.php' );
require_once( $CFG->dirroot .'/lib/pagelib.php' );
global $DB;

$courseid = required_param( 'courseid', PARAM_INT );
//  Load the course
$course = get_record('course', 'id', $courseid);
global $COURSE;
$COURSE = $course;
$title = get_string('giverating','block_rate_course');
$link[] = array('name' => $title,'link' => '','type' => 'misc');
$link = build_navigation($link);
print_header_simple($title, $title, $link);

//  Require user to be logged in to view this page
if((!isloggedin() || isguestuser())) {
    notice_yesno(get_string('noguestuseage', 'block_rate_course').'<br /><br />'.get_string('liketologin'),
    $CFG->wwwroot.'/login/index.php', get_referer(false));
    print_footer();
    exit();
}
require_capability('block/rate_course:rate', get_context_instance(CONTEXT_COURSE,$courseid));

echo "<div style='text-align:center'>";
$block = block_instance('rate_course');
$block->display_rating($course->id);

$existing_answer = $DB->get_record( 'block_rate_course', array('course'=>$course->id, 'userid'=>$USER->id));
if ($existing_answer) {
    $rate_text = get_string('completed','block_rate_course');
}else {
    $rate_text = get_string('intro','block_rate_course');
}
echo "<div><p>$rate_text</p></div>";

// now output the form
echo '<form name="form" method="post" action="'.$CFG->wwwroot.'/blocks/rate_course/update.php">
	<input name="id" type="hidden" value="'.$course->id.'" />';

	for( $i = 1; $i <= 5; $i++ ){
	    $checked = '';
	    if( isset( $existing_answer ) && ( $existing_answer !== false ) ){
	        if( $existing_answer->rating == $i ){
	            $checked = 'checked="checked"';
	        }
	    }

	    echo '<input type="radio" name="grade" ';
	    if ($existing_answer) {
	        echo 'disabled="disabled" ';
	    }
	    echo 'value="'.$i.'" '.$checked.' alt="Rating of '.$i.'"  />'.$i.' ';
	}

	echo '<p><input type="submit" value="'.get_string('submit','block_rate_course').'"';
	if ($existing_answer) {echo 'disabled';}
	echo '/></p></form>';

	echo '</div>';

	print_footer($course);
?>