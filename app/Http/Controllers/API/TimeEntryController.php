<?php

// app/Http/Controllers/API/TimeEntryController.php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\TimeEntry;
use App\Http\Controllers\Controller;

class TimeEntryController extends Controller
{
    public function index(Request $request)
    {
        $entries = TimeEntry::where('user_id', $request->user()->id)
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json(['time_entries' => $entries]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'note'       => 'nullable|string',
        ]);

        $hours = (strtotime($request->end_time) - strtotime($request->start_time)) / 3600;

        $entry = TimeEntry::create([
            'user_id'    => $request->user()->id,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'total_hours'=> $hours,
            'note'       => $request->note,
        ]);

        return response()->json(['message' => 'Time entry added successfully.', 'time_entry' => $entry], 201);
    }

    public function update(Request $request, $id)
    {
        $entry = TimeEntry::where('user_id', $request->user()->id)->findOrFail($id);

        $entry->update($request->only(['start_time', 'end_time', 'note']));

        return response()->json(['message' => 'Time entry updated.', 'time_entry' => $entry]);
    }

    public function destroy(Request $request, $id)
    {
        $entry = TimeEntry::where('user_id', $request->user()->id)->findOrFail($id);

        $entry->delete();

        return response()->json(['message' => 'Time entry deleted.']);
    }
}