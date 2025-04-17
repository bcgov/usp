<template>
    <div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>Report with % Used</div>
                <button @click="exportExcel" class="btn btn-outline-success btn-sm" :disabled="loading || !hotInstance" title="Export Excel">
                    <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Export Excel
                </button>
            </div>

            <div class="card-body">
                <h2 class="h5 mb-4">Reports</h2>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Primary Model</label>
                        <select v-model="form.model" class="form-select" :disabled="loading">
                            <option value="" disabled>-- Select Model --</option>
                            <option v-for="m in models" :key="m" :value="m">{{ m }}</option>
                        </select>
                    </div>

                    <div class="col-md-6" v-if="available.includes.length">
                        <label class="form-label fw-bold">Include Relations</label>
                        <div class="overflow-auto border rounded p-2" style="max-height: 200px;">
                            <div class="form-check" v-for="rel in available.includes" :key="rel">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    :value="rel"
                                    v-model="form.includes"
                                    :id="`include-${rel}`"
                                    :disabled="loading"
                                />
                                <label class="form-check-label" :for="`include-${rel}`">
                                    {{ rel }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="available.fillables.length" class="mb-4">
                    <h5 class="fw-bold">Filters (optional)</h5>
                    <div class="row g-3 mt-1">
                        <div v-for="col in available.fillables" :key="col" class="col-md-4">
                            <label class="form-label">{{ beautifyColumnName(col) }}</label>
                            <input
                                v-model="form.filters[col]"
                                type="text"
                                :placeholder="`Filter by ${beautifyColumnName(col)}`"
                                class="form-control"
                                :disabled="loading"
                            />
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <button @click="runReport" class="btn btn-primary me-2" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Run Report
                    </button>
                </div>

                <!-- Global Search -->
                <div class="mb-3" v-if="hotInstance">
                    <input
                        type="text"
                        v-model="globalSearchQuery"
                        @input="applyGlobalSearch"
                        class="form-control"
                        placeholder="Search in report..."
                    />
                </div>

                <div ref="hotContainer" class="table-responsive"></div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100" v-if="toastMessage">
            <div class="toast show align-items-center text-bg-primary border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ toastMessage }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="toastMessage = null" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';
import * as XLSX from 'xlsx';
import Handsontable from 'handsontable';
import 'handsontable/dist/handsontable.full.min.css';

export default {
    name: 'ReportExtended',
    props: {
        results: Object|Array|null,
    },
    data() {
        return {
            models: [],
            config: '',
            form: {
                model: '',
                filters: {},
                includes: [],
            },
            columns: [],
            rows: [],
            hotInstance: null,
            loading: false,
            errorMessage: null,
            globalSearchQuery: '',
            toastMessage: null,
            toastTimer: null,
        };
    },
    computed: {
        available() {
            if (!this.form.model) {
                return { fillables: [], includes: [] };
            }
            const cfg = this.config[this.form.model];
            return {
                fillables: cfg.fillables,
                includes: cfg.includes,
            };
        },
    },
    watch: {
        'form.model'(newVal) {
            this.form.filters = {};
            this.form.includes = [];
            this.columns = [];
            this.rows = [];
            this.hotInstance?.destroy();
            this.hotInstance = null;
        },
    },
    methods: {
        beautifyColumnName(name) {
            if (!name) return '';
            return name.replace(/_/g, ' ').replace(/\s+/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        },

        runReport() {
            this.loading = true;
            this.errorMessage = null;
            this.toastMessage = null;

            axios.post('/ministry/reports/generate', this.form)
                .then(response => {
                    this.columns = response.data.columns;
                    this.rows = response.data.rows;
                    this.renderTable();
                    this.toastMessage = 'Report loaded successfully!';
                    this.autoDismissToast();

                })
                .catch(error => {
                    console.error('Report generation failed:', error);
                    this.errorMessage = 'Failed to generate the report. Please try again.';
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        exportExcel() {
            if (!this.hotInstance) {
                this.errorMessage = 'No report available to export.';
                return;
            }

            try {
                this.loading = true;
                this.errorMessage = null;

                const exportData = [
                    this.columns.map(col => col.title),
                    ...this.hotInstance.getData(),
                ];

                const worksheet = XLSX.utils.aoa_to_sheet(exportData);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Report');

                XLSX.writeFile(workbook, `report-${new Date().toISOString().slice(0,10)}.xlsx`);

                this.toastMessage = 'Excel exported successfully!';
                this.autoDismissToast();

            } catch (error) {
                console.error('Excel export failed:', error);
                this.errorMessage = 'Failed to export Excel file.';
            } finally {
                this.loading = false;
            }
        },
        renderTable() {
            if (this.hotInstance) {
                this.hotInstance.destroy();
            }

            const container = this.$refs.hotContainer;

            this.hotInstance = new Handsontable(container, {
                data: this.rows,
                colHeaders: this.columns.map(col => col.title),
                columns: this.columns.map(col => ({ data: col.field })),
                rowHeaders: true,
                height: 'auto',
                licenseKey: 'non-commercial-and-evaluation',
                manualColumnResize: true,
                manualRowResize: true,
                contextMenu: true,
                dropdownMenu: [
                    'filter_by_condition',
                    'filter_action_bar',
                    'sort_asc',
                    'sort_desc',
                ],
                filters: true,
                stretchH: 'all',
                fixedRowsTop: 1,
                readOnly: true,
                search: true,
                hiddenRows: {
                    indicators: false,
                },
                columnSorting: {
                    initialConfig: {
                        column: 0,
                        sortOrder: 'asc',
                    },
                    multiColumnSorting: true,
                    indicator: true,
                },
            });
        },
        applyGlobalSearch() {
            if (!this.hotInstance) {
                return;
            }

            const query = this.globalSearchQuery.toLowerCase();

            const data = this.hotInstance.getData();
            const matchingRowIndexes = [];

            data.forEach((row, rowIndex) => {
                const rowText = row.join(' ').toLowerCase();
                if (rowText.includes(query)) {
                    matchingRowIndexes.push(rowIndex);
                }
            });

            const totalRows = data.length;
            const hiddenRows = [];

            for (let i = 0; i < totalRows; i++) {
                if (!matchingRowIndexes.includes(i)) {
                    hiddenRows.push(i);
                }
            }

            const plugin = this.hotInstance.getPlugin('hiddenRows');
            plugin.hideRows(hiddenRows);
            plugin.showRows(matchingRowIndexes);
            this.hotInstance.render();
        },
        autoDismissToast() {
            if (this.toastTimer) {
                clearTimeout(this.toastTimer);
            }

            this.toastTimer = setTimeout(() => {
                this.toastMessage = null;
            }, 3000); // 3 seconds
        }

    },
    mounted() {
        this.models = this.results.models;
        this.config = this.results.config;
    }
};
</script>

<style scoped>
.table-responsive {
    overflow-x: auto;
}
</style>
