<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Task Model
 * Core model of the system. Represents an assignment or to-do item.
 * Belongs to: User (student), Category (subject).
 */
class Task extends Model
{
    use HasFactory;

    /**
     * Mass-assignable fields.
     * All these can be set via Task::create([...]) or $task->update([...]).
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'due_date',
        'priority',
        'status',
    ];

    /**
     * Attribute casting.
     * due_date is automatically parsed as a Carbon date object.
     */
    protected $casts = [
        'due_date' => 'date',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────

    /**
     * A task belongs to one user (student).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A task belongs to one category (subject).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // ─── Helper Methods ─────────────────────────────────────────────────────

    /**
     * Returns a CSS class based on priority level.
     * Used in Blade views for color-coded badges.
     */
    public function priorityClass(): string
    {
        return match ($this->priority) {
            'high'   => 'badge-high',
            'medium' => 'badge-medium',
            'low'    => 'badge-low',
            default  => 'badge-medium',
        };
    }

    /**
     * Returns a CSS class based on task status.
     */
    public function statusClass(): string
    {
        return match ($this->status) {
            'done'        => 'status-done',
            'in_progress' => 'status-progress',
            'pending'     => 'status-pending',
            default       => 'status-pending',
        };
    }

    /**
     * Returns a human-readable status label.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'done'        => 'Done',
            'in_progress' => 'In Progress',
            'pending'     => 'Pending',
            default       => 'Pending',
        };
    }

    /**
     * Checks if the task's due date has passed and it's not done.
     */
    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && $this->status !== 'done';
    }
}
