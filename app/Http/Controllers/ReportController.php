<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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


    public function exportMonthlyDepositReport2()
    {
        // Same data preparation as above
        $users = User::where('is_type', '0')->where('status', 1)->orderby('id','DESC')->get();

        $deposits = Transaction::select(
            DB::raw('DATE_FORMAT(STR_TO_DATE(date, "%Y-%m-%d"), "%Y-%m") as month'),
            'user_id',
            DB::raw('SUM(amount) as total_amount')
        )
            ->groupBy('month', 'user_id')
            ->get()
            ->groupBy('month');

        $months = $deposits->keys()->sort()->values();
        $report = [];
        $columnSums = [];
        $rowSums = [];

        foreach ($users as $user) {
            $rowSums[$user->id] = 0;
        }

        foreach ($months as $month) {
            $columnSums[$month] = 0;
            $report[$month] = [];

            foreach ($users as $user) {
                $deposit = $deposits[$month]->firstWhere('user_id', $user->id);
                $amount = $deposit ? $deposit->total_amount : 0;

                $report[$month][$user->id] = [
                    'user_name' => $user->name,
                    'deposited' => $deposit ? true : false,
                    'amount' => $amount,
                ];

                $columnSums[$month] += $amount;
                $rowSums[$user->id] += $amount;
            }
        }

        // Load the PDF view
        $pdf = Pdf::loadView('report.monthly-report', compact('report', 'users', 'months', 'columnSums', 'rowSums'))
            ->setPaper('a4', 'landscape');

        // Download the PDF
        return $pdf->download('monthly_deposit_report.pdf');
    }

    public function exportMonthlyDepositReport()
    {
        // Same data preparation as above
        $users = User::where('is_type', '0')->where('status', 1)->orderby('id','DESC')->get();
        $deposits = Transaction::select(
            DB::raw('YEAR(STR_TO_DATE(date, "%Y-%m-%d")) as year'),
            DB::raw('MONTH(STR_TO_DATE(date, "%Y-%m-%d")) as month'),
            'user_id',
            DB::raw('SUM(amount) as total_amount')
        )
            ->groupBy('year', 'month', 'user_id')
            ->get()
            ->groupBy('year');

        $report = [];
        $columnSums = [];
        $rowSums = [];
        $years = $deposits->keys()->sort()->values();

        foreach ($years as $year) {
            $report[$year] = [];
            $columnSums[$year] = array_fill(1, 12, 0);
            $rowSums[$year] = [];

            foreach ($users as $user) {
                $rowSums[$year][$user->id] = 0;
            }

            for ($month = 1; $month <= 12; $month++) {
                $report[$year][$month] = [];
                foreach ($users as $user) {
                    $deposit = $deposits[$year]->where('month', $month)->firstWhere('user_id', $user->id);
                    $amount = $deposit ? $deposit->total_amount : 0;

                    $report[$year][$month][$user->id] = [
                        'user_name' => $user->name,
                        'deposited' => $deposit ? true : false,
                        'amount' => $amount,
                    ];

                    $columnSums[$year][$month] += $amount;
                    $rowSums[$year][$user->id] += $amount;
                }
            }
        }

        // Load the PDF view
        $pdf = Pdf::loadView('report.monthly-report', compact('report', 'users', 'years', 'columnSums', 'rowSums'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

        // Download the PDF
        return $pdf->download('monthly_deposit_report.pdf');
    }


}
