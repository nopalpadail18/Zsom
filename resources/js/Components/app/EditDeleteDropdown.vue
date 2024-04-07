<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import {
    PencilIcon,
    TrashIcon,
    EllipsisVerticalIcon,
} from "@heroicons/vue/20/solid";
import { ClipboardIcon } from "@heroicons/vue/24/outline";
import { EyeIcon } from "@heroicons/vue/24/solid";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    post: {
        type: Object,
        default: null,
    },
    comment: {
        type: Object,
        default: null,
    },
});

const authUser = usePage().props.auth.user;
const group = usePage().props.group;

// Membuat properti terkomputasi `user`, yang bergantung pada props.comment dan props.post.
// Jika props.comment tidak ada, maka user akan menjadi props.post.user.
// Jika props.comment ada, maka user akan menjadi props.comment.user.
const user = computed(() => props.comment?.user || props.post?.user);

// Membuat properti terkomputasi `editAllowed`, yang bergantung pada nilai `user` dan `authUser`.
// Jika `user` memiliki id yang sama dengan `authUser.id`, maka editAllowed akan menjadi true.
const editAllowed = computed(() => user.value.id === authUser.id);
const pinAllowed = computed(() => {
    return (
        user.value.id === authUser.id ||
        (props.post.group && props.post.group.role === "admin")
    );
});

const isPinned = computed(() => {
    if (group?.id) {
        return group?.pinned_post_id === props.post.id;
    }

    return authUser?.pinned_post_id === props.post.id;
});

// Membuat properti terkomputasi `deleteAllowed` untuk menentukan apakah pengguna diizinkan untuk menghapus.
const deleteAllowed = computed(() => {
    // Jika pengguna yang sedang terautentikasi adalah pemilik komentar, maka diizinkan untuk menghapus.
    if (user.value.id === authUser.id) return true;

    // Jika pengguna yang sedang terautentikasi adalah pemilik postingan, maka diizinkan untuk menghapus.
    if (props.post.user.id === authUser.id) return true;

    // Jika tidak ada komentar dan pengguna adalah administrator dari grup postingan, maka diizinkan untuk menghapus.
    return !props.comment && props.post.group?.role === "admin";
});

defineEmits(["edit", "delete", "pin"]);

function copyToClipboard() {
    const textCopy = route("post.view", props.post.id);
    const tempInput = document.createElement("input");
    tempInput.value = textCopy;
    document.body.appendChild(tempInput);

    tempInput.select();
    document.execCommand("copy");

    document.body.removeChild(tempInput);
}
</script>
<template>
    <Menu as="div" class="relative inline-block text-left">
        <div>
            <MenuButton
                class="w-8 h-8 z-10 rounded-full hover:bg-black/5 transition flex item-center justify-center"
            >
                <EllipsisVerticalIcon class="w-5 h-5 mt-1" aria-hidden="true" />
            </MenuButton>
        </div>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                class="absolute z-20 right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
            >
                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <Link
                            :href="route('post.view', post.id)"
                            :class="[
                                active
                                    ? 'bg-indigo-500 text-white'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <EyeIcon class="mr-2 h-5 w-5" aria-hidden="true" />
                            Open Post
                        </Link>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <button
                            @click="copyToClipboard"
                            :class="[
                                active
                                    ? 'bg-indigo-500 text-white'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <ClipboardIcon
                                class="mr-2 h-5 w-5"
                                aria-hidden="true"
                            />
                            Copy Link
                        </button>
                    </MenuItem>
                    <MenuItem v-if="editAllowed" v-slot="{ active }">
                        <button
                            @click="$emit('edit')"
                            :class="[
                                active
                                    ? 'bg-indigo-500 text-white'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <PencilIcon
                                class="mr-2 h-5 w-5"
                                aria-hidden="true"
                            />
                            Edit
                        </button>
                    </MenuItem>
                    <MenuItem v-if="pinAllowed" v-slot="{ active }">
                        <button
                            @click="$emit('pin')"
                            :class="[
                                active
                                    ? 'bg-indigo-500 text-white'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 66 65"
                                version="1.1"
                                class="w-7 h-7 rotate-45 -m-1 mr-1"
                            >
                                <path
                                    d="M39.4418525,19.945683 L41.5368613,33.9124087 C44.8731757,34.6460584 47,35.7567964 47,37 C47,39.209139 40.2842712,41 32,41 C23.7157288,41 17,39.209139 17,37 C17,35.7567964 19.1268243,34.6460584 22.4631387,33.9124087 L24.5581475,19.945683 C22.3708176,19.2145917 21,18.1655089 21,17 C21,14.790861 25.9248678,13 32,13 C38.0751322,13 43,14.790861 43,17 C43,18.1655089 41.6291824,19.2145917 39.4418525,19.945683 Z M31,51.9993209 L31,42.0006851 L34,42.0006851 L34,51.9993209 L32.5,53.9993209 L31,51.9993209 Z"
                                />
                            </svg>

                            {{ isPinned ? "Unpin" : "Pin" }}
                        </button>
                    </MenuItem>
                    <MenuItem v-if="deleteAllowed" v-slot="{ active }">
                        <button
                            @click="$emit('delete')"
                            :class="[
                                active
                                    ? 'bg-indigo-500 text-white'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <TrashIcon
                                class="mr-2 h-5 w-5"
                                aria-hidden="true"
                            />
                            Delete
                        </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
