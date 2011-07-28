<?php
/**
 * access.php
 * Created on 2008-03-12
 *
 * @copyright &copy; 2006 The Open University
 * @author j.m.gray@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */


$block_rate_course_capabilities = array(

    'block/rate_course:rate' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    )
);

?>
