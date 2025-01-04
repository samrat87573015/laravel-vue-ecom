<template>
  <Head title="Cart" />
  <GuestLayout >
    <div class="container mx-auto py-10">
      <table class="table">
      <thead>
        <tr>
          <th>Product Name</th>
          <th>Product Price</th>
          <th>Quantity</th>
          <th>Total Price</th>
          <th>Variation Attributes</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in cartItems" :key="item.id">
          <td>{{ item.product.name }}</td>
          <td>{{ item.price }}</td>
          <td>{{ item.quantity }}</td>
          <td>{{ calculateTotalPrice(item.price, item.quantity) }}</td>
          <td>
            <span v-for="attr in item.variation.attributes" :key="attr.id">
              {{ attr.value.attribute.name }}: {{ attr.value.value }}
              <span v-if="!isLastAttribute(item.variation.attributes, attr)">, </span>
            </span>
          </td>
          <td>
          <button @click="deleteItem(item.id)" class="btn btn-error">Delete Cart</button>
        </td>
        </tr>
      </tbody>
    </table>
    </div>
  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { defineProps } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import { toast } from '@steveyuowo/vue-hot-toast';


    const props = defineProps({
      cartItems: Array,
    });

    const calculateTotalPrice = (price, quantity) => {
      return (parseFloat(price) * quantity).toFixed(2);
    };

    const isLastAttribute = (attributes, attribute) => {
      return attributes.indexOf(attribute) === attributes.length - 1;
    };

const deleteItem = (itemId) => {
      // Sending delete request using Inertia.js
      router.post(route('products.deleteCartItem', { cart_id: itemId }), {}, {
        onSuccess: () => {
          toast.success('Cart item deleted successfully!');
        },
        onError: (error) => {
          toast.error('There was an error deleting the item.');
        },
      });
    };


</script>

