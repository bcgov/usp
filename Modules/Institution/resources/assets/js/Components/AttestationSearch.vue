<template>
    <form @submit.prevent="nameFormSubmit" class="m-3">
        <div class="row mb-3">
            <BreezeLabel class="col-auto col-form-label" for="inputFirstName" value="First Name" />
            <div class="col-12">
                <BreezeInput type="text" id="inputFirstName" class="form-control" v-model="nameForm.filter_first_name" autocomplete="off" />
            </div>
        </div>
        <div class="row mb-3">
            <BreezeLabel class="col-auto col-form-label" for="inputLastName" value="Last Name" />
            <div class="col-12">
                <BreezeInput type="text" id="inputLastName" class="form-control" v-model="nameForm.filter_last_name" autocomplete="off" />
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-auto">
                <BreezeButton class="btn btn-primary" :class="{ 'opacity-25': nameForm.processing }" :disabled="nameForm.processing">
                    Search
                </BreezeButton>
            </div>
        </div>
    </form>
</template>
<script setup>
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeButton from '@/Components/Button.vue';

import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    ftype: [String, null],
});


let searchType = ref('byName');

const nameFormTemplate = {
    filter_first_name: '',
    filter_last_name: '',
    filter_type: props.ftype ?? 'active',
};
const nameForm = useForm(nameFormTemplate);
const nameFormSubmit = () => {
    nameForm.get('/institution/attestations', {
        onFinish: () => nameForm.reset('inputFirstName', 'inputLastName'),
    });
};


</script>
