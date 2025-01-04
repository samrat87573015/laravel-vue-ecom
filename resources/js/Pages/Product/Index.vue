<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps } from 'vue';
import { toast } from "@steveyuowo/vue-hot-toast";

const props = defineProps({
  'products': Object
})

const deleteProduct= (id) => {
      if (confirm('Are you sure you want to delete this product?')) {
        router.delete(route('admin.product.delete', id), {
          onSuccess: () => {
            toast.success('Product deleted successfully!');
            router.reload();
          },
          onError: (errors) => {
            // Handle errors
          },
        });
      }
    }
</script>

<template>
  <Head title="Products" />
  <AuthenticatedLayout>

    <div class="container mx-auto py-10">
      <div class="flex justify-between">
        <h1 class="text-3xl font-bold mb-8 text-gray-800">Products</h1>
        <Link :href="route('admin.product.create')" class="btn btn-primary">
          Add Product
          </Link>
      </div>
        <table class="table">
            <thead>
              <tr>
                  <th class="px-4 py-2">Image</th>
                  <th class="px-4 py-2">Name</th>
                  <th class="px-4 py-2">Type</th>
                  <th class="px-4 py-2">Status</th>
                  <th class="px-4 py-2">Price</th>
                  <th class="px-4 py-2">Stock</th>
                  <th class="px-4 py-2">Description</th>
                  <th class="px-4 py-2">Action</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="product in products.data" :key="product.id">
                  <td class="border px-4 py-2">
                      <img :src="product.default_image" :alt="product.name" class="w-10 h-10 object-cover">
                  </td>
                  <td class="border px-4 py-2">{{ product.name }}</td>
                  <td class="border px-4 py-2">{{ product.type }}</td>
                  <td class="border px-4 py-2">{{ product.status }}</td>
                  <td class="border px-4 py-2">{{ product.price }}</td>
                  <td class="border px-4 py-2">{{ product.stock }}</td>
                  <td class="border px-4 py-2">{{ product.description }}</td>
                  <td class="border px-4 py-2"><button class="btn btn-error" @click="deleteProduct(product.id)">Delete Product</button></td>

              </tr>
            </tbody>
                
        </table>
    </div>


  </AuthenticatedLayout>
</template>