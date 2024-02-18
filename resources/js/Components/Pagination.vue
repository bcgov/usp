<template>
    <div v-if="links.length > 3">
        <div class="d-flex flex-row justify-content-center -mb-1">
            <template v-for="(link, key) in links">
                <div v-if="link.url === null" :key="key" class="btn btn-light border m-1" v-html="link.label" />
                <Link v-else :key="`link-${key}`" class="btn btn-light btn-link border m-1" :class="{ 'disabled': (link.label == activePage) }" :href="link.url" v-html="link.label" />
            </template>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },
    props: {
        links: Array,
        activePage: Number,
    },
}
</script>
