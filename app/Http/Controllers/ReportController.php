<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // últimos 30 dias por padrão
        $startDate = now()->subDays($period);
        
        // Estatísticas Gerais
        $totalUsers = User::where('role', 'student')->count();
        $totalInstructors = User::where('role', 'instructor')->count();
        $activeUsers = User::where('role', 'student')
            ->whereHas('bookings', function($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            })->count();
        
        // Reservas
        $totalBookings = Booking::where('created_at', '>=', $startDate)->count();
        $attendedBookings = Booking::where('status', 'attended')
            ->where('created_at', '>=', $startDate)->count();
        $canceledBookings = Booking::where('status', 'cancelled')
            ->where('created_at', '>=', $startDate)->count();
        $attendanceRate = $totalBookings > 0 ? round(($attendedBookings / $totalBookings) * 100, 2) : 0;
        
                // Revenue metrics
        $totalRevenue = Payment::where('status', 'paid')
            ->where('paid_at', '>=', $startDate)
            ->sum('amount');
        
        $pendingRevenue = Payment::where('status', 'pending')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');
        
        // Reservas por dia (últimos 7 dias)
        $bookingsByDay = Booking::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Top alunos (mais frequentes)
        $topStudents = User::where('role', 'student')
            ->withCount(['bookings' => function($q) use ($startDate) {
                $q->where('status', 'attended')
                  ->where('created_at', '>=', $startDate);
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();
        
        // Horários mais populares
        $popularSchedules = Schedule::withCount(['bookings' => function($q) use ($startDate) {
                $q->whereIn('status', ['booked', 'attended'])
                  ->where('created_at', '>=', $startDate);
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();
        
        return view('reports.index', compact(
            'totalUsers',
            'totalInstructors',
            'activeUsers',
            'totalBookings',
            'attendedBookings',
            'canceledBookings',
            'attendanceRate',
            'totalRevenue',
            'pendingRevenue',
            'bookingsByDay',
            'topStudents',
            'popularSchedules',
            'period'
        ));
    }
}
