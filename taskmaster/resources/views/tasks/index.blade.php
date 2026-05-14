@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
        <h1>📋 My Tasks</h1>
        <p>Hello, {{ Auth::user()->name }}! Here's your study planner.</p>
    </div>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">➕ Add Task</a>
</div>

{{-- ── Stat Cards ──────────────────────────────────────────── --}}
<div class="stats-grid">
    <div class="stat-card stat-total">
        <div class="num">{{ $stats['total'] }}</div>
        <div class="lbl">Total Tasks</div>
    </div>
    <div class="stat-card stat-pending">
        <div class="num">{{ $stats['pending'] }}</div>
        <div class="lbl">Pending</div>
    </div>
    <div class="stat-card stat-progress">
        <div class="num">{{ $stats['in_progress'] }}</div>
        <div class="lbl">In Progress</div>
    </div>
    <div class="stat-card stat-done">
        <div class="num">{{ $stats['done'] }}</div>
        <div class="lbl">Done</div>
    </div>
    <div class="stat-card stat-overdue">
        <div class="num">{{ $stats['overdue'] }}</div>
        <div class="lbl">Overdue</div>
    </div>
</div>

{{-- ── Filter Bar ──────────────────────────────────────────── --}}
<form method="GET" action="{{ route('tasks.index') }}" id="filterForm">
    <div class="filter-bar">
        <input type="search" name="search" class="form-control"
               placeholder="🔍 Search tasks…" value="{{ request('search') }}"
               oninput="document.getElementById('filterForm').submit()">

        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="pending"     {{ request('status')=='pending'     ? 'selected':'' }}>Pending</option>
            <option value="in_progress" {{ request('status')=='in_progress' ? 'selected':'' }}>In Progress</option>
            <option value="done"        {{ request('status')=='done'        ? 'selected':'' }}>Done</option>
        </select>

        <select name="priority" class="form-control" onchange="this.form.submit()">
            <option value="">All Priority</option>
            <option value="high"   {{ request('priority')=='high'   ? 'selected':'' }}>🔴 High</option>
            <option value="medium" {{ request('priority')=='medium' ? 'selected':'' }}>🟡 Medium</option>
            <option value="low"    {{ request('priority')=='low'    ? 'selected':'' }}>🟢 Low</option>
        </select>

        <select name="category_id" class="form-control" onchange="this.form.submit()">
            <option value="">All Subjects</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id ? 'selected':'' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>

        @if(request()->hasAny(['status','priority','category_id','search']))
            <a href="{{ route('tasks.index') }}" class="btn btn-ghost btn-sm">✕ Clear</a>
        @endif
    </div>
</form>

{{-- ── Task Table ──────────────────────────────────────────── --}}
<div class="card" style="padding:0;overflow:hidden;">
    @if($tasks->isEmpty())
        <div class="empty-state">
            <div class="icon">📭</div>
            <p>No tasks found. <a href="{{ route('tasks.create') }}" style="color:var(--accent2)">Add your first task!</a></p>
        </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:35%">Task</th>
                    <th>Subject</th>
                    <th>Due Date</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr class="{{ $task->isOverdue() ? 'overdue-row' : '' }}">
                    <td>
                        <div class="task-title {{ $task->status === 'done' ? 'done' : '' }}">
                            {{ $task->title }}
                        </div>
                        @if($task->description)
                            <div class="task-desc">{{ Str::limit($task->description, 60) }}</div>
                        @endif
                        @if($task->isOverdue())
                            <div style="font-size:11px;color:var(--red);margin-top:2px;">⚠ Overdue</div>
                        @endif
                    </td>
                    <td>
                        {{-- Category badge with its stored color --}}
                        <span class="badge" style="background:{{ $task->category->color }}22;color:{{ $task->category->color }}">
                            {{ $task->category->name }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:var(--text2);">
                        {{ $task->due_date->format('M d, Y') }}
                    </td>
                    <td>
                        <span class="badge {{ $task->priorityClass() }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </td>
                    <td>
                        {{-- React Status Toggle Component (mounted per row) --}}
                        <div class="react-toggle"
                             data-task-id="{{ $task->id }}"
                             data-status="{{ $task->status }}"
                             data-toggle-url="{{ route('tasks.toggle', $task) }}"
                             data-csrf="{{ csrf_token() }}">
                        </div>
                    </td>
                    <td style="text-align:right;white-space:nowrap;">
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-ghost btn-sm">✏ Edit</a>

                        {{-- Delete button with JS confirm dialog --}}
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                              style="display:inline;"
                              onsubmit="return confirmDelete(event, '{{ addslashes($task->title) }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">🗑 Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Task count footer --}}
@if(!$tasks->isEmpty())
<p style="font-size:12px;color:var(--text3);margin-top:10px;text-align:right;">
    Showing {{ $tasks->count() }} task(s)
</p>
@endif
@endsection

@section('scripts')
{{-- ════════════════════════════════════════════════════════
     REACT COMPONENT: StatusToggle
     A reusable React component that cycles task status
     without a full page reload, using the toggle API route.
════════════════════════════════════════════════════════ --}}
<script type="text/babel">
const { useState } = React;

/**
 * StatusToggle — React Component
 * Props:
 *   taskId     {number} — the task's database ID
 *   initialStatus {string} — 'pending' | 'in_progress' | 'done'
 *   toggleUrl  {string} — the PATCH endpoint
 *   csrf       {string} — Laravel CSRF token
 */
function StatusToggle({ taskId, initialStatus, toggleUrl, csrf }) {
    const [status, setStatus] = useState(initialStatus);
    const [loading, setLoading] = useState(false);

    // Map status to display config
    const config = {
        pending:     { label: 'Pending',     cls: 'status-pending'  },
        in_progress: { label: 'In Progress', cls: 'status-progress' },
        done:        { label: 'Done',        cls: 'status-done'     },
    };

    const current = config[status] || config.pending;

    /**
     * Sends a PATCH request to the toggle endpoint.
     * On success, updates local state — no page reload needed.
     */
    async function handleToggle() {
        if (loading) return;
        setLoading(true);
        try {
            const res = await fetch(toggleUrl, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();
            setStatus(data.status);
        } catch (err) {
            console.error('Toggle failed:', err);
        } finally {
            setLoading(false);
        }
    }

    return (
        <button
            onClick={handleToggle}
            disabled={loading}
            className={`badge ${current.cls}`}
            title="Click to change status"
            style={{
                cursor: loading ? 'wait' : 'pointer',
                border: 'none',
                fontFamily: "'DM Sans', sans-serif",
                opacity: loading ? 0.6 : 1,
                transition: 'all 0.2s',
            }}
        >
            {loading ? '⟳' : '⟳ '}{current.label}
        </button>
    );
}

// Mount a StatusToggle component into every .react-toggle div in the table
document.querySelectorAll('.react-toggle').forEach(el => {
    const root = ReactDOM.createRoot(el);
    root.render(
        <StatusToggle
            taskId={parseInt(el.dataset.taskId)}
            initialStatus={el.dataset.status}
            toggleUrl={el.dataset.toggleUrl}
            csrf={el.dataset.csrf}
        />
    );
});
</script>

<script>
/**
 * confirmDelete — JavaScript confirmation dialog
 * Shows a native browser dialog before deleting a task.
 * Returns false to cancel form submission if user clicks "Cancel".
 */
function confirmDelete(event, taskTitle) {
    const confirmed = window.confirm(
        `Are you sure you want to delete:\n"${taskTitle}"?\n\nThis action cannot be undone.`
    );
    if (!confirmed) {
        event.preventDefault();
        return false;
    }
    return true;
}
</script>
@endsection
