<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil data statistik milik user yang sedang login
        $stats = [
            'totalProjects' => Project::where('user_id', Auth::id())->count(),
            'pendingProjects' => Project::where('user_id', Auth::id())->where('status', 'pending')->count(),
            'completedProjects' => Project::where('user_id', Auth::id())->where('status', 'completed')->count(),
        ];
        
        // Mengirim data ke view dashboard
        return view('dashboard', $stats);
    }
}
