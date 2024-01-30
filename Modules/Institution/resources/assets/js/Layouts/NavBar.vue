<style scoped>
nav.navbar {
    background-color: #003366;
    border: none;
    border-bottom: 2px solid #fcba19;
    z-index: 99;
}
</style>
<template>
    <nav class="navbar navbar-expand-lg sticky-top navbar-dark shadow">
        <div class="container-fluid">
            <Link class="navbar-brand" href="/neb/dashboard">
                <ApplicationLogo width="126" height="34" class="d-inline-block align-text-top me-3" />
                <span class="d-none d-lg-inline">NEB - Nurses Education Bursary </span>
            </Link>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                    aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav flex-row flex-wrap ms-md-auto" style="--bs-scroll-height: 100px;">

                    <li class="nav-item">
                        <NavLink class="nav-link" href="/neb/dashboard"
                                 :class="{ 'active': $page.url.indexOf('/dashboard') > -1 ||
                            $page.url.indexOf('/bursary-period') > -1 }">
                            Bursary Periods
                        </NavLink>
                    </li>

                    <li class="nav-item">
                        <NavLink class="nav-link" href="/neb/restrictions"
                                 :class="{ 'active': $page.url.indexOf('/restrictions') > -1 }">
                            Restrictions
                        </NavLink>
                    </li>

                    <li class="nav-item">
                        <NavLink class="nav-link" href="/neb/programs"
                                 :class="{ 'active': $page.url.indexOf('/neb/programs') > -1 }">
                            NEB Programs
                        </NavLink>
                    </li>
                    <li class="nav-item">
                        <NavLink class="nav-link" href="/neb/sfas-programs"
                                 :class="{ 'active': $page.url.indexOf('/sfas-programs') > -1 }">
                            SFAS Programs
                        </NavLink>
                    </li>
                    <li v-if="isAdmin" class="nav-item">
                        <NavLink class="nav-link" href="/neb/staff"
                                 :class="{ 'active': $page.url.indexOf('/staff') > -1 }">
                            Staff
                        </NavLink>
                    </li>
                    <li class="nav-item dropdown">
                        <NavLink class="nav-link dropdown-toggle" href="#" id="navbarScrollingDropdown" role="button"
                                 data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $page.props.auth.user.user_id }}

                        </NavLink>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarScrollingDropdown">
                            <li class="dropdown-item px-4">
                                <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li class="dropdown-item mt-3 space-y-1">
                                <div class="d-grid gap-2">
                                    <ResponsiveNavLink class="text-left" href="/logout" as="button">
                                        Log Out
                                    </ResponsiveNavLink>
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
                //console.log(this.$attrs.auth.user.roles[i].name.indexOf('Admin'));
                if(this.$attrs.auth.user.roles[i].name.indexOf('Admin') > -1)
                {
                    this.isAdmin = true;
                    break;
                }
            }
        }

    },
}
</script>
