<template xmlns="http://www.w3.org/1999/html">
    <div>
    <div class="card mb-3">
        <div class="card-header">
            <div>FAQs
                <button @click="newFaq" type="button" class="btn btn-success btn-sm float-end">New FAQ</button>
            </div>
        </div>
        <div class="card-body">

            <div v-if="results != null && results.length > 0" class=" pb-3">
                <table class="table table-striped">
                    <thead>
                        <tr class="row">
                            <th class="col-5" scope="col">Question</th>
                            <th class="col-5" scope="col">Answer</th>
                            <th class="col-1" scope="col">Active</th>
                            <th class="col-1" scope="col">Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, i) in results" class="row">
                            <td class="col-5">
                                <button @click="editFaq(i)" type="button" class="btn btn-link text-start" data-bs-toggle="modal" data-bs-target="#editFaqModal">{{ row.question }}</button>
                            </td>
                            <td class="col-5">{{ row.answer }}</td>
                            <td class="col-1">
                                <span v-if="row.active_status" class="badge rounded-pill text-bg-success">True</span>
                                <span v-else class="badge rounded-pill text-bg-danger">False</span>
                            </td>
                            <td class="col-1">{{ row.order }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No FAQs</h1>
        </div>
    </div>


    <div class="modal modal-lg fade" id="newFaqModal" tabindex="-1" aria-labelledby="newFaqModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newFaqModalLabel">New Faq</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form v-if="newFaqForm != null" @submit.prevent="submitNewFaq">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-12">
                                    <BreezeLabel for="newFaqQuestion" class="form-label" value="Question" />
                                    <BreezeInput type="text" class="form-control" id="newFaqQuestion" v-model="newFaqForm.question" />
                                </div>
                                <div class="col-12">
                                    <BreezeLabel for="newFaqAnswer" class="form-label" value="Answer" />
                                    <textarea class="form-control" id="newFaqAnswer"
                                              v-model="newFaqForm.answer"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <BreezeLabel for="newFaqOrder" class="form-label" value="Order" />
                                    <BreezeInput type="number" class="form-control" id="newFaqOrder" v-model="newFaqForm.order" />
                                </div>

                                <div class="col-md-6">
                                    <BreezeLabel for="newFaqActiveFlag" class="form-label" value="Active?" />
                                    <BreezeSelect class="form-select" id="newFaqActiveFlag" v-model="newFaqForm.active_status">
                                        <option value="false">False</option>
                                        <option value="true">True</option>
                                    </BreezeSelect>
                                </div>

                            </div>

                            <div v-if="newFaqForm.errors != undefined" class="row">
                                <div class="col-12">
                                    <div v-if="newFaqForm.hasErrors == true" class="alert alert-danger mt-3">
                                        <ul>
                                            <li v-for="err in newFaqForm.errors"><small>{{ err }}</small></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn me-2 btn-outline-success" :disabled="newFaqForm.processing">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- end new util -->
    <FormSubmitAlert :form-state="newFaqForm.formState"></FormSubmitAlert>

    <div class="modal modal-lg fade" id="editFaqModal" tabindex="-1" aria-labelledby="editFaqModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFaqModalLabel">Edit {{ editFaqForm.field_type }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form v-if="editFaqForm != null" @submit.prevent="submitEditFaq">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-12">
                                    <BreezeLabel for="newFaqQuestion" class="form-label" value="Question" />
                                    <BreezeInput type="text" class="form-control" id="newFaqQuestion" v-model="editFaqForm.question" />
                                </div>
                                <div class="col-12">
                                    <BreezeLabel for="newFaqAnswer" class="form-label" value="Answer" />
                                    <textarea class="form-control" id="newFaqAnswer"
                                              v-model="editFaqForm.answer"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <BreezeLabel for="newFaqOrder" class="form-label" value="Order" />
                                    <BreezeInput type="number" class="form-control" id="newFaqOrder" v-model="editFaqForm.order" />
                                </div>

                                <div class="col-md-6">
                                    <BreezeLabel for="newFaqActiveFlag" class="form-label" value="Active?" />
                                    <BreezeSelect class="form-select" id="newFaqActiveFlag" v-model="editFaqForm.active_status">
                                        <option value="false">False</option>
                                        <option value="true">True</option>
                                    </BreezeSelect>
                                </div>

                            </div>

                            <div v-if="editFaqForm.errors != undefined" class="row">
                                <div class="col-12">
                                    <div v-if="editFaqForm.hasErrors == true" class="alert alert-danger mt-3">
                                        <ul>
                                            <li v-for="err in editFaqForm.errors"><small>{{ err }}</small></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn me-2 btn-outline-success" :disabled="editFaqForm.processing">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- end edit util -->
    <FormSubmitAlert :form-state="editFaqForm.formState"></FormSubmitAlert>

</div>
</template>
<script>
import {Link, useForm} from '@inertiajs/vue3';
import BreezeInput from '@/Components/Input.vue';
import FormSubmitAlert from "@/Components/FormSubmitAlert";
import BreezeSelect from "@/Components/Select";
import BreezeLabel from "@/Components/Label";

export default {
    name: 'MaintenanceFaq',
    components: {
        BreezeInput, Link, FormSubmitAlert, BreezeSelect, BreezeLabel
    },
    props: {
        results: Object,
        categories: Object
    },
    data() {
        return {
            newFaqForm: '',
            newFaqFormData: {
                formState: '',
                question:'', answer: '', order: 0, active_status: false,
            },
            editFaqForm: '',
        }
    },
    methods: {
        editFaq: function (index) {
            this.editFaqForm = useForm(this.results[index]);
            this.editFaqForm.formState = '';
        },

        submitEditFaq: function () {
            this.editFaqForm.formState = '';
            this.editFaqForm.put('/ministry/maintenance/faqs/' + this.editFaqForm.id, {
                onSuccess: (response) => {
                    $("#editFaqModal").modal('hide');
                    this.editFaqForm.formState = true;
                },
                onError: () => {
                    this.editFaqForm.formState = false;
                },
                preserveState: true,
                preserveScroll: true,
            });
        },

        newFaq: function (type) {
            $("#newFaqModal").modal('show');
            this.newFaqForm = useForm(this.newFaqFormData);
        },
        submitNewFaq: function ()
        {
            this.newFaqForm.formState = '';
            this.newFaqForm.post('/ministry/maintenance/faqs', {
                onSuccess: (response) => {
                    $("#newFaqModal").modal('hide');
                    this.newFaqForm.reset(this.newFaqFormData);
                    this.newFaqForm.formState = true;

                },
                onError: () => {
                    this.newFaqForm.formState = false;
                },
                preserveState: true,
                preserveScroll: true,
            });
        },
    },
}

</script>
