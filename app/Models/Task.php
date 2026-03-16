<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    public const PRIORITY_LABELS = [
        0 => 'Highest',
        1 => 'High',
        2 => 'Medium',
        3 => 'Low',
    ];

    protected $fillable = [
        'user_id',
        'assigned_to_user_id',
        'folder_id',
        'title',
        'description',
        'priority',
        'is_completed',
        'completed_at',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('priority')->orderByDesc('created_at');
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('due_date', today());
    }

    public function scopeImportant(Builder $query): Builder
    {
        return $query->where('priority', 0);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('is_completed', true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_completed', false);
    }

    public static function priorityOptions(): array
    {
        return collect(self::PRIORITY_LABELS)
            ->map(fn (string $label, int $value) => [
                'value' => $value,
                'label' => sprintf('%d - %s', $value, $label),
                'short' => sprintf('P%d', $value),
                'name' => $label,
            ])
            ->all();
    }

    public function priorityName(): string
    {
        return self::PRIORITY_LABELS[$this->priority] ?? 'Unknown';
    }

    public function priorityLabel(): string
    {
        return sprintf('%d - %s', $this->priority, $this->priorityName());
    }
}
