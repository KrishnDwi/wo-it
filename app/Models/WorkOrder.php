<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

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
        'staff_id',
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

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

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

                // Ambil nomor urut TERTINGGI yang pernah dipakai bulan ini
                // (bukan menghitung jumlah baris yang tersisa), supaya nomor
                // tidak bentrok setelah ada work order yang dihapus.
                $sequence = DB::transaction(function () use ($monthYear) {
                    $lastNumber = static::where('wo_number', 'like', $monthYear . '%')
                        ->lockForUpdate()
                        ->orderByDesc('wo_number')
                        ->value('wo_number');

                    $lastSequence = $lastNumber ? (int) substr($lastNumber, strlen($monthYear)) : 0;

                    return $lastSequence + 1;
                });

                // Generate wo_number: YYMM followed by sequence (0001, 0002, etc.)
                $model->wo_number = $monthYear . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}