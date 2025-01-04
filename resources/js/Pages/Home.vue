<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Slider from '@/Components/Slider.vue';
import { defineProps } from 'vue';


const props = defineProps({
  'products': Object
})



</script>




<template>
  <GuestLayout>
    <Head title="Home" />
    <Slider />

    <div class="container mx-auto py-10">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="product in props.products.data" :key="product.id">
          <div class="card w-96 bg-base-100 shadow-xl">
            <div class="card-image h-[300px] overflow-hidden">
              <Link :href="route('products.show', product.id)">
                <img class="w-full" :src="product.default_image" alt="Product Image" />
              </Link>
            </div>
            <div class="card-body">
              <h2 class="card-title">{{ product.name }}</h2>
              <p>{{ product.description }}</p>
              <div class="card-actions justify-end">
              <Link :href="route('products.show', product.id)" class="btn btn-primary">Select Options</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


  </GuestLayout>
</template>




<style>
.input_item {
  margin-bottom: 20px;
}

.input_item input,
.input_item textarea {
  width: 100%;
}

thead tr th {
  padding: 8px 5px;
  text-align: left;
  border: 1px solid #ccc;
}

tbody tr td {
  padding: 8px 5px;
  text-align: left;
  border: 1px solid #ccc;
}
</style>
