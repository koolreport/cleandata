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
    protected function onInit()
    {
        $this->newValue = Utility::get($this->params,"newValue");
        if($this->newValue===null)
        {
            throw new \Exception("Please specify newValue in FillNull process");
        }

        $this->targetValue = Utility::get($this->params,"targetValue");
        $this->targetColumnType = Utility::get($this->params,"targetColumnType");
        $this->targetColumns = Utility::get($this->params,"targetColumns");
        $this->excludedColumns = Utility::get($this->params,"excludedColumns");
    }

    public function setup()
    {
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