<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return $this->adminDashboard();
        } elseif ($user->role === 'instrutor') {
            return $this->instructorDashboard();
        } else {
            return $this->studentDashboard();
        }
    }

    protected function adminDashboard()
    {
        try {
            // Estatísticas gerais
            $stats = [
                'total_students' => User::where('role', 'aluno')->count(),
                'total_instructors' => User::where('role', 'instrutor')->count(),
                'bookings_today' => Booking::join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
                    ->whereDate('schedules.starts_at', today())
                    ->count(),
                'revenue_month' => Payment::whereMonth('created_at', now()->month)
                    ->where('status', 'paid')
                    ->sum('amount') ?? 0,
            ];

            // Reservas por dia (últimos 7 dias) - via schedules
            $bookingsByDay = Booking::join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
                ->select(
                    DB::raw('DATE(schedules.starts_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('schedules.starts_at', '>=', now()->subDays(7))
                ->groupBy(DB::raw('DATE(schedules.starts_at)'))
                ->orderBy('date')
                ->get();

            // Receita por mês (últimos 6 meses)
            $revenueByMonth = Payment::select(
                    DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                    DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy(DB::raw('EXTRACT(YEAR FROM created_at)'), DB::raw('EXTRACT(MONTH FROM created_at)'))
                ->orderBy(DB::raw('EXTRACT(YEAR FROM created_at)'))
                ->orderBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
                ->get();

            // Planos mais populares
            $popularPlans = Payment::select('plan_id', DB::raw('COUNT(*) as count'))
                ->where('status', 'paid')
                ->whereNotNull('plan_id')
                ->groupBy('plan_id')
                ->with('plan')
                ->orderByDesc('count')
                ->take(5)
                ->get();

            // Taxa de ocupação por horário
            $occupancyRate = Booking::join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
                ->select(
                    'bookings.schedule_id',
                    DB::raw('COUNT(*) as bookings')
                )
                ->where('schedules.starts_at', '>=', now()->startOfMonth())
                ->whereNotIn('bookings.status', ['canceled'])
                ->groupBy('bookings.schedule_id')
                ->with('schedule')
                ->get();

            return view('dashboard', compact(
                'stats',
                'bookingsByDay',
                'revenueByMonth',
                'popularPlans',
                'occupancyRate'
            ));
        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            
            // Retornar dashboard com dados vazios
            $stats = [
                'total_students' => 0,
                'total_instructors' => 0,
                'bookings_today' => 0,
                'revenue_month' => 0,
            ];
            $bookingsByDay = collect([]);
            $revenueByMonth = collect([]);
            $popularPlans = collect([]);
            $occupancyRate = collect([]);
            
            return view('dashboard', compact(
                'stats',
                'bookingsByDay',
                'revenueByMonth',
                'popularPlans',
                'occupancyRate'
            ));
        }
    }

    protected function instructorDashboard()
    {
        $stats = [
            'classes_today' => Booking::join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
                ->whereDate('schedules.starts_at', today())
                ->count(),
            'classes_week' => Booking::join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
                ->whereBetween('schedules.starts_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->count(),
            'total_students' => User::where('role', 'student')->count(),
        ];

        $upcomingClasses = Booking::with(['user', 'schedule'])
            ->whereHas('schedule', function($query) {
                $query->where('starts_at', '>=', now());
            })
            ->whereIn('status', ['booked', 'attended'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'upcomingClasses'));
    }

    protected function studentDashboard()
    {
        $user = auth()->user();

        $stats = [
            'active_plan' => $user->activePlan,
            'bookings_total' => $user->bookings()->count(),
            'bookings_upcoming' => $user->bookings()
                ->whereHas('schedule', function($query) {
                    $query->where('starts_at', '>=', now());
                })
                ->whereIn('status', ['booked', 'attended'])
                ->count(),
        ];

        $upcomingBookings = $user->bookings()
            ->with('schedule')
            ->whereHas('schedule', function($query) {
                $query->where('starts_at', '>=', now());
            })
            ->whereIn('status', ['booked', 'attended'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentPayments = $user->payments()
            ->with('plan')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'upcomingBookings', 'recentPayments'));
    }

    public function stats()
    {
        $bookingsByDay = Booking::join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
            ->select(
                DB::raw('DATE(schedules.starts_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('schedules.starts_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(schedules.starts_at)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => \Carbon\Carbon::parse($item->date)->format('d/m'),
                    'count' => $item->count,
                ];
            });

        $revenueByMonth = Payment::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => sprintf('%02d/%d', $item->month, $item->year),
                    'total' => (float) $item->total,
                ];
            });

        return response()->json([
            'bookingsByDay' => $bookingsByDay,
            'revenueByMonth' => $revenueByMonth,
        ]);
    }
}
