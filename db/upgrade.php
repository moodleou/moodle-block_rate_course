<?php

function xmldb_block_rate_course_upgrade($oldversion=0)
{
    global $CFG, $THEME, $db, $DB;
    $result = true;
    if($oldversion < 2009020307)
    {
        $oldblock = $DB->get_record('block', array('name'=>'rate_unit'));
        $newblock = $DB->get_record('block', array('name'=>'rate_course'));
        
        if ($oldblock)
        {
           // first migrate data from rate_unit
            $ratings = $DB->get_recordset('rate_unit');
            if ($ratings)
            {
                while (!$ratings->EOF)
                {
                    $newrow = new stdClass();
                    $newrow->course = $ratings->fields['course'];
                    $newrow->userid = $ratings->fields['userid'];
                    $newrow->rating = $ratings->fields['course'];
                    $DB->insert_record('block_rate_course',$newrow);
                    $ratings->MoveNext();
                }
            }

            //  swap the block instances over
            $instances = $DB->get_records('block_instance',
                    array('blockid'=>$oldblock->id));
            if(!empty($instances))
            {
                foreach($instances as $instance)
                {
                    $instance->blockid = $newblock->id;
                    $DB->update_record('block_instance',$instance);
                }
            }
            $instances = $DB->get_records('block_pinned',
                    array('blockid'=>$oldblock->id));
            if(!empty($instances))
            {
                foreach($instances as $instance)
                {
                    $instance->blockid = $newblock->id;
                    $DB->update_record('block_pinned',$instance);
                }
            }
            
            // and delete the old block stuff
            $DB->delete_records('block', array('id'=>$oldblock->id));
            $DB->drop_plugin_tables($oldblock->name, "$CFG->dirroot/blocks/$oldblock->name/db/install.xml", false); // old obsoleted table names
            $DB->drop_plugin_tables('block_'.$oldblock->name, "$CFG->dirroot/blocks/$oldblock->name/db/install.xml", false);
            capabilities_cleanup('block/'.$oldblock->name);
        }
    }
    return $result;
}
?>