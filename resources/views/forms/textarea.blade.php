<div class="space-y-1">
    <label for="{{ $field->html_id }}" class="block font-medium">{{ $label }}</label>
    <textarea id="{{ $field->html_id }}" wire:model.defer="{{ $model }}" name="{{ $name }}"
        class="w-full border p-2 rounded"></textarea>
</div>
