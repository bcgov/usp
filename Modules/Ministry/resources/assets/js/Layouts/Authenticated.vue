<template>
    <div>

        <div class="min-h-screen bg-gray-100">
            <NavBar @update="updateFCap" v-bind="$attrs" />

            <main>
                <div class="mt-3">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 mt-3 ">

                                <slot />

                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>

    </div>
</template>
<script>
import { ref } from 'vue';
import NavBar from "./NavBar";

export default {
    name: 'Authenticated',
    components: {
        NavBar
    },
    props: [],
    data() {
        return {
            showingNavigationDropdown: ref(false),
            searchType: '',
            searchData: '',
            updateFedCap: ref(false),
        }
    },
    methods: {
        emitSearch: function (data){
            this.$emit('search', data);
        },
        updateFCap() {
            this.updateFedCap = !this.updateFedCap;

            //dispatch a global event
            window.dispatchEvent(new CustomEvent('update-fed-cap', {
                detail: { updateFedCap: this.updateFedCap }
            }));
        }
    },
    mounted() {
    }
}
</script>
