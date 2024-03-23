<script setup>
import {
    ChatBubbleLeftRightIcon,
    HandThumbUpIcon,
} from "@heroicons/vue/24/outline";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import PostUserHeader from "./PostUserHeader.vue";
import { router } from "@inertiajs/vue3";
import axiosClient from "@/axiosClient";
import ReadMoreReadLess from "./ReadMoreReadLess.vue";
import EditDeleteDropdown from "./EditDeleteDropdown.vue";
import PostAttachments from "./PostAttachments.vue";
import CommentList from "./CommentList.vue";

const props = defineProps({
    post: Object,
});

const emit = defineEmits(["editClick", "attachmentClick"]);

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
</script>
<template>
    <div class="bg-white border rounded p-4 shadow mb-3">
        <div class="flex items-center justify-between mb-3">
            <PostUserHeader :post="post" />
            <EditDeleteDropdown
                :user="post.user"
                :post="post"
                @edit="openEditModal"
                @delete="deletePost"
            />
        </div>
        <div class="mb-3">
            <ReadMoreReadLess :content="post.body" />
        </div>
        <div
            class="grid gap-3 mb-3"
            :class="[
                post.attachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2',
            ]"
        >
            <PostAttachments
                :attachments="post.attachments"
                @attachmentClick="openAttachment"
            />
        </div>
        <Disclosure v-slot="{ open }">
            <div class="flex gap-2">
                <button
                    @click="sendReaction"
                    class="text-gray-800 flex gap-1 flex-center justify-center bg-gray-100 rounded-lg hover:bg-gray-200 py-2 px-4 flex-1"
                    :class="[
                        post.curent_user_has_reactions
                            ? 'bg-sky-100 hover:bg-sky-200'
                            : 'bg-gray-100 hover:bg-gray-200',
                    ]"
                >
                    <HandThumbUpIcon class="w-5 h-5" />
                    <span class="mr-2">{{ post.num_of_reactions }}</span>
                    {{ post.curent_user_has_reactions ? "Liked" : "Like" }}
                </button>
                <DisclosureButton
                    class="text-gray-800 flex gap-1 flex-center justify-center bg-gray-100 rounded-lg hover:bg-gray-200 py-2 px-4 flex-1"
                >
                    <ChatBubbleLeftRightIcon class="w-5 h-5 mr-2" />
                    <span class="mr-2">
                        {{ post.num_of_comments }}
                    </span>
                    Comment
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
