<script setup>
import GroupItem from "@/Components/app/GroupItem.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref } from "vue";

const searchKeyword = ref("");

</script>
<template>
    <TextInput
        :model-value="searchKeyword"
        placeholder="Cari apa saja"
        class="w-full mt-4"
    />
    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400 text-center p-3">
            ko blm join, join dlu kanda
        </div>
        <div v-else>
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
            <GroupItem
                image="https://picsum.photos/100"
                title="My Group"
                description="My Group Description"
            />
        </div>
    </div>
</template>
