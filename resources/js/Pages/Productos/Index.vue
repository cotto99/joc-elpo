<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ productos: Array })

const form = useForm({ nombre: '', descripcion: '', precio: '', activo: true })
const editando = ref(null)
const formEdit = useForm({ nombre: '', descripcion: '', precio: '' })

function guardar() {
    form.post(route('productos.store'), { onSuccess: () => form.reset() })
}

function iniciarEdicion(p) {
    editando.value = p.id
    formEdit.nombre = p.nombre
    formEdit.descripcion = p.descripcion
    formEdit.precio = p.precio
}

function guardarEdicion(p) {
    formEdit.put(route('productos.update', p.id), { onSuccess: () => editando.value = null })
}

function desactivar(p) {
    if (confirm('¿Desactivar este producto?'))
        useForm({}).delete(route('productos.destroy', p.id))
}
</script>

<template>
    <Head title="Productos" />
    <AuthenticatedLayout>
        <template #header><h1 class="text-xl font-bold text-blue-900">📦 Productos</h1></template>

        <!-- Formulario nuevo -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 mb-4">Nuevo Producto</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nombre *</label>
                    <input v-model="form.nombre" type="text" placeholder="Ej: Carga 10,000 lts"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                    <p v-if="form.errors.nombre" class="text-red-500 text-xs mt-1">{{ form.errors.nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Precio (GTQ) *</label>
                    <input v-model="form.precio" type="number" step="0.01" placeholder="0.00"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Descripción</label>
                    <input v-model="form.descripcion" type="text" placeholder="Opcional"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>
            <button @click="guardar" :disabled="form.processing"
                    class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50">
                + Agregar Producto
            </button>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Nombre</th>
                        <th class="text-left px-4 py-3">Descripción</th>
                        <th class="text-right px-4 py-3">Precio</th>
                        <th class="text-center px-4 py-3">Estado</th>
                        <th class="text-center px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in productos" :key="p.id" class="border-t hover:bg-gray-50">
                        <template v-if="editando === p.id">
                            <td class="px-4 py-2"><input v-model="formEdit.nombre" class="border rounded px-2 py-1 w-full text-sm" /></td>
                            <td class="px-4 py-2"><input v-model="formEdit.descripcion" class="border rounded px-2 py-1 w-full text-sm" /></td>
                            <td class="px-4 py-2"><input v-model="formEdit.precio" type="number" class="border rounded px-2 py-1 w-24 text-sm text-right" /></td>
                            <td></td>
                            <td class="px-4 py-2 text-center flex gap-2 justify-center">
                                <button @click="guardarEdicion(p)" class="text-green-600 hover:underline text-xs">Guardar</button>
                                <button @click="editando = null" class="text-gray-400 hover:underline text-xs">Cancelar</button>
                            </td>
                        </template>
                        <template v-else>
                            <td class="px-4 py-3 font-medium">{{ p.nombre }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ p.descripcion || '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono">Q{{ Number(p.precio).toFixed(2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span :class="p.activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                      class="px-2 py-1 rounded-full text-xs">
                                    {{ p.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center flex gap-3 justify-center">
                                <button @click="iniciarEdicion(p)" class="text-blue-500 hover:underline text-xs">Editar</button>
                                <button @click="desactivar(p)" class="text-red-400 hover:underline text-xs">Desactivar</button>
                            </td>
                        </template>
                    </tr>
                    <tr v-if="!productos.length">
                        <td colspan="5" class="text-center py-8 text-gray-400">No hay productos registrados</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>