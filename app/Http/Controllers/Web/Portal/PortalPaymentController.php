<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Services\Academic\PaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortalPaymentController extends Controller
{
    public function __construct(private PaymentService $service) {}

    public function index(Request $request): \Inertia\Response
    {
        $student      = $request->user()->student;
        $paymentPlans = $student->paymentPlans()
            ->with(['installments', 'transactions', 'enrollment.class.formation'])
            ->get();

        return Inertia::render('Portal/Payments/Index', compact('paymentPlans'));
    }

    public function show(string $planId): \Inertia\Response
    {
        $details = $this->service->getPlanDetails($planId);
        return Inertia::render('Portal/Payments/Show', $details);
    }
}