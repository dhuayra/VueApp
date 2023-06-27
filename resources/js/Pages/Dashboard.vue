<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import axios from 'axios';

const onGenerateToken = async () => {
    // await axios.post('/api/generate/',new_token.value).then(resp=>{
    //     if(resp.data.status == false){
    //                 toast.error(resp.data.message)
    //             }else {
    //                 toast.success(resp.data.message)
    //             }
    //             isOpen.value = false
    //             setTokens(user.value.id)
    //             isGenerate.value = false
    // })
    alert('hola222');
}

// const onGenerateToken = () => {
//     alert('Hola');
// };

onMounted(() => {
    
});

</script>


<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Datos - 
                        <button @click="onGenerateToken" type="button" class="btn btn-sm btn-outline-primary">Generar</button>
                    </div>
                
                    <div>
                        <div class="card">
                            <div class="form-control">
                                <label for="" class="">Token: </label>
                                <TextInput type="text" readonly />
                            </div>
                        </div>
                    </div>

                    <div>
                        <!-- <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        /> -->
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
