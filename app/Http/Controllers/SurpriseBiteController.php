<?php

namespace App\Http\Controllers;

use App\Models\CheckoutOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SurpriseBiteController extends Controller
{
    private function defaultCheckoutState(): array
    {
        return [
            'method' => 'delivery',
            'address' => '',
            'payment' => 'ewallet',
            'order_id' => null,
        ];
    }

    private function getCheckoutState(Request $request, string $slug): array
    {
        $state = $request->session()->get("checkout.$slug", []);

        if (!is_array($state)) {
            $state = [];
        }

        return array_replace($this->defaultCheckoutState(), $state);
    }

    /**
     * Data dummy supaya langsung jadi tanpa database.
     */
    private function catalog(): array
    {
        $categories = [
            ['id' => 'bakery', 'name' => 'Bakery', 'icon' => '🥐'],
            ['id' => 'rice', 'name' => 'Rice Bowl', 'icon' => '🍚'],
            ['id' => 'noodles', 'name' => 'Noodles', 'icon' => '🍜'],
            ['id' => 'salad', 'name' => 'Healthy', 'icon' => '🥗'],
            ['id' => 'drinks', 'name' => 'Drinks', 'icon' => '🧋'],
            ['id' => 'cafe', 'name' => 'Cafe', 'icon' => '☕'],
            ['id' => 'italian', 'name' => 'Italian', 'icon' => '🍕'],
            ['id' => 'japanese', 'name' => 'Japanese', 'icon' => '🍣'],
        ];

        $restaurants = [
            [
                'id' => 'sunrise-bakery',
                'name' => 'Sunrise Bakery',
                'area' => 'Senopati',
                'city' => 'Jakarta',
                'rating' => 4.8,
                'tags' => ['bakery'],
                'subtitle' => 'Premium bakery serving fresh artisan bread and pastries daily. Known for our sourdough and croissants.',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80',
                'boxes_available' => 1,
            ],
            [
                'id' => 'noodle-house',
                'name' => 'Noodle House',
                'area' => 'Kemang',
                'city' => 'Jakarta',
                'rating' => 4.6,
                'tags' => ['noodles', 'rice'],
                'subtitle' => 'Authentic Asian noodle restaurant with variety of soup and dry noodle dishes.',
                'image' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=800&q=80',
                'boxes_available' => 1,
            ],
            [
                'id' => 'urban-coffee',
                'name' => 'Urban Coffee & Bites',
                'area' => 'Kemang',
                'city' => 'Jakarta',
                'rating' => 4.7,
                'tags' => ['drinks', 'cafe'],
                'subtitle' => 'Specialty coffee, pastries, and light bites in a cozy urban setting.',
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=800&q=80',
                'boxes_available' => 1,
            ],
            [
                'id' => 'green-bowl-cafe',
                'name' => 'Green Bowl Cafe',
                'area' => 'Darmo',
                'city' => 'Surabaya',
                'rating' => 4.9,
                'tags' => ['salad', 'drinks'],
                'subtitle' => 'Fresh salads, grain bowls, and cold-pressed juices for a balanced day.',
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&q=80',
                'boxes_available' => 1,
            ],
            [
                'id' => 'sushi-master',
                'name' => 'Sushi Master',
                'area' => 'SCBD',
                'city' => 'Jakarta',
                'rating' => 4.8,
                'tags' => ['rice', 'drinks', 'japanese'],
                'subtitle' => 'Japanese cuisine with premium fish and creative rolls for dinner crowds.',
                'image' => 'https://images.unsplash.com/photo-1579584425555-c3ce17fd4351?w=800&q=80',
                'boxes_available' => 1,
            ],
            [
                'id' => 'pizza-paradise',
                'name' => 'Pizza Paradise',
                'area' => 'Cihampelas',
                'city' => 'Bandung',
                'rating' => 4.5,
                'tags' => ['bakery', 'rice', 'italian'],
                'subtitle' => 'Wood-fired pizzas and Italian comfort food with local twists.',
                'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800&q=80',
                'boxes_available' => 1,
            ],
            [
                'id' => 'warung-sari-rasa',
                'name' => 'Warung Sari Rasa',
                'area' => 'Malioboro',
                'city' => 'Yogyakarta',
                'rating' => 4.4,
                'tags' => ['rice', 'noodles'],
                'subtitle' => 'Home-style Indonesian plates — perfect for mystery rice & side combos.',
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&q=80',
                'boxes_available' => 1,
            ],
        ];

        $boxes = [
            [
                'slug' => 'bakery-surprise-box',
                'title' => 'Bakery Surprise Box',
                'restaurant_id' => 'sunrise-bakery',
                'category' => 'bakery',
                'category_label' => 'Bakery',
                'filter_key' => 'bakery',
                'card_rating' => 4.8,
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80',
                'price' => 25000,
                'original_price' => 80000,
                'pickup_time' => '20:00 - 21:00',
                'badge' => 'Hot',
                'distance_km' => 1.2,
                'stock' => 12,
                'description' =>
                    'Kombinasi roti & pastry yang masih layak konsumsi. Isi bervariasi tiap hari (surprise!).',
                'highlights' => [
                    'Fresh hari ini (sisa produksi)',
                    'Dikemas higienis',
                    'Harga hemat, dampak besar',
                ],
            ],
            [
                'slug' => 'cafe-mystery-box',
                'title' => 'Cafe Mystery Box',
                'restaurant_id' => 'urban-coffee',
                'category' => 'cafe',
                'category_label' => 'Cafe',
                'filter_key' => 'cafe',
                'card_rating' => 4.7,
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=800&q=80',
                'price' => 28000,
                'original_price' => 85000,
                'pickup_time' => '20:30 - 21:30',
                'badge' => 'New',
                'distance_km' => 1.8,
                'stock' => 10,
                'description' =>
                    'Pastry, sandwich mini, dan minuman surprise dari sisa stok layak konsumsi hari ini.',
                'highlights' => ['Perfect for coffee lovers', 'Campuran sweet & savory'],
            ],
            [
                'slug' => 'sushi-mystery-box',
                'title' => 'Sushi Mystery Box',
                'restaurant_id' => 'sushi-master',
                'category' => 'japanese',
                'category_label' => 'Japanese',
                'filter_key' => 'japanese',
                'card_rating' => 4.8,
                'image' => 'https://images.unsplash.com/photo-1579584425555-c3ce17fd4351?w=800&q=80',
                'price' => 40000,
                'original_price' => 120000,
                'pickup_time' => '20:00 - 21:00',
                'badge' => 'Premium',
                'distance_km' => 2.2,
                'stock' => 2,
                'description' =>
                    'Pilihan roll & sashimi sisa layak konsumsi dari dapur — surprise setiap hari.',
                'highlights' => ['Ikan segar', 'Standar higiene tinggi'],
            ],
            [
                'slug' => 'noodle-mystery-box',
                'title' => 'Restaurant Mystery Box',
                'restaurant_id' => 'noodle-house',
                'category' => 'noodles',
                'category_label' => 'Restaurant',
                'filter_key' => 'restaurant',
                'card_rating' => 4.6,
                'image' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=800&q=80',
                'price' => 30000,
                'original_price' => 90000,
                'pickup_time' => '21:00 - 22:00',
                'badge' => 'Value',
                'distance_km' => 2.5,
                'stock' => 3,
                'description' =>
                    'Mie + side menu pilihan chef (makanan sisa layak konsumsi yang tersisa hari itu).',
                'highlights' => ['Porsi kenyang', 'Bumbu khas', 'Dukung pengurangan food waste'],
            ],
            [
                'slug' => 'healthy-green-box',
                'title' => 'Healthy Surprise Box',
                'restaurant_id' => 'green-bowl-cafe',
                'category' => 'salad',
                'category_label' => 'Healthy',
                'filter_key' => 'healthy',
                'card_rating' => 4.9,
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&q=80',
                'price' => 20000,
                'original_price' => 70000,
                'pickup_time' => '19:30 - 20:30',
                'badge' => 'Fresh',
                'distance_km' => 3.1,
                'stock' => 20,
                'description' =>
                    'Salad/healthy bowl yang tersisa dari batch harian, masih segar dan layak konsumsi.',
                'highlights' => ['Segar', 'Topping bervariasi', 'Lebih ramah bumi'],
            ],
            [
                'slug' => 'pizza-surprise-box',
                'title' => 'Pizza Surprise Box',
                'restaurant_id' => 'pizza-paradise',
                'category' => 'italian',
                'category_label' => 'Italian',
                'filter_key' => 'italian',
                'card_rating' => 4.5,
                'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800&q=80',
                'price' => 35000,
                'original_price' => 100000,
                'pickup_time' => '21:00 - 22:00',
                'badge' => 'Hot',
                'distance_km' => 5.2,
                'stock' => 15,
                'description' =>
                    'Slice & side Italian surprise dari oven kayu — sisa layak konsumsi dengan harga hemat.',
                'highlights' => ['Keju melt', 'Crust wood-fired'],
            ],
        ];

        return compact('categories', 'restaurants', 'boxes');
    }

    private function moneyIDR(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    private function findBox(string $slug): array
    {
        $catalog = $this->catalog();

        foreach ($catalog['boxes'] as $box) {
            if ($box['slug'] === $slug) {
                return $box;
            }
        }

        abort(404);
    }

    private function findRestaurant(string $id): array
    {
        $catalog = $this->catalog();

        foreach ($catalog['restaurants'] as $r) {
            if ($r['id'] === $id) {
                return $r;
            }
        }

        abort(404);
    }

    public function home(Request $request): View
    {
        $catalog = $this->catalog();

        $selectedCategory = (string) $request->query('category', '');
        $q = trim((string) $request->query('q', ''));

        $boxes = array_values(array_filter($catalog['boxes'], function (array $box) use ($selectedCategory, $q, $catalog) {
            if ($selectedCategory !== '' && $box['category'] !== $selectedCategory) {
                return false;
            }

            if ($q === '') {
                return true;
            }

            $restaurant = null;
            foreach ($catalog['restaurants'] as $r) {
                if ($r['id'] === $box['restaurant_id']) {
                    $restaurant = $r;
                    break;
                }
            }

            $haystack = strtolower(($box['title'] ?? '') . ' ' . ($restaurant['name'] ?? ''));
            return str_contains($haystack, strtolower($q));
        }));

        return view('surprisebite.home', [
            ...$catalog,
            ...$this->getImpactMetrics(),
            'boxes' => $boxes,
            'catalog_boxes' => $catalog['boxes'],
            'selectedCategory' => $selectedCategory,
            'q' => $q,
            'money' => fn (int $n) => $this->moneyIDR($n),
        ]);
    }

    public function browse(Request $request): View
    {
        $catalog = $this->catalog();

        $filterType = (string) $request->query('ft', 'all');
        $allowedFt = ['all', 'bakery', 'restaurant', 'healthy', 'cafe', 'italian', 'japanese'];
        if (!in_array($filterType, $allowedFt, true)) {
            $filterType = 'all';
        }

        $maxPrice = (int) $request->query('max_price', 50000);
        $maxPrice = max(10000, min(200000, $maxPrice));

        $sort = (string) $request->query('sort', 'nearest');
        if (!in_array($sort, ['nearest', 'price', 'rating'], true)) {
            $sort = 'nearest';
        }

        $boxes = array_values(array_filter($catalog['boxes'], static function (array $box) use ($filterType, $maxPrice): bool {
            if ($filterType !== 'all' && ($box['filter_key'] ?? '') !== $filterType) {
                return false;
            }

            return $box['price'] <= $maxPrice;
        }));

        usort($boxes, static function (array $a, array $b) use ($sort): int {
            return match ($sort) {
                'price' => $a['price'] <=> $b['price'],
                'rating' => $b['card_rating'] <=> $a['card_rating'],
                default => $a['distance_km'] <=> $b['distance_km'],
            };
        });

        $filterLabels = [
            'all' => 'All',
            'bakery' => 'Bakery',
            'restaurant' => 'Restaurant',
            'healthy' => 'Healthy',
            'cafe' => 'Cafe',
            'italian' => 'Italian',
            'japanese' => 'Japanese',
        ];

        return view('surprisebite.browse', [
            ...$catalog,
            'boxes' => $boxes,
            'filterType' => $filterType,
            'maxPrice' => $maxPrice,
            'sort' => $sort,
            'filterLabels' => $filterLabels,
            'money' => fn (int $n) => $this->moneyIDR($n),
        ]);
    }

    public function impact(): View
    {
        return view('surprisebite.impact', $this->getImpactMetrics());
    }

    public function about(): View
    {
        return view('surprisebite.about', $this->getImpactMetrics());
    }

    public function box(string $slug): View
    {
        $box = $this->findBox($slug);
        $restaurant = $this->findRestaurant($box['restaurant_id']);

        return view('surprisebite.box', [
            'box' => $box,
            'restaurant' => $restaurant,
            'money' => fn (int $n) => $this->moneyIDR($n),
        ]);
    }

    public function checkoutDelivery(Request $request, string $slug): View
    {
        $box = $this->findBox($slug);
        $restaurant = $this->findRestaurant($box['restaurant_id']);

        $state = $this->getCheckoutState($request, $slug);

        return view('surprisebite.checkout.delivery', [
            'box' => $box,
            'restaurant' => $restaurant,
            'state' => $state,
            'money' => fn (int $n) => $this->moneyIDR($n),
        ]);
    }

    public function checkoutDeliverySubmit(Request $request, string $slug): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', 'in:pickup,delivery'],
            'address' => ['nullable', 'string', 'max:200'],
        ]);

        if ($validated['method'] === 'delivery' && trim((string) ($validated['address'] ?? '')) === '') {
            return back()->withErrors(['address' => 'Alamat wajib diisi untuk Delivery.'])->withInput();
        }

        $request->session()->put("checkout.$slug.method", $validated['method']);
        $request->session()->put("checkout.$slug.address", (string) ($validated['address'] ?? ''));

        return redirect()->route('checkout.payment', ['slug' => $slug]);
    }

    public function checkoutPayment(Request $request, string $slug): View
    {
        $box = $this->findBox($slug);
        $restaurant = $this->findRestaurant($box['restaurant_id']);

        $state = $this->getCheckoutState($request, $slug);

        return view('surprisebite.checkout.payment', [
            'box' => $box,
            'restaurant' => $restaurant,
            'state' => $state,
            'money' => fn (int $n) => $this->moneyIDR($n),
        ]);
    }

    public function checkoutPay(Request $request, string $slug): RedirectResponse
    {
        $validated = $request->validate([
            'payment' => ['required', 'in:va,cod'],
        ]);

        $box = $this->findBox($slug);
        $restaurant = $this->findRestaurant($box['restaurant_id']);
        $state = $this->getCheckoutState($request, $slug);
        $auth = $request->session()->get('auth', []);

        $orderId = $this->generateUniquePublicOrderId();

        CheckoutOrder::create([
            'public_order_id' => $orderId,
            'customer_id' => (int) ($auth['id'] ?? 0),
            'customer_email' => (string) ($auth['email'] ?? ''),
            'box_slug' => $slug,
            'box_title' => $box['title'],
            'restaurant_name' => $restaurant['name'],
            'amount_idr' => $box['price'],
            'payment_method' => $validated['payment'],
            'fulfillment_method' => $state['method'],
            'delivery_address' => $state['method'] === 'delivery'
                ? (trim((string) ($state['address'] ?? '')) ?: null)
                : null,
            'payment_status' => $validated['payment'] === 'cod' ? 'PENDING_COD' : 'PENDING',
        ]);

        $request->session()->put("checkout.$slug.payment", $validated['payment']);
        $request->session()->put("checkout.$slug.order_id", $orderId);

        if ($validated['payment'] === 'cod') {
            return redirect()->route('checkout.success', ['slug' => $slug]);
        }

        // Redirect ke Midtrans payment
        return redirect()->route('payment.checkout', ['order_id' => $orderId]);
    }

    public function checkoutSuccess(Request $request, string $slug): View
    {
        $box = $this->findBox($slug);
        $restaurant = $this->findRestaurant($box['restaurant_id']);

        $state = $this->getCheckoutState($request, $slug);

        return view('surprisebite.checkout.success', [
            'box' => $box,
            'restaurant' => $restaurant,
            'state' => $state,
            'money' => fn (int $n) => $this->moneyIDR($n),
        ]);
    }

    public function adminDashboard(): View
    {
        $totalCustomers = (int) DB::table('customers')->count();
        $totalTransactions = CheckoutOrder::count();
        $todayStart = now()->startOfDay();
        $ordersToday = CheckoutOrder::where('created_at', '>=', $todayStart)->count();
        $revenueToday = (int) CheckoutOrder::where('created_at', '>=', $todayStart)->sum('amount_idr');

        $recentOrders = CheckoutOrder::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        $paymentLabel = static function (string $method): string {
            return match ($method) {
                'va' => 'Midtrans VA',
                'cod' => 'Bayar di tempat',
                default => strtoupper($method),
            };
        };

        return view('surprisebite.admin.dashboard', [
            'totalCustomers' => $totalCustomers,
            'totalTransactions' => $totalTransactions,
            'ordersToday' => $ordersToday,
            'revenueToday' => $revenueToday,
            'recentOrders' => $recentOrders,
            'money' => fn (int $n) => $this->moneyIDR($n),
            'paymentLabel' => $paymentLabel,
        ]);
    }

    private function generateUniquePublicOrderId(): string
    {
        for ($i = 0; $i < 10; $i++) {
            $id = 'ORD-' . str_pad((string) random_int(0, 999_999), 6, '0', STR_PAD_LEFT);
            if (! CheckoutOrder::where('public_order_id', $id)->exists()) {
                return $id;
            }
        }

        return 'ORD-' . str_replace('.', '', uniqid('', true));
    }

    public function adminImpact(): View
    {
        return view('surprisebite.admin.impact', $this->getImpactMetrics());
    }

    private function getImpactMetrics(): array
    {
        $totalOrders = CheckoutOrder::count();
        $totalCustomers = DB::table('customers')->count();

        $impactMeals = $totalOrders + 1250;
        $impactActiveUsers = $totalCustomers + 850;

        $wasteKg = $impactMeals * 2.2;
        $wasteTons = $wasteKg / 1000;
        
        if ($wasteKg >= 1000) {
            $impactWasteValue = number_format($wasteKg / 1000, 1, '.', '');
            $impactWasteUnit = 'ton';
            $impactWasteDecimals = 1;
        } else {
            $impactWasteValue = number_format($wasteKg, 0, '.', '');
            $impactWasteUnit = 'kg';
            $impactWasteDecimals = 0;
        }
        
        $wasteDisplay = [
            'value' => (float) $impactWasteValue,
            'decimals' => $impactWasteDecimals,
            'unit' => $impactWasteUnit,
        ];
        
        $monthlyTrend = [
            ['m' => 'Jan', 'meals' => 120, 'waste_kg' => 120 * 2.2, 'waste_tons' => (120 * 2.2) / 1000],
            ['m' => 'Feb', 'meals' => 145, 'waste_kg' => 145 * 2.2, 'waste_tons' => (145 * 2.2) / 1000],
            ['m' => 'Mar', 'meals' => 190, 'waste_kg' => 190 * 2.2, 'waste_tons' => (190 * 2.2) / 1000],
            ['m' => 'Apr', 'meals' => 250, 'waste_kg' => 250 * 2.2, 'waste_tons' => (250 * 2.2) / 1000],
            ['m' => 'May', 'meals' => 310, 'waste_kg' => 310 * 2.2, 'waste_tons' => (310 * 2.2) / 1000],
        ];

        return [
            'impactMeals' => $impactMeals,
            'impactWasteValue' => $impactWasteValue,
            'impactWasteDecimals' => $impactWasteDecimals,
            'impactWasteUnit' => $impactWasteUnit,
            'impactActiveUsers' => $impactActiveUsers,
            
            'mealsSaved' => $impactMeals,
            'wasteDisplay' => $wasteDisplay,
            'activeUsers' => $impactActiveUsers,
            'wasteKg' => $wasteKg,
            'wasteTons' => $wasteTons,
            'monthlyTrend' => $monthlyTrend,
            'trendYear' => date('Y'),
        ];
    }
}

