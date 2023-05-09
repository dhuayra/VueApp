<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {nextTick, ref} from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Swal from 'sweetalert2';
import SelectInput from '@/Components/SelectInput.vue';
import WarningButton from '@/Components/WarningButton.vue';
import Modal from '@/Components/Modal.vue';
import vueTailwindPagination from '@ocrv/vue-tailwind-pagination';


 const nameInput = ref(null);
 const modal = ref(false);
 const title = ref('');
 const operation = ref(1);
 const id = ref('');


const props = defineProps({
    employees: {type:Object},
    departments: {type:Object},
});

const form = useForm({
    id:'', name: '', email: '', phone:'', department_id:'',
});
const formPage = useForm({});
const onPageClick=(event)=>{
    formPage.get(route('employees.index', {page:event}));
}
const openModal = (op, name, email, phone, department, employee)=>{
    modal.value=true;
    nextTick( () => nameInput.value.focus());
    operation.value = op;
    id.value = employee;
    if (op ==1) {
        title.value = 'Crear Empleado';

    }else{
        title.value = 'Edit Empleado';
        form.name = name;
        form.email = email;
        form.phone = phone;
        form.department_id = department;
    }
}
const closeModal = () =>{
    modal.value = false;
    form.reset();
}

const save = ()=>{
    if (operation.value == 1) {
        form.post(route('employees.store'),{
            onSuccess: () => (ok('Employee Created'))
        });
    }else{
        form.put(route('employees.update', id.value),{
            onSuccess: () => (ok('Employee Created'))
        });
    }
}
const ok = (msj) => {
    form.reset();
    closeModal();
    alert('bien');
}

const deleteEmployee = (id, name) =>{
    if (confirm("Eliminar?") == true) {
        form.delete(route('employees.destroy', id));
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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista de Empleados</h2>
        </template>

        <div class="py-12">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <PrimaryButton
                            @click="event=>openModal(1)">Add</PrimaryButton>
                            <table class="border">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>NAME</th>
                                        <th>EMAIL</th>
                                        <th>PHONE</th>
                                        <th>DEPARTMENT</th>
                                        <th colspan="2">Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="emp, i in employees" :key="emp.id">
                                        <td class="px-4 py-4">{{ (i+1) }}</td>
                                        <td class="px-4 py-4">{{ emp.name }}</td>
                                        <td class="px-4 py-4">{{ emp.email }}</td>
                                        <td class="px-4 py-4">{{ emp.phone }}</td>
                                        <td class="px-4 py-4">{{ emp.department }}</td>
                                        <td class="px-4 py-4">
                                            <WarningButton @click="openModal(2, emp.name, emp.email, emp.phone, emp.department_id, emp.id)" >Edit</WarningButton>
                                        </td>
                                        <td class="px-4 py-4">
                                            <DangerButton @click="$event => deleteEmployee(emp.id)">Del</DangerButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                    </div>
                </div>
                <div class="bg-withe overflow-hidden shadow-sm ">
                    <vueTailwindPagination :current="employees.currentPage" :total="employees.total"
                    :per-page="employees.perPage"
                    @page-changed="$event=>onPageClick($event)">
                    </vueTailwindPagination>
                </div>
        </div>
        <Modal :show="modal" @close="closeModal">
            <h2 class="p-3 text-lg">{{  title }}</h2>
            <div class="p-3 mt-6">
                <InputLabel for="name" value="Nombre" />
                <TextInput id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="p-3">
                <InputLabel for="department_id" value="Departamento" />
                <SelectInput id="department_id"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.department_id"
                    required
                    :options="departments"
                />
                <InputError class="mt-2" :message="form.errors.department_id" />
            </div>
            <div class="p-3 mt-6">
                <PrimaryButton :diabled="form.processing" @click="save">Guardar</PrimaryButton>
            </div>
            <div class="p-3 mt-6 flex justify-end">
                <SecondaryButton :diabled="form.processing" @click="closeModal">cancelar</SecondaryButton>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>
