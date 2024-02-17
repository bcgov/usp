<template>
    <div>
        <div class="min-h-screen bg-gray-100">

            <NavBar />

            <!-- Page Content -->
            <main>
                <slot />
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
        }
    },
    methods: {
        emitSearch: function (data){
            this.$emit('search', data);
        }
    },
    mounted() {

    }
}
</script>
