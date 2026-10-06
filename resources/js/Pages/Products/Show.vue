<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { ref, computed } from 'vue';
import { ShoppingBag, ArrowLeft, Plus, Minus, ArrowRight } from '@lucide/vue';

const props = defineProps({
    product: Object,
    related: Array,
});

const page     = usePage();
const user     = computed(() => page.props.auth?.user);
const quantity = ref(1);

const form = useForm({
    product_id: props.product.id,
    quantity:   1,
});

function increment() {
    if (quantity.value < props.product.stock) quantity.value++;
}

function decrement() {
    if (quantity.value > 1) quantity.value--;
}

function addToCart() {
    form.quantity = quantity.value;
    form.post(route('cart.store'));
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount);
}
</script>

<template>
    <Head :title="product.name" />
    <ShopLayout>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">

            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 mb-12">
                <Link :href="route('products.index')"
                    class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-900 transition-colors">
                    <ArrowLeft :size="12" />
                    Productos
                </Link>
                <span class="text-gray-200">/</span>
                <span class="text-xs text-gray-400">{{ product.category?.name }}</span>
                <span class="text-gray-200">/</span>
                <span class="text-xs text-gray-600">{{ product.name }}</span>
            </div>

            <!-- Producto -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-32">

                <!-- Imagen -->
                <div class="aspect-square rounded-3xl bg-gray-50 overflow-hidden border border-gray-100">
                    <img
                        v-if="product.image"
                        :src="`/storage/${product.image}`"
                        :alt="product.name"
                        class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <span class="text-8xl text-gray-200">◆</span>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex flex-col justify-center">

                    <!-- Badge categoría -->
                    <div class="inline-flex mb-6">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">
                            {{ product.category?.name }}
                        </span>
                    </div>

                    <h1 class="text-4xl font-semibold text-gray-900 tracking-tight leading-tight mb-4">
                        {{ product.name }}
                    </h1>

                    <p class="text-3xl font-light text-gray-900 mb-8">
                        {{ formatCurrency(product.price) }}
                    </p>

                    <p v-if="product.description" class="text-sm text-gray-500 leading-relaxed mb-10">
                        {{ product.description }}
                    </p>

                    <!-- Stock -->
                    <div class="flex items-center gap-2 mb-8">
                        <span v-if="product.stock > 0"
                            class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span v-else
                            class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                        <span class="text-xs font-medium text-gray-500">
                            {{ product.stock > 0 ? `${product.stock} unidades disponibles` : 'Agotado' }}
                        </span>
                    </div>

                    <!-- Cantidad -->
                    <div v-if="product.stock > 0" class="flex items-center gap-4 mb-6">
                        <div class="flex items-center gap-3 border border-gray-200 rounded-full px-4 py-2">
                            <button @click="decrement"
                                class="text-gray-400 hover:text-gray-900 transition-colors disabled:opacity-30"
                                :disabled="quantity <= 1">
                                <Minus :size="14" />
                            </button>
                            <span class="text-sm font-medium text-gray-900 w-6 text-center">{{ quantity }}</span>
                            <button @click="increment"
                                class="text-gray-400 hover:text-gray-900 transition-colors disabled:opacity-30"
                                :disabled="quantity >= product.stock">
                                <Plus :size="14" />
                            </button>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <template v-if="user">
                            <button
                                v-if="product.stock > 0"
                                @click="addToCart"
                                :disabled="form.processing"
                                class="flex-1 flex items-center justify-center gap-2 bg-gray-900 text-white text-sm font-medium py-4 rounded-full hover:bg-gray-700 transition-colors disabled:opacity-50"
                            >
                                <ShoppingBag :size="16" />
                                Añadir al carrito
                            </button>
                            <button v-else disabled
                                class="flex-1 text-sm font-medium py-4 rounded-full border border-gray-200 text-gray-300 cursor-not-allowed">
                                Agotado
                            </button>
                        </template>
                        <template v-else>
                            <Link :href="route('login')"
                                class="flex-1 flex items-center justify-center gap-2 bg-gray-900 text-white text-sm font-medium py-4 rounded-full hover:bg-gray-700 transition-colors">
                                Inicia sesión para comprar
                            </Link>
                        </template>
                    </div>

                    <!-- Garantías -->
                    <div class="mt-10 pt-8 border-t border-gray-100 grid grid-cols-2 gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center shrink-0">
                                <span class="text-xs">🚚</span>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Envío gratis</p>
                                <p class="text-xs text-gray-400">En pedidos +50€</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center shrink-0">
                                <span class="text-xs">↩️</span>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">30 días</p>
                                <p class="text-xs text-gray-400">Para devoluciones</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos relacionados -->
            <div v-if="related?.length">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">También te puede gustar</p>
                        <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Productos relacionados</h2>
                    </div>
                    <Link :href="route('products.index', { category: product.category?.slug })"
                        class="text-xs font-medium text-gray-500 hover:text-gray-900 transition-colors flex items-center gap-1">
                        Ver más <ArrowRight :size="12" />
                    </Link>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <Link
                        v-for="item in related"
                        :key="item.id"
                        :href="route('products.show', item.slug)"
                        class="group cursor-pointer"
                    >
                        <div class="aspect-square rounded-2xl bg-gray-50 overflow-hidden mb-3 border border-gray-100 group-hover:border-gray-200 transition-all duration-300">
                            <img v-if="item.image" :src="`/storage/${item.image}`" :alt="item.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <span class="text-3xl text-gray-200">◆</span>
                            </div>
                        </div>
                        <p class="text-xs font-medium text-gray-900 truncate">{{ item.name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ formatCurrency(item.price) }}</p>
                    </Link>
                </div>
            </div>

        </div>
    </ShopLayout>
</template>