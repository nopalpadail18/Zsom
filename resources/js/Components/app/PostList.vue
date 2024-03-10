<script setup>
import { usePage } from "@inertiajs/vue3";
import PostItem from "./PostItem.vue";
import PostModal from "./PostModal.vue";
import { onMounted, onUpdated, ref } from "vue";
import AttachmentPreviewModal from "./AttachmentPreviewModal.vue";
import axiosClient from "@/axiosClient";

const page = usePage();

const authUser = usePage().props.auth.user;
const showEditModal = ref(false);
const showAttachmentsModal = ref(false);
const editPost = ref({});
const previeAttachmentsPost = ref({});
const loadMoreIntersec = ref(null);

const allPosts = ref({
    data: page.props.posts.data,
    next: page.props.posts.links.next,
});

const props = defineProps({
    posts: Array,
});



function openEditModal(post) {
    editPost.value = post;
    showEditModal.value = true;
}

function openAttachmentPreviewModal(post, index) {
    previeAttachmentsPost.value = { post, index };
    showAttachmentsModal.value = true;
}

function onModalHide() {
    editPost.value = {
        id: null,
        body: "",
        user: authUser,
    };
}

function loadMore(){

    if (!allPosts.value.next) {
        return
    }
    axiosClient.get(allPosts.value.next)
        .then(({ data }) => {
            allPosts.value.data = [...allPosts.value.data, ...data.data]
            allPosts.value.next = data.links.next
        })
}

onMounted(() => {
    const observer = new IntersectionObserver((entries) =>
        entries.forEach((entry) => entry.isIntersecting && loadMore()), {
            rootMargin: "-250px 0px 0px 0px",
        }
    )

    observer.observe(loadMoreIntersec.value)
})
</script>
<template>
    <div class="overflow-auto">
        <PostItem
            v-for="post in allPosts.data"
            :key="post.id"
            :post="post"
            @editClick="openEditModal"
            @attachmentClick="openAttachmentPreviewModal"
        />
        <div ref="loadMoreIntersec"></div>
        <PostModal
            :post="editPost"
            v-model="showEditModal"
            @hide="onModalHide"
        />
        <AttachmentPreviewModal
            :attachments="previeAttachmentsPost.post?.attachments || []"
            v-model:index="previeAttachmentsPost.index"
            v-model="showAttachmentsModal"
        />
    </div>
</template>
