<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/DangerButton.vue';
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
        alert('eliminiado');
    } else {
        alert('cancelado');
    }
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
                        <Link :href="route('departments.create')" :class="'px-4 btn btn-info'" >Add</Link>
                            <table class="border">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>name</th>
                                        <th colspan="2">Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="dep, i in departments" :key="dep.id">
                                        <td class="border border-gray-400 px-4 py-4">{{ (i+1) }}</td>
                                        <td class="border border-gray-400 px-4 py-4">{{ dep.name }}</td>
                                        <td class="border border-gray-400 px-4 py-4">
                                            <Link :href="route('departments.edit', dep.id)">Edit</Link>
                                        </td>
                                        <td class="border border-gray-400 px-4 py-4">
                                            <PrimaryButton @click="$event => deleteDepartment(dep.id, dep.name)">Del</PrimaryButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                    </div>
                </div>
        </div>

    </AuthenticatedLayout>
</template>
