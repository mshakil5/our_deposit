<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function downloadPDF()
    {
        $data = Transaction::where('user_id', Auth::user()->id)->where('status', 1)->get();
        $totalAmount = Transaction::where('user_id', Auth::user()->id)->where('status', 1)->sum('amount');
        $totalFine = Transaction::where('user_id', Auth::user()->id)->where('status', 1)->sum('fine');
        $user = Auth::user();
        $pdf = Pdf::loadView('report.depositreport', compact('data','totalAmount','totalFine','user'));
        return $pdf->download('personal-report.pdf');
    }

    public function allmemberReport()
    {
        $data = User::with('transactions')->where('status', 1)->where('is_type', 0)
             ->withSum('transactions as total_fine', 'fine')
             ->withSum('transactions as total_amount', 'amount')
             ->get();
        $pdf = Pdf::loadView('report.allreport', compact('data'));
        return $pdf->download('report.pdf');
    }


}
