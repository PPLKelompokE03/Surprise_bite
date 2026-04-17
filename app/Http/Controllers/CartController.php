<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Catalog data (dari SurpriseBiteController)
     */
    private function getCatalogData(): array
    {
        $boxes = [
            'bakery-surprise-box' => [
                'slug' => 'bakery-surprise-box',
                'title' => 'Bakery Surprise Box',
                'restaurant_id' => 'sunrise-bakery',
                'restaurant_name' => 'Sunrise Bakery',
                'price' => 25000,
                'stock' => 12,
            ],
            'cafe-mystery-box' => [
                'slug' => 'cafe-mystery-box',
                'title' => 'Cafe Mystery Box',
                'restaurant_id' => 'urban-coffee',
                'restaurant_name' => 'Urban Coffee & Bites',
                'price' => 28000,
                'stock' => 10,
            ],
            'sushi-mystery-box' => [
                'slug' => 'sushi-mystery-box',
                'title' => 'Sushi Mystery Box',
                'restaurant_id' => 'sushi-master',
                'restaurant_name' => 'Sushi Master',
                'price' => 40000,
                'stock' => 2,
            ],
            'noodle-mystery-box' => [
                'slug' => 'noodle-mystery-box',
                'title' => 'Restaurant Mystery Box',
                'restaurant_id' => 'noodle-house',
                'restaurant_name' => 'Noodle House',
                'price' => 30000,
                'stock' => 3,
            ],
            'healthy-green-box' => [
                'slug' => 'healthy-green-box',
                'title' => 'Healthy Surprise Box',
                'restaurant_id' => 'green-bowl-cafe',
                'restaurant_name' => 'Green Bowl Cafe',
                'price' => 20000,
                'stock' => 20,
            ],
            'pizza-surprise-box' => [
                'slug' => 'pizza-surprise-box',
                'title' => 'Pizza Surprise Box',
                'restaurant_id' => 'pizza-paradise',
                'restaurant_name' => 'Pizza Paradise',
                'price' => 35000,
                'stock' => 15,
            ],
        ];

        return $boxes;
    }

    private function getOrCreateCart(int $customerId): Cart
    {
        return Cart::firstOrCreate(
            ['customer_id' => $customerId],
            ['customer_id' => $customerId]
        );
    }

    public function index(Request $request): View
    {
        $auth = $request->session()->get('auth', []);
        if (!$auth || ($auth['role'] ?? null) === 'admin') {
            return redirect()->route('login');
        }
        
        $customerId = $auth['id'] ?? null;
        if (!$customerId) {
            return redirect()->route('login');
        }
        
        $cart = $this->getOrCreateCart($customerId);
        $cart->load('items');

        return view('cart.index', [
            'cart' => $cart,
            'items' => $cart->items,
            'totalPrice' => $cart->getTotalPrice(),
            'totalQuantity' => $cart->getTotalQuantity(),
            'isEmpty' => $cart->isEmpty(),
            'restaurants' => $cart->getRestaurants(),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $auth = $request->session()->get('auth', []);
        if (!$auth || ($auth['role'] ?? null) === 'admin') {
            return redirect()->route('login');
        }
        
        $customerId = $auth['id'] ?? null;
        if (!$customerId) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'box_slug' => 'required|string',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $catalog = $this->getCatalogData();
        $boxSlug = $validated['box_slug'];
        $quantity = $validated['quantity'];

        if (!isset($catalog[$boxSlug])) {
            return back()->withErrors(['box' => 'Mystery box tidak ditemukan']);
        }

        $boxData = $catalog[$boxSlug];

        if ($quantity > $boxData['stock']) {
            return back()->withErrors(['quantity' => "Quantity melebihi stok tersedia ({$boxData['stock']})"])->withInput();
        }

        $cart = $this->getOrCreateCart($customerId);

        $existingItem = $cart->items()->where('box_slug', $boxSlug)->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if ($newQuantity > $boxData['stock']) {
                return back()->withErrors(['quantity' => "Total quantity melebihi stok ({$boxData['stock']})"]);
            }
            $existingItem->update(['quantity' => $newQuantity]);
            Session::flash('success', "'{$boxData['title']}' berhasil diperbarui");
        } else {
            $cart->items()->create([
                'box_slug' => $boxSlug,
                'box_title' => $boxData['title'],
                'restaurant_name' => $boxData['restaurant_name'],
                'price' => $boxData['price'],
                'quantity' => $quantity,
                'stock_available' => $boxData['stock'],
            ]);
            Session::flash('success', "'{$boxData['title']}' ditambahkan ke keranjang");
        }

        return redirect()->route('cart.index');
    }

    public function update(Request $request, int $itemId): RedirectResponse
    {
        $auth = $request->session()->get('auth', []);
        if (!$auth || ($auth['role'] ?? null) === 'admin') {
            return redirect()->route('login');
        }
        
        $customerId = $auth['id'] ?? null;
        if (!$customerId) {
            return redirect()->route('login');
        }

        $cart = $this->getOrCreateCart($customerId);

        $item = CartItem::where('id', $itemId)
            ->where('cart_id', $cart->id)
            ->first();

        if (!$item) {
            return back()->withErrors(['item' => 'Item tidak ditemukan']);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        $newQuantity = (int)$validated['quantity'];

        if ($newQuantity === 0) {
            $itemTitle = $item->box_title;
            $item->delete();
            Session::flash('success', "'{$itemTitle}' dihapus dari keranjang");
            return redirect()->route('cart.index');
        }

        if ($newQuantity > $item->stock_available) {
            return back()->withErrors(['quantity' => "Quantity tidak boleh melebihi stok ({$item->stock_available})"]);
        }

        $item->update(['quantity' => $newQuantity]);
        Session::flash('success', "Quantity diperbarui");

        return redirect()->route('cart.index');
    }

    public function remove(int $itemId): RedirectResponse
    {
        $request = request();
        $auth = $request->session()->get('auth', []);
        if (!$auth || ($auth['role'] ?? null) === 'admin') {
            return redirect()->route('login');
        }
        
        $customerId = $auth['id'] ?? null;
        if (!$customerId) {
            return redirect()->route('login');
        }

        $cart = $this->getOrCreateCart($customerId);

        $item = CartItem::where('id', $itemId)
            ->where('cart_id', $cart->id)
            ->first();

        if (!$item) {
            return back()->withErrors(['item' => 'Item tidak ditemukan']);
        }

        $itemTitle = $item->box_title;
        $item->delete();
        Session::flash('success', "'{$itemTitle}' dihapus dari keranjang");

        return redirect()->route('cart.index');
    }

    public function clear(): RedirectResponse
    {
        $request = request();
        $auth = $request->session()->get('auth', []);
        if (!$auth || ($auth['role'] ?? null) === 'admin') {
            return redirect()->route('login');
        }
        
        $customerId = $auth['id'] ?? null;
        if (!$customerId) {
            return redirect()->route('login');
        }

        $cart = $this->getOrCreateCart($customerId);
        $cart->clear();

        Session::flash('success', 'Keranjang telah dikosongkan');
        return redirect()->route('cart.index');
    }

    public function getCartData(Request $request)
    {
        $auth = $request->session()->get('auth', []);
        if (!$auth || ($auth['role'] ?? null) === 'admin') {
            return response()->json(['success' => false], 401);
        }
        
        $customerId = $auth['id'] ?? null;
        if (!$customerId) {
            return response()->json(['success' => false], 401);
        }

        $cart = $this->getOrCreateCart($customerId);

        return response()->json([
            'success' => true,
            'itemCount' => $cart->getTotalQuantity(),
            'totalPrice' => $cart->getTotalPrice(),
        ]);
    }

    public function validateForCheckout(Request $request)
    {
        $auth = $request->session()->get('auth', []);
        if (!$auth || ($auth['role'] ?? null) === 'admin') {
            return response()->json(['valid' => false, 'errors' => ['Unauthorized']], 401);
        }
        
        $customerId = $auth['id'] ?? null;
        if (!$customerId) {
            return response()->json(['valid' => false, 'errors' => ['Unauthorized']], 401);
        }

        $cart = $this->getOrCreateCart($customerId);
        $catalog = $this->getCatalogData();
        $errors = [];

        if ($cart->isEmpty()) {
            $errors[] = 'Keranjang kosong';
        }

        foreach ($cart->items as $item) {
            if (!isset($catalog[$item->box_slug])) {
                $errors[] = "'{$item->box_title}' tidak ada di katalog";
            } elseif ($item->quantity > $catalog[$item->box_slug]['stock']) {
                $errors[] = "'{$item->box_title}' stok tidak cukup";
            }
        }

        if (count($cart->getRestaurants()) > 1) {
            $errors[] = 'Cart memiliki item dari multiple restaurants (tidak diizinkan)';
        }

        return response()->json([
            'valid' => empty($errors),
            'errors' => $errors,
        ]);
    }
}
