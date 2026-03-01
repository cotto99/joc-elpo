<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ gastos: Array })

const form = useForm({ descripcion: '', monto: '', fecha: new Date().toISOString().split('T')[0] })
const editando = ref(null)
const formEdit = useForm({ descripcion: '', monto: '', fecha: '' })

function guardar() {
    form.post(route('gastos-variables.store'), { onSuccess: () => form.reset() })
}

function iniciarEdicion(g) {
    editando.value = g.id
    formEdit.descripcion = g.descripcion
    formEdit.monto = g.monto
    formEdit.fecha = g.fecha
}

function guardarEdicion(g) {
    formEdit.put(route('gastos-variables.update', g.id), {
        onSuccess: () => editando.value = null
    })
}

function eliminar(g) {
    if (confirm('¿Eliminar este gasto variable?'))
        useForm({}).delete(route('gastos-variables.destroy', g.id))
}

function fmt(val) {
    return new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ' }).format(val || 0)
}

const total = props.gastos?.reduce((s, g) => s + Number(g.monto), 0) || 0
</script>

<template>
    <Head title="Gastos Variables" />
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-blue-900">📝 Gastos Variables</h1>
        </template>

        <!-- Formulario -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 mb-4">Registrar Gasto Variable</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Descripción *</label>
                    <input v-model="form.descripcion" type="text"
                           placeholder="Ej: Combustible, mantenimiento..."
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                    <p v-if="form.errors.descripcion" class="text-red-500 text-xs mt-1">{{ form.errors.descripcion }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Monto (GTQ) *</label>
                    <input v-model="form.monto" type="number" step="0.01" placeholder="0.00"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Fecha *</label>
                    <input v-model="form.fecha" type="date"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>
            <button @click="guardar" :disabled="form.processing"
                    class="mt-4 bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 text-sm font-medium disabled:opacity-50">
                + Registrar Gasto Variable
            </button>
        </div>

        <!-- Total -->
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6 flex justify-between items-center">
            <span class="font-semibold text-orange-700">Total registrado:</span>
            <span class="text-2xl font-bold text-orange-700">{{ fmt(total) }}</span>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Descripción</th>
                        <th class="text-center px-4 py-3">Fecha</th>
                        <th class="text-center px-4 py-3">Mes</th>
                        <th class="text-right px-4 py-3">Monto</th>
                        <th class="text-center px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="g in gastos" :key="g.id" class="border-t hover:bg-gray-50">
                        <template v-if="editando === g.id">
                            <td class="px-4 py-2">
                                <input v-model="formEdit.descripcion" class="border rounded px-2 py-1 w-full text-sm" />
                            </td>
                            <td class="px-4 py-2">
                                <input v-model="formEdit.fecha" type="date" class="border rounded px-2 py-1 text-sm" />
                            </td>
                            <td></td>
                            <td class="px-4 py-2">
                                <input v-model="formEdit.monto" type="number" step="0.01"
                                       class="border rounded px-2 py-1 w-28 text-sm text-right" />
                            </td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex gap-2 justify-center">
                                    <button @click="guardarEdicion(g)" class="text-green-600 hover:underline text-xs">Guardar</button>
                                    <button @click="editando = null" class="text-gray-400 hover:underline text-xs">Cancelar</button>
                                </div>
                            </td>
                        </template>
                        <template v-else>
                            <td class="px-4 py-3 font-medium">{{ g.descripcion }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ g.fecha }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ g.mes_anio }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-orange-600">{{ fmt(g.monto) }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex gap-3 justify-center">
                                    <button @click="iniciarEdicion(g)" class="text-blue-500 hover:underline text-xs">Editar</button>
                                    <button @click="eliminar(g)" class="text-red-400 hover:underline text-xs">Eliminar</button>
                                </div>
                            </td>
                        </template>
                    </tr>
                    <tr v-if="!gastos?.length">
                        <td colspan="5" class="text-center py-8 text-gray-400">No hay gastos variables registrados</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>