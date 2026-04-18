<x-layouts.app :title="'Kelola ' . $restaurant->name">
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header Bar -->
            <div class="bg-gray-900 rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="p-6 sm:p-8 flex flex-col sm:flex-row justify-between items-center text-white">
                    <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                        <div class="bg-blue-500/20 p-3 rounded-xl border border-blue-400/30">
                            <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black">{{ $restaurant->name }}</h2>
                            <p class="text-gray-400 text-sm mt-1">Unlocked Mode (Aman)</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('mitra.restaurants.lock', $restaurant) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg text-white bg-red-600 hover:bg-red-700 shadow-sm transition">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Lock & Keluar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stats & Navigation -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <a href="{{ route('restaurants.menus.index', $restaurant) }}" class="bg-white rounded-2xl p-6 shadow hover:shadow-lg transition border border-gray-100 flex items-center justify-between group">
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Menu</p>
                        <p class="text-4xl font-black text-gray-900 mt-2">{{ $menusCount }}</p>
                    </div>
                    <div class="bg-blue-50 text-blue-600 p-4 rounded-full group-hover:scale-110 transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                </a>

                <a href="{{ route('restaurants.orders.index', $restaurant) }}" class="bg-white rounded-2xl p-6 shadow hover:shadow-lg transition border border-gray-100 flex items-center justify-between group">
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Pesanan Masuk</p>
                        <p class="text-4xl font-black text-gray-900 mt-2">{{ $ordersCount }}</p>
                    </div>
                    <div class="bg-green-50 text-green-600 p-4 rounded-full group-hover:scale-110 transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                </a>
            </div>

        </div>
    </div>
</x-layouts.app>
