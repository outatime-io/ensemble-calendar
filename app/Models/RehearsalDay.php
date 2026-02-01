<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class RehearsalDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'rehearsal_id',
        'rehearsal_date',
        'starts_at',
        'ends_at',
        'notes',
    ];

    protected $casts = [
        'rehearsal_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $day) {
            calendar_bump_cache_version();
            $day->rehearsal?->refreshDateRangeFromDays();
        });

        static::deleted(function (self $day) {
            calendar_bump_cache_version();
            $day->rehearsal?->refreshDateRangeFromDays();
        });
    }

    public function rehearsal(): BelongsTo
    {
        return $this->belongsTo(Rehearsal::class);
    }

    #[Scope]
    protected function missingTimes(Builder $query): void
    {
        $query->where(function (Builder $query) {
            $query
                ->where(function (Builder $query) {
                    $query
                        ->whereNull('starts_at')
                        ->orWhere('starts_at', '');
                })
                ->orWhere(function (Builder $query) {
                    $query
                        ->whereNull('ends_at')
                        ->orWhere('ends_at', '');
                });
        });
    }

    public function startDateTime(?string $timezone = null): Carbon
    {
        $tz = $timezone ?? $this->rehearsal?->timezone ?? config('app.timezone');

        return Carbon::parse(sprintf('%s %s', $this->rehearsal_date->toDateString(), $this->starts_at), $tz);
    }

    public function endDateTime(?string $timezone = null): Carbon
    {
        $tz = $timezone ?? $this->rehearsal?->timezone ?? config('app.timezone');

        return Carbon::parse(sprintf('%s %s', $this->rehearsal_date->toDateString(), $this->ends_at), $tz);
    }
}
