<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemLog::with('user')->latest();

        // 1. Pencarian Kata Kunci (Keyword Search)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter Kategori Aktivitas (What changed?)
        if ($request->filled('category') && $request->category !== 'semua') {
            $cat = $request->category;
            if ($cat === 'job_package') {
                $query->where(function ($q) {
                    $q->where('action', 'like', '%JOB PACKAGE%')
                      ->where('action', 'not like', '%DOCUMENT%');
                });
            } elseif ($cat === 'document') {
                $query->where(function ($q) {
                    $q->where('action', 'like', '%DOCUMENT%')
                      ->orWhere('action', 'like', '%UPLOAD%');
                });
            } elseif ($cat === 'auth') {
                $query->where(function ($q) {
                    $q->where('action', 'like', '%LOGIN%')
                      ->orWhere('action', 'like', '%LOGOUT%');
                });
            } elseif ($cat === 'security') {
                $query->where(function ($q) {
                    $q->where('action', 'like', '%2FA%')
                      ->orWhere('action', 'like', '%PASSWORD%')
                      ->orWhere('action', 'like', '%PROFILE%');
                });
            } elseif ($cat === 'user_management') {
                $query->where('action', 'like', '%USER%');
            } elseif ($cat === 'documentation') {
                $query->where('action', 'like', '%DOKUMENTASI%');
            }
        }

        // 3. Filter Pengguna (Performed by)
        if ($request->filled('user_id') && $request->user_id !== 'semua') {
            $query->where('user_id', $request->user_id);
        }

        // 4. Filter Rentang Waktu (Date Range)
        if ($request->filled('date_range') && $request->date_range !== 'semua') {
            $range = $request->date_range;
            if ($range === 'today') {
                $query->whereDate('created_at', now()->today());
            } elseif ($range === 'yesterday') {
                $query->whereDate('created_at', now()->yesterday());
            } elseif ($range === '7_days') {
                $query->where('created_at', '>=', now()->subDays(7)->startOfDay());
            } elseif ($range === '30_days') {
                $query->where('created_at', '>=', now()->subDays(30)->startOfDay());
            } elseif ($range === 'this_month') {
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            }
        } elseif ($request->filled('date_from') || $request->filled('date_to')) {
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name', 'email', 'role']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('admin.system_logs._table', compact('logs'))->render(),
                'timestamp' => now()->format('H:i:s'),
                'total' => $logs->total(),
            ]);
        }

        return view('admin.system_logs.index', compact('logs', 'users'));
    }
}
