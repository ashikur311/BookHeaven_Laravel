<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        $error_message = '';
        $success_message = session('success', '');

        $stats = [
            'total_plans'   => 0,
            'total_sales'   => 0,
            'premium_sales' => 0,
            'gold_sales'    => 0,
            'basic_sales'   => 0,
        ];

        $subscription_plans = collect();

        try {
            // Totals
            $stats['total_plans'] = (int) DB::table('subscription_plans')->count();
            $stats['total_sales'] = (int) DB::table('subscription_orders')->count();

            // Sales by plan name
            $sales = DB::table('subscription_plans as sp')
                ->leftJoin('subscription_orders as so', 'sp.plan_id', '=', 'so.plan_id')
                ->select('sp.plan_name', DB::raw('COUNT(so.id) as count'))
                ->groupBy('sp.plan_id', 'sp.plan_name')
                ->get();

            foreach ($sales as $row) {
                if ($row->plan_name === 'Premium') {
                    $stats['premium_sales'] = (int) $row->count;
                } elseif ($row->plan_name === 'Gold') {
                    $stats['gold_sales'] = (int) $row->count;
                } elseif ($row->plan_name === 'Basic') {
                    $stats['basic_sales'] = (int) $row->count;
                }
            }

            // Plans list
            $subscription_plans = DB::table('subscription_plans')
                ->select('plan_id','plan_name','price','validity_days','book_quantity','audiobook_quantity','description','status')
                ->orderByDesc('price')
                ->get();

        } catch (\Throwable $e) {
            $error_message = 'Error fetching subscription data: ' . $e->getMessage();
        }

        return view('admin.subscription', compact(
            'stats', 'subscription_plans', 'error_message', 'success_message'
        ));
    }

      public function edit(int $id)
            {
        $plan = DB::table('subscription_plans')->where('plan_id', $id)->first();
        if (!$plan) {
            return redirect()->route('admin.subscription')->with('error', 'Subscription plan not found!');
        }

        return view('admin.subscription_edit', [
            'plan' => $plan,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'plan_name'          => ['required','string','max:255'],
            'price'              => ['required','numeric','min:0'],
            'validity_days'      => ['required','integer','min:1'],
            'book_quantity'      => ['required','integer','min:0'],
            'audiobook_quantity' => ['required','integer','min:0'],
            'plan_description'   => ['nullable','string'],
            'status'             => ['required','in:active,inactive'],
        ]);

        try {
            DB::table('subscription_plans')
                ->where('plan_id', $id)
                ->update([
                    'plan_name'          => $validated['plan_name'],
                    'price'              => $validated['price'],
                    'validity_days'      => $validated['validity_days'],
                    'book_quantity'      => $validated['book_quantity'],
                    'audiobook_quantity' => $validated['audiobook_quantity'],
                    'description'        => $validated['plan_description'] ?? '',
                    'status'             => $validated['status'],
                ]);

            return redirect()->route('admin.subscription')->with('success', 'Subscription plan updated successfully!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error updating subscription plan: '.$e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required','integer'],
        ]);

        try {
            // If you don’t have ON DELETE CASCADE for orders → plan, clean up safely:
            DB::transaction(function () use ($data) {
                DB::table('subscription_orders')->where('plan_id', $data['plan_id'])->delete();
                DB::table('subscription_plans')->where('plan_id', $data['plan_id'])->delete();
            });

            return redirect()->route('admin.subscription')->with('success', 'Subscription plan deleted successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.subscription')->with('error', 'Error deleting subscription plan: '.$e->getMessage());
        }
    }
}
