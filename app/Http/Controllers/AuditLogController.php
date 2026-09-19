<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __invoke(Request $request)
    {
        if(!auth()->check() || !auth()->user()->isAdmin()) abort(403, 'Admin only — Audit logs restricted.');
        $q = trim($request->input('q',''));
        $logs = AuditLog::with('user')
            ->when($q, function($qq) use ($q){
                $qq->where(function($w) use ($q){
                    $w->where('action','like',"%{$q}%")
                      ->orWhere('model_type','like',"%{$q}%")
                      ->orWhereHas('user', fn($u)=>$u->where('name','like',"%{$q}%")->orWhere('email','like',"%{$q}%"));
                });
            })
            ->latest()
            ->paginate(20)->withQueryString();
        return view('audit-logs.index', compact('logs','q'));
    }
}
