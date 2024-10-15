import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';
import mask from '@alpinejs/mask';
import Alpine from 'alpinejs';
import htmx from 'htmx.org';
window.Alpine = Alpine
Alpine.plugin(mask)
Alpine.plugin(focus)
Alpine.plugin(collapse)
Alpine.plugin(intersect)

Alpine.start()
window.htmx = htmx

