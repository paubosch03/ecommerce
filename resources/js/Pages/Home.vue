<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { ArrowRight, ShieldCheck, Truck, RefreshCw } from '@lucide/vue';

defineProps({
    featuredProducts: { type: Array, default: () => [] },
    categories:       { type: Array, default: () => [] },
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount);
}
</script>

<template>
    <Head title="Inicio" />
    <ShopLayout>

        <!-- Hero -->
        <section class="bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
                <h1 class="text-5xl font-bold text-gray-900 tracking-tight mb-6">
                    Todo lo que necesitas,<br>
                    <span class="text-indigo-600">en un solo lugar.</span>
                </h1>
                <p class="text-lg text-gray-500 max-w-xl mx-auto mb-10">
                    Descubre nuestra selección de productos con los mejores precios y envío rápido.
                </p>
                <Link
                    :href="route('products.index')"
                    class="inline-flex items-center gap-2 bg-gray-900 text-white px-8 py-4 rounded-2xl text-sm font-medium hover:bg-gray-700 transition-colors"
                >
                    Ver productos
                    <ArrowRight :size="16" />
                </Link>
            </div>
        </section>

        <!-- Categorías -->
        <section v-if="categories.length" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Categorías</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="route('products.index', { category: category.slug })"
                    class="group rounded-2xl border border-gray-100 bg-white p-6 text-center hover:border-indigo-200 hover:shadow-sm transition-all"
                >
                    <div class="text-3xl mb-3">{{ category.icon ?? '📦' }}</div>
                    <p class="text-sm font-medium text-gray-700 group-hover:text-indigo-600 transition-colors">
                        {{ category.name }}
                    </p>
                </Link>
            </div>
        </section>

        <!-- Productos destacados -->
        <section v-if="featuredProducts.length" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Productos destacados</h2>
                <Link :href="route('products.index')" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    Ver todos →
                </Link>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link
                    v-for="product in featuredProducts"
                    :key="product.id"
                    :href="route('products.show', product.slug)"
                    class="group rounded-2xl border border-gray-100 bg-white overflow-hidden hover:shadow-md transition-shadow"
                >
                    <div class="aspect-square bg-gray-50 overflow-hidden">
                        <img
                            v-if="product.image"
                            :src="`/storage/${product.image}`"
                            :alt="product.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center text-4xl">📦</div>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-gray-400 mb-1">{{ product.category?.name }}</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ product.name }}</p>
                        <p class="text-lg font-bold text-gray-900 mt-2">{{ formatCurrency(product.price) }}</p>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Features -->
        <section class="bg-white border-t border-gray-100 mt-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center">
                            <Truck :size="22" class="text-indigo-600" />
                        </div>
                        <p class="text-sm font-semibold text-gray-800">Envío rápido</p>
                        <p class="text-xs text-gray-400">Entrega en 24-48h en península</p>
                    </div>
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                            <ShieldCheck :size="22" class="text-emerald-600" />
                        </div>
                        <p class="text-sm font-semibold text-gray-800">Pago seguro</p>
                        <p class="text-xs text-gray-400">Transacciones cifradas con Stripe</p>
                    </div>
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center">
                            <RefreshCw :size="22" class="text-amber-600" />
                        </div>
                        <p class="text-sm font-semibold text-gray-800">Devoluciones</p>
                        <p class="text-xs text-gray-400">30 días para devolver tu pedido</p>
                    </div>
                </div>
            </div>
        </section>

    </ShopLayout>
</template>