<script setup>
defineProps({
    image: String,
    title: String,
    description: String,
});
</script>

<template>
    <div class="mb-3 cursor-pointer hover:bg-gray-100">
        <div class="flex item-start gap-3 py-2 px-3">
            <img :src="image" alt="" class="w-[32px] rounded-full" />
            <div>
                <h1 class="font-black text-lg">{{ title }}</h1>
                <div>
                    <p class="text-gray-400">{{ description }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
