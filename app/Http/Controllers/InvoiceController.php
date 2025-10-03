<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;

class InvoiceController extends Controller
{

    public function generateInvoice($invoiceId)
    {
        $invoice = Invoice::with(['invoiceItems', 'payments'])->findOrFail($invoiceId);
        $items = $invoice->invoiceItems;
        $payments = $invoice->payments;

        $totalPaid = $invoice->payments->sum('amount_paid');
        $totalQuantity = $items->where('item_id')->sum('quantity');

        $itemCount = $items->count();
        $itemsPerPage = 3;
        $isLastChunk = ($itemsPerPage >= $itemCount);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'invoiceItems' => $itemsPerPage >= $itemCount ? $items : $items->slice(0, $itemsPerPage),
            'totalPaid' => $totalPaid,
            'payments' => $payments,
            'totalQuantity' => $totalQuantity,
            'showGrandTotal' => $isLastChunk,
        ]);

        return $pdf->stream('invoice_' . $invoice->id . '.pdf');
    }
}
