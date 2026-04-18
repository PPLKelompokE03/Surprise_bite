<x-layouts.app title="Mitra Dashboard">
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-3xl font-black text-gray-900 mb-8">Dashboard Mitra</h2>

            @if(session('status'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('status') }}
                </div>
            @endif

            <!-- List Restoran -->
            <div class="bg-white shadow rounded-lg mb-8">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                    <h3 class="text-lg font-bold text-gray-900">Restoran Anda</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($restaurants as $restaurant)
                        <div class="border rounded-xl p-5 hover:shadow-lg transition">
                            <h4 class="text-xl font-bold text-gray-900">{{ $restaurant->name }}</h4>
                            <p class="text-sm text-gray-500 mt-2">{{ Str::limit($restaurant->description, 50) }}</p>
                            
                            <div class="mt-6 flex items-center justify-between">
                                <a href="{{ route('mitra.restaurants.unlock.form', $restaurant) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Kelola (Unlock)
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-10 text-center text-gray-500">
                            Belum ada restoran yang dibuat.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Form Buat Restoran Baru -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                    <h3 class="text-lg font-bold text-gray-900">Buat Restoran Baru</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('mitra.restaurants.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Restoran</label>
                                <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PIN Rahasia (Untuk Unlock)</label>
                                <input type="password" name="pin" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Min 4 karakter">
                                @error('pin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-span-full">
                                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-green-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-green-700">
                                Buat Restoran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
