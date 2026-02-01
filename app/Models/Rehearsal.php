<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
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
            calendar_bump_cache_version();
            $rehearsal->refreshDateRangeFromDays();
        });

        static::deleted(function (self $rehearsal) {
            calendar_bump_cache_version();
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

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    #[Scope]
    protected function upcoming(Builder $query): void
    {
        $query->where('end_date', '>=', today());
    }

    #[Scope]
    protected function missingDetails(Builder $query): void
    {
        $query->where(function (Builder $query) {
            $query
                ->where('is_published', false)
                ->orWhere(function (Builder $query) {
                    self::applyMissingString($query, 'location_name');
                })
                ->orWhere(function (Builder $query) {
                    self::applyMissingString($query, 'location_address');
                })
                ->orWhere(function (Builder $query) {
                    self::applyMissingString($query, 'plan_path');
                })
                ->orWhereHas('days', fn (Builder $dayQuery) => $dayQuery->missingTimes());
        });
    }

    private static function applyMissingString(Builder $query, string $column): void
    {
        $query->where(function (Builder $query) use ($column) {
            $query
                ->whereNull($column)
                ->orWhere($column, '');
        });
    }

    public static function upcomingCached(): Collection
    {
        $ttlMinutes = (int) config('calendar.cache_ttl_minutes', 15);

        return Cache::remember(
            calendar_rehearsals_cache_key(),
            now()->addMinutes($ttlMinutes),
            function (): Collection {
                return self::query()
                    ->with(['days' => fn ($query) => $query->orderBy('rehearsal_date')->orderBy('starts_at')])
                    ->published()
                    ->upcoming()
                    ->orderBy('start_date')
                    ->get();
            }
        );
    }

    public static function publishedCached(): Collection
    {
        $ttlMinutes = (int) config('calendar.cache_ttl_minutes', 15);

        return Cache::remember(
            calendar_feed_rehearsals_cache_key(),
            now()->addMinutes($ttlMinutes),
            function (): Collection {
                return self::query()
                    ->with(['days' => fn ($query) => $query->orderBy('rehearsal_date')->orderBy('starts_at')])
                    ->published()
                    ->orderBy('start_date')
                    ->get();
            }
        );
    }

    public static function pastPublishedCached(): Collection
    {
        $ttlMinutes = (int) config('calendar.cache_ttl_minutes', 15);

        return Cache::remember(
            calendar_past_rehearsals_cache_key(),
            now()->addMinutes($ttlMinutes),
            function (): Collection {
                return self::query()
                    ->with(['days' => fn ($query) => $query->orderBy('rehearsal_date')->orderBy('starts_at')])
                    ->published()
                    ->where('end_date', '<', today())
                    ->orderByDesc('start_date')
                    ->get();
            }
        );
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
        if (! $this->start_date || ! $this->end_date) {
            return '';
        }

        if ($this->start_date->equalTo($this->end_date)) {
            return $this->start_date->translatedFormat('d.m.Y');
        }

        return $this->start_date->translatedFormat('d.m.').' – '.$this->end_date->translatedFormat('d.m.Y');
    }

    public function createCopy(): self
    {
        $copy = $this->replicate([
            'plan_path',
            'ics_uid',
        ]);

        $copy->is_published = false;
        $copy->created_by = auth()?->id();

        unset($copy->days_count);

        $copy->save();

        foreach ($this->days as $day) {
            $copy->days()->create([
                'rehearsal_date' => $day->rehearsal_date,
                'starts_at' => $day->starts_at,
                'ends_at' => $day->ends_at,
                'notes' => $day->notes,
            ]);
        }

        return $copy;
    }
}
