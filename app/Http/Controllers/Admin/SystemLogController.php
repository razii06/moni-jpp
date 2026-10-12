<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function index()
    {
        $logs = SystemLog::with('user')->latest()->paginate(20);
        return view('admin.system_logs.index', compact('logs'));
    }
}
