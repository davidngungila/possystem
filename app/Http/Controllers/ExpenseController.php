<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $shopId = currentShopId();
        $expenses = Expense::when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->when($q, fn($qq)=> $qq->where('category','like',"%{$q}%")->orWhere('description','like',"%{$q}%"))
            ->latest()->paginate(12)->withQueryString();
        $total = Expense::when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->sum('amount');
        return view('expenses.index', compact('expenses','q','total'));
    }

    public function create()
    {
        $categories = ['Rent','Electricity','Water','Internet','Transport','Salary','Fuel','Repairs','Packaging','Other'];
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'expense_date' => 'nullable|date',
        ]);
        $data['expense_date'] = $data['expense_date'] ?? now()->toDateString();
        $data['created_by'] = auth()->id();
        $data['shop_id'] = currentShopId();
        $expense = Expense::create($data);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'create_expense','model_type'=>Expense::class,'model_id'=>$expense->id,'new_values'=>$expense->toArray(),'ip_address'=>$request->ip()]);
        return redirect()->route('expenses.index')->with('success','Expense created: '.$expense->category.' TZS '.number_format($expense->amount,0));
    }

    public function edit($encId)
    {
        $expense = Expense::findOrFail(decIdOrRaw($encId));
        if($expense->shop_id && currentShopId() && $expense->shop_id !== currentShopId()) abort(404);
        $categories = ['Rent','Electricity','Water','Internet','Transport','Salary','Fuel','Repairs','Packaging','Other'];
        return view('expenses.edit', compact('expense','categories'));
    }

    public function update(Request $request, $encId)
    {
        $expense = Expense::findOrFail(decIdOrRaw($encId));
        if($expense->shop_id && currentShopId() && $expense->shop_id !== currentShopId()) abort(404);
        $data = $request->validate([
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'expense_date' => 'nullable|date',
        ]);
        $expense->update($data);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'update_expense','model_type'=>Expense::class,'model_id'=>$expense->id,'ip_address'=>$request->ip()]);
        return redirect()->route('expenses.index')->with('success','Expense updated');
    }

    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $expense = Expense::findOrFail(decIdOrRaw($encId));
        if($expense->shop_id && currentShopId() && $expense->shop_id !== currentShopId()) abort(404);
        $expense->delete();
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'delete_expense','model_type'=>Expense::class,'model_id'=>$expense->id,'ip_address'=>$request->ip()]);
        return redirect()->route('expenses.index')->with('success','Expense deleted');
    }
}

