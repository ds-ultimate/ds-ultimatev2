<?php

namespace App\Util;

/**
 * OUTDATED -> use C-Code instead
 * this lib implements the LZW Compression algorithm
 */
class LZWCompressor
{
    private static $MAX_CODE_SIZE = 500;
    
    private $compressed = "";
    private $clearCode;
    private $endOfImageCode;
    private $initTable;
    private $initTableBin;
    private $initTableSize;
    
    private $lastFoundCode = -1;
    private $codeTable;
    private $codeTableBin;
    private $indexCache = [];
    private $bitStream = "";
    private $codeSize;
    private $codeSizeLimit;
    
    public function __construct($initialTableSize) {
        $this->clearCode = pow(2, $initialTableSize);
        $this->endOfImageCode = $this->clearCode + 1;
        for($i = 0; $i < $this->clearCode; $i++) {
            $this->initTable[] = [$i];
            $this->initTableBin[static::codeToBinary([$i])] = $i;
        }
        //write something there in order to have correct indexing
        $this->initTable[$this->clearCode] = ["CC"];
        $this->initTable[$this->endOfImageCode] = ["EOIC"];
        
        $this->codeTable = $this->initTable;
        $this->codeTableBin = $this->initTableBin;
        $this->initTableSize = count($this->initTable);
        $this->sizeLimitCalc();
        $this->bitStreamAppend($this->clearCode);
    }
    
    /**
     * used for writing to the stream
     */
    public function append($data) {
        $this->indexCache[] = $data;
        
        $bin = static::codeToBinary($this->indexCache);
        $codeIndex = $this->codeTableBin[$bin] ?? -1;
        if($codeIndex == -1) {
            //not found
            $this->codeTableBin[$bin] = count($this->codeTable);
            $this->codeTable[] = $this->indexCache;
            $this->bitStreamAppend($this->lastFoundCode);
            
            if(count($this->codeTable) == static::$MAX_CODE_SIZE) {
                //table full -> flush
                $this->flushTable();
            }
            $this->indexCache = [];
            $this->indexCache[] = $data;
            //we can savely just set $data since that is what happens here
            $codeIndex = $data;
        }
        $this->lastFoundCode = $codeIndex;
    }
    
    private function bitStreamAppend($data) {
        $this->bitStream = str_pad(decbin($data), $this->codeSize, "0", STR_PAD_LEFT) . $this->bitStream;
        while(strlen($this->bitStream) > 7) {
            $part = substr($this->bitStream, strlen($this->bitStream) - 8);
            $this->bitStream = substr($this->bitStream, 0, strlen($this->bitStream) - 8);
            $this->compressed .= chr(bindec($part));
        }
        
        if(count($this->codeTable) == $this->codeSizeLimit) {
            $this->sizeLimitCalc();
        }
    }
    
    private function flushTable() {
        $this->bitStreamAppend($this->clearCode);
        $this->codeTable = $this->initTable;
        $this->codeTableBin = $this->initTableBin;
        $this->sizeLimitCalc();
        $this->indexCache = [];
        $this->lastFoundCode = -1;
    }
    
    private function sizeLimitCalc() {
        $this->codeSize = ceil(log(count($this->codeTable), 2));
        $this->codeSizeLimit = pow(2, $this->codeSize) + 1;
    }
    
    public function finish() {
        $this->bitStreamAppend($this->lastFoundCode);
        $this->bitStreamAppend($this->endOfImageCode);
        
        //must be less than one byte left
        if($this->bitStream != "") {
            $this->compressed .= chr(bindec($this->bitStream));
        }
    }
    
    public function getCompressed() {
        return $this->compressed;
    }
    
    /**
     * creates a binary representation of the given code
     */
    private static function codeToBinary($code) {
        $res = "";
        foreach($code as $c) {
            $res .= chr($c);
        }
        return $res;
    }
}