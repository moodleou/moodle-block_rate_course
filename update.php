<?PHP

/**
 * Script which handles clicking of the submit button in the Course Ratings block.
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

	require_once("../../config.php");

	$id        = required_param('id', PARAM_INT);                 // Course Module ID
    $grade     = required_param('grade', PARAM_INT);          // User selection.

if(! $course = $DB->get_record("course", array("id"=>$id)))
        error("Course ID not found");

	require_login($course, false);
if(!$context = get_context_instance(CONTEXT_COURSE, $course->id))
        print_error('nocontext');

    require_capability('block/rate_course:rate', $context);
    global $USER;

if ($form = data_submitted())
{
    if($DB->count_records('block_rate_course',
            array('course'=>$COURSE->id, 'userid'=>$USER->id)))
            print_error('completed','block_rate_course');

        $completion = new stdClass;
        $completion->course = $COURSE->id;
        $completion->userid = $USER->id;
        $completion->rating = $grade;
        $DB->insert_record( 'block_rate_course', $completion );

        redirect($CFG->wwwroot.'/course/view.php?id='.$COURSE->id);

    }
?>