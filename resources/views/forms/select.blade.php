<div class="space-y-1">
    <label for="{{ $field->html_id }}" class="block font-medium">{{ $label }}</label>
    <select id="{{ $name }}" wire:model.defer="{{ $model }}" name="{{ $name }}"
        class="w-full border p-2 rounded">
        <option value="">-- Select --</option>
        @foreach ($options as $val => $label)
            <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
