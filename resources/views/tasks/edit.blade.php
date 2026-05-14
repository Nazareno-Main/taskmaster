@extends('layouts.app')
@section('title', 'Edit Task')

@section('content')
<div class="page-header">
    <h1>✏ Edit Task</h1>
    <p>Update the details for "{{ $task->title }}".</p>
</div>

<div style="max-width:640px;">
    <div class="card">
        {{-- PUT method spoofing (HTML forms only support GET/POST) --}}
        <form action="{{ route('tasks.update', $task) }}" method="POST" id="editForm" novalidate>
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div class="form-group">
                <label class="form-label" for="title">Task Title <span style="color:var(--red)">*</span></label>
                <input id="title" type="text" name="title" class="form-control"
                       value="{{ old('title', $task->title) }}" maxlength="255">
                @error('title')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                <div class="form-error" id="titleErr"></div>
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label class="form-label" for="description">Description <span style="color:var(--text3);">(optional)</span></label>
                <textarea id="description" name="description" class="form-control"
                          rows="3" maxlength="1000">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Category + Due Date --}}
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="category_id">Subject / Category <span style="color:var(--red)">*</span></label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">— Select Subject —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id', $task->category_id) == $cat->id ? 'selected' : '' }}>
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
                           value="{{ old('due_date', $task->due_date->format('Y-m-d')) }}">
                    @error('due_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div class="form-error" id="dateErr"></div>
                </div>
            </div>

            {{-- Priority + Status --}}
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="priority">Priority <span style="color:var(--red)">*</span></label>
                    <select id="priority" name="priority" class="form-control">
                        <option value="low"    {{ old('priority',$task->priority)=='low'    ?'selected':'' }}>🟢 Low</option>
                        <option value="medium" {{ old('priority',$task->priority)=='medium' ?'selected':'' }}>🟡 Medium</option>
                        <option value="high"   {{ old('priority',$task->priority)=='high'   ?'selected':'' }}>🔴 High</option>
                    </select>
                    @error('priority')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Status <span style="color:var(--red)">*</span></label>
                    <select id="status" name="status" class="form-control">
                        <option value="pending"     {{ old('status',$task->status)=='pending'     ?'selected':'' }}>Pending</option>
                        <option value="in_progress" {{ old('status',$task->status)=='in_progress' ?'selected':'' }}>In Progress</option>
                        <option value="done"        {{ old('status',$task->status)=='done'        ?'selected':'' }}>Done</option>
                    </select>
                    @error('status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Metadata footer --}}
    <p style="font-size:12px;color:var(--text3);margin-top:12px;">
        Created: {{ $task->created_at->format('M d, Y h:i A') }} ·
        Last updated: {{ $task->updated_at->format('M d, Y h:i A') }}
    </p>
</div>
@endsection

@section('scripts')
<script>
/**
 * Client-side validation for the Edit Task form.
 * Same logic as the create form.
 */
document.getElementById('editForm').addEventListener('submit', function(e) {
    let valid = true;
    ['titleErr','catErr','dateErr'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = '';
    });

    const title   = document.getElementById('title').value.trim();
    const cat     = document.getElementById('category_id').value;
    const dueDate = document.getElementById('due_date').value;

    if (!title) {
        document.getElementById('titleErr').textContent = 'Task title is required.';
        valid = false;
    }
    if (!cat) {
        document.getElementById('catErr').textContent = 'Please select a subject.';
        valid = false;
    }
    if (!dueDate) {
        document.getElementById('dateErr').textContent = 'Please set a due date.';
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>
@endsection
