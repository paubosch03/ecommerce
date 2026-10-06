<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Package, ArrowRight, ShoppingBag } from '@lucide/vue';

defineProps({
    orders: Array,
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(date));
}

const statusConfig = {
    pending:   { label: 'Pendiente',   class: 'bg-amber-50 text-amber-600 border-amber-100' },
    paid:      { label: 'Pagado',      class: 'bg-emerald-50 text-emerald-600 border-emerald-100' },
    shipped:   { label: 'Enviado',     class: 'bg-blue-50 text-blue-600 border-blue-100' },
    delivered: { label: 'Entregado',   class: 'bg-gray-50 text-gray-600 border-gray-100' },
    cancelled: { label: 'Cancelado',   class: 'bg-rose-50 text-rose-500 border-rose-100' },
};
</script>

<template>
    <Head title="Mis pedidos" />
    <ShopLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">

            <!-- Header -->
            <div class="mb-12">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Historial</p>
                <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">
                    Mis pedidos
                    <span class="text-gray-400 font-normal text-lg ml-2">({{ orders.length }})</span>
                </h1>
            </div>

            <!-- Sin pedidos -->
            <div v-if="!orders.length" class="py-32 text-center">
                <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6">
                    <ShoppingBag :size="24" class="text-gray-300" />
                </div>
                <p class="text-sm font-medium text-gray-900 mb-1">No tienes pedidos todavía</p>
                <p class="text-xs text-gray-400 mb-8">Explora nuestros productos y haz tu primer pedido</p>
                <Link :href="route('products.index')"
                    class="text-xs font-medium bg-gray-900 text-white px-8 py-3 rounded-full hover:bg-gray-700 transition-colors">
                    Ver productos
                </Link>
            </div>

            <!-- Lista de pedidos -->
            <div v-else class="space-y-4">
                <Link
                    v-for="order in orders"
                    :key="order.id"
                    :href="route('orders.show', order.id)"
                    class="group flex items-center justify-between gap-6 p-6 rounded-2xl border border-gray-100 bg-white hover:border-gray-200 hover:shadow-sm transition-all"
                >
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-gray-100 transition-colors">
                            <Package :size="18" class="text-gray-400" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <p class="text-sm font-semibold text-gray-900">Pedido #{{ order.id }}</p>
                                <span
                                    :class="statusConfig[order.status]?.class"
                                    class="text-xs font-medium px-2.5 py-0.5 rounded-full border"
                                >
                                    {{ statusConfig[order.status]?.label }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400">
                                {{ formatDate(order.created_at) }} ·
                                {{ order.items?.length }} producto{{ order.items?.length !== 1 ? 's' : '' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <p class="text-sm font-bold text-gray-900">{{ formatCurrency(order.total) }}</p>
                        <ArrowRight :size="14" class="text-gray-300 group-hover:text-gray-600 transition-colors" />
                    </div>
                </Link>
            </div>
        </div>
    </ShopLayout>
</template>