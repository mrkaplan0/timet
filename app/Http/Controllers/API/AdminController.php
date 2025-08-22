<?php
// app/Http/Controllers/API/AdminController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeEntry;

class AdminController extends Controller
{
    public function timeReport(Request $request, $year = null, $month = null)
    {
        $query = TimeEntry::query();

        // Year and month parameters from URL
        if ($year) {
            $query->whereYear('start_time', $year);
            
            if ($month) {
                $query->whereMonth('start_time', $month);
            }
        }

        // Total hours overall
        $totalHours = $query->sum('total_hours');

        // User-based report
        $users = $query->selectRaw('user_id, COUNT(id) as entry_count, SUM(total_hours) as total_hours')
                       ->groupBy('user_id')
                       ->with('user:id,name')
                       ->get()
                       ->map(function ($row) {
                           $expectedHours = $row->entry_count * 8;
                           $difference = $row->total_hours - $expectedHours;

                           return [
                               'user_id'        => $row->user_id,
                               'name'           => $row->user->name,
                               'entry_count'    => $row->entry_count,
                               'expected_hours' => $expectedHours,
                               'total_hours'    => $row->total_hours,
                               'difference'     => $difference, // negative => shortage, positive => excess
                           ];
                       });

        $period = $year ? ($year . ($month ? '-' . str_pad($month, 2, '0', STR_PAD_LEFT) : '')) : 'Alle';

        return response()->json([
            'report_period' => $period,
            'total_hours_all_users' => $totalHours,
            'users' => $users,
        ]);
    }

    public function getUserTimeEntries(Request $request, $userId)
    {
        $query = TimeEntry::where('user_id', $userId);

        // Optional date filters
        if ($request->has('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('start_time', '<=', $request->end_date);
        }

        if ($request->has('year')) {
            $query->whereYear('start_time', $request->year);
        }

        if ($request->has('month')) {
            $query->whereMonth('start_time', $request->month);
        }

        // Get entries with user information
        $entries = $query->with('user:id,name,email')
                        ->orderBy('start_time', 'desc')
                        ->get();

        // Calculate summary statistics
        $totalHours = $entries->sum('total_hours');
        $entryCount = $entries->count();
        $expectedHours = $entryCount * 8; // Assuming 8 hours per day
        $difference = $totalHours - $expectedHours;

        return response()->json([
           'user_id' => $userId,
            'user' => $entries->first()?->user,
            'summary' => [
                'total_entries' => $entryCount,
                'total_hours' => $totalHours,
                'expected_hours' => $expectedHours,
                'difference' => $difference,
                'average_hours_per_day' => $entryCount > 0 ? round($totalHours / $entryCount, 2) : 0,
            ],
            'entries' => $entries->map(function ($entry) use ($userId) {
                return [
                    'id' => $entry->id,
                    'start_time' => $entry->start_time,
                    'end_time' => $entry->end_time,
                    'user_id' => $userId,
                    'total_hours' => $entry->total_hours,
                    'note' => $entry->note,
                ];
            }),
        ]);
    }
}
