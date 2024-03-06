<script setup>
import {
    ChatBubbleLeftEllipsisIcon,
    HandThumbUpIcon,
    PaperAirplaneIcon,
} from "@heroicons/vue/24/outline";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import InputTextarea from "@/Components/InputTextarea.vue";
import IndigoButton from "../IndigoButton.vue";
import EditDeleteDropdown from "./EditDeleteDropdown.vue";
import DangerButton from "../DangerButton.vue";
import axiosClient from "@/axiosClient";
import { usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import ReadMoreReadLess from "./ReadMoreReadLess.vue";

const authUser = usePage().props.auth.user;
const editingComment = ref(null);

const newCommentText = ref("");

const props = defineProps({
    post: Object,
    data: Object,
    parentComment: {
        type: [Object, null],
        default: null,
    },
});

function createComment() {
    axiosClient
        .post(route("post.comment.create", props.post), {
            comment: newCommentText.value,
            parent_id: props.parentComment?.id || null,
        })
        .then(({ data }) => {
            newCommentText.value = "";
            props.data.comments.unshift(data);
            if (props.parentComment) {
                props.parentComment.num_of_comments++;
            }
            props.post.num_of_comments++;
        });
}

function deleteComment(comment) {
    if (!window.confirm("Are you sure you want to delete this comment?")) {
        return false;
    }
    axiosClient.delete(route("comment.delete", comment.id)).then(({ data }) => {
        const commentIndex = props.data.comments.findIndex(
            (c) => c.id === comment.id
        );
        props.data.comments.splice(commentIndex, 1);
        if (props.parentComment) {
            props.parentComment.num_of_comments--;
        }
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
        .put(route("comment.update", editingComment.value.id), {
            comment: editingComment.value.comment,
        })
        .then(({ data }) => {
            editingComment.value = null;
            props.data.comments = props.data.comments.map((c) => {
                if (c.id === data.id) {
                    return data;
                }
                return c;
            });
        });
}

function sendCommentReaction(comment) {
    axiosClient
        .post(route("comment.reaction", comment.id), {
            reaction: "like",
        })
        .then(({ data }) => {
            comment.curent_user_has_reactions = data.curent_user_has_reactions;
            comment.num_of_reactions = data.num_of_reactions;
        });
}
</script>
<template>
    <div class="flex gap-2 mb-3">
        <a href="javascript:void(0)">
            <img
                :src="authUser.avatar_url"
                alt=""
                class="w-[40px] rounded-full border-2 transition-all hover:border-blue-400"
            />
        </a>
        <div class="relative flex flex-1">
            <InputTextarea
                v-model="newCommentText"
                placeholder="Write a comment"
                rows="1"
                class="w-full resize-none block overflow-hidden"
            />
            <button
                class="text-white absolute end-2.5 bottom-2 bg-blue-700 hover:bg-blue-800 font-medium rounded-md text-sm px-2 py-1"
                @click="createComment"
            >
                <PaperAirplaneIcon class="w-5 h-5" />
            </button>
        </div>
    </div>
    <div>
        <div>
            <div
                v-for="comment in data.comments"
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
                <div class="pl-12">
                    <div
                        v-if="
                            editingComment && editingComment.id === comment.id
                        "
                    >
                        <InputTextarea
                            v-model="editingComment.comment"
                            placeholder="Write a comment"
                            rows="1"
                            class="w-full max-h-[160px] resize-none"
                        />
                        <div class="flex gap-2 justify-end">
                            <DangerButton @click="editingComment = null">
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
                        content-class="text-sm flex flex-1"
                    />
                    <Disclosure>
                        <div class="flex gap-2 mt-1">
                            <button
                                @click="sendCommentReaction(comment)"
                                class="flex items-center text-sm text-indigo-500 py-0.5 px-1 rounded-lg"
                                :class="[
                                    comment.curent_user_has_reactions
                                        ? 'bg-indigo-50 hover:bg-indigo-100'
                                        : ' hover:bg-indigo-50',
                                ]"
                            >
                                <HandThumbUpIcon class="w-3 h-3 mr-1" />
                                <span class="mr-2">
                                    {{ comment.num_of_reactions }}
                                </span>
                                {{
                                    comment.curent_user_has_reactions
                                        ? "liked"
                                        : "like"
                                }}
                            </button>
                            <DisclosureButton
                                class="flex items-center text-sm text-indigo-500 py-0.5 px-1 hover:bg-indigo-100 rounded-lg"
                            >
                                <ChatBubbleLeftEllipsisIcon
                                    class="w-3 h-3 mr-1"
                                />
                                <span class="mr-2">
                                    {{ comment.num_of_comments }}
                                </span>
                                reply
                            </DisclosureButton>
                        </div>
                        <DisclosurePanel class="mt-3">
                            <CommentList
                                :post="post"
                                :data="{ comments: comment.comments }"
                                :parent-comment="comment"
                            />
                        </DisclosurePanel>
                    </Disclosure>
                </div>
            </div>
        </div>
    </div>
</template>
