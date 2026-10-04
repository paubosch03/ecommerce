<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    // Página de checkout
    public function create()
    {
        $items = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $total = $items->sum(fn($i) => $i->quantity * $i->product->price);

        return Inertia::render('Checkout/Index', [
            'items' => $items->map(fn($i) => [
                'name'     => $i->product->name,
                'price'    => $i->product->price,
                'quantity' => $i->quantity,
                'subtotal' => $i->quantity * $i->product->price,
            ]),
            'total'           => round($total, 2),
            'stripePublicKey' => config('services.stripe.key'),
        ]);
    }

    // Procesar pedido tras pago
    public function store(Request $request)
    {
        $request->validate([
            'shipping_name'    => 'required|string',
            'shipping_email'   => 'required|email',
            'shipping_address' => 'required|string',
            'shipping_city'    => 'required|string',
            'shipping_zip'     => 'required|string',
            'stripe_payment_id' => 'required|string',
        ]);

        $items = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();

        $total = $items->sum(fn($i) => $i->quantity * $i->product->price);

        // Crear pedido
        $order = Order::create([
            'user_id'           => Auth::id(),
            'status'            => 'paid',
            'total'             => round($total, 2),
            'stripe_payment_id' => $request->stripe_payment_id,
            'shipping_name'     => $request->shipping_name,
            'shipping_email'    => $request->shipping_email,
            'shipping_address'  => $request->shipping_address,
            'shipping_city'     => $request->shipping_city,
            'shipping_zip'      => $request->shipping_zip,
        ]);

        // Crear líneas del pedido y reducir stock
        foreach ($items as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $item->product->price,
            ]);

            $item->product->decrement('stock', $item->quantity);
        }

        // Vaciar carrito
        CartItem::where('user_id', Auth::id())->delete();

        return redirect()->route('orders.show', $order->id)
            ->with('success', '¡Pedido realizado correctamente!');
    }

    // Historial de pedidos
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    // Detalle de un pedido
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        $order->load('items.product');

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }
}