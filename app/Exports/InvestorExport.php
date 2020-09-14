<?php
namespace App\Exports;

use App\Http\Resources\InvestorResource;
use App\Services\Transactions\InvestorTransaction;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InvestorExport extends ViewExport implements WithColumnFormatting
{
    protected function _getTemplate()
    {
        return 'exports.investors';
    }

    protected function _getData()
    {        
        return InvestorResource::collection($this->query_builder->get())->toArray($this->request);
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
            'AA' => NumberFormat::FORMAT_TEXT,
            'AE' => NumberFormat::FORMAT_TEXT,
            'AA' => NumberFormat::FORMAT_TEXT,
            'AH' => NumberFormat::FORMAT_TEXT,
            'AK' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
