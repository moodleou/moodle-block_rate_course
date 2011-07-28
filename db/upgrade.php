<?php

function xmldb_block_rate_course_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;
    $result = true;
    if($oldversion < 2009020307) {
        $oldblock = get_record('block','name','rate_unit');
        $newblock = get_record('block','name','rate_course');
        
        if ($oldblock) {
           // first migrate data from rate_unit
            $ratings = get_recordset('rate_unit');
            if ($ratings) {
                while (!$ratings->EOF) {
                    $newrow = new stdClass();
                    $newrow->course = $ratings->fields['course'];
                    $newrow->userid = $ratings->fields['userid'];
                    $newrow->rating = $ratings->fields['course'];
                    insert_record('block_rate_course',$newrow);
                    $ratings->MoveNext();
                }
            }

            //  swap the block instances over
            $instances = get_records('block_instance', 'blockid', $oldblock->id);
            if(!empty($instances)) {
                foreach($instances as $instance) {
                  $instance->blockid = $newblock->id;
                  update_record('block_instance',$instance);
                }
            }
            $instances = get_records('block_pinned', 'blockid', $oldblock->id);
            if(!empty($instances)) {
                foreach($instances as $instance) {
                  $instance->blockid = $newblock->id;
                  update_record('block_pinned',$instance);
                }
            }
            
            // and delete the old block stuff
            delete_records('block', 'id', $oldblock->id);
            drop_plugin_tables($oldblock->name, "$CFG->dirroot/blocks/$oldblock->name/db/install.xml", false); // old obsoleted table names
            drop_plugin_tables('block_'.$oldblock->name, "$CFG->dirroot/blocks/$oldblock->name/db/install.xml", false);
            capabilities_cleanup('block/'.$oldblock->name);
        }
    }
    return $result;
}
?>