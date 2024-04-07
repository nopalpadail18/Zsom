<script setup>
defineProps({
    preview: Object,
    url: String,
});
</script>
<template>
    <a
        :href="url"
        target="_blank"
        v-if="preview && preview.title"
        class="mt-4 block"
    >
        <img :src="preview.image" class="w-full" :alt="preview.title" />
        <div>
            <h3 class="font-bold">
                {{ preview.title }}
            </h3>
            <p class="text-sm p-2">
                {{ preview.description }}
            </p>
        </div>
    </a>
    <a :href="url" target="_blank" v-else-if="url" class="mt-4 block">
        <div class="w-full h-40 bg-gray-300">
            {{ url }}
        </div>
    </a>
</template>
