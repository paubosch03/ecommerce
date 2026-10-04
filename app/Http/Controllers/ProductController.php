<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    // Catálogo de productos con filtros
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('active', true);

        // Filtro por categoría
        if ($request->category) {
            $query->whereHas('category', fn($q) =>
                $q->where('slug', $request->category)
            );
        }

        // Filtro por búsqueda
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Filtro por precio
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Ordenar
        $query->orderBy(
            $request->sort_by ?? 'created_at',
            $request->sort_dir ?? 'desc'
        );

        $products   = $query->paginate(12);
        $categories = Category::all();

        return Inertia::render('Products/Index', [
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $request->only([
                'category', 'search', 'min_price', 'max_price', 'sort_by'
            ]),
        ]);
    }

    // Detalle de un producto
    public function show(Product $product)
    {
        $product->load('category');

        // Productos relacionados de la misma categoría
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('active', true)
            ->take(4)
            ->get();

        return Inertia::render('Products/Show', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}