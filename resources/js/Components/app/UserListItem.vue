<script setup>
import { Link } from "@inertiajs/vue3";
defineProps({
    user: Object,
    forApprove: {
        type: Boolean,
        default: false,
    }
});

defineEmits(["approve"]);
</script>

<template>
    <div class="cursor-pointer border-2 transition duration-300 border-transparent rounded-md bg-white hover:border-indigo-500">
        <Link :href="route('profile',user.username)" class="flex item-center gap-2 py-2 px-2">
            <img :src="user.avatar_url || '/img/default_avatar.jpeg'" alt="" class="w-[32px] rounded-full" />
            <div class="flex justify-between flex-1">
                <h3 class="font-black text-lg">{{ user.name }}</h3>
                <div class="flex gap-1">
                    <button class="text-xs py-1 px-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white"
                            v-if="forApprove" @click.prevent.stop="$emit('approve',user)">
                        Approve
                    </button>
                    <button class="text-xs py-1 px-2 rounded bg-red-500 hover:bg-red-600 text-white"
                            v-if="forApprove" @click.prevent.stop="$emit('reject',user)">
                        Reject
                    </button>
                </div>
            </div>
        </Link>
    </div>
</template>
