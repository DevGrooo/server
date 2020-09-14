<?php

namespace App\Imports;

use App\Models\FileImport;
use App\Models\FileImportLine;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

abstract class BasicImport implements WithHeadingRow, ToCollection
{    
    protected $total_line = 0;
    protected $data_result = [];
    protected $warnings = [];
    protected $errors = [];
    protected $lines = [];

    abstract protected function _processValueInRow($row);
    abstract protected function _getRulesValidateRow($row);
    

    function __construct()
    {
        
    }

    public function callTransactionAfterFileImportProcessed(FileImport $file_import)
    {

    }

    public function callTransactionAfterFileImportLineSuccess(FileImportLine $file_import_line)
    {

    }

    final public function getTotalLine()
    {
        return $this->total_line;
    }

    public function headingRow() : int
    {
        return 1;
    }

    final public function checkFileImportLine(FileImportLine $file_import_line)
    {
        if ($this->_validateRow($file_import_line->data)) {
            $this->_processValueInRow($file_import_line->data);
            if (empty($this->errors)) {
                return true;
            }
        }
        return false;
    }

    final public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $row = $this->_convertRow($row);
            if ($this->_isRow($row)) {
                $this->lines[] = $row;
                $this->total_line++;
            }    
        }
    }

    protected function _isRow($row) 
    {
        if ($this->_isRowEmpty($row)) {
            return false;
        }
        $rules = $this->_getRulesValidateRow($row);
        $keys = array_keys($rules);
        if (!empty($keys)) {
            foreach ($keys as $key) {                
                if (!array_key_exists($key, $row)) {
                    throw new Exception('File import không hợp lệ, thiếu cột:'. $key);
                }
            }
        }        
        return true;
    }

    final protected function _convertRow($row)
    {
        $row = $this->_convertToArray($row);
        foreach ($row as $key => $value) {
            $row[$key] = trim($value);
        }
        return $row;
    }

    final protected function _convertToArray($object)
    {
        return json_decode(json_encode($object), true);
    }

    final protected function _isRowEmpty($row)
    {
        foreach ($row as $cell_value) {
            if ($cell_value != '') {
                return false;
            }
        }
        return true;
    }

    public function getLines()
    {
        return $this->lines;
    }

    public function getDataResult() 
    {
        return $this->data_result;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getWarnings()
    {
        return $this->warnings;
    }

    protected function _addError($message)
    {
        $this->errors[] = $message;
    }

    protected function _addErrors($messages)
    {
        foreach ($messages as $key => $message) {
            $this->_addError($message);
        }
    }

    protected function _addWarning($message)
    {
        $this->warnings[] = $message;
    }

    protected function _validateRow($row)
    {
        return $this->_validateRowByRule($row, $this->_getRulesValidateRow($row));
    }

    final protected function _validateRowByRule($row, $rules, $messages = [])
    {
        $validator = Validator::make($row, $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->errors()->unique(':key: :message');
            $this->_addErrors($errors);
            return false;
        }
        return true;
    }
}