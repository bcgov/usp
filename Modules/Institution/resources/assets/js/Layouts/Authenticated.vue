<template>
    <div>
        <div v-if="$attrs.auth.isAdmin == false || $attrs.auth.disabled == true" class="card text-center">
            <h2 class="card-body">You are not authorized.</h2>
        </div>
        <div v-else class="min-h-screen bg-gray-100">
            <NavBar v-bind="$attrs" />

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
import NavBar from "../Layouts/NavBar";

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
        }
    },
    methods: {
        emitSearch: function (data) {
            this.$emit('search', data);
        }
    },
    mounted() {

    }
}
</script>
