<header>
    <div class="max-m:hidden bg-secondary">
        <div class="container flex items-center justify-between py-2 text-xs gap-y-2 gap-x-4">
            <div class="flex flex-wrap items-center justify-center text-black uppercase gap-y-1 gap-x-3">
                @foreach ($phones as $phone)
                    <x-s::link class="link link-dark link-underline">{{ $phone }} </x-s::link>
                @endforeach
            </div>
            <div class="flex flex-wrap items-center uppercase text-main gap-x-2">
                @foreach ($top_links as $link)
                    <x-s::link :href="$link->slug" class="link link-dark link-underline">{{ $link->name }}</x-s::link>
                @endforeach
            </div>
        </div>
    </div>
</header>
{{ $slot }}
<footer>
    ...
</footer>
