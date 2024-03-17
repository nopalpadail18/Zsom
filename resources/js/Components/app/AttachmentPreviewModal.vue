<script setup>
import { computed, ref } from "vue";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from "@headlessui/vue";
import { isImage } from "@/helpers";
import {
    ChevronLeftIcon,
    ChevronRightIcon,
    PaperClipIcon,
    XMarkIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    attachments: {
        type: Array,
        required: true,
    },
    index: Number,
    modelValue: Boolean,
});

const attachment = computed(() => {
    return props.attachments[currentIndex.value];
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const currentIndex = computed({
    get: () => props.index,
    set: (value) => emit("update:index", value),
});

const emit = defineEmits(["update:modelValue", "update:index", "hide"]);

function closeModal() {
    show.value = false;
    emit("hide");
}

function prev() {
    if (currentIndex.value > 0) {
        currentIndex.value--;
    }
}

function next() {
    if (currentIndex.value === props.attachments.length - 1) return;
    currentIndex.value++;
}

const onTouchStart = (event) => {
    touchStartX.value = event.touches[0].clientX;
};

const onTouchMove = (event) => {
    touchEndX.value = event.touches[0].clientX;
};

const onTouchEnd = () => {
    const deltaX = touchEndX.value - touchStartX.value;
    if (deltaX > 0) {
        // Swipe ke kanan
        prev();
    } else if (deltaX < 0) {
        // Swipe ke kiri
        next();
    }
    touchStartX.value = 0;
    touchEndX.value = 0;
};

const touchStartX = ref(0);
const touchEndX = ref(0);
</script>

<template>
    <teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="h-screen w-screen p-4">
                        <TransitionChild
                            as="template"
                            class="w-full h-full"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="flex flex-col w-full transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-xl transition-all"
                            >
                                <button
                                    @click="closeModal"
                                    class="absolute right-3 top-3 z-30 w-10 h-10 rounded-full hover:bg-black/10 transition flex item-center justify-center"
                                >
                                    <XMarkIcon class="w-6 h-6" />
                                </button>
                                <div class="relative group h-full"
                                    @touchstart="onTouchStart"
                                    @touchmove="onTouchMove"
                                    @touchend="onTouchEnd"
                                >
                                    <div
                                        @click="prev()"
                                        class="absolute opacity-0 group-hover:opacity-100 text-gray-300 cursor-pointer flex items-center w-12 h-full left-0 bg-black/5"
                                    >
                                        <ChevronLeftIcon class="w-12" />
                                    </div>
                                    <div
                                        @click="next()"
                                        class="absolute opacity-0 group-hover:opacity-100 text-gray-300 cursor-pointer flex items-center w-12 h-full right-0 bg-black/5"
                                    >
                                        <ChevronRightIcon class="w-12" />
                                    </div>
                                    <div
                                        class="flex items-center justify-center w-full h-full p-3"
                                    >
                                        <img
                                            v-if="isImage(attachment)"
                                            :src="attachment.url"
                                            class="max-h-full max-w-full"
                                        />
                                        <div
                                            v-else
                                            class="flex felx-col justify-center items-center p-32"
                                        >
                                            <PaperClipIcon
                                                class="w-10 h-10 mb-3"
                                            />
                                            <small class="text-center">
                                                {{ attachment.name }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>
