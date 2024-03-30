<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";

defineProps({
    content: String,
    contentClass: String,
});
</script>
<template>
    <Disclosure v-slot="{ open }">
        <div
            class="ck-content-output text-sm"
            v-if="!open"
            v-html="
                content.substring(0, 35) + (content.length > 35 ? '...' : '')
            "
            :class="contentClass"
        />
        <template v-if="content && content.length > 35">
            <DisclosurePanel>
                <div
                    class="ck-content-output text-sm"
                    :class="contentClass"
                    v-html="content"
                />
            </DisclosurePanel>
            <div class="flex justify-start">
                <DisclosureButton class="text-gray-400 text-sm">
                    {{ open ? "less" : "more" }}
                </DisclosureButton>
            </div>
        </template>
    </Disclosure>
</template>
