<script setup>
import GroupListItems from "@/Components/app/GroupListItems.vue";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import GroupModal from "./GroupModal.vue";
import { ref } from "vue";

const showNewGroupModal = ref(false);
</script>
<template>
    <div
        class="px-3 bg-white rounded border h-full py-3 overflow-hidden flex flex-col"
    >
        <div class="block lg:hidden">
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="w-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">My Group</h2>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="w-6 h-6 transition-all"
                            :class="open ? 'rotate-90 transform' : ''"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                </DisclosureButton>
                <DisclosurePanel>
                    <button @click="showNewGroupModal = true"
                    class="text-white text-sm bg-indigo-600 hover:bg-indigo-500 px-2 py-1 rounded">Add Group</button>

                    <GroupListItems />
                </DisclosurePanel>
            </Disclosure>
        </div>
        <div class="h-full overflow-hidden flex-col hidden lg:flex">
            <div class="flex justify-between">
                <h2 class="text-xl font-bold">My Group</h2>
                <button @click="showNewGroupModal = true"
                    class="text-white text-sm bg-indigo-600 hover:bg-indigo-500 px-2 py-1 rounded">Add Group</button>
            </div>
            <GroupListItems />
        </div>
    </div>
    <GroupModal :model-value="showNewGroupModal" @hide="showNewGroupModal = false"/>
</template>
