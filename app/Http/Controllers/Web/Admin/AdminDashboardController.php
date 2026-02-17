<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Certificate;
use App\Models\PaymentPlan;
use App\Models\Student;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'applications_pending'  => Application::where('status', 'submitted')->count(),
                'students_active'       => Student::where('status', 'active')->count(),
                'payments_overdue'      => PaymentPlan::where('statut', 'overdue')->count(),
                'certificates_issued'   => Certificate::where('statut', 'emis')->count(),
            ],
            'recent_applications' => Application::with('reviewer')
                ->latest()->take(5)->get(),
            'recent_transactions' => Transaction::with([
                    'paymentPlan.student',
                    'paymentPlan.enrollment.class.formation',
                ])
                ->where('statut', 'completed')
                ->latest()->take(5)->get(),
        ]);
    }
}
