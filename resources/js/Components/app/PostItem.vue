<script setup>
import {
    ChatBubbleLeftRightIcon,
    HandThumbUpIcon,
    ArrowDownTrayIcon,
} from "@heroicons/vue/24/outline";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import PostUserHeader from "./PostUserHeader.vue";
import { router, usePage } from "@inertiajs/vue3";
import { isImage } from "@/helpers";
import { PaperClipIcon } from "@heroicons/vue/24/solid";
import axiosClient from "@/axiosClient";
import InputTextarea from "@/Components/InputTextarea.vue";
import IndigoButton from "../IndigoButton.vue";
import { ref } from "vue";
import ReadMoreReadLess from "./ReadMoreReadLess.vue";
import EditDeleteDropdown from "./EditDeleteDropdown.vue";
import DangerButton from "../DangerButton.vue";

const authUser = usePage().props.auth.user;
const editingComment = ref(null);

const props = defineProps({
    post: Object,
});

const newCommentText = ref("");

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

function createComment() {
    axiosClient
        .post(route("post.comment.create", props.post), {
            comment: newCommentText.value,
        })
        .then(({ data }) => {
            newCommentText.value = "";
            props.post.comments.unshift(data);
            props.post.num_of_comments++;
        });
}

function deleteComment(comment) {
    if (!window.confirm("Are you sure you want to delete this comment?")) {
        return false;
    }
    axiosClient
        .delete(route("post.comment.delete", comment.id))
        .then(({ data }) => {
            props.post.comments = props.post.comments.filter(
                (c) => c.id !== comment.id
            );
            props.post.num_of_comments--;
        });
}

function startEditingComment(comment) {
    editingComment.value = {
        id: comment.id,
        comment: comment.comment.replace(/<br\s*\/?>/gi, "\n"),
    };
}

function updateComment() {
    axiosClient
        .put(route("post.comment.update", editingComment.value.id), {
            comment: editingComment.value.comment,
        })
        .then(({ data }) => {
            editingComment.value = null;
            props.post.comments = props.post.comments.map((c) => {
                if (c.id === data.id) {
                    return data;
                }
                return c;
            });
        });
}
</script>
<template>
    <div class="bg-white border rounded p-4 shadow mb-3">
        <div class="flex items-center justify-between mb-3">
            <PostUserHeader :post="post" />
            <EditDeleteDropdown
                :user="post.user"
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
            <template v-for="(attachment, ind) in post.attachments.slice(0, 4)">
                <div
                    @click="openAttachment(ind)"
                    class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative cursor-pointer"
                >
                    <div v-if="ind === 3 && post.attachments.length > 4">
                        <span
                            class="absolute top-0 left-0 bottom-0 right-0 z-10 bg-black/60 flex items-center justify-center text-white text-2xl"
                            >+ {{ post.attachments.length - 4 }} more
                        </span>
                    </div>

                    <!-- Download -->
                    <a
                        @click.stop
                        :href="route('post.download', attachment)"
                        class="z-20 opacity-0 group-hover:opacity-100 transition-all w-8 h-8 bg-gray-700 rounded flex items-center justify-center text-gray-100 absolute top-2 right-2 cursor-pointer hover:bg-gray-800"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                    </a>
                    <!--/ Download -->

                    <img
                        v-if="isImage(attachment)"
                        :src="attachment.url"
                        alt=""
                        class="object-contain aspect-square"
                    />
                    <div
                        v-else
                        class="flex flex-col justify-center items-center"
                    >
                        <PaperClipIcon class="w-10 h-10 mb-3" />
                        <small>{{ attachment.name }}</small>
                    </div>
                </div>
            </template>
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
            <DisclosurePanel class="mt-3">
                <div class="flex gap-2 mb-3">
                    <a href="javascript:void(0)">
                        <img
                            :src="authUser.avatar_url"
                            alt=""
                            class="w-[40px] rounded-full border-2 transition-all hover:border-blue-400"
                        />
                    </a>
                    <div class="flex flex-1">
                        <InputTextarea
                            v-model="newCommentText"
                            placeholder="Write a comment"
                            rows="1"
                            class="w-full max-h-[160px] resize-none rounded-r-none"
                        />
                        <IndigoButton
                            class="w-[100px] max-w-[100] rounded-l-none"
                            @click="createComment"
                        >
                            Submit
                        </IndigoButton>
                    </div>
                </div>
                <div>
                    <div>
                        <div
                            v-for="comment in post.comments"
                            :key="comment.id"
                            class="mb-4"
                        >
                            <div class="flex justify-between gap-2">
                                <div class="flex gap-2">
                                    <a href="javascript:void(0)">
                                        <img
                                            :src="comment.user.avatar_url"
                                            alt=""
                                            class="w-[40px] rounded-full border-2 transition-all hover:border-blue-400"
                                        />
                                    </a>
                                    <div>
                                        <h4 class="font-bold">
                                            <a
                                                href="javascript:void(0)"
                                                class="hover:underline"
                                            >
                                                {{ comment.user.name }}
                                            </a>
                                        </h4>
                                        <small class="text-gray-400 text-xs">
                                            {{ comment.updated_at }}
                                        </small>
                                    </div>
                                </div>
                                <EditDeleteDropdown
                                    :user="comment.user"
                                    @edit="startEditingComment(comment)"
                                    @delete="deleteComment(comment)"
                                />
                            </div>
                            <div
                                class="ml-12"
                                v-if="
                                    editingComment &&
                                    editingComment.id === comment.id
                                "
                            >
                                <InputTextarea
                                    v-model="editingComment.comment"
                                    placeholder="Write a comment"
                                    rows="1"
                                    class="w-full max-h-[160px] resize-none"
                                />
                                <div class="flex gap-2 justify-end">
                                    <DangerButton
                                        @click="editingComment = null"
                                    >
                                        Cancle
                                    </DangerButton>
                                    <IndigoButton
                                        class="w-[100px] max-w-[100]"
                                        @click="updateComment"
                                    >
                                        Update
                                    </IndigoButton>
                                </div>
                            </div>
                            <ReadMoreReadLess
                                v-else
                                :content="comment.comment"
                                content-class="text-sm flex flex-1 ml-12"
                            />
                        </div>
                    </div>
                </div>
            </DisclosurePanel>
        </Disclosure>
    </div>
</template>
