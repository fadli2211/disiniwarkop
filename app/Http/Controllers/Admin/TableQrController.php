<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\TableQr;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableQrController extends Controller
{
    public function index(Table $table)
    {
        $qrs = $table->qrs;
        return view('admin.table_code.index', compact('table', 'qrs'));
    }

    public function store(Table $table)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $qrCode = '';
        for ($i = 0; $i < 8; $i++) {
            $qrCode .= $characters[rand(0, strlen($characters) - 1)];
        }

        while ($table->qrs()->where('code', $qrCode)->exists()) {
            $qrCode = '';
            for ($i = 0; $i < 8; $i++) {
                $qrCode .= $characters[rand(0, strlen($characters) - 1)];
            }
        }

        $table->qrs()->create([
            'code'   => $qrCode,
            'status' => 0
        ]);

        return back()->with('success', 'QR baru ditambahkan untuk Meja ' . $table->number);
    }


    public function destroy(Table $table, TableQr $qr)
    {
        $qr->delete();
        return redirect()->back()->with('success', 'QR berhasil dihapus');
    }

    public function printAll()
    {
        $qrs = TableQr::where('status', 0)->with('table')->get();

        foreach ($qrs as $qr) {
            $result = Builder::create()
                ->data(url('/').'?ref='.$qr->code)
                ->size(150)
                ->margin(5)
                ->build();

            $qr->base64 = base64_encode($result->getString());
        }

        $pdf = Pdf::loadView('admin.table_code.print', [
            'title' => 'Semua Meja',
            'qrs'   => $qrs,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('qr-semua-meja.pdf');
    }

    public function printTable(Table $table)
    {
        $qrs = $table->qrs()->where('status', 0)->get();

        foreach ($qrs as $qr) {
            $result = Builder::create()
                ->data(url('/').'?ref='.$qr->code)
                ->size(150)
                ->margin(5)
                ->build();

            $qr->base64 = base64_encode($result->getString());
        }

        $pdf = Pdf::loadView('admin.table_code.print', [
            'title' => 'QR Meja '.$table->number,
            'qrs'   => $qrs,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('qr-meja-'.$table->number.'.pdf');
    }
}
