<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * TaskController — MVC Controller Layer
 *
 * Handles all business logic for tasks:
 * - index()   → List all tasks (with filtering and search)
 * - create()  → Show the "add task" form
 * - store()   → Save new task to database (validated via TaskRequest)
 * - edit()    → Show the "edit task" form pre-filled with existing data
 * - update()  → Save edits to an existing task
 * - destroy() → Delete a task from the database
 * - toggle()  → Quick status toggle (AJAX-ready)
 *
 * All task operations are scoped to the currently logged-in user.
 */
class TaskController extends Controller
{
    /**
     * Display the task dashboard with optional filters.
     * SQL: SELECT tasks JOIN categories WHERE user_id = ? [AND filters]
     *
     * @param  Request $request  May contain ?status=, ?priority=, ?category_id=, ?search=
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Start a query scoped to the current user (uses JOIN via Eloquent eager load)
        $query = Task::with('category')
            ->where('user_id', Auth::id());

        // Filter by status if provided (e.g. ?status=pending)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority if provided (e.g. ?priority=high)
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by category if provided (e.g. ?category_id=1)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by task title (LIKE query)
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Order by due date ascending (soonest deadline first)
        $tasks = $query->orderBy('due_date', 'asc')->get();

        // Fetch dashboard stats using DB aggregate queries
        $stats = [
            'total'       => Task::where('user_id', Auth::id())->count(),
            'pending'     => Task::where('user_id', Auth::id())->where('status', 'pending')->count(),
            'in_progress' => Task::where('user_id', Auth::id())->where('status', 'in_progress')->count(),
            'done'        => Task::where('user_id', Auth::id())->where('status', 'done')->count(),
            'overdue'     => Task::where('user_id', Auth::id())
                                 ->where('status', '!=', 'done')
                                 ->where('due_date', '<', now()->toDateString())
                                 ->count(),
        ];

        $categories = Category::all();

        return view('tasks.index', compact('tasks', 'stats', 'categories'));
    }

    /**
     * Show the form for creating a new task.
     * Passes all categories to the view for the dropdown select.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        return view('tasks.create', compact('categories'));
    }

    /**
     * Store a newly created task in the database.
     * Input is validated by TaskRequest before reaching this method.
     * SQL: INSERT INTO tasks (user_id, category_id, title, ...) VALUES (...)
     *
     * @param  TaskRequest $request  Validated form data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TaskRequest $request)
    {
        // Create task using validated data; attach to current user
        Task::create([
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'description' => $request->description,
            'due_date'    => $request->due_date,
            'priority'    => $request->priority,
            'status'      => $request->status,
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Task added successfully!');
    }

    /**
     * Show the edit form pre-filled with the task's current data.
     * Verifies the task belongs to the current user (authorization).
     *
     * @param  Task $task  Route model binding — Laravel auto-fetches by ID
     * @return \Illuminate\View\View
     */
    public function edit(Task $task)
    {
        // Prevent users from editing other users' tasks
        abort_if($task->user_id !== Auth::id(), 403, 'Unauthorized');

        $categories = Category::all();
        return view('tasks.edit', compact('task', 'categories'));
    }

    /**
     * Update an existing task in the database.
     * Input validated by TaskRequest; ownership check enforced.
     * SQL: UPDATE tasks SET title=?, ... WHERE id=? AND user_id=?
     *
     * @param  TaskRequest $request  Validated form data
     * @param  Task        $task     The task to update (route model binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TaskRequest $request, Task $task)
    {
        abort_if($task->user_id !== Auth::id(), 403, 'Unauthorized');

        $task->update($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully!');
    }

    /**
     * Delete a task from the database.
     * SQL: DELETE FROM tasks WHERE id=?
     *
     * @param  Task $task  The task to delete
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Task $task)
    {
        abort_if($task->user_id !== Auth::id(), 403, 'Unauthorized');

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted.');
    }

    /**
     * Quick-toggle the status of a task (for the React toggle component).
     * Cycles: pending → in_progress → done → pending
     * SQL: UPDATE tasks SET status=? WHERE id=?
     *
     * @param  Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Task $task)
    {
        abort_if($task->user_id !== Auth::id(), 403, 'Unauthorized');

        // Cycle through statuses
        $next = match ($task->status) {
            'pending'     => 'in_progress',
            'in_progress' => 'done',
            'done'        => 'pending',
            default       => 'pending',
        };

        $task->update(['status' => $next]);

        // Return JSON for the React component to consume
        return response()->json([
            'status' => $next,
            'label'  => $task->statusLabel(),
        ]);
    }
}
