<x-filament-panels::page>
    {{ $this->form }}

    @if($this->activeTab === 'my-templates')
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($this->templates as $template)
                <div class="template-card">
                    <div class="p-4">
                        <img src="{{ $template['thumbnail'] }}"
                             alt="{{ $template['name'] }}"
                             class="template-thumbnail">
                        <div class="template-info">
                            <h3 class="text-lg font-medium text-gray-900">{{ $template['name'] }}</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ $template['description'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="mt-4">
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 text-primary-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ strans('coming_soon') }}</h2>
                <p class="text-gray-500">
                    {{ strans('marketplace_message') }}
                </p>
            </div>
        </div>
    @endif
</x-filament-panels::page>
