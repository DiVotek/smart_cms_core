@vite('resources/css/app.css')
@vite('resources/js/app.js')
 <header class="bg-white shadow-lg">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <div class="text-2xl font-semibold text-gray-900">
        <a href="#">SMART CMS</a>
      </div>
      <nav class="hidden md:flex space-x-6">
        <a href="#" target="_blank" class="text-gray-600 hover:text-gray-900">Docs</a>
        <a href="#" class="text-gray-600 hover:text-gray-900">Features</a>
      </nav>
      <div class="md:hidden">
        <button @click="open = !open" class="text-gray-600 hover:text-gray-900 focus:outline-none" x-data="{ open: false }">
          <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
    <nav x-show="open" class="md:hidden bg-white shadow-lg">
      <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900">Docs</a>
        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900">Features</a>
        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900">Pricing</a>
        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900">Contact</a>
      </div>
    </nav>
  </header>
  <section class="bg-white py-20 min-h-screen">
    <div class="container mx-auto px-4 text-center">
      <h1 class="text-5xl font-extrabold text-gray-900 mb-6">Welcome to SMART CMS</h1>
      <p class="text-lg text-gray-600 mb-6">Build and manage your content effortlessly.</p>
      <div class="flex justify-center space-x-4">
        <a href="/admin" class="bg-blue-500 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-blue-600">Get Started</a>
        <a href="#" target="_blank" class="bg-gray-100 text-gray-900 px-6 py-3 rounded-lg text-lg font-semibold hover:bg-gray-200">Learn More</a>
      </div>
    </div>
  </section>
