<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $id = $getId();
        $isDisabled = $isDisabled();
        $statePath = $getStatePath();
    @endphp

    <ul role="list" class="grid gap-8 grid-cols-1 md:grid-cols-3">
        @foreach ($options as $value)
            <li class="overflow-hidden">
                <label class="relative">
                    <input id="{{ $id }}-{{ $value['name'] }}" name="{{ $id }}" type="radio"
                        value="{{ $value['path'] }}" wire:loading.attr="disabled"
                        {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}" class="rb-image" />
                    <span class="img-radio-selected"></span>
                    <div class="img-radio">
                        @if (isset($value['image']))
                            <img src="{{ $value['image'] }}" alt="{{ $value['name'] }}"
                                class="w-8 h-8 cursor-pointer focus:bg-primary-500">
                        @else
                            <div class="w-8 h-8 text-center cursor-pointer focus:bg-primary-500 whitespace-nowrap">
                                {{ $value['name'] }}</div>
                        @endif
                    </div>
                </label>
            </li>
        @endforeach
    </ul>
</x-dynamic-component>

<style>
    input[name="{{ $id }}"]:checked+.img-radio-selected {
        background-color: rgba(var(--primary-500), var(--tw-bg-opacity));
        transform: rotate(0.8648rad);
        width: 110px;
        height: 20px;
        position: absolute;
        top: 15px;
        right: -30px;
        z-index: 99999;
    }

    .rb-image {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .img-radio {
        border: 1px solid #dee2e6;
        max-width: 100%;
        border-radius: 5px;
        cursor: pointer;
        display: block;
        height: auto;
        margin: auto;
        padding: 5px;
        position: relative;
        width: 100%;
    }

    .img-radio:hover img {
        -o-object-position: bottom;
        object-position: bottom;
    }

    .img-radio img {
        -o-object-fit: cover;
        object-fit: cover;
        -o-object-position: top;
        object-position: top;
        transform-origin: 50% 50%;
        transition-duration: .1s;
        transition: all 2s ease;
        width: 100%;
    }

    .overflow-hidden {
        overflow: hidden;
    }
</style>
