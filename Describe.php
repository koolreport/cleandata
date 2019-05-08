<?php
/**
 * This file contains Describe process providing the mean and median for data.
 *
 * @author KoolPHP Inc (support@koolphp.net)
 * @link https://www.koolphp.net
 * @copyright KoolPHP Inc
 * @license https://www.koolreport.com/license
 */
namespace koolreport\cleandata;
use \koolreport\core\Process;
use \koolreport\core\Utility;

class Describe extends Process
{
    protected $data = array();
    protected $targetColumns;
    protected $excludedColumns;
    protected $targetColumnType;
    protected $columns;

    protected function onInit()
    {
        $this->newValue = Utility::get($this->params,"newValue");
        $this->targetValue = Utility::get($this->params,"targetValue");
        $this->targetColumnType = Utility::get($this->params,"targetColumnType");
        $this->targetColumns = Utility::get($this->params,"targetColumns");
        $this->excludedColumns = Utility::get($this->params,"excludedColumns");
    }

    protected function onInput($row)
    {
        array_push($this->data,$row);
    }

	public function receiveMeta($meta,$source)
	{
		$this->streamingSource = $source;
        $this->metaData = $meta;
        
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
    }

    protected function median($arr)
    {
        sort($arr);
        $count = count($arr);
        if($count%2==0)
        {
            return ($arr[$count/2-1]+$arr[$count/2])/2;
        }
        else
        {
            return $arr[floor($count/2)];
        }
    }
    protected function onInputEnd()
    {
        $dMeta = array();
        
        if($this->newValue==FillNull::MEAN)
        {
            foreach($this->columns as $cName)
            {
                $dMeta[$cName] = array(
                    "count"=>0,
                    "sum"=>0,
                );
                foreach($this->data as $row)
                {
                    if($row[$cName]!=$this->targetValue)
                    {
                        $dMeta[$cName]["count"]++;
                        $dMeta[$cName]["sum"]+=$row[$cName];
                    }
                }
                $dMeta[$cName]["mean"] = $dMeta[$cName]["sum"]/$dMeta[$cName]["count"];
            }
        }
        else if($this->newValue==FillNull::MEDIAN)
        {
            foreach($this->columns as $cName)
            {
                $list = array();
                foreach($this->data as $row)
                {
                    if($row[$cName]!=$this->targetValue)
                    {
                        array_push($list,$row[$cName]);
                    }
                }
                $dMeta[$cName] = array("median"=>$this->median($list));
            }
        
        }

        $meta = $this->metaData;
        $meta["describe"] = $dMeta;
        $this->sendMeta($meta);

        while($item = array_shift($this->data))
        {
            $this->next($item);
        }
    }
}