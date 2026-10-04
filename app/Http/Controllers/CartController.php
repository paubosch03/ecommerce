<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    // Ver carrito
    public function index()
    {
        $items = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get()
            ->map(fn($item) => [
                'id'       => $item->id,
                'quantity' => $item->quantity,
                'product'  => [
                    'id'    => $item->product->id,
                    'name'  => $item->product->name,
                    'price' => $item->product->price,
                    'image' => $item->product->image,
                    'stock' => $item->product->stock,
                ],
                'subtotal' => $item->quantity * $item->product->price,
            ]);

        $total = $items->sum('subtotal');

        return Inertia::render('Cart/Index', [
            'items' => $items,
            'total' => round($total, 2),
        ]);
    }

    // Añadir producto al carrito
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Comprobar stock
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'No hay suficiente stock.');
        }

        // Si ya existe en el carrito, sumamos cantidad
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            CartItem::create([
                'user_id'    => Auth::id(),
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
            ]);
        }

        return back()->with('success', 'Producto añadido al carrito.');
    }

    // Actualizar cantidad
    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) abort(403);

        $request->validate(['quantity' => 'required|integer|min:1']);

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Carrito actualizado.');
    }

    // Eliminar del carrito
    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) abort(403);

        $cartItem->delete();

        return back()->with('success', 'Producto eliminado del carrito.');
    }
}