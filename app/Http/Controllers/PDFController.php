<?php


namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade as PDF ;
use Illuminate\Support\Facades\App;


class PDFController extends Controller
{
    public function exportBuyConfirmation() {
        $pdf = App::make('dompdf.wrapper');
        $pdf_view=$pdf->loadView('exports.buy_confirmation')->setPaper('a4', 'portrait')->setWarnings(false);
        return $pdf->download('buy_confirmation.pdf');
    }

    public function exportSaleConfirmation() {
        $pdf = App::make('dompdf.wrapper');
        $pdf_view=$pdf->loadView('exports.sale_confirmation')->setPaper('a4', 'portrait')->setWarnings(false);
        return $pdf->download('sale_confirmation.pdf');
    }
}
