<template>
    <div>
    <div class="card">
        <div class="card-header">
            <div>Executive Summary
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
            </div>

            <div v-if="reportData != null && reportData.publicReport != null" class="table-responsive pb-3">
                <table id="summaryReportTbl" class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">PAL Allocation</th>
                        <th scope="col">Total Issued</th>
                        <th scope="col">Total Draft</th>
                        <th scope="col">Res. Grad. Allocation</th>
                        <th scope="col">Grad. Issued</th>
                        <th scope="col">Grad. Draft</th>
                        <th scope="col">Undergrad. Issued</th>
                        <th scope="col">Undergrad. Draft</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><strong>PUBLIC INSTITUTIONS</strong></td>
                        <td><strong>{{ reportData.publicReport.total }}</strong></td>
                        <td><strong>{{ reportData.publicReport.issued}}</strong></td>
                        <td><strong>{{ reportData.publicReport.draft }}</strong></td>
                        <td><strong>{{ reportData.publicReport.total_res_grad }}</strong></td>
                        <td><strong>{{ reportData.publicReport.issued_res_grad }}</strong></td>
                        <td><strong>{{ reportData.publicReport.draft_res_grad }}</strong></td>
                        <td><strong>{{ reportData.publicReport.issued - reportData.publicReport.issued_res_grad }}</strong></td>
                        <td><strong>{{ reportData.publicReport.draft - reportData.publicReport.draft_res_grad }}</strong></td>

                    </tr>
                    <template v-for="(value, name, index) in reportData.publicReport">
                        <template v-if="typeof value === 'object'">
                        <tr>
                            <td><strong>&nbsp;{{ name }}</strong></td>
                            <td><strong>{{ value.total }}</strong></td>
                            <td><strong>{{ value.issued}}</strong></td>
                            <td><strong>{{ value.draft }}</strong></td>
                            <td><strong>{{ value.total_res_grad }}</strong></td>
                            <td><strong>{{ value.issued_res_grad}}</strong></td>
                            <td><strong>{{ value.draft_res_grad }}</strong></td>
                            <td><strong>{{ value.issued - value.issued_res_grad}}</strong></td>
                            <td><strong>{{ value.draft - value.draft_res_grad }}</strong></td>
                        </tr>
                        <tr v-for="(row, k, i) in value.instList">
                            <td>&nbsp;&nbsp;{{ k }}</td>
                            <td>{{ row.total }}</td>
                            <td>{{ row.issued }}</td>
                            <td>{{ row.draft }}</td>
                            <td>{{ row.total_res_grad }}</td>
                            <td>{{ row.issued_res_grad }}</td>
                            <td>{{ row.draft_res_grad }}</td>
                            <td>{{ row.issued - row.issued_res_grad }}</td>
                            <td>{{ row.draft - row.draft_res_grad }}</td>
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
                        <td><strong>{{ reportData.privateReport.total }}</strong></td>
                        <td><strong>{{ reportData.privateReport.issued}}</strong></td>
                        <td><strong>{{ reportData.privateReport.draft }}</strong></td>
                        <td><strong>{{ reportData.privateReport.total_res_grad }}</strong></td>
                        <td><strong>{{ reportData.privateReport.issued_res_grad }}</strong></td>
                        <td><strong>{{ reportData.privateReport.draft_res_grad }}</strong></td>
                        <td><strong>{{ reportData.privateReport.issued - reportData.privateReport.issued_res_grad }}</strong></td>
                        <td><strong>{{ reportData.privateReport.draft - reportData.privateReport.draft_res_grad }}</strong></td>

                    </tr>
                    <template v-for="(value, name, index) in reportData.privateReport">
                        <template v-if="typeof value === 'object'">
                            <tr>
                                <td><strong>&nbsp;{{ name }}</strong></td>
                                <td><strong>{{ value.total }}</strong></td>
                                <td><strong>{{ value.issued}}</strong></td>
                                <td><strong>{{ value.draft }}</strong></td>
                                <td><strong>{{ value.total_res_grad }}</strong></td>
                                <td><strong>{{ value.issued_res_grad }}</strong></td>
                                <td><strong>{{ value.draft_res_grad }}</strong></td>
                                <td><strong>{{ value.issued - value.issued_res_grad }}</strong></td>
                                <td><strong>{{ value.draft - value.draft_res_grad }}</strong></td>
                            </tr>
                            <tr v-for="(row, k, i) in value.instList">
                                <td>&nbsp;&nbsp;{{ k }}</td>
                                <td>{{ row.total }}</td>
                                <td>{{ row.issued}}</td>
                                <td>{{ row.draft }}</td>
                                <td>{{ row.total_res_grad }}</td>
                                <td>{{ row.issued_res_grad }}</td>
                                <td>{{ row.draft_res_grad }}</td>
                                <td>{{ row.issued - row.issued_res_grad }}</td>
                                <td>{{ row.draft - row.draft_res_grad }}</td>
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
                        <th scope="col">{{ reportData.publicReport.total + reportData.privateReport.total}}</th>
                        <th scope="col">{{ reportData.publicReport.issued + reportData.privateReport.issued}}</th>
                        <th scope="col">{{ reportData.publicReport.draft + reportData.privateReport.draft}}</th>
                        <th scope="col">{{ reportData.publicReport.total_res_grad  + reportData.privateReport.total_res_grad }}</th>
                        <th scope="col">{{ reportData.publicReport.issued_res_grad  + reportData.privateReport.issued_res_grad }}</th>
                        <th scope="col">{{ reportData.publicReport.draft_res_grad  + reportData.privateReport.draft_res_grad }}</th>
                        <th scope="col">{{ totalUndergradIssued }}</th>
                        <th scope="col">{{ totalUndergradDraft }}</th>
                    </tr>
                    </tfoot>
                </table>
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
    computed: {
        totalUndergradIssued() {
            if (!this.reportData || !this.reportData.publicReport || !this.reportData.privateReport) {
                return 0;
            }
            return (this.reportData.publicReport.issued + this.reportData.privateReport.issued) -
                (this.reportData.publicReport.issued_res_grad + this.reportData.privateReport.issued_res_grad);
        },
        totalUndergradDraft() {
            if (!this.reportData || !this.reportData.publicReport || !this.reportData.privateReport) {
                return 0;
            }
            return (this.reportData.publicReport.draft + this.reportData.privateReport.draft) -
                (this.reportData.publicReport.draft_res_grad + this.reportData.privateReport.draft_res_grad);
        }
    },
    methods: {
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
            axios.post('/ministry/reports/summary', data)
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
        //this.reportData = this.results;
        this.toDate = this.$getFormattedDate();
        this.fromDate = this.$getFormattedDate();
        this.submitForm();
    }
}

</script>
