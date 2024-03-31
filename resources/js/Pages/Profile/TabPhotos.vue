<script setup>
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { PaperClipIcon } from "@heroicons/vue/24/solid";
import { isImage, isVideo } from "@/helpers";
import { ref } from "vue";
import AttachmentPreviewModal from "@/Components/app/AttachmentPreviewModal.vue";

defineProps({
    photos: Array,
});

const currenPhotosIndex = ref(0);
const showModal = ref(false);

defineEmits(["attachmentClick"]);
function openPhoto(index) {
    currenPhotosIndex.value = index;
    showModal.value = true;
}
</script>
<template>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
        <template v-for="(attachment, ind) in photos">
            <div
                @click="openPhoto(ind)"
                class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative cursor-pointer"
            >
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
                <div v-else-if="isVideo(attachment)" class="flex items-center">
                    <video :src="attachment.url" controls></video>
                </div>
                <div v-else class="flex flex-col justify-center items-center">
                    <PaperClipIcon class="w-10 h-10 mb-3" />
                    <small>{{ attachment.name }}</small>
                </div>
            </div>
        </template>
    </div>
    <div v-if="!photos.length" class="py-8 text-center">No Photos</div>

    <AttachmentPreviewModal
        :attachments="photos || []"
        v-model="showModal"
        v-model:index="currenPhotosIndex"
    />
</template>
