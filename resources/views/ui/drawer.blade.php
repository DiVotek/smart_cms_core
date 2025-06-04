@props(['open' => false, 'maxWidth' => 'max-w-md', 'title' => null])
<div x-data="drawer(@js($open))" x-init="init" x-cloak {{ $attributes }}>
    <div x-show="open" class="fixed inset-0 z-50 flex items-end justify-center">
        <div class="absolute inset-0 bg-black/50" @click="close" aria-hidden="true"></div>
        <div
            x-show="open"
            x-ref="panel"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="w-full bg-white shadow-xl rounded-t-lg"
            :class="maxWidth"
            role="dialog"
            aria-modal="true"
            @touchstart="start"
            @touchmove="move"
            @touchend="end"
        >
            @if($title)
                <div class="flex items-center justify-between p-4 border-b">
                    <h2 id="drawer-title" class="text-lg font-semibold">{{ $title }}</h2>
                    <button @click="close" class="text-gray-600 hover:text-gray-800">&times;</button>
                </div>
            @endif
            <div class="p-4 max-h-[75vh] overflow-y-auto">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
<script>
    function drawer(initialOpen) {
        return {
            open: initialOpen,
            startY: 0,
            currentY: 0,
            maxWidth: '{{ $maxWidth }}',
            init() {
                window.addEventListener('toggle-drawer', () => this.toggle());
            },
            toggle() { this.open = !this.open },
            close() { this.open = false },
            start(e) {
                this.startY = e.touches[0].clientY;
            },
            move(e) {
                this.currentY = e.touches[0].clientY;
                const delta = this.currentY - this.startY;
                if (delta > 0) {
                    this.$refs.panel.style.transform = `translateY(${delta}px)`;
                }
            },
            end() {
                const delta = this.currentY - this.startY;
                if (delta > 100) {
                    this.close();
                }
                this.$refs.panel.style.transform = '';
            }
        }
    }
</script>

