console.log(await fetch('/api/notifications', {
   method: 'GET',
   headers: {
     // Pass the CSRF token (make sure you have a meta tag in your HTML like <meta name="csrf-token" content="{{ csrf_token() }}">)
     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
     'Accept': 'application/json'
   },
 }))
