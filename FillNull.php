<?php
/**
 * This file contains FillNull process
 *
 * @author KoolPHP Inc (support@koolphp.net)
 * @link https://www.koolphp.net
 * @copyright KoolPHP Inc
 * @license https://www.koolreport.com/license
 */
namespace koolreport\cleandata;

use \koolreport\core\ProcessGroup;
use \koolreport\core\Utility;

class FillNull extends ProcessGroup
{
    const MEAN="~FILLNULLMEAN";
    const MEDIAN="~FILLNULLMEDIAN";
    protected $targetValue;
    protected $newValue;
    protected $targetColumns;
    protected $excludedColumns;
    protected $targetColumnType;
    // Column => new value, from the shorthand new FillNull(array("state" => "Unknown")).
    protected $columnValues;
    protected function onInit()
    {
        $this->newValue = Utility::get($this->params,"newValue");
        $this->columnValues = $this->shorthandColumnValues();
        if($this->newValue===null && $this->columnValues===null)
        {
            throw new \Exception("Please specify newValue in FillNull process");
        }

        $this->targetValue = Utility::get($this->params,"targetValue");
        $this->targetColumnType = Utility::get($this->params,"targetColumnType");
        $this->targetColumns = Utility::get($this->params,"targetColumns");
        $this->excludedColumns = Utility::get($this->params,"excludedColumns");
    }

    /**
     * The shorthand form names columns directly: array("state" => "Unknown", "score" => FillNull::MEAN).
     * It applies when none of the option keys is used; returns null otherwise.
     */
    protected function shorthandColumnValues()
    {
        if (!is_array($this->params) || count($this->params) === 0) {
            return null;
        }
        $options = array("newValue", "targetValue", "targetColumns", "excludedColumns", "targetColumnType", "strict", "thresh");
        foreach ($this->params as $key => $value) {
            if (!is_string($key) || in_array($key, $options, true)) {
                return null;
            }
        }
        return $this->params;
    }

    public function setup()
    {
        if ($this->columnValues !== null) {
            // One fill per column, so each column can have its own new value.
            $node = $this->incoming();
            foreach ($this->columnValues as $column => $value) {
                $params = array("targetColumns" => array($column), "newValue" => $value);
                if ($value === FillNull::MEAN || $value === FillNull::MEDIAN) {
                    $node = $node->pipe(new Describe($params));
                }
                $node = $node->pipe(new DoFill($params));
            }
            $node->pipe($this->outcoming());
            return;
        }
        switch($this->newValue)
        {
            case FillNull::MEAN:
            case FillNull::MEDIAN:
                $this->incoming()
                ->pipe(new Describe($this->params))
                ->pipe(new DoFill($this->params))
                ->pipe($this->outcoming());
            break;
            default:
                $this->incoming()
                ->pipe(new DoFill($this->params))
                ->pipe($this->outcoming());
            break;
        }
    }
}