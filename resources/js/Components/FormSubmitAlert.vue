<script>

export default {
    name: 'FormSubmitAlert',
    components: {
    },
    props: {
        formState: Boolean|String,
        successMsg: {
            type: [String, null],
            default: null,
        },
        failMsg: {
            type: [String, null],
            default: null,
        },
    },
    data() {
        return {
            showHide: false,
            successMessage: '',
            failMessage: '',
        }
    },
    mounted() {
        this.successMessage = this.successMsg == null ? 'Form was submitted successfully.' : this.successMsg;
        this.failMessage = this.failMsg == null ? 'There was an error submitting this form.' : this.failMsg;
    },
    watch: {
        formState: function (newVal, oldVal) {
            let vm = this;
            if (newVal != null) {
                vm.showHide = true;
                setTimeout(function () {
                    vm.showHide = false;
                }, 2500);
            }
        },
        failMsg: function (newVal, oldVal) {
            this.failMessage = newVal == null ? 'There was an error submitting this form.' : newVal;
        }
    },

}
</script>

<template>
    <div v-if="showHide">
        <div v-if="formState" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="updateSuccessAlert" class="alert alert-success alert-dismissible fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="100">
                <div class="">
                    <div class="toast-body">{{ successMessage }}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <div v-if="formState === false" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="updateFailAlert" class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="100">
                <div class="">
                    <div class="toast-body">{{ failMessage }}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>

</template>
