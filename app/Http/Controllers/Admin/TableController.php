<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        return view('admin.table.index', compact('tables'));
    }

    public function store()
    {
        $lastNumber = Table::max('number') ?? 0;
        $newNumber = $lastNumber + 1;

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $qrCode = '';
        for ($i = 0; $i < 8; $i++) {
            $qrCode .= $characters[rand(0, strlen($characters) - 1)];
        }

        Table::create([
            'number' => $newNumber,
            'code'   => $qrCode,
            'status' => 0,
        ]);

        return redirect()->back()->with('success', 'Meja ' . $newNumber . ' berhasil ditambahkan');
    }

    public function updateStatus(Request $request, Table $table)
    {
        $request->validate([
            'status' => 'required|in:0,1,2'
        ]);

        $table->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
        ]);
    }

    public function printAll()
    {
        $qrs = Table::get();

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

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->back()->with('success', 'Meja berhasil dihapus');
    }
}
