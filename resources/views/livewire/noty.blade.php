<div class="space-y-3 mx-auto fixed top-cs inset-x-cs z-40">
    @foreach ($notifications as $notification)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000);
        window.addEventListener('notification-added', e => {
            if (e.detail.id === '{{ $notification['id'] }}') {
                show = true;
                setTimeout(() => show = false, 3000);
            }
        });" x-show="show" x-transition
            @click="show = false; $wire.dismiss('{{ $notification['id'] }}')" role="alert"
            class="notification relative flex ml-auto w-full max-w-112 p-2 xs:p-3 text-xs xs:text-sm text-text-success bg-surface-success rounded-sm">
            <svg class="size-5 xs:size-6 shrink-0" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                fill="currentColor" viewBox="0 0 256 256">
                <path
                    d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z">
                </path>
            </svg>
            <div class="flex items-center grow">
                <p class="w-full pl-1 xs:pl-2 pr-2 xs:pr-4">
                    {{ $notification['message'] }}
                </p>
                <button @click="removeNotification(item.id)" type="button" aria-label="Remove notification"
                    class="size-4 xs:size-5 shrink-0"><svg class="text-text-normal size-full"
                        xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        viewBox="0 0 256 256">
                        <path
                            d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z">
                        </path>
                    </svg></button>
            </div>
        </div>
    @endforeach
</div>
