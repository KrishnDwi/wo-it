<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WorkOrder extends Model
{
    protected $table = 'work_order';
    public $timestamps = false;

    protected $fillable = [
        'department',
        'issue_type',
        'description',
        'location',
        'status',
        'image',
        'resolution_note',
        'completed_at',
        'started_at',
        'duration_minutes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'completed_at' => 'datetime',
        'started_at'   => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Set created_at if not already set
            if (!$model->created_at) {
                $model->created_at = Carbon::now();
            }

            if (!$model->wo_number) {
                $now = Carbon::parse($model->created_at);
                $monthYear = $now->format('ym');
                $startOfMonth = $now->copy()->startOfMonth();
                $endOfMonth = $now->copy()->endOfMonth();

                // Count work orders created in current month
                $count = static::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->count();

                // Generate wo_number: YYYYMM followed by sequence (001, 002, etc.)
                $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
                $model->wo_number = $monthYear . $sequence;
            }
        });
    }
}