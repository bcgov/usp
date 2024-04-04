<template>
    <Head title="Attestations" />

    <AuthenticatedLayout v-bind="$attrs">

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            FAQs Search
                        </div>
                        <div class="card-body">
                            <form class="m-3">
                                <div class="row mb-3">
                                    <BreezeLabel class="col-auto col-form-label" for="inputKey" value="Search term" />
                                    <div class="col-12">
                                        <BreezeInput type="text" id="inputKey" class="form-control" v-model="filterTerm" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <button type="button" @click="filterFaq" class="btn btn-primary">
                                            Search
                                        </button>
                                        <button v-if="listFiltered" type="button" @click="clearFilter" class="btn btn-warning float-end">
                                            Clear
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card mb-3">
                        <div class="card-header">
                            FAQs
                        </div>
                        <div class="card-body">
                                <div v-if="resultSorted !== ''" class="accordion pb-3" id="accordionFaq">
                                    <div v-for="(faq, i) in resultSorted" class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse'+i" aria-expanded="false" :aria-controls="'collapse'+i">
                                                {{ faq.question }}
                                            </button>
                                        </h2>
                                        <div :id="'collapse'+i" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                                            <div class="accordion-body">
                                                {{ faq.answer }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <h1 v-else class="lead">No results</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>

</template>
<script>
import AuthenticatedLayout from '../Layouts/Authenticated.vue';
import { Link, Head } from '@inertiajs/vue3';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
export default {
    name: 'Faq',
    components: {
        AuthenticatedLayout, Head, Link, BreezeInput, BreezeLabel
    },
    props: {
        results: Object,

    },
    data() {
        return {
            filterTerm: '',
            resultSorted: '',
            listFiltered: false
        }
    },

    methods: {
        filterFaq: function (){
            this.resultSorted = this.resultSorted.filter(item => {
                return item.question.toLowerCase().includes(this.filterTerm.toLowerCase()) ||
                    item.answer.toLowerCase().includes(this.filterTerm.toLowerCase());
            });
            this.listFiltered = true;
        },
        clearFilter: function (){
            this.resultSorted = this.results;
            this.listFiltered = false;
        }
    },
    mounted() {
        this.resultSorted = this.results;
    }
}
</script>
