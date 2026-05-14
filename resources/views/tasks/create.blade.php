@extends('layouts.app')
@section('title', 'Add Task')

@section('content')
<div class="page-header">
    <h1>➕ Add New Task</h1>
    <p>Fill in the details for your new assignment or to-do item.</p>
</div>

<div style="max-width:640px;">
    <div class="card">
        <form action="{{ route('tasks.store') }}" method="POST" id="taskForm" novalidate>
            @csrf

            {{-- Title --}}
            <div class="form-group">
                <label class="form-label" for="title">Task Title <span style="color:var(--red)">*</span></label>
                <input id="title" type="text" name="title" class="form-control"
                       placeholder="e.g. Solve Chapter 3 Exercises"
                       value="{{ old('title') }}" maxlength="255">
                @error('title')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                <div class="form-error" id="titleErr"></div>
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label class="form-label" for="description">Description <span style="color:var(--text3);">(optional)</span></label>
                <textarea id="description" name="description" class="form-control"
                          rows="3" placeholder="Additional notes or instructions…"
                          maxlength="1000">{{ old('description') }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Category + Due Date (2-column grid) --}}
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="category_id">Subject / Category <span style="color:var(--red)">*</span></label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">— Select Subject —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div class="form-error" id="catErr"></div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="due_date">Due Date <span style="color:var(--red)">*</span></label>
                    <input id="due_date" type="date" name="due_date" class="form-control"
                           value="{{ old('due_date') }}"
                           min="{{ date('Y-m-d') }}">
                    @error('due_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div class="form-error" id="dateErr"></div>
                </div>
            </div>

            {{-- Priority + Status (2-column grid) --}}
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="priority">Priority <span style="color:var(--red)">*</span></label>
                    <select id="priority" name="priority" class="form-control">
                        <option value="low"    {{ old('priority','medium')=='low'    ? 'selected':'' }}>🟢 Low</option>
                        <option value="medium" {{ old('priority','medium')=='medium' ? 'selected':'' }}>🟡 Medium</option>
                        <option value="high"   {{ old('priority','medium')=='high'   ? 'selected':'' }}>🔴 High</option>
                    </select>
                    @error('priority')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status <span style="color:var(--red)">*</span></label>
                    <select id="status" name="status" class="form-control">
                        <option value="pending"     {{ old('status','pending')=='pending'     ? 'selected':'' }}>Pending</option>
                        <option value="in_progress" {{ old('status','pending')=='in_progress' ? 'selected':'' }}>In Progress</option>
                        <option value="done"        {{ old('status','pending')=='done'        ? 'selected':'' }}>Done</option>
                    </select>
                    @error('status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Submit + Cancel --}}
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">💾 Save Task</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
/**
 * Client-side validation for the Add Task form.
 * Checks required fields before allowing server submission.
 */
document.getElementById('taskForm').addEventListener('submit', function(e) {
    let valid = true;

    // Clear previous client-side errors
    ['titleErr','catErr','dateErr'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = '';
    });

    const title   = document.getElementById('title').value.trim();
    const cat     = document.getElementById('category_id').value;
    const dueDate = document.getElementById('due_date').value;
    const today   = new Date().toISOString().split('T')[0];

    // Validate title
    if (!title) {
        document.getElementById('titleErr').textContent = 'Task title is required.';
        valid = false;
    } else if (title.length > 255) {
        document.getElementById('titleErr').textContent = 'Title must be under 255 characters.';
        valid = false;
    }

    // Validate category
    if (!cat) {
        document.getElementById('catErr').textContent = 'Please select a subject.';
        valid = false;
    }

    // Validate due date (must not be in the past)
    if (!dueDate) {
        document.getElementById('dateErr').textContent = 'Please set a due date.';
        valid = false;
    } else if (dueDate < today) {
        document.getElementById('dateErr').textContent = 'Due date cannot be in the past.';
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>
@endsection
