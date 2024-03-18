<script setup>
import TextInput from "@/Components/TextInput.vue";
import { ref } from "vue";
// import UserListItem from "@/Components/app/UserListItem.vue";

const searchKeyword = ref("");
</script>
<template>
    <TextInput
        :model-value="searchKeyword"
        placeholder="Cari apa saja"
        class="w-full mt-3"
    />
    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400 text-center p-3">
            ko blm join
        </div>
        <div v-else>

        </div>
    </div>
</template>
