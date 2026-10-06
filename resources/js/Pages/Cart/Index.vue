<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Trash2, Plus, Minus, ArrowLeft, ArrowRight, ShoppingBag } from '@lucide/vue';

const props = defineProps({
    items: Array,
    total: Number,
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount);
}

function updateQuantity(item, quantity) {
    useForm({ quantity }).patch(route('cart.update', item.id));
}

function removeItem(item) {
    useForm({}).delete(route('cart.destroy', item.id));
}
</script>

<template>
    <Head title="Carrito" />
    <ShopLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">

            <!-- Header -->
            <div class="mb-12">
                <Link :href="route('products.index')"
                    class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-900 transition-colors mb-6">
                    <ArrowLeft :size="12" />
                    Seguir comprando
                </Link>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Tu selección</p>
                <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">
                    Carrito
                    <span class="text-gray-400 font-normal text-lg ml-2">({{ items.length }})</span>
                </h1>
            </div>

            <!-- Carrito vacío -->
            <div v-if="!items.length" class="py-32 text-center">
                <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6">
                    <ShoppingBag :size="24" class="text-gray-300" />
                </div>
                <p class="text-sm font-medium text-gray-900 mb-1">Tu carrito está vacío</p>
                <p class="text-xs text-gray-400 mb-8">Añade productos para continuar</p>
                <Link :href="route('products.index')"
                    class="text-xs font-medium bg-gray-900 text-white px-8 py-3 rounded-full hover:bg-gray-700 transition-colors">
                    Ver productos
                </Link>
            </div>

            <!-- Carrito con productos -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-12">

                <!-- Lista de productos -->
                <div class="lg:col-span-2 space-y-4">
                    <div
                        v-for="item in items"
                        :key="item.id"
                        class="flex gap-5 p-5 rounded-2xl border border-gray-100 bg-white hover:border-gray-200 transition-colors"
                    >
                        <!-- Imagen -->
                        <div class="w-24 h-24 rounded-xl bg-gray-50 overflow-hidden shrink-0 border border-gray-100">
                            <img
                                v-if="item.product.image"
                                :src="`/storage/${item.product.image}`"
                                :alt="item.product.name"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <span class="text-2xl text-gray-200">◆</span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ item.product.name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ formatCurrency(item.product.price) }} / ud
                                    </p>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 shrink-0">
                                    {{ formatCurrency(item.subtotal) }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between mt-4">
                                <!-- Cantidad -->
                                <div class="flex items-center gap-3 border border-gray-200 rounded-full px-3 py-1.5">
                                    <button
                                        @click="updateQuantity(item, item.quantity - 1)"
                                        :disabled="item.quantity <= 1"
                                        class="text-gray-400 hover:text-gray-900 transition-colors disabled:opacity-30"
                                    >
                                        <Minus :size="12" />
                                    </button>
                                    <span class="text-xs font-medium text-gray-900 w-4 text-center">{{ item.quantity }}</span>
                                    <button
                                        @click="updateQuantity(item, item.quantity + 1)"
                                        :disabled="item.quantity >= item.product.stock"
                                        class="text-gray-400 hover:text-gray-900 transition-colors disabled:opacity-30"
                                    >
                                        <Plus :size="12" />
                                    </button>
                                </div>

                                <!-- Eliminar -->
                                <button
                                    @click="removeItem(item)"
                                    class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-rose-500 transition-colors"
                                >
                                    <Trash2 :size="12" />
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 rounded-3xl border border-gray-100 bg-white p-8">
                        <p class="text-sm font-semibold text-gray-900 mb-8">Resumen del pedido</p>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Subtotal</span>
                                <span class="text-xs font-medium text-gray-900">{{ formatCurrency(total) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Envío</span>
                                <span class="text-xs font-medium text-emerald-500">
                                    {{ total >= 50 ? 'Gratis' : formatCurrency(4.99) }}
                                </span>
                            </div>
                            <div v-if="total < 50" class="text-xs text-gray-400 bg-gray-50 rounded-xl px-4 py-3">
                                Añade {{ formatCurrency(50 - total) }} más para envío gratis
                            </div>
                            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900">Total</span>
                                <span class="text-lg font-bold text-gray-900">
                                    {{ formatCurrency(total < 50 ? total + 4.99 : total) }}
                                </span>
                            </div>
                        </div>

                        <Link
                            :href="route('checkout.index')"
                            class="flex items-center justify-center gap-2 w-full bg-gray-900 text-white text-sm font-medium py-4 rounded-full hover:bg-gray-700 transition-colors"
                        >
                            Finalizar compra
                            <ArrowRight :size="14" />
                        </Link>

                        <p class="text-xs text-gray-400 text-center mt-4">
                            Pago seguro con Stripe
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>