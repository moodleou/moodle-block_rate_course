<?php

/**
 * This block allows the user to give the course a rating, which
 * is displayed in a custom table (<prefix>_block_rate_course).
 *
 * @copyright &copy; 2008 The Open University
 * @author j.m.gray@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class block_rate_course extends block_list {

    function init() {
        $this->title = get_string('courserating','block_rate_course');
        $this->version = 2009102000;
    }

    function applicable_formats() {
        return array('course' => true);
    }

    function has_config(){
        return class_exists('ouflags'); // config only for review part
    }

    function get_content() {
        global $CFG, $COURSE, $USER;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();

        if (class_exists('ouflags') && isset($CFG->block_rate_course_quest)) {
            //  Get the Give a Review instance id
            $questionnaire = get_record_sql("SELECT id,sid FROM {$CFG->prefix}questionnaire
             WHERE name = '".$CFG->block_rate_course_quest."' AND course = {$COURSE->id}");
            if ($questionnaire) {
            $this->content->items[] = '<a href="'.$CFG->wwwroot.'/mod/questionnaire/report.php?
             instance='.$questionnaire->id.'&sid='.$questionnaire->sid.'&action=vall">'
             .get_string('viewreview','block_rate_course').'</a>';
             $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/yui_menu/icons/viewall.gif" width="16" height="16" />';
        }
        }

        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/rate_course/star.gif" width="16" height="16" />';
        $this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/rate_course/rate.php?courseid='.$COURSE->id.'">'
         .get_string('giverating','block_rate_course').'</a>';
        $this->content->items[] = '';
        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" width="1" height="1" />';

        // output current rating
        $this->content->footer = '<div class="centered">'.
        $this->display_rating($COURSE->id,true).'</div>';
        return $this->content;

    }


    /**
     * This function checks whether any version of the course already exists.
     * @param int $courseid The ID of the course.
     * @return int  rating.
     */
    function get_rating( $courseid ){
        global $CFG;
        $sql = "SELECT AVG(rating) AS avg FROM {$CFG->prefix}block_rate_course
            WHERE course = $courseid";

        $avg = -1;
        if( $avgrec = get_record_sql($sql) ){
            $avg = $avgrec->avg * 2;  //Double it for half star scores
            //Now round it up or down.
            $avg = round($avg);
        }
        return $avg;
    }

    /**
     * This function will output the current rating
     * and can be called outside the block if you wish
     * @param int $courseid the ID of the course
     * @param bool $return return the string (true) or echo it immediately (false)
     * @return string the html to output graphic, alt text and number of ratings
     */
    function display_rating( $courseid,$return=false) {
        global $CFG;
        $count = count_records('block_rate_course','course',$courseid);
        $ratedby = '';
        if ($count > 0) {
            $ratedby = get_string ('rating_users','block_rate_course',$count);
        }

        $numstars = $this->get_rating( $courseid );
        if( $numstars == -1 ){
            $alt = '';
        }else if ($numstars == 0) {
            $alt = get_string( 'rating_alt0', 'block_rate_course' );
        } else {
            $alt = get_string( 'rating_altnum', 'block_rate_course', $numstars/2 );
        }

        $res = '<img src="'.$CFG->wwwroot.'/blocks/rate_course/graphic/rating_graphic.php?courseid='.
        $courseid.'" alt="'.$alt.'"/><br/>'.$ratedby;

        if ($return) {
            return $res;
        } else {
            echo $res;
        }
    }

    function show_rating($courseid) {
        global $CFG;
        if (!isset($_SESSION['starsenabled'])) {// pinned block check once per session for performance
            $_SESSION['starsenabled'] = get_field('block','visible','name','rate_course');
            if ($_SESSION['starsenabled'] && !isset($_SESSION['starspinned'])) {
                $_SESSION['starspinned'] = get_record_sql("select * from ".$CFG->prefix."block_pinned p JOIN ".$CFG->prefix."block b on b.id=p.blockid
                                        where pagetype = 'course-view' and p.visible=1 and b.name='rate_course'");
            }
        }
        if (!$_SESSION['starsenabled']) {return false;}
        if ($_SESSION['starspinned']) { return true;}
        
        return get_record_sql("select * from ".$CFG->prefix."block_instance i JOIN ".$CFG->prefix."block b on b.id=i.blockid
                                       where pageid=".$courseid." and b.name='rate_course'");
    }
}
?>