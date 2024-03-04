<script setup>
import { computed, ref } from "vue";
import {
    XMarkIcon,
    PaperClipIcon,
    BookmarkIcon,
    ArrowUturnLeftIcon,
} from "@heroicons/vue/24/solid";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from "@headlessui/vue";
import PostUserHeader from "./PostUserHeader.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { watch } from "vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { isImage } from "@/helpers";

const editor = ClassicEditor;
const editorConfig = {
    toolbar: [
        "bold",
        "italic",
        "|",
        "bulletedList",
        "numberedList",
        "|",
        "heading",
    ],
};

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    modelValue: Boolean,
});

const attachmentExtentions = usePage().props.attachmentExtentions;

const form = useForm({
    body: "",
    attachments: [],
    deleted_file_ids: [],
    _method: "POST",
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const computedAttachments = computed(() => {
    return [...attachmentFiles.value, ...(props.post.attachments || [])];
});

const emit = defineEmits(["update:modelValue", "hide"]);

watch(
    () => props.post,
    () => {
        form.body = props.post.body || "";
    }
);

function closeModal() {
    show.value = false;
    emit("hide");
    resetModal();
}

function resetModal() {
    form.reset();
    attachmentFiles.value = [];
    showExtentionText.value = false;
    attachmentErrors.value = [];
    if (props.post.attachments) {
        props.post.attachments.forEach((file) => (file.deleted = false));
    }
}

function submit() {
    form.attachments = attachmentFiles.value.map((myFile) => myFile.file);
    if (props.post.id) {
        form._method = "PUT";
        form.post(route("posts.update", props.post.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
            onError: (errors) => {
                proccessErrors(errors);
            },
        });
    } else {
        form.post(route("posts.create"), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
            onError: (errors) => {
                proccessErrors(errors);
            },
        });
    }
}

function proccessErrors(errors) {
    for (const key in errors) {
        if (key.includes(".")) {
            const [, index] = key.split(".");
            attachmentErrors.value[index] = errors[key];
        }
    }
}

const attachmentFiles = ref([]);
const attachmentErrors = ref([]);
const showExtentionText = ref(false);

async function onAttachmentChoose($event) {
    showExtentionText.value = false;

    for (const file of $event.target.files) {
        let parts = file.name.split(".");
        let ext = parts.pop().toLowerCase();
        if (!attachmentExtentions.includes(ext)) {
            showExtentionText.value = true;
        }
        const myFile = {
            file,
            url: await readFile(file),
        };
        attachmentFiles.value.push(myFile);
    }
    $event.target.value = null;
}

async function readFile(file) {
    return new Promise((res, rej) => {
        if (isImage(file)) {
            const reader = new FileReader();
            reader.onload = () => {
                res(reader.result);
            };
            reader.onerror = rej;

            reader.readAsDataURL(file);
        } else {
            res(null);
        }
    });
}

function removeFile(myFile) {
    if (myFile.file) {
        attachmentFiles.value = attachmentFiles.value.filter(
            (f) => f !== myFile
        );
    } else {
        form.deleted_file_ids.push(myFile.id);
        myFile.deleted = true;
    }
}

function undoDeleted(myFile) {
    myFile.deleted = false;
    form.deleted_file_ids = form.deleted_file_ids.filter(
        (id) => id !== myFile.id
    );
}
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
                    <div
                        class="flex min-h-full items-center justify-center p-4 text-center"
                    >
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="h3"
                                    class="flex items-center justify-between py-3 px-4 font-medium text-gray-900"
                                >
                                    {{
                                        post.id ? "Update Post" : "Create Post"
                                    }}
                                    <button
                                        @click="closeModal"
                                        class="w-8 h-8 rounded-full hover:bg-black/5 transition flex item-center justify-center"
                                    >
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                </DialogTitle>
                                <div class="p-4">
                                    <PostUserHeader
                                        :post="post"
                                        :show-time="false"
                                        class="mb-4"
                                    />

                                    <ckeditor
                                        :editor="editor"
                                        v-model="form.body"
                                        :config="editorConfig"
                                    ></ckeditor>

                                    <div
                                        v-if="showExtentionText"
                                        class="border-l-4 border-lime-300 py-2 px-3 bg-lime-100 mt-3 text-gray-800"
                                    >
                                        File must be one following extention:
                                        <br />
                                        <small>
                                            {{
                                                attachmentExtentions.join(", ")
                                            }}
                                        </small>
                                    </div>

                                    <div
                                        class="grid gap-3 my-3"
                                        :class="[
                                            computedAttachments.length === 1
                                                ? 'grid-cols-1'
                                                : 'grid-cols-2',
                                        ]"
                                    >
                                        <div
                                            v-for="(
                                                myFile, ind
                                            ) in computedAttachments"
                                        >
                                            <div
                                                class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative border-2"
                                                :class="
                                                    attachmentErrors[ind]
                                                        ? 'border-red-500'
                                                        : ''
                                                "
                                            >
                                                <div
                                                    v-if="myFile.deleted"
                                                    class="absolute left-0 bottom-0 right-0 py-2 px-3 text-sm bg-black text-white flex items-center justify-between z-10"
                                                >
                                                    To be deleted
                                                    <ArrowUturnLeftIcon
                                                        @click="
                                                            undoDeleted(myFile)
                                                        "
                                                        class="w-4 h-4 cursor-pointer"
                                                    />
                                                </div>

                                                <button
                                                    @click="removeFile(myFile)"
                                                    class="z-20 absolute top-3 right-3 w-7 h-7 flex item-center justify-center bg-black/30 text-white rounded-full hover:bg-black/40"
                                                >
                                                    <XMarkIcon
                                                        class="h-5 w-5"
                                                    />
                                                </button>
                                                <img
                                                    v-if="
                                                        isImage(
                                                            myFile.file ||
                                                                myFile
                                                        )
                                                    "
                                                    :src="myFile.url"
                                                    class="object-contain aspect-square"
                                                    :class="
                                                        myFile.deleted
                                                            ? 'opacity-50'
                                                            : ''
                                                    "
                                                />
                                                <div
                                                    v-else
                                                    class="flex felx-col justify-center items-center"
                                                    :class="
                                                        myFile.deleted
                                                            ? 'opacity-50'
                                                            : ''
                                                    "
                                                >
                                                    <PaperClipIcon
                                                        class="w-10 h-10 mb-3"
                                                    />
                                                    <small class="text-center">
                                                        {{
                                                            (
                                                                myFile.file ||
                                                                myFile
                                                            ).name
                                                        }}
                                                    </small>
                                                </div>
                                            </div>
                                            <small
                                                class="text-red-400 text-center"
                                                >{{
                                                    attachmentErrors[ind]
                                                }}</small
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2 py-3 px-4">
                                    <button
                                        type="button"
                                        class="flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 w-full relative"
                                        @click="submit"
                                    >
                                        <PaperClipIcon class="w-4 h-4 mr-2" />
                                        Attach Files
                                        <input
                                            @click.stop
                                            @change="onAttachmentChoose"
                                            type="file"
                                            multiple
                                            class="absolute left-0 bottom-0 top-0 right-0 opacity-0"
                                        />
                                    </button>
                                    <button
                                        type="button"
                                        class="flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 w-full"
                                        @click="submit"
                                    >
                                        <BookmarkIcon class="w-4 h-4 mr-2" />
                                        Submit
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>
