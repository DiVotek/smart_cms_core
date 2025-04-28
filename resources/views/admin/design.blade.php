<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $id = $getId();
        $isDisabled = $isDisabled();
        $statePath = $getStatePath();
    @endphp

    <ul role="list" class="grid gap-8 grid-cols-1 md:grid-cols-3" x-data="{
        selected: $wire.entangle('{{ $getStatePath() }}')
    }">
        @foreach ($options as $value)
            <li class="card-radio">
                <label class="card" for="{{ $id }}-{{ $value['name'] }}">
                    <input x-model="selected" id="{{ $id }}-{{ $value['name'] }}" name="{{ $id }}" type="radio"
                        value="{{ $value['path'] }}" wire:loading.attr="disabled"
                        {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
                        class="radio-hidden"
                    >
                    @if (isset($value['image']))
                        <img src="{{ $value['image'] }}"
                        width="284" height="160" alt="FAQ" draggable="false" loading="lazy" class="image" />
                    @else
                     <img src="{{ no_image() }}"
                        width="284" height="160" alt="FAQ" draggable="false" loading="lazy" class="image" />
                    @endif

                    <svg xmlns="http://www.w3.org/2000/svg" x-show="selected === '{{ $value['path'] }}'" class="check-icon" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
{{-- <div class="overlay-buttons">
                            <button type="button" class="btn preview-btn">{{_actions('select')}}</button>
                        </div> --}}
                    <div class="overlay">
                        <span class="label">{{ $value['name'] }}</span>
                    </div>
                </label>
            </li>
        @endforeach
    </ul>
</x-dynamic-component>

<style>
/* --- Core Styles --- */
.card {
    height: 180px;
    position: relative;
    overflow: hidden;
    border-radius: 0.5rem;
    border: 2px solid #e5e7eb;
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    display: block;
}

.radio-hidden {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    user-select: none;
    pointer-events: none;
}

/* --- Icon --- */
.check-icon {
    color: white;
    background-color: #2490D0;
    width: 1.5rem;
    height: 1.5rem;
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    z-index: 20;
    transition: all 0.3s ease-in-out;
    pointer-events: none;
    border-radius: 50%;
}

/* --- Overlay --- */
.overlay {
    position: absolute;
    inset: 0;
    padding: 0.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    background: rgba(17, 24, 39, 0.5);
    backdrop-filter: blur(2px);
    opacity: 1; /* <- DEFAULT VISIBLE */
    z-index: 10;
    transition: all 0.3s ease-in-out;
}

.card:hover .overlay {
    opacity: 0;
}
.card:hover .overlay-buttons {
    display: flex;
}


/* --- Buttons and Label --- */
.label {
    font-size: 1.125rem;
    font-weight: 500;
    text-transform: uppercase;
    color: white;
    text-align: center;
}

.overlay-buttons {
    position: absolute;
    bottom: 1rem;
    left: 0.5rem;
    right: 0.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    display: none;
}

.btn {
    min-width: 6rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease-in-out;
    cursor: pointer;
}

.preview-btn {
    background-color: #2490D0 !important;
    padding: 0.5rem 0.75rem;
    color: white;
}

.preview-btn:hover {
    background-color: #2490D0;
    color: white;
}

.select-btn {
    background-color: #2490D0;
    color: white;
    pointer-events: none;
}

/* --- Radio active border --- */
.radio-hidden:checked + .image + .check-icon + .overlay {
    opacity: 1;
}

.radio-hidden:checked ~ .card {
    border-color: #2490D0;
}

</style>
