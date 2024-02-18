<template>
    <div class="card mb-3">
        <div class="card-header">
            <div>Federal Cap Institutions <strong v-if="fedcap != ''">{{fedcap.start_date}} - {{fedcap.end_date}}</strong>. Cap size: {{ fedcap.total_attestations }}</div>
        </div>

        <div class="card-body">
            <div v-if="fedcap != ''" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col" style="min-width: 120px;">
                            <span>Institution</span>
                        </th>
                        <th scope="col" style="min-width: 120px;">
                            <span>Total Atte.</span>
                        </th>
                        <th scope="col" style="min-width: 80px;">
                            <span>Issued Atte.</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, i) in fedcap.institution_caps">
                        <td><Link :href="'/ministry/institutions/' + row.inst_id">{{ row.inst_name }}</Link></td>
                        <td>{{ row.total_attestations }}</td>
                        <td>{{ row.issued_attestations}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No results</h1>
        </div>
    </div>

</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import { Link, useForm } from '@inertiajs/vue3';

export default {
    name: 'FedCapInstitutions',
    components: { Link },
    props: {
        results: Object,
    },
    data() {
        return {
            fedcap: '',
        }
    },
    methods: {
        fetchFedCapInstitutions: function () {
            let vm = this;
            let data = {
                fed_cap_guid: this.results.guid,
            }
            axios.post('/ministry/api/fetch/fedcap_inst', data)
                .then(function (response) {
                    vm.fedcap = response.data.body;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    },
    mounted() {
        this.fetchFedCapInstitutions();
    }
}
</script>
