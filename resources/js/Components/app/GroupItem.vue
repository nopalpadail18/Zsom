<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    group: Object,
});
</script>

<template>
    <div class="mb-3 cursor-pointer hover:bg-gray-100">
        <Link :href="route('group.profile',group.slug)" class="flex item-start gap-3 py-2 px-3">
            <div class="w-[32px] h-[32px] relative">
                <img :src="group.thumbnail_url" class="absolute inset-0 h-full w-full object-cover rounded-full" />
            </div>
            <div class="flex-1">
                <div class="flex justify-between">
                    <h3 class="font-black text-lg">{{ group.name }}</h3>
                    {{ group.status === 'approved' ? (group.role === 'admin' ? group.role : '') : 'not approved' }}
                </div>
                <div class="text-xs text-gray-500" v-html="group.description"></div>
            </div>
        </Link>
    </div>
</template>
