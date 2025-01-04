<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductForm from '@/Components/ProductForm.vue';
import { Toaster } from "@steveyuowo/vue-hot-toast";
import "@steveyuowo/vue-hot-toast/vue-hot-toast.css";

defineProps({
  attributeValues: Array,
});
</script>

<template>
  <Head title="Create Product" />
  <AuthenticatedLayout>
    <Toaster />
    <ProductForm :attribute-values="attributeValues" />
  </AuthenticatedLayout>
</template>