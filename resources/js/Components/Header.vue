<template>
  <header class="w-full border-b">
    <!-- Top bar -->
    <div class="container mx-auto px-4 py-4">
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Logo -->
        <div class="text-2xl font-bold tracking-wider order-first md:order-none">
          <Link href="/">MAJORI</Link>
        </div>

        <!-- Search -->
        <div class="relative flex-1 w-full max-w-md">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search..."
            class="w-full pl-4 pr-10 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
        </div>

        <!-- User actions -->
        <div class="flex items-center gap-4">
          <div v-if="!$page.props.auth.user" class="flex items-center">
              <Link href="/login" class="p-2">
              Login
            </Link>
            <Link href="/register" class="p-2">
              Register
            </Link>
          </div>
          <div v-else class="flex items-center">
            <Link href="/dashboard" class="p-2 flex items-center gap-2">Dashboard</Link>

          </div>
          <div class="btn">
            <Link href="/cart-list" class="p-2 flex items-center gap-2">
              <ShoppingCart class="w-5 h-5" />
              <span >Cart</span>
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <div class="border-t">
      <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
          <!-- Categories button -->
          <button class="flex items-center gap-2 py-2 w-full md:w-auto justify-between md:justify-start">
            <span class="flex items-center gap-2">
              <Menu class="w-5 h-5" />
              SHOP BY CATEGORIES
            </span>
            <ChevronDown class="w-4 h-4 md:hidden" />
          </button>

          <!-- Main navigation -->
          <nav class="w-full md:w-auto">
            <ul class="flex flex-col md:flex-row">
              <li
                v-for="(item, index) in navItems"
                :key="index"
                class="relative group w-full md:w-auto"
              >
                <a
                  :href="item.link"
                  class="px-4 py-2 flex items-center justify-between md:justify-start w-full"
                  @click.prevent="toggleDropdown(item)"
                  @mouseenter="handleHover(item, true)"
                  @mouseleave="handleHover(item, false)"
                >
                  <span class="flex items-center">
                    {{ item.label }}
                    <span
                      v-if="item.tag"
                      :class="[
                        'ml-1 text-xs px-1 rounded',
                        item.tag === 'SALE' ? 'bg-red-500 text-white' : 'bg-pink-500 text-white'
                      ]"
                    >
                      {{ item.tag }}
                    </span>
                  </span>
                  <ChevronDown v-if="item.children" class="w-4 h-4 ml-1" />
                </a>
                <div
                  v-if="item.children"
                  class="absolute left-0 mt-0 w-full md:w-48 bg-white border rounded shadow-lg"
                  :class="{ hidden: !item.isOpen }"
                >
                  <a
                    v-for="(child, childIndex) in item.children"
                    :key="childIndex"
                    :href="child.link"
                    class="block px-4 py-2 hover:bg-gray-100"
                  >
                    {{ child.label }}
                  </a>
                </div>
              </li>
            </ul>
          </nav>

          <!-- Best offers -->
          <button class="flex items-center gap-2 py-2 w-full md:w-auto justify-between md:justify-start">
            <span class="flex items-center gap-2">
              <span class="w-5 h-5 rounded-full bg-gray-800 flex items-center justify-center text-white text-xs">%</span>
              BEST OFFERS
            </span>
            <ChevronDown class="w-4 h-4 md:hidden" />
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { Search, User, Heart, ShoppingCart, Menu, ChevronDown } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'

const searchQuery = ref('')

const navItems = ref([
  { label: 'HOME', link: '/' },
  {
    label: 'SHOP',
    link: '#',
    children: [
      { label: 'Category 1', link: '#' },
      { label: 'Category 2', link: '#' },
    ],
    isOpen: false,
  },
  {
    label: 'CATEGORIES',
    link: '#',
    tag: 'SALE',
    children: [
      { label: 'Category A', link: '#' },
      { label: 'Category B', link: '#' },
    ],
    isOpen: false,
  },
  {
    label: 'PRODUCTS',
    link: '#',
    tag: 'HOT',
    children: [
      { label: 'Product X', link: '#' },
      { label: 'Product Y', link: '#' },
      
    ],
    isOpen: false,
  },
  {
    label: 'TOP DEALS',
    link: '#',
    children: [
      { label: 'Deal 1', link: '#' },
      { label: 'Deal 2', link: '#' },
    ],
    isOpen: false,
  },
  {
    label: 'ELEMENTS',
    link: '#',
    children: [
      { label: 'Element A', link: '#' },
      { label: 'Element B', link: '#' },
    ],
    isOpen: false,
  },
])

const isMobile = ref(window.innerWidth <= 768)

const toggleDropdown = (item) => {
  if (isMobile.value) {
    item.isOpen = !item.isOpen
  }
}

const handleHover = (item, isHovering) => {
  if (!isMobile.value) {
    item.isOpen = isHovering
  }
}

window.addEventListener('resize', () => {
  isMobile.value = window.innerWidth <= 768
})
</script>


