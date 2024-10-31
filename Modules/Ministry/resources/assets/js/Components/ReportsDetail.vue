<template>
    <div>
        <div class="card">
            <div class="card-header">
                <div>Report with % Used
                    <button @click="exportCsv" class="btn btn-outline-success btn-sm float-end me-1" title="Export Attestations"><i class="bi bi-filetype-csv"></i></button>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">From:</label>
                        <Input type="date" min="2024-03-01" :max="$getFormattedDate()" placeholder="YYYY-MM-DD"
                               class="form-control" v-model="fromDate"/>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">To:</label>
                        <Input type="date" min="2024-03-01" :max="$getFormattedDate()" placeholder="YYYY-MM-DD"
                               class="form-control" v-model="toDate"/>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-success w-100" @click="submitForm">Refresh</button>
                    </div>
                    <div v-if="toDate !== $getFormattedDate() || fromDate !== $getFormattedDate()" class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-success w-100" @click="clearForm">Clear</button>
                    </div>

                    <div class="d-none col-12 accordion" id="accordionFlushExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                    Filters
                                </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <div class="row justify-content-between">
                                        <div class="col-6">
                                            <div class="list-group overflow-box" id="list-tab-institutions" role="tablist">
                                                <button type="button" class="list-group-item list-group-item-action" disabled><strong>Institutions</strong></button>
                                                <template v-for="inst in results.institutions">
                                                    <button @click="updateInst(inst.name,inst.category)" type="button" class="list-group-item list-group-item-action" :class="filterInst === inst.name ? 'active' : ''">
                                                    {{ inst.name }}</button>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="list-group" id="list-tab-categories" role="tablist">
                                                <button type="button" class="list-group-item list-group-item-action" disabled><strong>Categories</strong></button>
                                                <template v-for="cat in results.categories">
                                                    <button @click="updateCat(cat.category)" type="button" class="list-group-item list-group-item-action" :class="filterCat === cat.category ? 'active' : ''">
                                                    {{ cat.category }}</button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="filteredReport != null && filteredReport.publicReport != null" class="table-responsive pb-3">
                    <table id="summaryReportTbl" class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">PAL Allocation</th>
                            <th scope="col">Total Issued</th>
                            <th scope="col">Total Draft</th>
                            <th scope="col">% Used</th>
                            <th scope="col">PAL Res. Grad. Allocation</th>
                            <th scope="col">Total Res. Grad. Issued</th>
                            <th scope="col">Total Res. Grad. Draft</th>
                            <th scope="col">% Res. Grad. Used</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><strong>PUBLIC INSTITUTIONS</strong></td>
                            <td><strong>{{ filteredReport.publicReport.total }}</strong></td>
                            <td><strong>{{ filteredReport.publicReport.issued }}</strong></td>
                            <td><strong>{{ filteredReport.publicReport.draft }}</strong></td>
                            <td><strong>{{ roundIt(filteredReport.publicReport) }}%</strong></td>
                            <td><strong>{{ filteredReport.publicReport.total_res_grad }}</strong></td>
                            <td><strong>{{ filteredReport.publicReport.issued_res_grad }}</strong></td>
                            <td><strong>{{ filteredReport.publicReport.draft_res_grad }}</strong></td>
                            <td><strong>{{ roundResGradIt(filteredReport.publicReport) }}%</strong></td>

                        </tr>
                        <template v-for="(value, name, index) in filteredReport.publicReport">
                            <template v-if="typeof value === 'object'">
                                <tr>
                                    <td><strong>&nbsp;{{ name }}</strong></td>
                                    <td><strong>{{ value.total }}</strong></td>
                                    <td><strong>{{ value.issued }}</strong></td>
                                    <td><strong>{{ value.draft }}</strong></td>
                                    <td><strong>{{ (value.issued / value.total * 100).toFixed(2) }}%</strong></td>
                                    <td><strong>{{ value.total_res_grad }}</strong></td>
                                    <td><strong>{{ value.issued_res_grad}}</strong></td>
                                    <td><strong>{{ value.draft_res_grad }}</strong></td>
                                    <td><strong>{{ (value.issued_res_grad / value.total_res_grad * 100).toFixed(2) }}%</strong></td>
                                </tr>
                                <tr v-for="(row, k, i) in value.instList">
                                    <td>&nbsp;&nbsp;{{ k }}</td>
                                    <td>{{ row.total }}</td>
                                    <td>{{ row.issued }}</td>
                                    <td>{{ row.draft }}</td>
                                    <td>{{ roundIt(row) }}%</td>
                                    <td>{{ row.total_res_grad }}</td>
                                    <td>{{ row.issued_res_grad }}</td>
                                    <td>{{ row.draft_res_grad }}</td>
                                    <td>{{ roundResGradIt(row) }}%</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </template>
                        </template>
                        </tbody>

                        <tfoot>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                        </tfoot>

                        <tbody>
                        <tr>
                            <td><strong>PRIVATE INSTITUTIONS</strong></td>
                            <td><strong>{{ filteredReport.privateReport.total }}</strong></td>
                            <td><strong>{{ filteredReport.privateReport.issued }}</strong></td>
                            <td><strong>{{ filteredReport.privateReport.draft }}</strong></td>
                            <td><strong>{{ roundIt(filteredReport.privateReport) }}%</strong></td>
                            <td><strong>{{ filteredReport.privateReport.total_res_grad }}</strong></td>
                            <td><strong>{{ filteredReport.privateReport.issued_res_grad }}</strong></td>
                            <td><strong>{{ filteredReport.privateReport.draft_res_grad }}</strong></td>
                            <td><strong>{{ roundResGradIt(filteredReport.privateReport) }}%</strong></td>

                        </tr>
                        <template v-for="(value, name, index) in filteredReport.privateReport">
                            <template v-if="typeof value === 'object'">
                                <tr>
                                    <td><strong>&nbsp;{{ name }}</strong></td>
                                    <td><strong>{{ value.total }}</strong></td>
                                    <td><strong>{{ value.issued }}</strong></td>
                                    <td><strong>{{ value.draft }}</strong></td>
                                    <td><strong>{{ roundIt(value) }}%</strong></td>
                                    <td><strong>{{ value.total_res_grad }}</strong></td>
                                    <td><strong>{{ value.issued_res_grad  }}</strong></td>
                                    <td><strong>{{ value.draft_res_grad  }}</strong></td>
                                    <td><strong>{{ roundResGradIt(value) }}%</strong></td>
                                </tr>
                                <tr v-for="(row, k, i) in value.instList">
                                    <td>&nbsp;&nbsp;{{ k }}</td>
                                    <td>{{ row.total }}</td>
                                    <td>{{ row.issued }}</td>
                                    <td>{{ row.draft }}</td>
                                    <td>{{ roundIt(row) }}%</td>
                                    <td>{{ row.total_res_grad  }}</td>
                                    <td>{{ row.issued_res_grad  }}</td>
                                    <td>{{ row.draft_res_grad  }}</td>
                                    <td>{{ roundResGradIt(row) }}%</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </template>
                        </template>
                        </tbody>


                        <tfoot>
                        <tr>
                            <th scope="col">Grand Total</th>
                            <th scope="col">{{ filteredReport.publicReport.total + filteredReport.privateReport.total }}</th>
                            <th scope="col">{{ filteredReport.publicReport.issued + filteredReport.privateReport.issued }}</th>
                            <th scope="col">{{ filteredReport.publicReport.draft + filteredReport.privateReport.draft }}</th>
                            <th scope="col">{{ roundGrandTotal(filteredReport.publicReport, filteredReport.privateReport) }}%</th>
                            <th scope="col">{{ filteredReport.publicReport.total_res_grad + filteredReport.privateReport.total_res_grad }}</th>
                            <th scope="col">{{ filteredReport.publicReport.issued_res_grad + filteredReport.privateReport.issued_res_grad }}</th>
                            <th scope="col">{{ filteredReport.publicReport.draft_res_grad + filteredReport.privateReport.draft_res_grad }}</th>
                            <th scope="col">{{ roundResGradGrandTotal(filteredReport.publicReport, filteredReport.privateReport) }}%</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

        </div>
    </div>

</template>
<style scoped>
.overflow-box{
    max-height: 250px;
    overflow-y: auto;
}
</style>
<script>

import {Link} from '@inertiajs/vue3';
import Input from '@/Components/Input.vue';

export default {
    name: 'ReportsDetail',
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
            filteredReport: {},
            reportData: '',
            filterInst: '',
            filterCat: ''
        }
    },
    methods: {
        roundIt: function (report) {
            if(report.total === 0) return 0;
            return (report.issued / report.total * 100).toFixed(2);
        },
        roundResGradIt: function (report) {
            if(report.total_res_grad === 0) return 0;
            return (report.issued_res_grad / report.total_res_grad * 100).toFixed(2);
        },
        roundGrandTotal: function (publicReport, privateReport) {
            let total = publicReport.total + privateReport.total;
            let issued = publicReport.issued + privateReport.issued;
            if(total.total === 0) return 0;
            return (issued / total * 100).toFixed(2);
        },
        roundResGradGrandTotal: function (publicReport, privateReport) {
            let total = publicReport.total_res_grad + privateReport.total_res_grad;
            let issued = publicReport.issued_res_grad + privateReport.issued_res_grad;
            if(total.total === 0) return 0;
            return (issued / total * 100).toFixed(2);
        },
        updateCat: function (cat){
            this.filterCat = this.filterCat === cat ? '' : cat;
        },
        updateInst: function (name,cat) {
            this.filterInst = this.filterInst === name ? '' : name;
            this.filteredReport.publicReport = {
                [cat]: this.filteredReport.publicReport[cat].instList[name]
            }

        },
        exportCsv: function (){
            const table = document.getElementById("summaryReportTbl");

            // Initialize CSV string
            let csv = "";

            // Iterate through table rows
            for (let i = 0; i < table.rows.length; i++) {
                const row = table.rows[i];

                // Iterate through table cells
                for (let j = 0; j < row.cells.length; j++) {
                    // Append cell value to CSV string
                    const cellValue = this.cleanCellValue(row.cells[j].innerHTML);
                    // csv += cellValue + ",";
                    csv += '"' + cellValue.replace(/"/g, '""') + '",';
                }

                // Remove trailing comma and add line break
                csv += "\n";
            }

            // Trigger file download
            this.downloadCSV(csv, "table_data.csv");
        },
        cleanCellValue: function(value) {
            // Remove HTML tags and entities using regular expression
            return value.replace(/<[^>]+>/g, '').replace(/&nbsp;/g, '').trim();
        },
        downloadCSV: function(csv, filename) {
            const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
            if (navigator.msSaveBlob) {
                // For IE
                navigator.msSaveBlob(blob, filename);
            } else {
                const link = document.createElement("a");
                if (link.download !== undefined) {
                    // For other browsers
                    const url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", filename);
                    link.style.visibility = "hidden";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        },
        clearForm: function () {
            this.toDate = this.$getFormattedDate();
            this.fromDate = this.$getFormattedDate();
            this.submitForm();
        },

        submitForm: function () {
            let vm = this;
            let data = {
                from_date: this.fromDate,
                to_date: this.toDate,
            }
            axios.post('/ministry/reports/detail', data)
                .then(function (response) {
                    vm.reportData = response.data.body;
                    vm.filteredReport = vm.reportData;
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                });
        }
    },
    mounted() {
        //this.reportData = this.results;
        this.toDate = this.$getFormattedDate();
        this.fromDate = this.$getFormattedDate();
        this.submitForm();
    }
}

</script>
