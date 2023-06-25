<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    departments: {type:Object}
});
const form = useForm({
    id: ''
});
const deleteDepartment = (id, name) =>{
    if (confirm("Eliminar?") == true) {
        form.delete(route('departments.destroy', id));
        //alert('eliminiado');
    } else {
        //alert('cancelado');
    }
}
const importarTxT = () =>{
    // form.post(route('departments,importar'));
    alert('ff');
}
</script>

<template>
    <Head title="Departments" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista de Departamentos</h2>
        </template>

        <div class="py-12">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <Link :href="route('departments.create')" :class="'bg-black'" >Add</Link>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>name</th>
                                        <th colspan="2">Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="dep, i in departments" :key="dep.id">
                                        <td>{{ (i+1) }}</td>
                                        <td>{{ dep.name }}</td>
                                        <td>
                                            <div class="btn btn-primary btn-sm">
                                                <Link :href="route('departments.edit', dep.id)">Edit</Link>
                                            </div>
                                            
                                        </td>
                                        <td>
                                            <PrimaryButton @click="$event => deleteDepartment(dep.id, dep.name)">Del</PrimaryButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                    </div>
                </div>
                <PrimaryButton @click="importarTxT">Imp Txt</PrimaryButton>
                <!-- <Link :href="route('departments.importar')">Imp</Link> -->
                <label >Aqui: {{ $var }}</label>
        </div>

    </AuthenticatedLayout>
</template>
