<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { ArrowLeft } from '@lucide/vue';

const props = defineProps({
    order: Object,
});

const statusForm = useForm({
    status: props.order.status,
});

function updateStatus() {
    statusForm.put(route('admin.orders.update', props.order.id));
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('es-ES', {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
    }).format(new Date(date));
}

const statusConfig = {
    pending:   { label: 'Pendiente',  class: 'bg-amber-50 text-amber-600 border-amber-100' },
    paid:      { label: 'Pagado',     class: 'bg-emerald-50 text-emerald-600 border-emerald-100' },
    shipped:   { label: 'Enviado',    class: 'bg-blue-50 text-blue-600 border-blue-100' },
    delivered: { label: 'Entregado',  class: 'bg-gray-50 text-gray-600 border-gray-100' },
    cancelled: { label: 'Cancelado',  class: 'bg-rose-50 text-rose-500 border-rose-100' },
};
</script>

<template>
    <Head :title="`Admin — Pedido #${order.id}`" />
    <ShopLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">

            <div class="mb-12">
                <Link :href="route('admin.orders.index')"
                    class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-900 transition-colors mb-6">
                    <ArrowLeft :size="12" />
                    Pedidos
                </Link>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Admin</p>
                        <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Pedido #{{ order.id }}</h1>
                        <p class="text-xs text-gray-400 mt-1">{{ formatDate(order.created_at) }}</p>
                    </div>

                    <!-- Cambiar estado -->
                    <div class="flex items-center gap-3">
                        <select v-model="statusForm.status"
                            class="text-xs font-medium border border-gray-200 rounded-full px-4 py-2 bg-white focus:outline-none focus:border-gray-400">
                            <option value="pending">Pendiente</option>
                            <option value="paid">Pagado</option>
                            <option value="shipped">Enviado</option>
                            <option value="delivered">Entregado</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                        <button @click="updateStatus" :disabled="statusForm.processing"
                            class="text-xs font-medium bg-gray-900 text-white px-5 py-2 rounded-full hover:bg-gray-700 disabled:opacity-50 transition-colors">
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Productos -->
                <div class="lg:col-span-2">
                    <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-6">Productos</p>
                    <div class="rounded-2xl border border-gray-100 bg-white overflow-hidden">
                        <div v-for="item in order.items" :key="item.id"
                            class="flex gap-5 p-5 border-b border-gray-50 last:border-0">
                            <div class="w-16 h-16 rounded-xl bg-gray-50 overflow-hidden shrink-0">
                                <img v-if="item.product?.image" :src="`/storage/${item.product.image}`"
                                    :alt="item.product?.name" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <span class="text-xl text-gray-200">◆</span>
                                </div>
                            </div>
                            <div class="flex-1 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ item.product?.name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ formatCurrency(item.price) }} × {{ item.quantity }}</p>
                                </div>
                                <p class="text-sm font-bold text-gray-900">{{ formatCurrency(item.price * item.quantity) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info lateral -->
                <div class="space-y-6">

                    <!-- Cliente -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6">
                        <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-4">Cliente</p>
                        <p class="text-sm font-medium text-gray-900">{{ order.user?.name }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ order.user?.email }}</p>
                    </div>

                    <!-- Envío -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6">
                        <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-4">Dirección</p>
                        <p class="text-sm font-medium text-gray-900">{{ order.shipping_name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ order.shipping_address }}</p>
                        <p class="text-xs text-gray-500">{{ order.shipping_zip }} {{ order.shipping_city }}</p>
                    </div>

                    <!-- Total -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6">
                        <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-4">Resumen</p>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Subtotal</span>
                                <span class="text-xs font-medium text-gray-900">{{ formatCurrency(order.total) }}</span>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <span class="text-sm font-semibold text-gray-900">Total</span>
                                <span class="text-base font-bold text-gray-900">{{ formatCurrency(order.total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stripe -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6">
                        <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-3">Stripe ID</p>
                        <p class="text-xs text-gray-400 font-mono break-all">{{ order.stripe_payment_id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>