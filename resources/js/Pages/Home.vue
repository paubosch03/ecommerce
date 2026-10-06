<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { ArrowRight, Truck, ShieldCheck, RefreshCw } from '@lucide/vue';

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
        <section class="relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="py-32 md:py-48 text-center">

                    <!-- Pill badge -->
                    <div class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-4 py-1.5 mb-8">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-medium text-gray-600 tracking-wide">Nuevos productos disponibles</span>
                    </div>

                    <h1 class="text-5xl md:text-7xl font-semibold text-gray-900 tracking-tight leading-none mb-6">
                        Diseñado para<br>
                        <span class="text-gray-400">tu estilo de vida.</span>
                    </h1>

                    <p class="text-base md:text-lg text-gray-400 font-light max-w-lg mx-auto mb-12 leading-relaxed">
                        Productos seleccionados con atención al detalle.<br>
                        Envío rápido. Devoluciones sin complicaciones.
                    </p>

                    <div class="flex items-center justify-center gap-4">
                        <Link :href="route('products.index')"
                            class="inline-flex items-center gap-2 bg-gray-900 text-white text-sm font-medium px-8 py-3.5 rounded-full hover:bg-gray-700 transition-all duration-200">
                            Ver colección
                            <ArrowRight :size="14" />
                        </Link>
                        <Link :href="route('products.index')"
                            class="inline-flex items-center gap-2 text-gray-900 text-sm font-medium px-8 py-3.5 rounded-full border border-gray-200 hover:border-gray-400 transition-all duration-200">
                            Explorar
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Línea decorativa -->
            <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
        </section>

        <!-- Categorías -->
        <section v-if="categories.length" class="py-24">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-end justify-between mb-12">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Explorar</p>
                        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Categorías</h2>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    <Link
                        v-for="category in categories"
                        :key="category.id"
                        :href="route('products.index', { category: category.slug })"
                        class="group relative rounded-2xl bg-gray-50 p-6 hover:bg-gray-900 transition-all duration-300 cursor-pointer"
                    >
                        <div class="text-2xl mb-4">{{ category.icon ?? '◆' }}</div>
                        <p class="text-sm font-medium text-gray-700 group-hover:text-white transition-colors">
                            {{ category.name }}
                        </p>
                        <p class="text-xs text-gray-400 group-hover:text-gray-400 mt-1 transition-colors">
                            {{ category.products_count ?? 0 }} productos
                        </p>
                        <ArrowRight
                            :size="14"
                            class="absolute bottom-5 right-5 text-gray-300 group-hover:text-white transition-colors"
                        />
                    </Link>
                </div>
            </div>
        </section>

        <!-- Productos destacados -->
        <section v-if="featuredProducts.length" class="py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-end justify-between mb-12">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Selección</p>
                        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Destacados</h2>
                    </div>
                    <Link :href="route('products.index')"
                        class="text-xs font-medium text-gray-500 hover:text-gray-900 transition-colors flex items-center gap-1">
                        Ver todos <ArrowRight :size="12" />
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <Link
                        v-for="product in featuredProducts"
                        :key="product.id"
                        :href="route('products.show', product.slug)"
                        class="group cursor-pointer"
                    >
                        <!-- Imagen -->
                        <div class="aspect-square rounded-2xl bg-white overflow-hidden mb-4 border border-gray-100 group-hover:border-gray-200 transition-colors">
                            <img
                                v-if="product.image"
                                :src="`/storage/${product.image}`"
                                :alt="product.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-gray-50">
                                <span class="text-4xl">◆</span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="px-1">
                            <p class="text-xs text-gray-400 mb-1">{{ product.category?.name }}</p>
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ product.name }}</p>
                                <p class="text-sm font-semibold text-gray-900 ml-2 shrink-0">
                                    {{ formatCurrency(product.price) }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="py-24">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-gray-100 rounded-3xl overflow-hidden">
                    <div class="bg-white px-10 py-12 flex flex-col gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-gray-50 flex items-center justify-center">
                            <Truck :size="18" class="text-gray-600" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 mb-1">Envío rápido</p>
                            <p class="text-xs text-gray-400 leading-relaxed">Entrega en 24-48h en península. Gratis a partir de 50€.</p>
                        </div>
                    </div>
                    <div class="bg-white px-10 py-12 flex flex-col gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-gray-50 flex items-center justify-center">
                            <ShieldCheck :size="18" class="text-gray-600" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 mb-1">Pago seguro</p>
                            <p class="text-xs text-gray-400 leading-relaxed">Transacciones cifradas con Stripe. Tus datos siempre protegidos.</p>
                        </div>
                    </div>
                    <div class="bg-white px-10 py-12 flex flex-col gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-gray-50 flex items-center justify-center">
                            <RefreshCw :size="18" class="text-gray-600" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 mb-1">Devoluciones</p>
                            <p class="text-xs text-gray-400 leading-relaxed">30 días para devolver tu pedido sin preguntas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </ShopLayout>
</template>