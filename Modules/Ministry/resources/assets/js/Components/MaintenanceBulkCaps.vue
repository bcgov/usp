<template>
    <div class="card">
        <div class="card-header">
            Bulk Institution Caps
        </div>
        <form v-if="form != null" class="card-body">
            <p class="text-muted">
                Select a Federal Cap to apply to every institution in the list below, then add one row per
                institution with its Total Attest. Allowed. New caps will replace any existing active cap for that
                institution under the selected Federal Cap.
            </p>

            <div class="row g-3 mb-4">
                <div class="col-lg-6 col-md-12">
                    <Label for="inputFedCap" class="form-label" value="Federal Cap" required="true"/>
                    <Select class="form-select" id="inputFedCap" v-model="form.fed_cap_id">
                        <option value=""></option>
                        <option v-for="f in fedCaps" :key="f.id" :value="f.id">{{ f.start_date }} - {{ f.end_date }}</option>
                    </Select>
                </div>
                <div class="col-lg-6 col-md-12">
                    <Label for="inputConfirmed" class="form-label" value="Confirmed?"/>
                    <Select class="form-select" id="inputConfirmed" v-model="form.confirmed">
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
            </div>

            <datalist id="bulkCapsInstitutions">
                <option v-for="inst in institutions" :key="inst.id" :value="inst.name"></option>
            </datalist>

            <div v-if="selectedFedCap" class="alert alert-info py-2 d-flex justify-content-between">
                <span>Federal Cap total: <strong>{{ maxAllowed }}</strong></span>
                <span>Allocated: <strong>{{ allocatedTotal }}</strong></span>
                <span :class="remainingTotal < 0 ? 'text-danger fw-bold' : ''">Remaining: <strong>{{ remainingTotal }}</strong></span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Institution</th>
                            <th style="width: 20%;">Total Attest. Allowed{{ selectedFedCap ? ' / ' + maxAllowed : '' }}</th>
                            <th style="width: 22%;">Total Reserved Graduate Attest. Allowed</th>
                            <th style="width: 8%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, i) in form.rows" :key="i">
                            <td>
                                <input type="text" class="form-control" list="bulkCapsInstitutions"
                                       placeholder="Type to search..." autocomplete="off"
                                       v-model="row.institution_name" @change="updateInstitution(i, $event)"/>
                                <div v-if="row.institution_name !== '' && row.institution_id === ''" class="text-danger mt-1">
                                    No matching institution.
                                </div>
                            </td>
                            <td>
                                <Input type="number" min="0" class="form-control"
                                       v-model="row.total_attestations" @keyup="validateRowTotal(i)"/>
                            </td>
                            <td>
                                <Input type="number" min="0" class="form-control"
                                       v-model="row.total_reserved_graduate_attestations" @keyup="validateRowGrad(i)"/>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        @click="removeRow(i)" :disabled="form.rows.length === 1">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-3">
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="addRow">
                    + Add Institution
                </button>
            </div>

            <div v-if="validationError" class="alert alert-danger">{{ validationError }}</div>

            <div v-if="form.errors != undefined && form.hasErrors" class="alert alert-danger">
                <ul class="mb-0">
                    <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
                </ul>
            </div>

            <div class="d-flex justify-content-end">
                <button @click="submitForm" type="button" class="btn btn-sm btn-success" :disabled="form.processing">
                    Create Institution Caps
                </button>
            </div>

            <FormSubmitAlert :form-state="form.formState" :success-msg="form.formSuccessMsg"
                             :fail-msg="form.formFailMsg"></FormSubmitAlert>
        </form>
    </div>
</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import { useForm } from '@inertiajs/vue3';

export default {
    name: 'MaintenanceBulkCaps',
    components: {
        Input, Label, Select, FormSubmitAlert
    },
    props: {
        fedCaps: Object,
        institutions: Object,
    },
    data() {
        return {
            form: null,
            formData: {
                formState: true,
                formSuccessMsg: 'Institution caps were created successfully.',
                formFailMsg: 'There was an error creating the caps.',
                fed_cap_id: '',
                confirmed: 'true',
                rows: [{
                    institution_name: '',
                    institution_id: '',
                    total_attestations: '',
                    total_reserved_graduate_attestations: 0,
                }],
            },
            validationError: '',
        };
    },
    computed: {
        selectedFedCap() {
            if (this.form === null || this.form.fed_cap_id === '') {
                return null;
            }
            return this.fedCaps.find(f => f.id == this.form.fed_cap_id) || null;
        },
        maxAllowed() {
            if (this.selectedFedCap === null) {
                return '';
            }
            const overAllocation = parseFloat(this.selectedFedCap.over_allocation_percentage) || 0;
            return Math.floor(this.selectedFedCap.total_attestations * (1 + overAllocation));
        },
        allocatedTotal() {
            if (this.form === null) {
                return 0;
            }
            return this.form.rows.reduce((acc, r) => acc + (parseInt(r.total_attestations) || 0), 0);
        },
        remainingTotal() {
            if (this.maxAllowed === '') {
                return '';
            }
            return this.maxAllowed - this.allocatedTotal;
        },
    },
    methods: {
        emptyRow() {
            return {
                institution_name: '',
                institution_id: '',
                total_attestations: '',
                total_reserved_graduate_attestations: 0,
            };
        },
        addRow() {
            this.form.rows.push(this.emptyRow());
        },
        removeRow(i) {
            if (this.form.rows.length > 1) {
                this.form.rows.splice(i, 1);
            }
        },
        updateInstitution(i, e) {
            const match = this.institutions.find(inst => inst.name === e.target.value);
            this.form.rows[i].institution_id = match ? match.id : '';
        },
        validateRowTotal(i) {
            if (this.maxAllowed === '') {
                return;
            }
            const remaining = this.remainingForRow(i);
            if (parseInt(this.form.rows[i].total_attestations) > remaining) {
                this.form.rows[i].total_attestations = Math.max(remaining, 0);
            }
            this.validateRowGrad(i);
        },
        remainingForRow(i) {
            if (this.maxAllowed === '') {
                return '';
            }
            const allocatedOthers = this.form.rows.reduce((acc, r, idx) => {
                if (idx === i) {
                    return acc;
                }
                return acc + (parseInt(r.total_attestations) || 0);
            }, 0);
            return this.maxAllowed - allocatedOthers;
        },
        validateRowGrad(i) {
            const total = parseInt(this.form.rows[i].total_attestations);
            const grad = parseInt(this.form.rows[i].total_reserved_graduate_attestations);
            if (!isNaN(total) && grad > total) {
                this.form.rows[i].total_reserved_graduate_attestations = total;
            }
        },
        submitForm() {
            this.validationError = '';

            if (this.form.fed_cap_id === '') {
                this.validationError = 'Please select a Federal Cap.';
                return;
            }

            const validRows = this.form.rows.filter(
                r => r.institution_id !== '' && r.total_attestations !== '' && r.total_attestations !== null
            );

            if (validRows.length === 0) {
                this.validationError = 'Please add at least one institution with a Total Attest. Allowed value.';
                return;
            }

            const instIds = validRows.map(r => r.institution_id);
            if (new Set(instIds).size !== instIds.length) {
                this.validationError = 'The same institution is listed more than once.';
                return;
            }

            const check = confirm('You are about to create ' + validRows.length +
                ' institution cap(s). Any existing active cap for these institutions under the selected Federal Cap will be replaced. Continue?');
            if (!check) {
                return;
            }

            this.form.formState = null;
            this.form
                .transform(data => ({
                    fed_cap_id: data.fed_cap_id,
                    confirmed: data.confirmed,
                    rows: validRows.map(r => ({
                        institution_id: r.institution_id,
                        total_attestations: r.total_attestations,
                        total_reserved_graduate_attestations: r.total_reserved_graduate_attestations === '' || r.total_reserved_graduate_attestations === null
                            ? 0
                            : r.total_reserved_graduate_attestations,
                    })),
                }))
                .post('/ministry/maintenance/bulk-caps', {
                    onSuccess: () => {
                        this.form.formState = true;
                        this.form.reset();
                        this.form.rows = [this.emptyRow()];
                    },
                    onError: () => {
                        this.form.formState = false;
                    },
                    preserveScroll: true,
                });
        },
    },
    mounted() {
        this.form = useForm(this.formData);
    },
};
</script>
