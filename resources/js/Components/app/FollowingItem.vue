<script setup>
defineProps({
    image: String,
    title: String,
});
</script>

<template>
    <div class="mb-3 cursor-pointer hover:bg-gray-100">
        <div class="flex item-center gap-2 py-2 px-2">
            <img :src="image" alt="" class="w-[32px] rounded-full" />
            <div>
                <h1 class="font-black text-lg">{{ title }}</h1>
            </div>
        </div>
    </div>
</template>
