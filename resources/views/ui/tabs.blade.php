@props(['tabs' => []])
<div x-data="{ tab: 0 }" {{ $attributes }}>
    <div role="tablist" class="flex border-b">
        @foreach($tabs as $i => $t)
            <button type="button" :class="tab === {{ $i }} ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600'" class="px-4 py-2 focus:outline-none" @click="tab = {{ $i }}">
                {{ $t['label'] }}
            </button>
        @endforeach
    </div>
    <div class="mt-4">
        @foreach($tabs as $i => $t)
            <div x-show="tab === {{ $i }}" x-cloak>
                {!! $t['content'] ?? '' !!}
            </div>
        @endforeach
    </div>
</div>
