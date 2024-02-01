<style scoped>
nav.navbar {
    background-color: #003366;
    border: none;
    border-bottom: 2px solid #fcba19;
    z-index: 99;
}
</style>
<template>
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
</template>
<script>
export default {
    name: 'BodyContainer',
    components: {

    },
    props: [],
    data() {
        return {
        }
    },
    methods: {
    },
    mounted() {

    },
}
</script>
