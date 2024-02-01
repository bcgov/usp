<template>


    <form @submit.prevent="nameFormSubmit" class="m-3">
                <div class="row mb-3">
                    <BreezeLabel class="col-auto col-form-label" for="inputName" value="Name" />
                    <div class="col-auto">
                        <BreezeInput type="text" id="inputName" class="form-control" v-model="nameForm.filter_name" />
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
    filter_name: '',
    filter_type: props.ftype ?? 'active',
};
const nameForm = useForm(nameFormTemplate);
const nameFormSubmit = () => {
    nameForm.get('/ministry/institutions', {
        onFinish: () => nameForm.reset('inputName'),
    });
};


</script>
