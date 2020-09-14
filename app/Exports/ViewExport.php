<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

abstract class ViewExport implements FromView, ShouldAutoSize
{
    abstract protected function _getTemplate();
    abstract protected function _getData();

    protected $query_builder = '';    
    protected $request = null;

    function __construct($query_builder, $request)
    {
        $this->query_builder = $query_builder;
        $this->request = $request;
    }

    public function view(): View {
        return view($this->_getTemplate(), ['data' => $this->_getData(), 'request' => $this->request]);
    }
}