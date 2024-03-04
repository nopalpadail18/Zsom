<script setup>
import TextInput from "@/Components/TextInput.vue";
import { ref } from "vue";
import FollowingItem from "@/Components/app/FollowingItem.vue";

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
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
            <FollowingItem
                image="https://picsum.photos/100"
                title="My Friend"
            />
        </div>
    </div>
</template>
