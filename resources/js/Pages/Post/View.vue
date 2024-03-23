<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import PostItem from "@/Components/app/PostItem.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PostModal from "@/Components/app/PostModal.vue";
import AttachmentPreviewModal from "@/Components/app/AttachmentPreviewModal.vue";
import { ref } from "vue";
defineProps({
    post: Object,
});

const authUser = usePage().props.auth.user;
const showEditModal = ref(false);
const showAttachmentsModal = ref(false);
const editPost = ref({});
const previeAttachmentsPost = ref({});

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
</script>
<template>
    <Head title="Post" />
    <AuthenticatedLayout>
        <div class="p-8 w-full max-w-[600px] mx-auto h-full overflow-auto">
            <PostItem
                :post="post"
                @editClick="openEditModal"
                @attachmentClick="openAttachmentPreviewModal"
            />
        </div>
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
    </AuthenticatedLayout>
</template>
