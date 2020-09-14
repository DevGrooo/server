<?php

namespace App\Imports;

use App\Services\Banks\StandardCharteredBank;
use App\Services\Transactions\StatementLineTransaction;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StatementImport implements WithHeadingRow, ToCollection
{
    protected $statement_id;
    protected $fund_company_id;
    protected $fund_distributor_id;
    protected $supervising_bank_id;
    protected $total_line = 0;

    function __construct($statement_id, $fund_company_id, $fund_distributor_id, $supervising_bank_id)
    {
        $this->statement_id = $statement_id;
        $this->fund_company_id = $fund_company_id;
        $this->fund_distributor_id = $fund_distributor_id;
        $this->supervising_bank_id = $supervising_bank_id;
    }

    public function getTotalLine()
    {
        return $this->total_line;
    }

    public function headingRow() : int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        $transaction = new StatementLineTransaction();
        foreach ($rows as $row) {
            if (!StandardCharteredBank::validateData($row)) {
                throw new Exception('Dữ liệu file sao kê không hợp lệ');
            }
            if (StandardCharteredBank::isDepositTransaction($row)) {
                $transaction->create([
                    'statement_id' => $this->statement_id, 
                    'fund_company_id' => $this->fund_company_id, 
                    'fund_distributor_id' => $this->fund_distributor_id, 
                    'supervising_bank_id' => $this->supervising_bank_id, 
                    'data' => $row,
                    'bank_trans_note' => $row['transaction_detail'],
                    'created_by' => 0,
                ]);
                $this->total_line++;
            }            
        }
    }
}