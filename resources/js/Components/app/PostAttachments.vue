<script setup>
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { PaperClipIcon } from "@heroicons/vue/24/solid";
import { isImage } from "@/helpers";
defineProps({
    attachments: Array,
});

defineEmits(["attachmentClick"]);
</script>
<template>
    <template v-for="(attachment, ind) in attachments.slice(0, 4)">
        <div
            @click="$emit('attachmentClick', ind)"
            class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative cursor-pointer"
        >
            <div v-if="ind === 3 && attachments.length > 4">
                <span
                    class="absolute top-0 left-0 bottom-0 right-0 z-10 bg-black/60 flex items-center justify-center text-white text-2xl"
                    >+ {{ attachments.length - 4 }} more
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
            <div v-else class="flex flex-col justify-center items-center">
                <PaperClipIcon class="w-10 h-10 mb-3" />
                <small>{{ attachment.name }}</small>
            </div>
        </div>
    </template>
</template>
