<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load('role', 'department');

        return match(true) {
            $user->isAdmin()      => view('dashboard.admin',      compact('user')),
            $user->isManager()    => view('dashboard.manager',    compact('user')),
            $user->isTechnician() => view('dashboard.technician', compact('user')),
            default               => view('dashboard.employee',   compact('user')),
        };
    }
}
