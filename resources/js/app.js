import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';
import mask from '@alpinejs/mask';
import Alpine from 'alpinejs';
import { dialog, dropdown, tooltip } from './component';
window.Alpine = Alpine
Alpine.plugin(mask)
Alpine.plugin(focus)
Alpine.plugin(collapse)
Alpine.plugin(intersect)

Alpine.data('dialog', dialog);
Alpine.data('dropdown', dropdown);
Alpine.data('tooltip', tooltip);

Alpine.start()
