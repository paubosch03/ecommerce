<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { ArrowLeft, Package, MapPin, CreditCard } from '@lucide/vue';

defineProps({
    order: Object,
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('es-ES', {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
    }).format(new Date(date));
}

const statusConfig = {
    pending:   { label: 'Pendiente',  class: 'bg-amber-50 text-amber-600 border-amber-100',     step: 1 },
    paid:      { label: 'Pagado',     class: 'bg-emerald-50 text-emerald-600 border-emerald-100', step: 2 },
    shipped:   { label: 'Enviado',    class: 'bg-blue-50 text-blue-600 border-blue-100',          step: 3 },
    delivered: { label: 'Entregado',  class: 'bg-gray-50 text-gray-600 border-gray-100',          step: 4 },
    cancelled: { label: 'Cancelado',  class: 'bg-rose-50 text-rose-500 border-rose-100',          step: 0 },
};

const steps = [
    { label: 'Pedido realizado', key: 'pending' },
    { label: 'Pago confirmado',  key: 'paid' },
    { label: 'En camino',        key: 'shipped' },
    { label: 'Entregado',        key: 'delivered' },
];
</script>

<template>
    <Head :title="`Pedido #${order.id}`" />
    <ShopLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">

            <!-- Header -->
            <div class="mb-12">
                <Link :href="route('orders.index')"
                    class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-900 transition-colors mb-6">
                    <ArrowLeft :size="12" />
                    Mis pedidos
                </Link>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Detalle</p>
                        <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Pedido #{{ order.id }}</h1>
                        <p class="text-xs text-gray-400 mt-1">{{ formatDate(order.created_at) }}</p>
                    </div>
                    <span
                        :class="statusConfig[order.status]?.class"
                        class="text-xs font-medium px-4 py-1.5 rounded-full border"
                    >
                        {{ statusConfig[order.status]?.label }}
                    </span>
                </div>
            </div>

            <!-- Seguimiento -->
            <div v-if="order.status !== 'cancelled'" class="mb-12 p-8 rounded-3xl border border-gray-100 bg-white">
                <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-8">Seguimiento</p>
                <div class="relative flex items-center justify-between">
                    <!-- Línea de progreso -->
                    <div class="absolute left-0 right-0 h-px bg-gray-100 top-4 z-0"></div>
                    <div
                        class="absolute left-0 h-px bg-gray-900 top-4 z-0 transition-all duration-500"
                        :style="{ width: `${((statusConfig[order.status]?.step - 1) / 3) * 100}%` }"
                    ></div>

                    <div v-for="(step, i) in steps" :key="step.key" class="relative z-10 flex flex-col items-center gap-3">
                        <div
                            :class="statusConfig[order.status]?.step > i
                                ? 'bg-gray-900 border-gray-900'
                                : 'bg-white border-gray-200'"
                            class="w-8 h-8 rounded-full border-2 flex items-center justify-center transition-all duration-300"
                        >
                            <span v-if="statusConfig[order.status]?.step > i" class="text-white text-xs">✓</span>
                        </div>
                        <p class="text-xs font-medium text-center"
                            :class="statusConfig[order.status]?.step > i ? 'text-gray-900' : 'text-gray-400'">
                            {{ step.label }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Productos -->
                <div class="lg:col-span-2">
                    <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-6">Productos</p>
                    <div class="space-y-4">
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex gap-5 p-5 rounded-2xl border border-gray-100 bg-white"
                        >
                            <div class="w-20 h-20 rounded-xl bg-gray-50 overflow-hidden shrink-0 border border-gray-100">
                                <img
                                    v-if="item.product?.image"
                                    :src="`/storage/${item.product.image}`"
                                    :alt="item.product?.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <span class="text-2xl text-gray-200">◆</span>
                                </div>
                            </div>
                            <div class="flex-1 flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ item.product?.name }}</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ formatCurrency(item.price) }} × {{ item.quantity }}
                                    </p>
                                </div>
                                <p class="text-sm font-bold text-gray-900 shrink-0">
                                    {{ formatCurrency(item.price * item.quantity) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info lateral -->
                <div class="space-y-6">

                    <!-- Resumen pago -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <CreditCard :size="14" class="text-gray-400" />
                            <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest">Pago</p>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Subtotal</span>
                                <span class="text-xs font-medium text-gray-900">{{ formatCurrency(order.total) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Envío</span>
                                <span class="text-xs font-medium text-emerald-500">
                                    {{ order.total >= 50 ? 'Gratis' : formatCurrency(4.99) }}
                                </span>
                            </div>
                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900">Total</span>
                                <span class="text-base font-bold text-gray-900">{{ formatCurrency(order.total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección envío -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <MapPin :size="14" class="text-gray-400" />
                            <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest">Envío</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-gray-900">{{ order.shipping_name }}</p>
                            <p class="text-xs text-gray-500">{{ order.shipping_email }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ order.shipping_address }}</p>
                            <p class="text-xs text-gray-500">{{ order.shipping_zip }} {{ order.shipping_city }}</p>
                        </div>
                    </div>

                    <!-- Stripe ID -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6">
                        <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-3">Referencia</p>
                        <p class="text-xs text-gray-400 font-mono break-all">{{ order.stripe_payment_id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>