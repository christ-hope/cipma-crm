<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlan;
use App\Services\Academic\PaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminPaymentController extends Controller
{
    public function __construct(private PaymentService $service)
    {
    }

    public function index(Request $request): \Inertia\Response
    {
        $plans = PaymentPlan::with(['student', 'enrollment.class.formation'])
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->when($request->search, fn($q) => $q->whereHas(
                'student',
                fn($q2) =>
                $q2->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('student_number', 'like', "%{$request->search}%")
            ))
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Payments/Index', [
            'plans' => $plans,
            'filters' => $request->only('statut', 'search'),
        ]);
    }

    public function show(string $uuid): \Inertia\Response
    {
        $details = $this->service->getPlanDetails($uuid);
        return Inertia::render('Admin/Payments/Show', $details);
    }

    public function pay(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'payment_plan_id' => 'required|uuid|exists:payment_plans,id',
            'installment_id' => 'nullable|uuid|exists:installments,id',
            'montant' => 'required|numeric|min:1',
            'methode' => 'required|in:cash,bank_transfer,credit_card,mobile_money,check,other',
        ]);

        try {
            $this->service->pay(
                $validated['payment_plan_id'],
                $validated['montant'],
                $validated['methode'],
                $request->user()->id,
                $validated['installment_id'] ?? null,
            );

            return back()->with('success', 'Paiement enregistré avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}