<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    /** @use HasFactory<\Database\Factories\TimeEntryFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'total_hours',
        'note',
        
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'total_hours' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the time entry.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
