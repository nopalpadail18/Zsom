<script setup>
import TextInput from "@/Components/TextInput.vue";
import InputTextarea from "../InputTextarea.vue";
import InputLabel from "../InputLabel.vue";

defineProps({
    form: Object,
});
</script>
<template>
    <div class="mb-3">
        <InputLabel for="name" value="Group Name" />
        <TextInput
            v-model="form.name"
            placeholder="Group Name"
            type="text"
            class="w-full block mt-1"
            required
            autofocus
        />
    </div>

    <div class="mb-3">
        <label for="auto_approval">
            <input
                type="checkbox"
                v-model="form.auto_approval"
                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
            />
            Enable Auto Approval
        </label>
    </div>

    <div class="mb-3">
        <InputLabel for="about" value="About Group" />
        <InputTextarea
            v-model="form.about"
            placeholder="About Group"
            class="w-full block mt-1"
        />
    </div>
</template>
