<script setup>
import Header from '@/Components/Header.vue';
import { Toaster } from '@steveyuowo/vue-hot-toast';

</script>

<template>
    <div>
        <Header />
    </div>
    <div class="guestLayout"  >
     <slot />
    <Toaster />
    </div>
</template>
