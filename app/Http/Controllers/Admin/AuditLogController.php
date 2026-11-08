<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = TicketHistory::with(['user', 'ticket'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q
                ->where('description', 'like', "%{$search}%")
                ->orWhere('action', 'like', "%{$search}%")
                ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
            );
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs    = $query->paginate(25)->withQueryString();
        $actions = TicketHistory::distinct()->pluck('action')->sort()->values();

        return view('admin.audit-log.index', compact('logs', 'actions'));
    }
}
