<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);

        // Calculate Totals for Top Cards
        $totalPayable = Supplier::where('current_balance', '>', 0)->sum('current_balance');
        $totalAdvance = Supplier::where('current_balance', '<', 0)->sum('current_balance');

        return view('suppliers.index', compact('suppliers', 'totalPayable', 'totalAdvance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'opening_balance' => 'nullable|numeric'
        ]);

        Supplier::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'current_balance' => $request->opening_balance ?? 0,
            'opening_balance' => $request->opening_balance ?? 0,
        ]);

        return redirect()->back()->with('success', 'Supplier Added Successfully');
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $supplier->update([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'address' => $request->address,
            // Note: We usually DO NOT update balance manually here. 
            // Balance should only change via Purchase/Payment.
        ]);

        return redirect()->back()->with('success', 'Supplier Updated');
    }

    public function destroy($id)
    {
        Supplier::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Supplier Deleted');
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $supplier = Supplier::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'current_balance' => 0,
            'opening_balance' => 0,
        ]);

        return response()->json([
            'success' => true,
            'supplier' => $supplier
        ]);
    }
}
