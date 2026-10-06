<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { ArrowLeft, Sparkles } from '@lucide/vue';
import { ref } from 'vue';
import axios from 'axios';

defineProps({
    categories: Array,
});

const form = useForm({
    name:        '',
    category_id: '',
    description: '',
    price:       '',
    stock:       '',
    image:       null,
    featured:    false,
    active:      true,
});

const imagePreview   = ref(null);
const generatingDesc = ref(false);

function handleImage(e) {
    const file = e.target.files[0];
    form.image = file;
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => imagePreview.value = e.target.result;
        reader.readAsDataURL(file);
    }
}

async function generateDescription() {
    if (!form.name) return;
    generatingDesc.value = true;
    try {
        const { data } = await axios.post(route('admin.products.generate-description'), {
            name: form.name,
        });
        form.description = data.description;
    } catch {
        // silently fail
    } finally {
        generatingDesc.value = false;
    }
}

function submit() {
    form.post(route('admin.products.store'), {
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Admin — Nuevo producto" />
    <ShopLayout>
        <div class="max-w-3xl mx-auto px-6 lg:px-8 py-16">

            <!-- Header -->
            <div class="mb-12">
                <Link :href="route('admin.products.index')"
                    class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-900 transition-colors mb-6">
                    <ArrowLeft :size="12" />
                    Productos
                </Link>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Admin</p>
                <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Nuevo producto</h1>
            </div>

            <div class="space-y-8">

                <!-- Imagen -->
                <div class="rounded-2xl border border-gray-100 bg-white p-8">
                    <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-6">Imagen</p>
                    <div class="flex items-start gap-6">
                        <div class="w-32 h-32 rounded-2xl bg-gray-50 border border-gray-100 overflow-hidden shrink-0 flex items-center justify-center">
                            <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                            <span v-else class="text-3xl text-gray-200">◆</span>
                        </div>
                        <div class="flex-1">
                            <label class="block w-full cursor-pointer">
                                <div class="border-2 border-dashed border-gray-200 rounded-xl px-6 py-8 text-center hover:border-gray-400 transition-colors">
                                    <p class="text-xs font-medium text-gray-500">Haz clic para subir imagen</p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG hasta 2MB</p>
                                </div>
                                <input type="file" accept="image/*" @change="handleImage" class="hidden" />
                            </label>
                            <p v-if="form.errors.image" class="text-xs text-rose-500 mt-2">{{ form.errors.image }}</p>
                        </div>
                    </div>
                </div>

                <!-- Info básica -->
                <div class="rounded-2xl border border-gray-100 bg-white p-8">
                    <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-6">Información</p>
                    <div class="space-y-5">

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Nombre</label>
                            <input v-model="form.name" type="text" placeholder="Nombre del producto"
                                class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors" />
                            <p v-if="form.errors.name" class="text-xs text-rose-500 mt-1">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-medium text-gray-500">Descripción</label>
                                <button @click="generateDescription" :disabled="!form.name || generatingDesc"
                                    class="flex items-center gap-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-700 disabled:opacity-40 transition-colors">
                                    <Sparkles :size="12" />
                                    {{ generatingDesc ? 'Generando...' : 'Generar con IA' }}
                                </button>
                            </div>
                            <textarea v-model="form.description" rows="4" placeholder="Descripción del producto..."
                                class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors resize-none" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-2">Categoría</label>
                                <select v-model="form.category_id"
                                    class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors">
                                    <option value="">Seleccionar...</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                                <p v-if="form.errors.category_id" class="text-xs text-rose-500 mt-1">{{ form.errors.category_id }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-2">Precio (€)</label>
                                <input v-model="form.price" type="number" step="0.01" placeholder="0.00"
                                    class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors" />
                                <p v-if="form.errors.price" class="text-xs text-rose-500 mt-1">{{ form.errors.price }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Stock</label>
                            <input v-model="form.stock" type="number" placeholder="0"
                                class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-gray-400 focus:bg-white transition-colors" />
                            <p v-if="form.errors.stock" class="text-xs text-rose-500 mt-1">{{ form.errors.stock }}</p>
                        </div>
                    </div>
                </div>

                <!-- Opciones -->
                <div class="rounded-2xl border border-gray-100 bg-white p-8">
                    <p class="text-xs font-semibold text-gray-900 uppercase tracking-widest mb-6">Opciones</p>
                    <div class="space-y-4">
                        <label class="flex items-center justify-between cursor-pointer">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Producto activo</p>
                                <p class="text-xs text-gray-400">Visible en la tienda</p>
                            </div>
                            <div @click="form.active = !form.active"
                                :class="form.active ? 'bg-gray-900' : 'bg-gray-200'"
                                class="relative w-10 h-5 rounded-full transition-colors cursor-pointer">
                                <div :class="form.active ? 'translate-x-5' : 'translate-x-0.5'"
                                    class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"></div>
                            </div>
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Producto destacado</p>
                                <p class="text-xs text-gray-400">Aparece en la página principal</p>
                            </div>
                            <div @click="form.featured = !form.featured"
                                :class="form.featured ? 'bg-gray-900' : 'bg-gray-200'"
                                class="relative w-10 h-5 rounded-full transition-colors cursor-pointer">
                                <div :class="form.featured ? 'translate-x-5' : 'translate-x-0.5'"
                                    class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('admin.products.index')"
                        class="text-xs font-medium text-gray-500 border border-gray-200 px-6 py-3 rounded-full hover:border-gray-400 transition-colors">
                        Cancelar
                    </Link>
                    <button @click="submit" :disabled="form.processing"
                        class="text-xs font-medium bg-gray-900 text-white px-8 py-3 rounded-full hover:bg-gray-700 disabled:opacity-50 transition-colors">
                        {{ form.processing ? 'Guardando...' : 'Crear producto' }}
                    </button>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>