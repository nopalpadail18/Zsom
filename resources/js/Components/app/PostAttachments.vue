<script setup>
import {
    ArrowDownTrayIcon,
    SpeakerWaveIcon,
    SpeakerXMarkIcon,
} from "@heroicons/vue/24/outline";
import { PaperClipIcon } from "@heroicons/vue/24/solid";
import { isImage, isVideo } from "@/helpers";
import { computed, ref } from "vue";

const props = defineProps({
    attachments: Array,
});

defineEmits(["attachmentClick"]);

let currentVideo = null;
const isMuted = ref(true);
const isAudioIcon = computed(() =>
    isMuted.value ? SpeakerXMarkIcon : SpeakerWaveIcon
);

// Fungsi untuk memainkan video
function playVideo(video) {
    if (currentVideo && currentVideo !== video) {
        currentVideo.pause();
    }
    currentVideo = video;
    if (video && typeof video.play === "function") {
        video.play();
    }
}

// Fungsi untuk menghentikan pemutaran video
function pauseVideo(video) {
    if (video && typeof video.pause === "function") {
        video.pause();
    }
}

// Fungsi untuk menginisialisasi Intersection Observer
function initializeIntersectionObserver(video) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                // Video masuk dalam tampilan layar
                playVideo(video);
            } else {
                // Video keluar dari tampilan layar
                pauseVideo(video);
            }
        });
    });

    observer.observe(video);
}

// Fungsi untuk memutar dan menghentikan pemutaran video
window.addEventListener("blur", function () {
    if (currentVideo) {
        pauseVideo(currentVideo);
    }
});

function playAndStopOnClick(video) {
    if (video.paused) {
        playVideo(video);
    } else {
        pauseVideo(video);
    }
}

// Fungsi untuk memutar dan menghentikan pemutaran audio
function toggleAud(video) {
    if (video) {
        video.muted = !video.muted;
        isMuted.value = video.muted;
    }
}
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
            <div v-else-if="isVideo(attachment)" class="relative text-white">
                <button
                    class="absolute bottom-1 right-1 w-8 h-8 z-20 flex items-end justify-end"
                    @click.stop="toggleAud($refs[`videoPlayer${ind}`][0])"
                >
                    <component
                        :is="isAudioIcon"
                        class="shadow rounded-full bg-black/100 p-2"
                    />
                </button>
                <button
                    @click.stop="
                        playAndStopOnClick($refs[`videoPlayer${ind}`][0])
                    "
                    class="absolute top-0 left-0 w-full h-full z-10 flex items-center justify-center"
                ></button>
                <video
                    v-if="attachment.url"
                    :ref="`videoPlayer${ind}`"
                    :src="attachment.url"
                    class="w-full h-full"
                    autoplay
                    muted
                    loop
                    @loadedmetadata="
                        initializeIntersectionObserver($event.target)
                    "
                ></video>
            </div>
            <div v-else class="flex flex-col justify-center items-center">
                <PaperClipIcon class="w-10 h-10 mb-3" />
                <small>{{ attachment.name }}</small>
            </div>
        </div>
    </template>
</template>
