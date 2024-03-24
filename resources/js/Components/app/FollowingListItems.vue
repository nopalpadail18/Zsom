<script setup>
import TextInput from "@/Components/TextInput.vue";
import { ref } from "vue";
import UserListItem from "./UserListItem.vue";
// import UserListItem from "@/Components/app/UserListItem.vue";

const searchKeyword = ref("");

defineProps({
    users: Array,
});
</script>
<template>
    <TextInput
        :model-value="searchKeyword"
        placeholder="Cari apa saja"
        class="w-full mt-3"
    />
    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="!users.length" class="text-gray-400 text-center p-3">
            Nolep yaa??
        </div>
        <div v-else>
            <UserListItem
                v-for="user of users"
                :key="user.id"
                :user="user"
                class="rounded-lg"
            />
        </div>
    </div>
</template>
