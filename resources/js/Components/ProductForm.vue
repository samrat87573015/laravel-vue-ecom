<script setup>
import { ref, watch } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { toast } from "@steveyuowo/vue-hot-toast";
import {
    PlusIcon,
    XIcon,
    TagIcon,
    BoxIcon,
    TruckIcon,
    DollarSignIcon,
} from "lucide-vue-next";

const props = defineProps({
    attributeValues: Array,
});

const activeTab = ref("general");
const defaultImageName = ref("");
const variationImageNames = ref([]);

const selectedAttributes = ref(
    props.attributeValues.map((attr) => ({
        attribute_name: attr.attribute_name,
        values: [],
    }))
);

const form = useForm({
    name: "",
    description: "",
    price: "",
    default_image: null,
    default_image_preview: null,
    stock: "",
    variations: [
        {
            price: "",
            stock: "",
            image_path: null,
            image_preview: null,
            attributes: [],
        },
    ],
});

watch(
    selectedAttributes,
    (newAttributes) => {
        form.variations.forEach((variation) => {
            variation.attributes = newAttributes
                .filter((attr) => attr.values.length > 0)
                .map((attr) => ({
                    attribute_name: attr.attribute_name,
                    attribute_value_id: "",
                }));
        });
    },
    { deep: true }
);

const handleDefaultImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.default_image = file;
        form.default_image_preview = URL.createObjectURL(file);
        defaultImageName.value = file.name;
    }
};

const handleVariationImageUpload = (event, index) => {
    const file = event.target.files[0];
    if (file) {
        // Set the actual file in the form data
        form.variations[index].image_path = file;
        form.variations[index].image_preview = URL.createObjectURL(file);
        if (!variationImageNames.value[index]) {
            variationImageNames.value = [...variationImageNames.value];
        }
        variationImageNames.value[index] = file.name;
    }
};

const removeDefaultImage = () => {
    form.default_image = null;
    form.default_image_preview = null;
    defaultImageName.value = "";
};

const removeVariationImage = (index) => {
    form.variations[index].image_path = null;
    form.variations[index].image_preview = null;
    variationImageNames.value[index] = "";
};

const addVariation = () => {
    form.variations.push({
        price: "",
        stock: "",
        image_path: null,
        image_preview: null,
        attributes: selectedAttributes.value
            .filter((attr) => attr.values.length > 0)
            .map((attr) => ({
                attribute_name: attr.attribute_name,
                attribute_value_id: "",
            })),
    });
    variationImageNames.value.push("");
};

const removeVariation = (index) => {
  form.variations.splice(index, 1);
};

const handleImageUpload = (event, variationIndex) => {
  const file = event.target.files[0];
  form.variations[variationIndex].image_path = file; 
};

const submit = () => {
  form.post(route("products.store"), {
    onSuccess: () => {
        toast.success("Product created successfully!"); // Show success toast
        setTimeout(() => {
          router.get(route('admin.products.index')); // Redirect to the products index page after the toast
        }, 2000);
    },
    onError: () => {
      toast.error("There was an error creating the product."); // Show error toast
    },
  });
};
</script>

<template>
    <div class="bg-gray-100 min-h-screen">
        <div class="container mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold mb-8 text-gray-800">
                Add New Product
            </h1>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <!-- Tabs -->
                <div class="flex border-b border-gray-200">
                    <button
                        v-for="tab in [
                            'general',
                            'inventory',
                            'attributes',
                            'variations',
                        ]"
                        :key="tab"
                        @click="activeTab = tab"
                        :class="[
                            'px-6 py-3 text-sm font-medium focus:outline-none',
                            activeTab === tab
                                ? 'text-blue-600 border-b-2 border-blue-600'
                                : 'text-gray-500 hover:text-gray-700',
                        ]"
                    >
                        <component
                            :is="
                                tab === 'general'
                                    ? TagIcon
                                    : tab === 'inventory'
                                    ? BoxIcon
                                    : tab === 'variations'
                                    ? TruckIcon
                                    : TagIcon
                            "
                            class="inline-block w-5 h-5 mr-2"
                        />
                        {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
                    </button>
                </div>

                <form @submit.prevent="submit" class="p-6">
                    <!-- General Tab -->
                    <div v-if="activeTab === 'general'">
                        <div class="mb-6">
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Product Name</label
                            >
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="mb-6">
                            <label
                                for="description"
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Description</label
                            >
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            ></textarea>
                            <p
                                v-if="form.errors.description"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    for="price"
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Regular Price</label
                                >
                                <div class="relative rounded-md shadow-sm">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                    >
                                        <DollarSignIcon
                                            class="h-5 w-5 text-gray-400"
                                        />
                                    </div>
                                    <input
                                        id="price"
                                        v-model="form.price"
                                        type="number"
                                        step="0.01"
                                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.price"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.price }}
                                </p>
                            </div>

                            <div>
                                <label
                                    for="default_image"
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Default Image</label
                                >
                                <div class="space-y-2">
                                    <!-- Add a container for the file input and selected file info -->
                                    <div class="relative">
                                        <div
                                            v-if="!form.default_image"
                                            class="flex items-center"
                                        >
                                            <input
                                                id="default_image"
                                                type="file"
                                                @change="
                                                    handleDefaultImageUpload
                                                "
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            />
                                        </div>
                                        <div
                                            v-else
                                            class="flex items-center space-x-2 bg-gray-50 p-3 rounded-md"
                                        >
                                            <span
                                                class="text-sm text-gray-600"
                                                >{{ defaultImageName }}</span
                                            >
                                            <button
                                                type="button"
                                                @click="removeDefaultImage"
                                                class="text-red-500 hover:text-red-700"
                                            >
                                                <XIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Image preview -->
                                    <div
                                        v-if="form.default_image_preview"
                                        class="mt-2"
                                    >
                                        <img
                                            :src="form.default_image_preview"
                                            class="h-32 w-32 object-cover rounded-lg"
                                            alt="Default image preview"
                                        />
                                    </div>
                                </div>
                                <p
                                    v-if="form.errors.default_image"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.default_image }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Tab -->
                    <div v-if="activeTab === 'inventory'">
                        <div class="mb-6">
                            <label
                                for="stock"
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Stock Quantity</label
                            >
                            <input
                                id="stock"
                                v-model="form.stock"
                                type="number"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            />
                            <p
                                v-if="form.errors.stock"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.stock }}
                            </p>
                        </div>
                    </div>

                    <!-- Attributes Tab -->
                    <div v-if="activeTab === 'attributes'">
                        <div
                            v-for="(attribute, index) in props.attributeValues"
                            :key="index"
                            class="mb-6"
                        >
                            <label
                                :for="'attribute-' + index"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                {{ attribute.attribute_name }}
                            </label>
                            <div class="flex flex-wrap gap-5">
                                <div
                                    v-for="value in attribute.values"
                                    :key="value.id"
                                    class="flex items-center"
                                >
                                    <input
                                        :id="'attribute-value-' + value.id"
                                        type="checkbox"
                                        :value="value.id"
                                        v-model="
                                            selectedAttributes[index].values
                                        "
                                        class="mr-2"
                                    />
                                    <label
                                        :for="'attribute-value-' + value.id"
                                        >{{ value.value }}</label
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Variations Tab -->
                    <div v-if="activeTab === 'variations'">
                        <div
                            v-for="(variation, index) in form.variations"
                            :key="index"
                            class="mb-8 p-6 bg-gray-50 rounded-lg relative"
                        >
                            <button
                                type="button"
                                @click="removeVariation(index)"
                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-500"
                            >
                                <XIcon class="h-5 w-5" />
                            </button>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Variation {{ index + 1 }}
                            </h3>

                            <!-- Attribute Select Inputs -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div
                                    v-for="(
                                        attribute, attrIndex
                                    ) in variation.attributes"
                                    :key="attrIndex"
                                >
                                    <label
                                        :for="
                                            'variation-attr-' +
                                            index +
                                            '-' +
                                            attrIndex
                                        "
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        {{ attribute.attribute_name }}
                                    </label>
                                    <select
                                        :id="
                                            'variation-attr-' +
                                            index +
                                            '-' +
                                            attrIndex
                                        "
                                        v-model="attribute.attribute_value_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="">
                                            Select
                                            {{ attribute.attribute_name }}
                                        </option>
                                        <option
                                            v-for="value in selectedAttributes.find(
                                                (attr) =>
                                                    attr.attribute_name ===
                                                    attribute.attribute_name
                                            )?.values"
                                            :key="value"
                                            :value="value"
                                        >
                                            {{
                                                props.attributeValues
                                                    .find(
                                                        (attr) =>
                                                            attr.attribute_name ===
                                                            attribute.attribute_name
                                                    )
                                                    ?.values.find(
                                                        (val) =>
                                                            val.id === value
                                                    )?.value
                                            }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Price Input -->
                            <div class="mt-4">
                                <label
                                    :for="'variation-price-' + index"
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Price
                                </label>
                                <input
                                    type="number"
                                    :id="'variation-price-' + index"
                                    v-model="variation.price"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter price"
                                />
                            </div>

                            <!-- Stock Input -->
                            <div class="mt-4">
                                <label
                                    :for="'variation-stock-' + index"
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Stock
                                </label>
                                <input
                                    type="number"
                                    :id="'variation-stock-' + index"
                                    v-model="variation.stock"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter stock quantity"
                                />
                            </div>

              <!-- Image Input -->
              <div class="form-control">
                <label for="variation-image" class="label font-medium">Image</label>
                <input :id="'variation-image-' + index" type="file" @change="(e) => handleImageUpload(e, index)"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mb-5" />
                <span v-if="form.errors[`variations.${index}.image_path`]" class="text-error text-sm">{{
                  form.errors[`variations.${index}.image_path`] }}</span>
              </div>

              <!-- Add Variation Button -->
              <button type="button" @click="addVariation"
                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                <PlusIcon class="h-5 w-5 mr-2" />
                Add Variation
              </button>
            </div>
          </div>




            <div class="mt-8 flex justify-end">
              <button type="submit"
                class="inline-flex items-center px-6 py-3 text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                Publish
              </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</template>
