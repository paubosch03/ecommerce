<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Plus, Pencil, Trash2, Eye, Package } from '@lucide/vue';

defineProps({
    products: Object,
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount);
}

function deleteProduct(id) {
    if (confirm('¿Eliminar este producto?')) {
        useForm({}).delete(route('admin.products.destroy', id));
    }
}
</script>

<template>
    <Head title="Admin — Productos" />
    <ShopLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">

            <!-- Header -->
            <div class="flex items-end justify-between mb-12">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Panel admin</p>
                    <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Productos</h1>
                </div>
                <Link :href="route('admin.products.create')"
                    class="flex items-center gap-2 bg-gray-900 text-white text-xs font-medium px-5 py-2.5 rounded-full hover:bg-gray-700 transition-colors">
                    <Plus :size="14" />
                    Nuevo producto
                </Link>
            </div>

            <!-- Tabla -->
            <div class="rounded-2xl border border-gray-100 bg-white overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-widest px-6 py-4">Producto</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-widest px-6 py-4">Categoría</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-widest px-6 py-4">Precio</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-widest px-6 py-4">Stock</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-widest px-6 py-4">Estado</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="product in products.data" :key="product.id"
                            class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 overflow-hidden shrink-0">
                                        <img v-if="product.image" :src="`/storage/${product.image}`"
                                            :alt="product.name" class="w-full h-full object-cover" />
                                        <div v-else class="w-full h-full flex items-center justify-center">
                                            <Package :size="14" class="text-gray-300" />
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                                        <p class="text-xs text-gray-400 font-mono">{{ product.slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-gray-500">{{ product.category?.name ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ formatCurrency(product.price) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="product.stock > 0 ? 'text-emerald-500' : 'text-rose-400'"
                                    class="text-xs font-medium">
                                    {{ product.stock }} uds
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="product.active ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-gray-50 text-gray-400 border-gray-100'"
                                    class="text-xs font-medium px-2.5 py-1 rounded-full border">
                                    {{ product.active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="route('products.show', product.slug)"
                                        class="p-2 text-gray-400 hover:text-gray-900 transition-colors rounded-lg hover:bg-gray-100">
                                        <Eye :size="14" />
                                    </Link>
                                    <Link :href="route('admin.products.edit', product.id)"
                                        class="p-2 text-gray-400 hover:text-gray-900 transition-colors rounded-lg hover:bg-gray-100">
                                        <Pencil :size="14" />
                                    </Link>
                                    <button @click="deleteProduct(product.id)"
                                        class="p-2 text-gray-400 hover:text-rose-500 transition-colors rounded-lg hover:bg-rose-50">
                                        <Trash2 :size="14" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!products.data.length">
                            <td colspan="6" class="px-6 py-16 text-center">
                                <p class="text-sm text-gray-400">No hay productos todavía</p>
                                <Link :href="route('admin.products.create')"
                                    class="text-xs font-medium text-gray-900 underline mt-2 inline-block">
                                    Crear el primero
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Paginación -->
                <div v-if="products.last_page > 1" class="flex items-center justify-center gap-2 px-6 py-4 border-t border-gray-100">
                    <Link v-for="link in products.links" :key="link.label"
                        :href="link.url ?? '#'"
                        :class="[
                            'text-xs font-medium px-4 py-2 rounded-full transition-colors',
                            link.active ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-900 border border-gray-200',
                            !link.url ? 'opacity-30 pointer-events-none' : ''
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </ShopLayout>
</template>