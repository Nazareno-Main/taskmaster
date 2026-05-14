<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Category Model
 * Represents a subject/topic group for tasks (e.g., Mathematics, Science).
 * Relationship: A category has many tasks (one-to-many).
 */
class Category extends Model
{
    use HasFactory;

    /**
     * Mass-assignable fields.
     */
    protected $fillable = ['name', 'color'];

    // ─── Relationships ──────────────────────────────────────────────────────

    /**
     * A category has many tasks.
     * tasks.category_id references categories.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
