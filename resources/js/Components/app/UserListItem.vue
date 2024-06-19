<script setup>
import { ChatBubbleOvalLeftIcon } from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";
defineProps({
    user: Object,
    forApprove: {
        type: Boolean,
        default: false,
    },
    showRoleDropdown: {
        type: Boolean,
        default: false,
    },
    disableRoleDropdown: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["approve", "reject", "roleChange", "delete"]);
</script>

<template>
    <div
        class="cursor-pointer border-2 transition duration-300 border-transparent rounded-md bg-white hover:border-indigo-500"
    >
        <div class="flex item-center gap-2 py-2 px-2">
            <Link :href="route('profile', user.username)">
                <img
                    :src="user.avatar_url || '/img/default_avatar.jpeg'"
                    alt="user"
                    class="w-[32px] rounded-full"
                />
            </Link>
            <div class="flex justify-between flex-1">
                <Link :href="route('profile', user.username)">
                    <h3 class="font-black text-lg">{{ user.name }}</h3>
                </Link>
                <a :href="`http://chat-zsosm.test/chat/${user.name}`" target="_blank" class="mt-1">
                    <ChatBubbleOvalLeftIcon class="w-6 h-6" />
                </a>
                <div class="flex gap-1" v-if="forApprove">
                    <button
                        class="text-xs py-1 px-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white"
                        @click.prevent.stop="$emit('approve', user)"
                    >
                        Approve
                    </button>
                    <button
                        class="text-xs py-1 px-2 rounded bg-red-500 hover:bg-red-600 text-white"
                        @click.prevent.stop="$emit('reject', user)"
                    >
                        Reject
                    </button>
                </div>
                <div v-if="showRoleDropdown">
                    <select
                        @change="$emit('roleChange', user, $event.target.value)"
                        class="rounded-md border-0 bg-transparent bg-none py-1 text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
                        :disabled="disableRoleDropdown"
                    >
                        <option :selected="user.role === 'admin'">admin</option>
                        <option :selected="user.role === 'user'">
                            user
                        </option></select
                    >:
                    <button
                        class="text-xs py-1.5 px-2 rounded bg-gray-700 hover:bg-gray-800 text-white ml-3 disabled:bg-gray-500"
                        :disabled="disableRoleDropdown"
                        @click="$emit('delete', user)"
                    >
                        delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
