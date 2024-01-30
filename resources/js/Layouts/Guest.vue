<script>
import ApplicationLoginLogo from '@/Components/ApplicationLoginLogo.vue';

export default {
    name: 'GuestLayout',
    components: {
        ApplicationLoginLogo
    }
}
</script>
<style scoped>
.bg-bc-gov {
	background: none;
	background-color: #036;
	font-family: 'BCSans', 'Noto Sans', Verdana, Arial, sans-serif;
	color: #036;
}
</style>
<template>
	<div class="min-h-screen text-center flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-bc-gov">
		<h2 class="text-center text-white">Undergraduate Study Permit Attestation</h2>

		<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
			<div class="inline-flex mb-5">
				<a href="/login">
					<ApplicationLoginLogo class="inline-flex" />
				</a>
			</div>

			<slot />
		</div>
	</div>
</template>
