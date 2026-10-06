<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { ShoppingBag, Menu, X, User, Package, Search } from '@lucide/vue';

const page  = usePage();
const user  = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash);
const mobileOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-white" style="font-family: 'Inter', sans-serif;">

        <!-- Navbar -->
        <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-center justify-between h-14">

                    <!-- Logo -->
                    <Link :href="route('home')" class="text-base font-semibold text-gray-900 tracking-tight">
                        ◆ Shop
                    </Link>

                    <!-- Links centro -->
                    <div class="hidden md:flex items-center gap-8">
                        <Link :href="route('home')"
                            class="text-xs font-medium text-gray-500 hover:text-gray-900 transition-colors tracking-wide">
                            Inicio
                        </Link>
                        <Link :href="route('products.index')"
                            class="text-xs font-medium text-gray-500 hover:text-gray-900 transition-colors tracking-wide">
                            Productos
                        </Link>
                    </div>

                    <!-- Acciones derecha -->
                    <div class="flex items-center gap-1">
                        <Link :href="route('products.index')"
                            class="p-2 text-gray-400 hover:text-gray-900 transition-colors rounded-full hover:bg-gray-50">
                            <Search :size="16" />
                        </Link>

                        <Link v-if="user" :href="route('orders.index')"
                            class="p-2 text-gray-400 hover:text-gray-900 transition-colors rounded-full hover:bg-gray-50">
                            <Package :size="16" />
                        </Link>

                        <Link v-if="user" :href="route('profile.edit')"
                            class="p-2 text-gray-400 hover:text-gray-900 transition-colors rounded-full hover:bg-gray-50">
                            <User :size="16" />
                        </Link>

                        <Link :href="route('cart.index')"
                            class="p-2 text-gray-400 hover:text-gray-900 transition-colors rounded-full hover:bg-gray-50">
                            <ShoppingBag :size="16" />
                        </Link>

                        <template v-if="!user">
                            <Link :href="route('login')"
                                class="text-xs font-medium text-gray-500 hover:text-gray-900 transition-colors ml-2">
                                Entrar
                            </Link>
                            <Link :href="route('register')"
                                class="ml-3 text-xs font-medium bg-gray-900 text-white px-4 py-2 rounded-full hover:bg-gray-700 transition-colors">
                                Registrarse
                            </Link>
                        </template>

                        <Link v-if="user?.is_admin" :href="route('admin.products.index')"
                            class="ml-3 text-xs font-medium bg-gray-900 text-white px-4 py-2 rounded-full hover:bg-gray-700 transition-colors">
                            Admin
                        </Link>

                        <!-- Móvil -->
                        <button @click="mobileOpen = !mobileOpen"
                            class="md:hidden p-2 text-gray-400 hover:text-gray-900 transition-colors ml-1">
                            <X v-if="mobileOpen" :size="16" />
                            <Menu v-else :size="16" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Menú móvil -->
            <div v-if="mobileOpen" class="md:hidden border-t border-gray-100 bg-white/95 backdrop-blur-xl px-6 py-4 space-y-3">
                <Link :href="route('home')" class="block text-sm text-gray-600 hover:text-gray-900 py-1" @click="mobileOpen = false">Inicio</Link>
                <Link :href="route('products.index')" class="block text-sm text-gray-600 hover:text-gray-900 py-1" @click="mobileOpen = false">Productos</Link>
                <template v-if="!user">
                    <Link :href="route('login')" class="block text-sm text-gray-600 hover:text-gray-900 py-1" @click="mobileOpen = false">Entrar</Link>
                    <Link :href="route('register')" class="block text-sm text-gray-600 hover:text-gray-900 py-1" @click="mobileOpen = false">Registrarse</Link>
                </template>
            </div>
        </nav>

        <!-- Flash messages -->
        <div class="fixed top-16 left-1/2 -translate-x-1/2 z-40 w-full max-w-sm px-4">
            <div v-if="flash?.success"
                class="flex items-center gap-3 bg-gray-900 text-white rounded-2xl px-5 py-3 shadow-lg">
                <span class="text-emerald-400 text-xs">✓</span>
                <p class="text-xs font-medium">{{ flash.success }}</p>
            </div>
            <div v-if="flash?.error"
                class="flex items-center gap-3 bg-gray-900 text-white rounded-2xl px-5 py-3 shadow-lg">
                <span class="text-rose-400 text-xs">✕</span>
                <p class="text-xs font-medium">{{ flash.error }}</p>
            </div>
        </div>

        <!-- Contenido -->
        <main class="pt-14">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-32 border-t border-gray-100 py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-xs font-semibold text-gray-900 tracking-tight">◆ Shop</p>
                    <div class="flex items-center gap-6">
                        <Link :href="route('products.index')" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">Productos</Link>
                        <Link :href="route('cart.index')" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">Carrito</Link>
                        <Link v-if="user" :href="route('orders.index')" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">Pedidos</Link>
                    </div>
                    <p class="text-xs text-gray-300">© 2026 Shop. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>

    </div>
</template>