<template>
    <form @submit.prevent="nameFormSubmit" class="m-3">
        <div class="row mb-3">
            <BreezeLabel class="col-auto col-form-label" for="inputFirstName" value="Search term" />
            <div class="col-12">
                <BreezeInput type="text" id="inputFirstName" class="form-control" v-model="nameForm.filter_term" autocomplete="off" />
            </div>
        </div>
        <div class="row mb-3">
            <BreezeLabel class="col-auto col-form-label" for="inputLastName" value="Search type" />
            <div class="col-12">
                <BreezeSelect id="inputType" class="form-control" v-model="nameForm.filter_type">
                    <option value="snumber">Student Number</option>
                    <option value="fname">First Name</option>
                    <option value="lname">Last Name</option>
                    <option value="travel_id">Travel ID</option>
                    <option value="pal_id">PAL #</option>
                    <option value="city">City</option>
                    <option value="country">Country</option>
                </BreezeSelect>
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
import BreezeSelect from '@/Components/Select.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeButton from '@/Components/Button.vue';

import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    ftype: [String, null],
});


let searchType = ref('byName');

const nameFormTemplate = {
    filter_term: '',
    filter_type: 'student_number',
};
const nameForm = useForm(nameFormTemplate);
const nameFormSubmit = () => {
    nameForm.get('/institution/attestations', {
        onFinish: () => nameForm.reset('filter_term', 'filter_type'),
    });
};


</script>
