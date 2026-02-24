@php
    $isEdit = isset($student);
@endphp

@if ($errors->any())
    <div class="error-box">
        <strong>Please fix the errors below.</strong>
    </div>
@endif

<div class="grid">
    <div>
        <label for="name">Name</label>
        <input id="name" name="name" type="text" required value="{{ old('name', $student->name ?? '') }}" />
        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="{{ old('email', $student->email ?? '') }}" />
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label for="phone">Phone</label>
        <input id="phone" name="phone" type="text" required value="{{ old('phone', $student->phone ?? '') }}" />
        @error('phone')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label for="dob">Date of Birth</label>
        <input id="dob" name="dob" type="date" required value="{{ old('dob', $student->dob ?? '') }}" />
        @error('dob')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>
    <div class="span-2">
        <label for="address">Address</label>
        <textarea id="address" name="address" required>{{ old('address', $student->address ?? '') }}</textarea>
        @error('address')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="actions">
    <a class="ghost" href="{{ route('student.index') }}">Cancel</a>
    <button class="button" type="submit">{{ $isEdit ? 'Update Student' : 'Save Student' }}</button>
</div>
