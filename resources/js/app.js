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

document.addEventListener("htmx:afterRequest", function (event) {
   const xhr = event.detail.xhr;
   console.log(xhr.status);
   if (xhr.status >= 300 && xhr.status < 400) {
      const location = xhr.getResponseHeader("Location");
      if (location) {
         window.location.href = location;
      }
   }
   if (xhr.status == 200) {
      try {
         const response = JSON.parse(xhr.responseText);
         console.log(response);
         if (response.dataLayer) {
            console.log(response.dataLayer);
            const dataLayer = window.dataLayer || [];
            dataLayer.push(response.dataLayer);
         }
      } catch (e) {
         console.error(e);
      }
   }
});
