<style scoped>
nav.navbar {
    background-color: #003366;
    border: none;
    border-bottom: 2px solid #fcba19;
    z-index: 99;
}
nav.navbar .beta-icon{
    color: #fcba19;
    margin-top: -6px;
    text-transform: uppercase;
    font-weight: 600;
    font-size: 14px;
    margin-left: 2px;
    position: absolute;
}
</style>
<template>
    <nav class="navbar navbar-expand-lg sticky-top navbar-dark shadow">
        <div class="container-fluid">
            <Link class="navbar-brand" href="/ministry/institutions">
                <ApplicationLogo width="126" height="34" class="d-inline-block align-text-top me-3" />
                <span class="d-none d-lg-inline fw-light">BCSPA - BC Study Permit Attestation</span>
                <span aria-label="This application is currently in Beta phase" class="d-none d-lg-inline beta-icon">Beta</span>
            </Link>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                    aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav flex-row flex-wrap ms-md-auto" style="--bs-scroll-height: 100px;">

                    <li class="nav-item">
                        <NavLink class="nav-link" href="/ministry/institutions"
                                 :class="{ 'active':
                                     $page.url.indexOf('/institution') > -1  }">
                            Institutions
                        </NavLink>
                    </li>
                    <li class="nav-item">
                        <NavLink class="nav-link" href="/ministry/attestations"
                                 :class="{ 'active': $page.url.indexOf('/attestations') > -1 && $page.url.indexOf('/institutions/') === -1 }">
                            Attestations
                        </NavLink>
                    </li>
                    <li class="nav-item">
                        <NavLink class="nav-link" href="/ministry/fed_caps"
                                 :class="{ 'active': $page.url.indexOf('/fed_caps') > -1 }">
                            Federal Caps
                        </NavLink>
                    </li>
                    <li class="nav-item">
                        <NavLink class="nav-link" href="/ministry/maintenance/staff"
                                 :class="{ 'active': $page.url.indexOf('maintenance') > -1 }">
                            Maintenance
                        </NavLink>
                    </li>

                    <li class="nav-item dropdown">
                        <NavLink class="nav-link dropdown-toggle" href="#" id="navbarScrollingDropdown" role="button"
                                 data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $attrs.auth.user.first_name }}
                        </NavLink>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarScrollingDropdown">
                            <li class="dropdown-item px-4">
                                <div class="font-medium text-sm text-gray-500">{{ $attrs.auth.user.email }}</div>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li class="dropdown-item mt-3 space-y-1">
                                <div class="d-grid gap-2">
                                    <a class="text-left text-gray-600 hover:text-gray-800" :href="logoutUrl">
                                        Log Out
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>
<script>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';


export default {
    name: 'NavBar',
    components: {
        ApplicationLogo, ResponsiveNavLink, NavLink, Link
    },
    props: [],
    data() {
        return {
            showingNavigationDropdown: ref(false),
            searchType: '',
            searchData: '',
            isAdmin: ref(false),
        }
    },
    methods: {
    },
    mounted() {
        if(this.$attrs.auth.user.roles != undefined){
            for(let i=0; i<this.$attrs.auth.user.roles.length; i++)
            {
                if(this.$attrs.auth.user.roles[i].name.indexOf('Admin') > -1)
                {
                    this.isAdmin = true;
                    break;
                }
            }
        }

    },

    computed:{
        logoutUrl: function(){
            return this.$attrs.logoutUrl;
        }
    }
}
</script>
