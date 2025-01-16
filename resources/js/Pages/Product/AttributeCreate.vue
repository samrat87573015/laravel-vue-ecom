<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { toast } from "@steveyuowo/vue-hot-toast";
import { defineProps } from 'vue';

const props = defineProps({
  attrList: Array
});

const form = useForm({
  "name": '',
  "values": []
});

const addValue = () => {
  form.values.push('');
};

const submitForm = () => {
  form.post(route('attributes.store'), {
    onSuccess: () => {
      toast.success('Attribute Created');
      router.reload();
      form.reset();
    },
    onError: (errors) => {
      console.log('Form submission failed', errors);
    }
  });
};


const deleteAttr = (id) => {
  if (confirm('Are you sure you want to delete this attribute?')) {
    router.get(route('admin.attribute.delete', id), {
      onSuccess: () => {
        toast.success('Attribute deleted successfully!');
        router.reload();
      },
      onError: (errors) => {
        // Handle errors
        console.log(errors);
      },
    });
  }
};





</script>

<template>

  <Head title="Create Attribute" />
  <AuthenticatedLayout>
    <div class="container mx-auto p-4">
      <div class="grid grid-cols-2 gap-5">
        <div class="create_attr">

          <form @submit.prevent="submitForm" class="max-w-lg mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h1 class="text-2xl font-bold mb-4">Create Attribute</h1>
            <div class="mb-4">
              <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Attribute Name</label>
              <input v-model="form.name" id="name" type="text"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Enter attribute name" required />
              <span v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</span>
            </div>

            <div v-for="(value, index) in form.values" :key="index" class="mb-4 relative">
              <label :for="'value_' + index" class="block text-gray-700 text-sm font-bold mb-2">Value {{ index + 1
                }}</label>
              <input v-model="form.values[index]" :id="'value_' + index" type="text"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Enter value" required />
              <span v-if="form.errors.values && form.errors.values[index]" class="text-red-500 text-xs mt-1">
                {{ form.errors.values[index] }}
              </span>
              <div class="absolute top-[26px] -right-[38px]">
                <button type="button" @click="form.values.splice(index, 1)"
                  class="text-red-500 hover:text-red-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                  </svg>

                </button>
              </div>
            </div>

            <div class="flex items-center justify-between">
              <button type="button" @click="addValue"
                class="text-blue-500 hover:text-blue-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                + Add More Values
              </button>
              <button type="submit" :disabled="form.processing" class="btn btn-primary">
                Submit
              </button>
            </div>
          </form>
        </div>
        <div class="attr_list_wrapper">
          <div class="attr_list p-5 bg-white shadow-md rounded">
          <h1 class="text-2xl font-bold mb-4">Attribute List</h1>
          <table class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Values</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="attr in attrList" :key="attr.id">
                <td>{{ attr.name }}</td>
                <td>
                  <ul class="flex items-center gap-2">
                    <li v-for="value in attr.values" :key="value.id">
                      {{ value.value }}
                    </li>
                  </ul>
                </td>
                <td>
                  <button @click="deleteAttr(attr.id)" class="btn btn-error">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>