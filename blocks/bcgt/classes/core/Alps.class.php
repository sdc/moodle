<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Alps
 *
 * @author mchaney
 */
class Alps {
    //put your code here
    
    const ENTRIESMULTIPLYER = 50;
    protected $alpsMultiplier = 50;
    
    public function Alps()
    {
        
    }
    
    public function set_alps_multiplier($multiplier)
    {
        $this->alpsMultiplier = $multiplier;
    }
    
    public function calculate_class_alps_report($ucasTarget, $ucasAchieved, $qualID, $noEntries, $showCoefficient = false)
    {
        $coefficientScore = $this->calculate_alps_score($ucasTarget, $ucasAchieved, $noEntries);
        //get the weightings:
        $qualWeighting = new QualWeighting();
        $qualWeightingRecord = $qualWeighting->get_alps_temperature($qualID, $coefficientScore);
        if($qualWeightingRecord)
        {
            if($showCoefficient)
            {
                $stdObj = new stdClass();
                $stdObj->number = $qualWeightingRecord->number;
                $stdObj->score = $coefficientScore;
                
                return $stdObj;
            }
            else
            {
                return $qualWeightingRecord->number;
            }
        }
        return -1;
    }
    
    public function calculate_students_alps_report($ucasTarget, $ucasAchieved, $qualID, $showCoefficient = false)
    {
        $coefficientScore = $this->calculate_alps_score($ucasTarget, $ucasAchieved, 1);
        //get the weightings:
        $qualWeighting = new QualWeighting();
        $qualWeightingRecord = $qualWeighting->get_alps_temperature($qualID, $coefficientScore);
        if($qualWeightingRecord)
        {
            if($showCoefficient)
            {
                $stdObj = new stdClass();
                $stdObj->number = $qualWeightingRecord->number;
                $stdObj->score = $coefficientScore;
                
                return $stdObj;
            }
            else
            {
                return $qualWeightingRecord->number;
            }
        }
        return -1;
    }
    
    public function calculate_alps_score($ucasTarget, $ucasAchieved, $noEntries)
    {
        //The alps entrymultipyer is dependant on the Qualification
        return 1 + (($ucasAchieved - $ucasTarget)/($this->alpsMultiplier * $noEntries));
    }
    
    public function calculate_students_alps_overall()
    {
        
    }
    
    public function get_student_overall_alps($userID, $qualID, $assID)
    {

    }
}

?>
