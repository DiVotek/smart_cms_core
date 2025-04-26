<div class="space-y-1">
    <label for="{{ $field->html_id }}" class="block font-medium">{{ $label }}</label>
    <input id="{{ $field->html_id }}" wire:model.defer="{{ $model }}"
        type="{{$type}}" name="{{ $field->html_name }}" class="w-full border p-2 rounded">
</div>
