<?php

namespace koolreport\cleandata;

use \koolreport\core\Process;
use \koolreport\core\Utility;

class DropNull extends Process
{
    protected $targetValue;
    protected $targetColumnType;
    protected $targetColumns;
    protected $excludedColumns;
    protected $thresh; 
    protected $columns;

    protected function onInit()
    {
        $this->targetValue = Utility::get($this->params,"targetValue");
        $this->targetColumnType = Utility::get($this->params,"targetColumnType");
        $this->targetColumns = Utility::get($this->params,"targetColumns");
        $this->excludedColumns = Utility::get($this->params,"excludedColumns");
        $this->thresh = Utility::get($this->params,"thresh",1);
    }

    protected function onMetaReceived($meta)
    {
        $columns = array();
        foreach($meta["columns"] as $cName=>$cSetting)
        {
            $columnName = $cName;
            if($this->targetColumns!==null && !in_array($cName,$this->targetColumns))
            {
                $columnName = null;
            }
            if($columnName!==null)
            {
                if($this->excludedColumns!==null && in_array($cName,$this->excludedColumns))
                {
                    $columnName = null;
                }
            }
            if($columnName!==null)
            {
                if($this->targetColumnType!==null && $this->targetColumnType!==Utility::get($cSetting,"type"))
                {
                    $columnName = null;
                }
            }
            if($columnName!==null)
            {
                array_push($columns,$columnName);
            }
        }
        $this->columns = $columns;
        return $meta;
    }

    protected function onInput($row)
    {
        $thresh=0;
        foreach($this->columns as $cName)
        {
            if($row[$cName]==$this->targetValue)
            {
                $thresh++;
            }
            if($thresh>=$this->thresh)
            {
                return;
            }
        }
        $this->next($row);
    }
}