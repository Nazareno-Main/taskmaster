<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * TaskRequest — Server-Side Validation
 * Validates all input before a task is created or updated.
 * Automatically triggered by type-hinting in the controller.
 */
class TaskRequest extends FormRequest
{
    /**
     * Authorize the request.
     * Returns true since we handle auth via middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for task input fields.
     * These run on both store() and update() in the controller.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Title is required, must be a string, max 255 characters
            'title'       => ['required', 'string', 'max:255'],

            // Description is optional
            'description' => ['nullable', 'string', 'max:1000'],

            // Category must exist in the categories table
            'category_id' => ['required', 'exists:categories,id'],

            // Due date must be a valid date format and not in the past
            'due_date'    => ['required', 'date', 'after_or_equal:today'],

            // Priority must be one of the allowed enum values
            'priority'    => ['required', 'in:low,medium,high'],

            // Status must be one of the allowed enum values
            'status'      => ['required', 'in:pending,in_progress,done'],
        ];
    }

    /**
     * Custom error messages for validation failures.
     */
    public function messages(): array
    {
        return [
            'title.required'       => 'Please enter a task title.',
            'category_id.required' => 'Please select a subject/category.',
            'category_id.exists'   => 'The selected category does not exist.',
            'due_date.required'    => 'Please set a due date.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
            'priority.in'          => 'Priority must be low, medium, or high.',
            'status.in'            => 'Status must be pending, in_progress, or done.',
        ];
    }
}
