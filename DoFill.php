<?php
/**
 * This file contains DoFill class helping to fill data to cell.
 *
 * @author KoolPHP Inc (support@koolphp.net)
 * @link https://www.koolphp.net
 * @copyright KoolPHP Inc
 * @license https://www.koolreport.com/license
 */
namespace koolreport\cleandata;

use \koolreport\core\Process;
use \koolreport\core\Utility;

class DoFill extends Process
{
    protected $targetValue;
    protected $newValue;
    protected $targetColumns;
    protected $excludedColumns;
    protected $targetColumnType;
    protected $strict;
    
    protected $columns;

    protected function onInit()
    {
        $this->newValue = Utility::get($this->params,"newValue");
        $this->targetValue = Utility::get($this->params,"targetValue");
        $this->targetColumnType = Utility::get($this->params,"targetColumnType");
        $this->targetColumns = Utility::get($this->params,"targetColumns");
        $this->excludedColumns = Utility::get($this->params,"excludedColumns");
        $this->strict = Utility::get($this->params,"strict",false);
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
        foreach($this->columns as $cName)
        {
            if(($this->strict===false && $row[$cName]==$this->targetValue)||($this->strict===true && $row[$cName]===$this->targetValue))
            {
                switch($this->newValue)
                {
                    case FillNull::MEAN:
                        $row[$cName] = $this->metaData["describe"][$cName]["mean"];
                    break;
                    case FillNull::MEDIAN:
                        $row[$cName] = $this->metaData["describe"][$cName]["median"];
                    break;
                    default:
                        $row[$cName]=$this->newValue;
                    break;
                }        
            }
        }
        $this->next($row);
    }
    
}