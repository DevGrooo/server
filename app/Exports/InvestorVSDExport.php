<?php
namespace App\Exports;

use App\Models\Investor;
use App\Services\Transactions\InvestorTransaction;

class InvestorVSDExport extends InvestorExport
{
    protected function _getData()
    {        
        $data = parent::_getData();
        if (!empty($data)) {
            foreach ($data as $row) {
                if ($row['status'] == Investor::STATUS_NEW) {
                    (new InvestorTransaction)->updateStatusImportVSD([
                        'investor_id' => $row['id'], 
                        'updated_by' => auth()->user()->id,
                    ], true);
                }
            }
        }
        return $data;
    }
}
