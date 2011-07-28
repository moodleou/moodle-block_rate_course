<?PHP

/**
 * Script which handles clicking of the submit button in the Course Ratings block.
 *
 * @copyright &copy; 2008 The Open University
 * @author j.m.gray@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

	require_once("../../config.php");

	$id        = required_param('id', PARAM_INT);                 // Course Module ID
    $grade     = required_param('grade', PARAM_INT);          // User selection.

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID not found");
    }

	require_login($course, false);
    if (!$context = get_context_instance(CONTEXT_COURSE, $course->id)) {
        print_error('nocontext');
    }
    require_capability('block/rate_course:rate', $context);
    global $USER;

    if ($form = data_submitted()) {

        if( count_records( 'block_rate_course', 'course', $COURSE->id, 'userid', $USER->id ) ){
            print_error('completed','block_rate_course');
        }

        $completion = new stdClass;
        $completion->course = $COURSE->id;
        $completion->userid = $USER->id;
        $completion->rating = $grade;
        insert_record( 'block_rate_course', $completion );

        redirect($CFG->wwwroot.'/course/view.php?id='.$COURSE->id);

    }
?>