<?php

function xmldb_block_bcgtbtec_upgrade($oldversion = 0)
{
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2013052200)
    {
        $record = new stdClass();
        $record->type = 'Final Project';
        $record->bcgttypeid = 4;
        $DB->insert_record('block_bcgt_unit_type', $record);
    }
    
    if ($oldversion < 2013060800)
    {
        $record = new stdClass();
        $record->id = 5;
        $record->type = 'BTEC Lower';
        $record->bcgttypefamilyid = 2;
        $DB->update_record('block_bcgt_type', $record);
    }
    
    if ($oldversion < 2013061900)
    {
        //need to alter all of the values with 
        //special val and also enabled
        $sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ? ";
        $record = $DB->get_record_sql($sql, array(2, 'A'));
        if($record)
        {
            $record->specialval = 'A';
            $record->enabled = 1;
            $record->ranking = 1;
            $DB->update_record('block_bcgt_value', $record);
        }
        $record = $DB->get_record_sql($sql, array(2, 'PA'));
        if($record)
        {
            $record->specialval = '';
            $record->enabled = 1;
            $record->ranking = 2;
            $DB->update_record('block_bcgt_value', $record);
        }
        $record = $DB->get_record_sql($sql, array(2, 'X'));
        if($record)
        {
            $record->specialval = 'X';
            $record->enabled = 1;
            $record->ranking = 3;
            $DB->update_record('block_bcgt_value', $record);
        }
        $record = $DB->get_record_sql($sql, array(2, 'N/A'));
        if($record)
        {
            $record->specialval = '';
            $record->enabled = 1;
            $record->ranking = 4;
            $DB->update_record('block_bcgt_value', $record);
        }
        $record = $DB->get_record_sql($sql, array(2, 'R'));
        if($record)
        {
            $record->specialval = '';
            $record->enabled = 1;
            $record->ranking = 5;
            $DB->update_record('block_bcgt_value', $record);
        }
        $record = $DB->get_record_sql($sql, array(2, 'L'));
        if($record)
        {
            $record->specialval = 'L';
            $record->enabled = 1;
            $record->ranking = 6;
            $DB->update_record('block_bcgt_value', $record);
        }
        $record = $DB->get_record_sql($sql, array(2, 'WS'));
        if($record)
        {
            $record->specialval = 'WS';
            $record->enabled = 1;
            $record->ranking = 7;
            $DB->update_record('block_bcgt_value', $record);
        }
        $record = $DB->get_record_sql($sql, array(2, 'WNS'));
        if($record)
        {
            $record->specialval = 'WNS';
            $record->enabled = 1;
            $record->ranking = 8;
            $DB->update_record('block_bcgt_value', $record);
        }
    }
    
    if($oldversion < 2013062000)
    {
        $sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $record = $DB->get_record_sql($sql, array(2, 'A'));
        if($record)
        {
            $id = $record->id;
            $record = new stdClass();
            $record->bcgtvalueid = $id;
            $record->coreimg = '/pix/grid_symbols/core/achieved.png';
            $record->coreimglate = '/pix/grid_symbols/core/achievedLate.png';
            $DB->insert_record('block_bcgt_value_settings', $record);
        }
        
        $sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $record = $DB->get_record_sql($sql, array(2, 'PA'));
        if($record)
        {
            $id = $record->id;
            $record = new stdClass();
            $record->bcgtvalueid = $id;
            $record->coreimg = '/pix/grid_symbols/core/pachieved.png';
            $record->coreimglate = '/pix/grid_symbols/core/paLate.png';
            $DB->insert_record('block_bcgt_value_settings', $record);
        }
        
        $sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $record = $DB->get_record_sql($sql, array(2, 'X'));
        if($record)
        {
            $id = $record->id;
            $record = new stdClass();
            $record->bcgtvalueid = $id;
            $record->coreimg = '/pix/grid_symbols/core/notachieved.png';
            $record->coreimglate = '/pix/grid_symbols/core/notachievedLate.png';
            $DB->insert_record('block_bcgt_value_settings', $record);
        }
        
        $sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $record = $DB->get_record_sql($sql, array(2, 'N/A'));
        if($record)
        {
            $id = $record->id;
            $record = new stdClass();
            $record->bcgtvalueid = $id;
            $record->coreimg = '/pix/grid_symbols/core/notattempted.png';
            $DB->insert_record('block_bcgt_value_settings', $record);
        }
        
        $sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $record = $DB->get_record_sql($sql, array(2, 'R'));
        if($record)
        {
            $id = $record->id;
            $record = new stdClass();
            $record->bcgtvalueid = $id;
            $record->coreimg = '/pix/grid_symbols/core/referred.png';
            $DB->insert_record('block_bcgt_value_settings', $record);
        }
        
        $sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $record = $DB->get_record_sql($sql, array(2, 'L'));
        if($record)
        {
            $id = $record->id;
            $record = new stdClass();
            $record->bcgtvalueid = $id;
            $record->coreimg = '/pix/grid_symbols/core/late.png';
            $DB->insert_record('block_bcgt_value_settings', $record);
        }
        
        $sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $record = $DB->get_record_sql($sql, array(2, 'WS'));
        if($record)
        {
            $id = $record->id;
            $record = new stdClass();
            $record->bcgtvalueid = $id;
            $record->coreimg = '/pix/grid_symbols/core/in.png';
            $DB->insert_record('block_bcgt_value_settings', $record);
        }
        
        $sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $record = $DB->get_record_sql($sql, array(2, 'WNS'));
        if($record)
        {
            $id = $record->id;
            $record = new stdClass();
            $record->bcgtvalueid = $id;
            $record->coreimg = '/pix/grid_symbols/core/notin.png';
            $DB->insert_record('block_bcgt_value_settings', $record);
        }
    }
    
    if($oldversion < 2013071200)
    {
        $record = new stdClass();
        $record->id = 2;
        $record->pluginname = 'bcgtbtec';
        $DB->update_record('block_bcgt_type_family', $record);
    }
    
    if ($oldversion < 2013081200){
        //insert BTEC values into database
        //also insert the BTEC Grades into the database
        //get the targetqualid for the btecs
        //level 3 ext dip 
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(3, 2, 2));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'PPP';
            $stdObj->shortvalue = 'PPP';
            $stdObj->ranking = 1;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?  
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(1, 120, $targetQualID, 'PPP'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'PPP/MPP';
            $stdObj->shortvalue = 'PPP/MPP';
            $stdObj->ranking = 1.3;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'PPP/MPP';
            $stdObj->ucaspoints = 133.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MPP/PPP';
            $stdObj->shortvalue = 'MPP/PPP';
            $stdObj->ranking = 1.6;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MPP/PPP';
            $stdObj->ucaspoints = 146.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MPP';
            $stdObj->shortvalue = 'MPP';
            $stdObj->ranking = 2;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(2, 160, $targetQualID, 'MPP'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MPP/MMP';
            $stdObj->shortvalue = 'MPP/MMP';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MPP/MMP';
            $stdObj->ucaspoints = 173.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MMP/MPP';
            $stdObj->shortvalue = 'MMP/MPP';
            $stdObj->ranking = 2.6;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MMP/MPP';
            $stdObj->ucaspoints = 186.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MMP';
            $stdObj->shortvalue = 'MMP';
            $stdObj->ranking = 3;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(3, 200, $targetQualID, 'MMP'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MMP/MMM';
            $stdObj->shortvalue = 'MMP/MMM';
            $stdObj->ranking = 3.3;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MMP/MMM';
            $stdObj->ucaspoints = 213.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MMM/MMP';
            $stdObj->shortvalue = 'MMM/MMP';
            $stdObj->ranking = 3.6;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MMM/MMP';
            $stdObj->ucaspoints = 226.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MMM';
            $stdObj->shortvalue = 'MMM';
            $stdObj->ranking = 4;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(4, 240, 34.0,0.0, $targetQualID, 'MMM'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MMM/DMM';
            $stdObj->shortvalue = 'MMM/DMM';
            $stdObj->ranking = 4.3;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MMM/DMM';
            $stdObj->ucaspoints = 253.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 4.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DMM/MMM';
            $stdObj->shortvalue = 'DMM/MMM';
            $stdObj->ranking = 4.6;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DMM/MMM';
            $stdObj->ucaspoints = 266.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 4.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DMM';
            $stdObj->shortvalue = 'DMM';
            $stdObj->ranking = 5;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(5, 280,38.2,34.0,$targetQualID, 'DMM'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DMM/DDM';
            $stdObj->shortvalue = 'DMM/DDM';
            $stdObj->ranking = 5.3;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DMM/DDM';
            $stdObj->ucaspoints = 293.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 5.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DDM/DMM';
            $stdObj->shortvalue = 'DDM/DMM';
            $stdObj->ranking = 5.6;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DDM/DMM';
            $stdObj->ucaspoints = 306.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 5.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DDM';
            $stdObj->shortvalue = 'DDM';
            $stdObj->ranking = 6;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(6, 320,38.2,44.8,$targetQualID, 'DDM'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DDM/DDD';
            $stdObj->shortvalue = 'DDM/DDD';
            $stdObj->ranking = 6.3;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DDM/DDD';
            $stdObj->ucaspoints = 333.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 6.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DDD/DDM';
            $stdObj->shortvalue = 'DDD/DDM';
            $stdObj->ranking = 6.6;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DDD/DDM';
            $stdObj->ucaspoints = 346.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 6.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DDD';
            $stdObj->shortvalue = 'DDD';
            $stdObj->ranking = 7;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(7, 360,46.6,44.8,$targetQualID, 'DDD'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DDD/D*DD';
            $stdObj->shortvalue = 'DDD/D*DD';
            $stdObj->ranking = 7.3;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DDD/D*DD';
            $stdObj->ucaspoints = 366.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 7.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*DD/DDD';
            $stdObj->shortvalue = 'D*DD/DDD';
            $stdObj->ranking = 7.6;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*DD/DDD';
            $stdObj->ucaspoints = 373.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 7.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*DD';
            $stdObj->shortvalue = 'D*DD';
            $stdObj->ranking = 8;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'DDD*'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D*DD', $record->id));
            }
            
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(8, 380,50.2,46.6,$targetQualID, 'D*DD'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*DD/D*D*D';
            $stdObj->shortvalue = 'D*DD/D*D*D';
            $stdObj->ranking = 8.3;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*DD/D*D*D';
            $stdObj->ucaspoints = 386.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 8.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*D*D/D*DD';
            $stdObj->shortvalue = 'D*D*D/D*DD';
            $stdObj->ranking = 8.6;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*D*D/D*DD';
            $stdObj->ucaspoints = 393.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 8.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*D*D';
            $stdObj->shortvalue = 'D*D*D';
            $stdObj->ranking = 9;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'DD*D*'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D*D*D', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(9, 400, $targetQualID, 'D*D*D'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*D*D/D*D*D*';
            $stdObj->shortvalue = 'D*D*D/D*D*D*';
            $stdObj->ranking = 9.3;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*D*D/D*D*D*';
            $stdObj->ucaspoints = 406.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 9.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*D*D*/D*D*D';
            $stdObj->shortvalue = 'D*D*D*/D*D*D';
            $stdObj->ranking = 9.6;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*D*D*/D*D*D';
            $stdObj->ucaspoints = 413.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 9.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*D*D*';
            $stdObj->shortvalue = 'D*D*D*';
            $stdObj->ranking = 10;
            $stdObj->context = 'assessment';
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(10, 420,58.0,50.2,$targetQualID, 'D*D*D*'));
        }
        
        //Diploma
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(3, 2, 3));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'PP';
            $stdObj->shortvalue = 'PP';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(1, 80, $targetQualID, 'PP'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'PP/MP';
            $stdObj->shortvalue = 'PP/MP';
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'PP/MP';
            $stdObj->ucaspoints = 93.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'MP/PP';
            $stdObj->shortvalue = 'MP/PP';
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MP/PP';
            $stdObj->ucaspoints = 106.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'MP';
            $stdObj->shortvalue = 'MP';
            $stdObj->ranking = 2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(2, 120, $targetQualID, 'MP'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'MP/MM';
            $stdObj->shortvalue = 'MP/MM';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MP/MM';
            $stdObj->ucaspoints = 133.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'MM/MP';
            $stdObj->shortvalue = 'MM/MP';
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MM/MP';
            $stdObj->ucaspoints = 146.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'MM';
            $stdObj->shortvalue = 'MM';
            $stdObj->ranking = 3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(3, 160,34.0,0,$targetQualID, 'MM'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'MM/DM';
            $stdObj->shortvalue = 'MM/DM';
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MM/DM';
            $stdObj->ucaspoints = 173.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'DM/MM';
            $stdObj->shortvalue = 'DM/MM';
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DM/MM';
            $stdObj->ucaspoints = 186.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.6;
            $stdObj->entryscoreupper = 35.8;
            $stdObj->entryscorelower = 34.0;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'DM';
            $stdObj->shortvalue = 'DM';
            $stdObj->ranking = 4;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(4, 200,41.2,35.8,$targetQualID, 'DM'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'DM/DD';
            $stdObj->shortvalue = 'DM/DD';
            $stdObj->ranking = 4.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DM/DD';
            $stdObj->ucaspoints = 213.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 4.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'DD/DM';
            $stdObj->shortvalue = 'DD/DM';
            $stdObj->ranking = 4.6;
            $stdObj->entryscoreupper = 43.0;
            $stdObj->entryscorelower = 41.2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DD/DM';
            $stdObj->ucaspoints = 226.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 4.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'DD';
            $stdObj->shortvalue = 'DD';
            $stdObj->ranking = 5;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(5, 240,46.6,43.0,$targetQualID, 'DD'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'DD/D*D';
            $stdObj->shortvalue = 'DD/D*D';
            $stdObj->ranking = 5.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DD/D*D';
            $stdObj->ucaspoints = 246.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 5.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'D*D/DD';
            $stdObj->shortvalue = 'D*D/DD';
            $stdObj->ranking = 5.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*D/DD';
            $stdObj->ucaspoints = 253.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 5.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'D*D';
            $stdObj->shortvalue = 'D*D';
            $stdObj->ranking = 6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(6, 260,50.2,46.6,$targetQualID, 'D*D'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'D*D/D*D*';
            $stdObj->shortvalue = 'D*D/D*D*';
            $stdObj->ranking = 6.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*D/D*D*';
            $stdObj->ucaspoints = 266.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 6.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'D*D*/D*D';
            $stdObj->shortvalue = 'D*D*/D*D';
            $stdObj->ranking = 6.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*D*/D*D';
            $stdObj->ucaspoints = 273.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 6.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->bcgttypeid = -1;
            $stdObj->context = 'assessment';
            $stdObj->value = 'D*D*';
            $stdObj->shortvalue = 'D*D*';
            $stdObj->ranking = 7;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(7, 280,58.0,50.2,$targetQualID, 'D*D*'));
        }
        
        //90 cred Diploma
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(3, 2, 9));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'PP';
            $stdObj->shortvalue = 'PP';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(1, 60, $targetQualID, 'PP'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'PP/MP';
            $stdObj->shortvalue = 'PP/MP';
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'PP/MP';
            $stdObj->ucaspoints = 73.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MP/PP';
            $stdObj->shortvalue = 'MP/PP';
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MP/PP';
            $stdObj->ucaspoints = 86.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MP';
            $stdObj->shortvalue = 'MP';
            $stdObj->ranking = 2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(2, 100, $targetQualID, 'MP'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MP/MM';
            $stdObj->shortvalue = 'MP/MM';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MP/MM';
            $stdObj->ucaspoints = 106.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MM/MP';
            $stdObj->shortvalue = 'MM/MP';
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MM/MP';
            $stdObj->ucaspoints = 113.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MM';
            $stdObj->shortvalue = 'MM';
            $stdObj->ranking = 3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(3, 120,34.0,0,$targetQualID, 'MM'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'MM/DM';
            $stdObj->shortvalue = 'MM/DM';
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'MM/DM';
            $stdObj->ucaspoints = 133.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DM/MM';
            $stdObj->shortvalue = 'DM/MM';
            $stdObj->ranking = 3.6;
            $stdObj->entryscoreupper = 35.8;
            $stdObj->entryscorelowet = 34.0;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DM/MM';
            $stdObj->ucaspoints = 146.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DM';
            $stdObj->shortvalue = 'DM';
            $stdObj->ranking = 4;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(4, 160,41.2,35.8,$targetQualID, 'DM'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DM/DD';
            $stdObj->shortvalue = 'DM/DD';
            $stdObj->ranking = 4.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DM/DD';
            $stdObj->ucaspoints = 166.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 4.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DD/DM';
            $stdObj->shortvalue = 'DD/DM';
            $stdObj->ranking = 4.6;
            $stdObj->enryscoreupper = 43.0;
            $stdObj->entryscorelower = 41.2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DD/DM';
            $stdObj->ucaspoints = 173.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 4.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DD';
            $stdObj->shortvalue = 'DD';
            $stdObj->ranking = 5;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(5, 180,46.6,43.0,$targetQualID, 'DD'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'DD/D*D';
            $stdObj->shortvalue = 'DD/D*D';
            $stdObj->ranking = 5.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'DD/D*D';
            $stdObj->ucaspoints = 186.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 5.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*D/DD';
            $stdObj->shortvalue = 'D*D/DD';
            $stdObj->ranking = 5.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*D/DD';
            $stdObj->ucaspoints = 193.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 5.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*D';
            $stdObj->shortvalue = 'D*D';
            $stdObj->ranking = 6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(6, 200,50.2,46.6,$targetQualID, 'D*D'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*D/D*D*';
            $stdObj->shortvalue = 'D*D/D*D*';
            $stdObj->ranking = 6.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*D/D*D*';
            $stdObj->ucaspoints = 203.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 6.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*D*/D*D';
            $stdObj->shortvalue = 'D*D*/D*D';
            $stdObj->ranking = 6.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*D*/D*D';
            $stdObj->ucaspoints = 206.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 6.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*D*';
            $stdObj->shortvalue = 'D*D*';
            $stdObj->ranking = 7;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(7, 210,58.0,50.2,$targetQualID, 'D*D*'));
        }
        
        //subsid Diploma
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(3, 2, 4));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(1, 40, $targetQualID, 'P'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P/M';
            $stdObj->shortvalue = 'P/M';
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'P/M';
            $stdObj->ucaspoints = 53.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/P';
            $stdObj->shortvalue = 'M/P';
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/P';
            $stdObj->ucaspoints = 66.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M';
            $stdObj->shortvalue = 'M';
            $stdObj->ranking = 2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(2, 80, 35.8,0,$targetQualID, 'M'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/D';
            $stdObj->shortvalue = 'M/D';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/D';
            $stdObj->ucaspoints = 93.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/M';
            $stdObj->shortvalue = 'D/M';
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/M';
            $stdObj->ucaspoints = 106.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $stdObj->entryscoreupper = 41.2;
            $stdObj->entryscorelowet = 35.8;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D';
            $stdObj->shortvalue = 'D';
            $stdObj->ranking = 3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(3, 120,46.6,41.2,$targetQualID, 'D'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/D*';
            $stdObj->shortvalue = 'D/D*';
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/D*';
            $stdObj->ucaspoints = 126.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*/D';
            $stdObj->shortvalue = 'D*/D';
            $stdObj->ranking = 3.6;
            $stdObj->entryscoreupper = 48.4;
            $stdObj->entryscorelower = 46.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/D*';
            $stdObj->ucaspoints = 133.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*';
            $stdObj->shortvalue = 'D*';
            $stdObj->ranking = 4;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(4, 140,58.0,50.2,$targetQualID, 'D*'));
        }
        
        //cert
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(3, 2, 5));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(1, 20, $targetQualID, 'P'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P/M';
            $stdObj->shortvalue = 'P/M';
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'P/M';
            $stdObj->ucaspoints = 26.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/P';
            $stdObj->shortvalue = 'M/P';
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/P';
            $stdObj->ucaspoints = 33.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M';
            $stdObj->shortvalue = 'M';
            $stdObj->ranking = 2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(2, 40,35.8,0,$targetQualID, 'M'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/D';
            $stdObj->shortvalue = 'M/D';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/D';
            $stdObj->ucaspoints = 46.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/M';
            $stdObj->shortvalue = 'D/M';
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/M';
            $stdObj->ucaspoints = 53.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $stdObj->entryscoreupper = 41.2;
            $stdObj->entryscorelower = 38.2;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D';
            $stdObj->shortvalue = 'D';
            $stdObj->ranking = 3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(3, 60,46.6,41.2,$targetQualID, 'D'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/D*';
            $stdObj->shortvalue = 'D/D*';
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/D*';
            $stdObj->ucaspoints = 63.3;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*/D';
            $stdObj->shortvalue = 'D*/D';
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*/D';
            $stdObj->ucaspoints = 66.6;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.6;
            $stdObj->entryscoreupper = 48.4;
            $stdObj->entryscorelower = 46.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*';
            $stdObj->shortvalue = 'D*';
            $stdObj->ranking = 4;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(4, 70,58.0,48.4,$targetQualID, 'D*'));
        }
        
        //Level 4 HNC
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(4, 3, 7));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Pass'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('P', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(1, 0, $targetQualID, 'P'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P/M';
            $stdObj->shortvalue = 'P/M';
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'P/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/P';
            $stdObj->shortvalue = 'M/P';
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/P';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M';
            $stdObj->shortvalue = 'M';
            $stdObj->ranking = 2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Merit'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('M', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(2, 0, $targetQualID, 'M'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/D';
            $stdObj->shortvalue = 'M/D';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/D';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/M';
            $stdObj->shortvalue = 'D/M';
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D';
            $stdObj->shortvalue = 'D';
            $stdObj->ranking = 3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Distinction'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(3, 0, $targetQualID, 'D'));
        }
        
        //Level 5 HND
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(5, 3, 8));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Pass'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('P', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(1, 0, $targetQualID, 'P'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P/M';
            $stdObj->shortvalue = 'P/M';
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'P/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/P';
            $stdObj->shortvalue = 'M/P';
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/P';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M';
            $stdObj->shortvalue = 'M';
            $stdObj->ranking = 2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Merit'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('M', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(2, 0, $targetQualID, 'M'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/D';
            $stdObj->shortvalue = 'M/D';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/D';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/M';
            $stdObj->shortvalue = 'D/M';
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D';
            $stdObj->shortvalue = 'D';
            $stdObj->ranking = 3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //UPdate the target breakdown with ucas points and ranking
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Disticntion'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(3, 0, $targetQualID, 'D'));
        }
        
        //Level 3 Found Diploma
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(3, 4, 10));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Pass'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('P', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(1, 165, $targetQualID, 'P'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P/M';
            $stdObj->shortvalue = 'P/M';
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'P/M';
            $stdObj->ucaspoints = 185;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/P';
            $stdObj->shortvalue = 'M/P';
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/P';
            $stdObj->ucaspoints = 205;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M';
            $stdObj->shortvalue = 'M';
            $stdObj->ranking = 2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Merit'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('M', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(2, 225, $targetQualID, 'M'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/D';
            $stdObj->shortvalue = 'M/D';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/D';
            $stdObj->ucaspoints = 245;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/M';
            $stdObj->shortvalue = 'D/M';
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/M';
            $stdObj->ucaspoints = 265;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D';
            $stdObj->shortvalue = 'D';
            $stdObj->ranking = 3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Distinction'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(3, 285, $targetQualID, 'D'));
        }
        
        //Level 2 Award
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(2, 2, 6));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Pass'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('P', $record->id));
            }
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P/M';
            $stdObj->shortvalue = 'P/M';
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'P/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/P';
            $stdObj->shortvalue = 'M/P';
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/P';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);

            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M';
            $stdObj->shortvalue = 'M';
            $stdObj->ranking = 2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Merit'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('M', $record->id));
            }

            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/D';
            $stdObj->shortvalue = 'M/D';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/D';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);

            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/M';
            $stdObj->shortvalue = 'D/M';
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);

            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D';
            $stdObj->shortvalue = 'D';
            $stdObj->ranking = 3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Distinction'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D', $record->id));
            }

            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/D*';
            $stdObj->shortvalue = 'D/D*';
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/D*';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);

            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*/D';
            $stdObj->shortvalue = 'D*/D';
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*/D';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);

            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*';
            $stdObj->shortvalue = 'D*';
            $stdObj->ranking = 4;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Distinction*'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D*', $record->id));
            }
        }
        
        //Level 2 Cert
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(2, 2, 5));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Pass'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('P', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(1, 0, $targetQualID, 'P'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P/M';
            $stdObj->shortvalue = 'P/M';
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'P/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/P';
            $stdObj->shortvalue = 'M/P';
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/P';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M';
            $stdObj->shortvalue = 'M';
            $stdObj->ranking = 2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Merit'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('M', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(2, 0, $targetQualID, 'M'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/D';
            $stdObj->shortvalue = 'M/D';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/D';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/M';
            $stdObj->shortvalue = 'D/M';
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D';
            $stdObj->shortvalue = 'D';
            $stdObj->ranking = 3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Distinction'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(3, 0, $targetQualID, 'D'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/D*';
            $stdObj->shortvalue = 'D/D*';
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/D*';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*/D';
            $stdObj->shortvalue = 'D*/D';
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*/D';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*';
            $stdObj->shortvalue = 'D*';
            $stdObj->ranking = 4;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Distinction*'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D*', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(4, 0, $targetQualID, 'D*'));
        }
        
        //Level 2 Ext Cert
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(2, 2, 11));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Pass'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('P', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(1, 0, $targetQualID, 'P'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P/M';
            $stdObj->shortvalue = 'P/M';
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'P/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/P';
            $stdObj->shortvalue = 'M/P';
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/P';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M';
            $stdObj->shortvalue = 'M';
            $stdObj->ranking = 2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Merit'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('M', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(2, 0, $targetQualID, 'M'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/D';
            $stdObj->shortvalue = 'M/D';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/D';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/M';
            $stdObj->shortvalue = 'D/M';
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D';
            $stdObj->shortvalue = 'D';
            $stdObj->ranking = 3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Distinction'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(3, 0, $targetQualID, 'D'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/D*';
            $stdObj->shortvalue = 'D/D*';
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/D*';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*/D';
            $stdObj->shortvalue = 'D*/D';
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*/D';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*';
            $stdObj->shortvalue = 'D*';
            $stdObj->ranking = 4;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Distinction*'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D*', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(4, 0, $targetQualID, 'D*'));
        }
        
        //Level 2 Dip
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(2, 2, 3));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Pass'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('P', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(1, 0, $targetQualID, 'P'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P/M';
            $stdObj->shortvalue = 'P/M';
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'P/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/P';
            $stdObj->shortvalue = 'M/P';
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/P';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 1.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M';
            $stdObj->shortvalue = 'M';
            $stdObj->ranking = 2;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Merit'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('M', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(2, 0, $targetQualID, 'M'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'M/D';
            $stdObj->shortvalue = 'M/D';
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'M/D';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/M';
            $stdObj->shortvalue = 'D/M';
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/M';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 2.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D';
            $stdObj->shortvalue = 'D';
            $stdObj->ranking = 3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Distinction'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(3, 0, $targetQualID, 'D'));
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D/D*';
            $stdObj->shortvalue = 'D/D*';
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D/D*';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.3;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*/D';
            $stdObj->shortvalue = 'D*/D';
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            //insert this as a target breeakdown.
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->targetgrade = 'D*/D';
            $stdObj->ucaspoints = 0;
            $stdObj->unitscorelower = -1;
            $stdObj->unitsscoreupper = -1;
            $stdObj->ranking = 3.6;
            $DB->insert_record('block_bcgt_target_breakdown', $stdObj);
            
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'D*';
            $stdObj->shortvalue = 'D*';
            $stdObj->ranking = 4;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Distinction*'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('D*', $record->id));
            }
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(4, 0, $targetQualID, 'D*'));
        }
        
        //Level 1 Award
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(1, 5, 6));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Pass'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('P', $record->id));
            }
        }
        
        //Level 1 Certificate
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(1, 5, 5));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Pass'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('P', $record->id));
            }
        }
        
        //Level 1 Diploma
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(1, 5, 3));
        if($record)
        {
            $targetQualID = $record->id;
            $stdObj = new stdClass();
            $stdObj->bcgttargetqualid = $targetQualID;
            $stdObj->context = 'assessment';
            $stdObj->bcgttypeid = -1;
            $stdObj->value = 'P';
            $stdObj->shortvalue = 'P';
            $stdObj->ranking = 1;
            $DB->insert_record('block_bcgt_value', $stdObj);
            
            $record = $DB->get_record_sql("SELECT * FROM {block_bcgt_target_breakdown} 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array($targetQualID, 'Pass'));
            if($record)
            {
                $DB->execute("UPDATE {block_bcgt_target_breakdown} SET targetgrade = ? WHERE id = ?", array('P', $record->id));
            }
        }
        
        //find all of the breakdowns for the targetquals that are for the btec families. 
        $sql = "SELECT distinct(breakdown.id), breakdown.* FROM {block_bcgt_target_breakdown} breakdown 
            JOIN {block_bcgt_target_qual} targetqual ON targetqual.id = breakdown.bcgttargetqualid 
            JOIN {block_bcgt_type} type ON type.id = targetqual.bcgttypeid 
            JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid 
            WHERE family = ?";
        $records = $DB->get_records_sql($sql, array('btec'));
        if($records)
        {
            foreach($records AS $record)
            {
                $stdObj = new stdClass();
                $stdObj->bcgttargetqualid = $record->bcgttargetqualid;
                $stdObj->grade = $record->targetgrade;
                $stdObj->ucaspoints = $record->ucaspoints;
                $stdObj->ranking = $record->ranking;
                $stdObj->upperscore = $record->entryscoreupper;
                $stdObj->lowerscore = $record->entryscorelower;
                $DB->insert_record('block_bcgt_target_grades', $stdObj);
            }
        }
    }
    
    if($oldversion < 2013091003)
    {
        //want to find the reffered value if it exists. 
        $sql = "SELECT * FROM {block_bcgt_value} value WHERE shortvalue = ?";
        $record = $DB->get_record_sql($sql , array('Reffered'));
        if($record)
        {
            $record->shortvalue = 'Referred';
            $DB->update_record('block_bcgt_value', $record);
        }
    }
    
    if($oldversion < 2013091003)
    {
        //due to an installation error we dont have extended certificate
        if(!($DB->record_exists('block_bcgt_subtype', array('subtype'=>'Extended Certificate'))))
        {
    //        $record = new stdClass();
    //        $record->id = 11;
    //        $record->subtype = 'Extended Certificate';
    //        $record->subtypeshort = 'ExCert';
    //        $DB->insert_record_raw('block_bcgt_subtype', $record, false, false, true);

            //THIS HAS BEEN CHANGED TO THE BELOW DUE TO AN ERROR IN moodle 2.2 core code. THE below should fix this.
            $DB->execute("INSERT INTO {block_bcgt_subtype} (id,subtype,subtypeshort) 
            VALUES (11,'Extended Certificate','ExCert')");
        }
    }
    
    if($oldversion < 2013091502)
    {
        //level 3 ext dip 
        $sql = "SELECT * FROM {block_bcgt_target_qual} WHERE bcgtlevelid = ? AND bcgttypeid = ? AND bcgtsubtypeid = ?";
        $record = $DB->get_record_sql($sql, array(3, 2, 2));
        if($record)
        {
            $targetQualID = $record->id;
    
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(5, 280,38.2,34.0,$targetQualID, 'DMM'));
            
            $DB->execute("UPDATE {block_bcgt_target_grades} SET ranking = ?, ucaspoints = ?, upperscore = ?, 
            lowerscore = ? 
                WHERE bcgttargetqualid = ? AND grade = ?", array(5, 280,38.2,34.0,$targetQualID, 'DMM'));
            
            //UPdate the target breakdown with ucas points and ranking
            $DB->execute("UPDATE {block_bcgt_target_breakdown} SET ranking = ?, ucaspoints = ?, entryscoreupper = ?, 
            entryscorelower = ? 
                WHERE bcgttargetqualid = ? AND targetgrade = ?", array(6, 320,44.8,38.2,$targetQualID, 'DDM'));
            
            $DB->execute("UPDATE {block_bcgt_target_grades} SET ranking = ?, ucaspoints = ?, upperscore = ?, 
            lowerscore = ? 
                WHERE bcgttargetqualid = ? AND grade = ?", array(6, 320,44.8,38.2,$targetQualID, 'DDM'));
        }
    }
    
    if($oldversion < 20131018000)
    {
        //want to find the reffered value if it exists. 
        $sql = "SELECT * FROM {block_bcgt_value} value WHERE shortvalue = ?";
        $record = $DB->get_record_sql($sql , array('Reffered'));
        if($record)
        {
            $record->shortvalue = 'Referred';
            $DB->update_record('block_bcgt_value', $record);
        }
    }
}
