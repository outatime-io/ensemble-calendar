<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Rehearsal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location_name',
        'location_address',
        'notes',
        'timezone',
        'start_date',
        'end_date',
        'plan_path',
        'is_published',
        'ics_uid',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $rehearsal) {
            if (empty($rehearsal->ics_uid)) {
                $rehearsal->ics_uid = (string) Str::uuid();
            }
        });

        static::saved(function (self $rehearsal) {
            $rehearsal->refreshDateRangeFromDays();
        });
    }

    public function days(): HasMany
    {
        return $this->hasMany(RehearsalDay::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function refreshDateRangeFromDays(): void
    {
        $first = $this->days()->orderBy('rehearsal_date')->first();
        $last = $this->days()->orderByDesc('rehearsal_date')->first();

        if (! $first || ! $last) {
            return;
        }

        $start = $first->rehearsal_date;
        $end = $last->rehearsal_date;

        if ($this->start_date !== $start || $this->end_date !== $end) {
            $this->forceFill([
                'start_date' => $start,
                'end_date' => $end,
            ])->saveQuietly();
        }
    }

    public function dateRangeLabel(): string
    {
        if (!$this->start_date || !$this->end_date) {
            return '';
        }

        if ($this->start_date->equalTo($this->end_date)) {
            return $this->start_date->translatedFormat('d.m.Y');
        }

        return $this->start_date->translatedFormat('d.m.') . ' – ' . $this->end_date->translatedFormat('d.m.Y');
    }
}
