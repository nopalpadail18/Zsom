<script setup>
import {
    BookmarkIcon,
    ChatBubbleOvalLeftIcon,
    HeartIcon,
    PaperAirplaneIcon,
} from "@heroicons/vue/24/outline";
import { HeartIcon as HeartIconSolid } from "@heroicons/vue/24/solid";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import PostUserHeader from "./PostUserHeader.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import axiosClient from "@/axiosClient";
import ReadMoreReadLess from "./ReadMoreReadLess.vue";
import EditDeleteDropdown from "./EditDeleteDropdown.vue";
import PostAttachments from "./PostAttachments.vue";
import CommentList from "./CommentList.vue";
import { computed } from "vue";
import UrlPreview from "./UrlPreview.vue";
import Pinned from "./Icons/Pinned.vue";

const props = defineProps({
    post: Object,
});

const group = usePage().props.group;
const authUser = usePage().props.auth.user;

const emit = defineEmits(["editClick", "attachmentClick"]);

const postBody = computed(() => {
    let content = props.post.body.replace(
        /(?:(\s+)|<p>)((#\w+)(?![^<]*<\/a>))/g,
        (match, group1, group2) => {
            const encodeGroup = encodeURIComponent(group2);
            return `${group1 || ""}
            <a href="/search/${encodeGroup}" class="hastag">${group2}</a>`;
        }
    );

    return content;
});

const isPinned = computed(() => {
    if (group?.id) {
        return group?.pinned_post_id === props.post.id;
    }

    return authUser?.pinned_post_id === props.post.id;
});

function openEditModal() {
    emit("editClick", props.post);
}

function deletePost() {
    if (window.confirm("Are you sure you want to delete this post?")) {
        router.delete(route("posts.destroy", props.post), {
            preserveScroll: true,
        });
    }
}

function pinUnpinPost() {
    const form = useForm({
        forGroup: group?.id,
    });
    let isPinned = false;

    if (group?.id) {
        isPinned = group?.pinned_post_id === props.post.id;
    } else {
        isPinned = authUser?.pinned_post_id === props.post.id;
    }

    form.post(route("post.pinUnpin", props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (group?.id) {
                group.pinned_post_id = isPinned ? null : props.post.id;
            } else {
                authUser.pinned_post_id = isPinned ? null : props.post.id;
            }
        },
    });
}

function openAttachment(ind) {
    emit("attachmentClick", props.post, ind);
}

function sendReaction() {
    axiosClient
        .post(route("post.reaction", props.post), {
            reaction: "like",
        })
        .then(({ data }) => {
            props.post.curent_user_has_reactions =
                data.curent_user_has_reactions;
            props.post.num_of_reactions = data.num_of_reactions;
        });
}

const likeIcon = computed(() => {
    if (props.post.curent_user_has_reactions) {
        return HeartIconSolid;
    } else {
        return HeartIcon;
    }
});

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
    <div class="bg-white border rounded p-4 shadow mb-3">
        <div class="flex items-center justify-between mb-3">
            <PostUserHeader :post="post" />
            <div class="flex items-center">
                <div v-if="isPinned" class="flex items-center mr-4">
                    <Pinned :className="'w-5 h-5 -mr-0'" />
                    <span class="text-xs">Pinned</span>
                </div>
                <EditDeleteDropdown
                    :user="post.user"
                    :post="post"
                    @edit="openEditModal"
                    @delete="deletePost"
                    @pin="pinUnpinPost"
                />
            </div>
        </div>

        <div
            class="grid gap-3 mb-3"
            :class="[
                post.attachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2',
            ]"
        >
            <PostAttachments :attachments="post.attachments" />
            <!-- @attachmentClick="openAttachment" -->
        </div>
        <Disclosure v-slot="{ open }">
            <div class="flex items-center justify-between">
                <div class="grid grid-cols-2 items-center">
                    <button
                        @click="sendReaction"
                        class="text-gray-800"
                        :class="
                            post.curent_user_has_reactions ? 'text-red-500' : ''
                        "
                    >
                        <transition
                            name="like-btn-animation"
                            mode="out-in"
                            enter-active-class="transition ease-in duration-200 scale-70"
                            enter-to-class="scale-100"
                            leave-active-class="transition ease-out duration-150 scale-50"
                        >
                            <component :is="likeIcon" class="w-7 h-7" />
                        </transition>
                    </button>
                    <DisclosureButton class="text-gray-800">
                        <ChatBubbleOvalLeftIcon
                            class="w-7 h-7 mr-2 transform scale-x-[-1]"
                        />
                    </DisclosureButton>
                </div>
                <button @click="copyToClipboard">
                    <PaperAirplaneIcon
                        class="w-7 h-7 text-gray-800 -rotate-45"
                    />
                </button>
            </div>
            <div class="mb-3">
                <div class="w-full">
                    <span class="mr-2 text-sm font-semibold">
                        {{ post.num_of_reactions }} Likes
                    </span>
                </div>
                <ReadMoreReadLess
                    :content="postBody"
                    :uploader="post.user.name"
                />
                <UrlPreview :preview="post.preview" :url="post.preview_url" />
                <DisclosureButton class="mr-2 text-sm text-gray-400">
                    View all {{ post.num_of_comments }} Comment
                </DisclosureButton>
            </div>

            <DisclosurePanel
                class="comment-list mt-3 max-h-[400px] lg:flex-1 overflow-auto"
            >
                <CommentList :post="post" :data="{ comments: post.comments }" />
            </DisclosurePanel>
        </Disclosure>
    </div>
</template>
