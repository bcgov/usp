<template>
    <div>
    <div class="card">
        <div class="card-header">
            <div>Report Sources</div>
        </div>

        <div class="card-body">
            <div class="row g-3 mb-5">
                <div class="col-md-6">
                    <label class="form-label">From:</label>
                    <Input type="date" min="2024-03-01" :max="$getFormattedDate()" placeholder="YYYY-MM-DD"
                           class="form-control" v-model="fromDate"/>
                </div>
                <div class="col-md-6">
                    <label class="form-label">To:</label>
                    <Input type="date" min="2024-03-01" :max="$getFormattedDate()" placeholder="YYYY-MM-DD"
                           class="form-control" v-model="toDate"/>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-2">
                    <a :href="'/ministry/reports/sources-download/' + fromDate + '/' + toDate + '/attestation'" target="_blank" class="btn btn-outline-success w-100">
                        <i class="bi bi-filetype-csv h1"></i><br/>Attestation</a>
                </div>
                <div class="col-md-2">
                    <a :href="'/ministry/reports/sources-download/' + fromDate + '/' + toDate + '/cap'" target="_blank" class="btn btn-outline-success w-100">
                        <i class="bi bi-filetype-csv h1"></i><br/>Cap</a>
                </div>
                <div class="col-md-2">
                    <a :href="'/ministry/reports/sources-download/' + fromDate + '/' + toDate + '/staff'" target="_blank" class="btn btn-outline-success w-100">
                        <i class="bi bi-filetype-csv h1"></i><br/>Staff</a>
                </div>
                <div class="col-md-3">
                    <a :href="'/ministry/reports/sources-download/' + fromDate + '/' + toDate + '/ircc'" target="_blank" class="btn btn-outline-success w-100">
                        <i class="bi bi-filetype-csv h1"></i><br/>IRCC</a>
                </div>
                <div class="col-md-3">
                    <a :href="'/ministry/reports/sources-download/' + fromDate + '/' + toDate + '/bi-weekly'" target="_blank" class="btn btn-outline-success w-100">
                        <i class="bi bi-filetype-csv h1"></i><br/>IRCC Bi-Weekly</a>
                </div>
            </div>

        </div>

    </div>
    </div>

</template>
<script>
import {Link} from '@inertiajs/vue3';
import Input from '@/Components/Input.vue';

export default {
    name: 'ReportsSummary',
    components: {
        Input, Link
    },
    props: {
        results: Object,
    },
    data() {
        return {
            fromDate: '',
            toDate: '',
            reportData: ''
        }
    },
    methods: {


        download: function (type) {
            let vm = this;
            let data = {
                from_date: this.fromDate,
                to_date: this.toDate,
                type: type
            }
            axios.post('/ministry/reports/sources', data)
                .then(function (response) {
                    vm.reportData = response.data.body;
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                });
        }
    },
    mounted() {
        this.toDate = this.$getFormattedDate();
        this.fromDate = this.$getFormattedDate();
    }
}

</script>
