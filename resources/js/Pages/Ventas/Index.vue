<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
    ventas: Object,
    clientes: Array,
    productos: Array,
})


const mostrarForm = ref(false)

const form = useForm({
    cliente_id: '',
    fecha: new Date().toISOString().split('T')[0],
    tipo_pago: 'contado',
    notas: '',
    detalles: [{ producto_id: '', cantidad: 1, precio_unitario: 0 }],
})

function agregarDetalle() {
    form.detalles.push({ producto_id: '', cantidad: 1, precio_unitario: 0 })
}

function quitarDetalle(i) {
    if (form.detalles.length > 1) form.detalles.splice(i, 1)
}

function fmtFecha(f) {
    if (!f) return '—'
    const fecha = new Date(f)
    return fecha.toLocaleDateString('es-GT', {
        day: '2-digit',
        month: '2-digit', 
        year: 'numeric'
    })
}
function seleccionarProducto(i) {
    const prod = props.productos.find(p => p.id == form.detalles[i].producto_id)
    if (prod) form.detalles[i].precio_unitario = prod.precio
}

const total = computed(() =>
    form.detalles.reduce((s, d) => s + (d.cantidad * d.precio_unitario), 0)
)

function guardar() {
    form.post(route('ventas.store'), {
        onSuccess: () => {
            form.reset()
            form.detalles = [{ producto_id: '', cantidad: 1, precio_unitario: 0 }]
            mostrarForm.value = false
        }
    })
}

function fmt(val) {
    return new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ' }).format(val || 0)
}
</script>

<template>
    <Head title="Ventas" />
    <AuthenticatedLayout>
        <template #header><h1 class="text-xl font-bold text-blue-900">💧 Ventas / Viajes</h1></template>

        <!-- Botón nueva venta -->
        <div class="mb-6">
            <button @click="mostrarForm = !mostrarForm"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
                {{ mostrarForm ? '✕ Cancelar' : '+ Nueva Venta' }}
            </button>
        </div>

        <!-- Formulario -->
        <div v-if="mostrarForm" class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 mb-4">Registrar Venta</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Cliente *</label>
                    <select v-model="form.cliente_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar...</option>
                        <option v-for="c in clientes" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                    </select>
                    <p v-if="form.errors.cliente_id" class="text-red-500 text-xs mt-1">{{ form.errors.cliente_id }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Fecha *</label>
                    <input v-model="form.fecha" type="date"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Tipo de Pago *</label>
                    <select v-model="form.tipo_pago"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="contado">Contado</option>
                        <option value="credito">Crédito</option>
                    </select>
                </div>
            </div>

            <!-- Detalles -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Productos / Servicios</label>
                <div v-for="(d, i) in form.detalles" :key="i"
                     class="grid grid-cols-12 gap-2 mb-2 items-center">
                    <div class="col-span-5">
                        <select v-model="d.producto_id" @change="seleccionarProducto(i)"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar producto...</option>
                            <option v-for="p in productos" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <input v-model.number="d.cantidad" type="number" min="1" placeholder="Cant."
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="col-span-3">
                        <input v-model.number="d.precio_unitario" type="number" step="0.01" placeholder="Precio"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="col-span-1 text-right text-sm font-mono text-gray-600">
                        Q{{ (d.cantidad * d.precio_unitario).toFixed(2) }}
                    </div>
                    <div class="col-span-1 text-center">
                        <button @click="quitarDetalle(i)" class="text-red-400 hover:text-red-600 text-lg">✕</button>
                    </div>
                </div>
                <button @click="agregarDetalle"
                        class="text-blue-500 hover:underline text-sm mt-1">+ Agregar línea</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-purple-50 border border-purple-200 rounded-lg px-4 py-3 flex items-center justify-between">
    <div>
        <p class="text-xs text-purple-600 font-medium uppercase tracking-wide">Aporte Caja Chica (10%)</p>
        <p class="text-lg font-bold text-purple-700 mt-1">{{ fmt(total * 0.10) }}</p>
    </div>
    <span class="text-2xl">🏦</span>
</div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Notas</label>
                    <input v-model="form.notas" type="text"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>

            <!-- Total -->
            <div class="flex items-center justify-between border-t pt-4">
                <div class="text-lg font-bold text-gray-700 flex flex-col gap-1">
    <div>
        Total venta: <span class="text-blue-700">{{ fmt(total) }}</span>
        <span v-if="form.tipo_pago === 'credito'"
              class="ml-2 text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">A crédito</span>
    </div>
    <div class="text-sm font-normal text-gray-500">
        Caja chica (10%): <span class="text-purple-600 font-semibold">{{ fmt(total * 0.10) }}</span>
        — Neto negocio: <span class="text-green-600 font-semibold">{{ fmt(total * 0.90) }}</span>
    </div>
</div>
                <button @click="guardar" :disabled="form.processing"
                        class="bg-green-600 text-white px-8 py-2 rounded-lg hover:bg-green-700 font-medium disabled:opacity-50">
                    💾 Guardar Venta
                </button>
            </div>
        </div>

        <!-- Tabla de ventas -->
        <!-- Tabla de ventas -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <!-- Vista móvil: tarjetas -->
    <div class="lg:hidden divide-y">
        <div v-for="v in ventas.data" :key="v.id" class="p-4">
            <div class="flex justify-between items-start mb-1">
                <span class="font-bold text-gray-800">{{ v.cliente?.nombre }}</span>
                <span class="font-mono font-bold text-blue-700">{{ fmt(v.total) }}</span>
            </div>
            <div class="flex justify-between items-center text-sm text-gray-500">
                <span>{{ fmtFecha(v.fecha) }}</span>
                <span :class="v.tipo_pago === 'contado'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-yellow-100 text-yellow-700'"
                      class="px-2 py-0.5 rounded-full text-xs capitalize">
                    {{ v.tipo_pago }}
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-1">
                {{ v.detalles?.map(d => d.producto?.nombre).join(', ') }}
            </p>
            <p class="text-xs text-purple-500 mt-1">
                🏦 Caja chica: {{ fmt(v.aporte_caja_chica) }}
            </p>
        </div>
        <div v-if="!ventas.data?.length" class="text-center py-8 text-gray-400">
            No hay ventas registradas
        </div>
    </div>

    <!-- Vista desktop: tabla -->
        <table class="w-full text-sm min-w-[600px]">        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Fecha</th>
                <th class="text-left px-4 py-3">Cliente</th>
                <th class="text-left px-4 py-3">Productos</th>
                <th class="text-center px-4 py-3">Pago</th>
                <th class="text-right px-4 py-3">Total</th>
                <th class="text-right px-4 py-3">Caja Chica</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="v in ventas.data" :key="v.id" class="border-t hover:bg-gray-50">
                <td class="px-4 py-3">{{ fmtFecha(v.fecha) }}</td>
                <td class="px-4 py-3 font-medium">{{ v.cliente?.nombre }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    {{ v.detalles?.map(d => d.producto?.nombre).join(', ') }}
                </td>
                <td class="px-4 py-3 text-center">
                    <span :class="v.tipo_pago === 'contado'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-yellow-100 text-yellow-700'"
                          class="px-2 py-1 rounded-full text-xs capitalize">
                        {{ v.tipo_pago }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right font-mono font-bold text-blue-700">{{ fmt(v.total) }}</td>
                <td class="px-4 py-3 text-right font-mono text-purple-600">{{ fmt(v.aporte_caja_chica) }}</td>
            </tr>
            <tr v-if="!ventas.data?.length">
                <td colspan="6" class="text-center py-8 text-gray-400">No hay ventas registradas</td>
            </tr>
        </tbody>
    </table>

    <!-- Paginación -->
    <div v-if="ventas.last_page > 1" class="px-4 py-3 border-t flex gap-2 justify-end text-sm flex-wrap">
        <a v-for="p in ventas.links" :key="p.label"
           :href="p.url" v-html="p.label"
           class="px-3 py-1 rounded border"
           :class="p.active ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-600 hover:bg-gray-50'" />
    </div>
</div>
    </AuthenticatedLayout>
</template>