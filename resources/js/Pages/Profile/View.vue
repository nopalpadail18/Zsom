<script setup>
import { ref } from "vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import Edit from "./Edit.vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { XMarkIcon, CheckCircleIcon, CameraIcon } from "@heroicons/vue/24/outline";
import { Head, useForm } from "@inertiajs/vue3";

const imagesForm = useForm({
  cover: null,
  avatar: null,
});

const showNotification = ref(true);
const coverImageSrc = ref("");
const avatarImageSrc = ref("");
const authUser = usePage().props.auth.user;
const isMyProfile = computed(() => authUser && authUser.id === props.user.id);

const props = defineProps({
  errors: Object,
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
  success: {
    type: String,
  },
  user: {
    type: Object,
  },
});

function onCoverChange(e) {
  imagesForm.cover = e.target.files[0];
  if (imagesForm.cover) {
    const reader = new FileReader();
    reader.onload = () => {
      coverImageSrc.value = reader.result;
    };
    reader.readAsDataURL(imagesForm.cover);
  }
}
function onAvatarChange(e) {
  imagesForm.avatar = e.target.files[0];
  if (imagesForm.avatar) {
    const reader = new FileReader();
    reader.onload = () => {
      avatarImageSrc.value = reader.result;
    };
    reader.readAsDataURL(imagesForm.avatar);
  }
}

function cancleCoverImage() {
  imagesForm.cover = null;
  coverImageSrc.value = null;
}
function cancleAvatarImage() {
  imagesForm.avatar = null;
  avatarImageSrc.value = null;
}

function submitCoverImage() {
  imagesForm.post(route("profile.updateImages"), {
    preserveScroll:true,
    onSuccess: () => {
        showNotification.value = true
      cancleCoverImage();
      setTimeout(() => {
        showNotification.value = false;
      }, 3000);
    },
  });
}
function submitAvatarImage() {
  imagesForm.post(route("profile.updateImages"), {
    preserveScroll:true,
    onSuccess: () => {
        showNotification.value = true
      cancleAvatarImage();
      setTimeout(() => {
        showNotification.value = false;
      }, 3000);
    },
  });
}
</script>

<template>
    <Head title="Profile"/>
  <AuthenticatedLayout>
    <div class="max-w-[768px] mx-auto h-full overflow-auto">
      <div v-show="showNotification && success" class="my-2 py-2 px-3 font-medium text-sm text-white bg-emerald-500">
        {{ success }}
      </div>
      <div v-if="errors.cover" class="my-2 py-2 px-3 font-medium text-sm text-white bg-red-400">
        {{ errors.cover }}
      </div>
      <div class="group relative bg-white">
        <img :src="coverImageSrc || user.cover_url || '/img/default_cover.jpeg'" class="w-full h-[200px] object-cover" />
        <div class="absolute top-2 right-2">
          <button v-if="!coverImageSrc" class="bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs rounded flex items-center opacity-0 group-hover:opacity-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3 mr-2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"
              />
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
            </svg>

            update cover
            <input type="file" class="absolute left-0 top-0 right-0 bottom-0 opacity-0" @change="onCoverChange" />
          </button>
          <div v-else class="flex gap-2 bg-white p-2 opacity-0 group-hover:opacity-100">
            <button @click="cancleCoverImage" class="bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs rounded flex items-center transition">
              <XMarkIcon class="h-3 w-3 mr-2" />
              Cancle
            </button>
            <button @click="submitCoverImage" class="bg-gray-800 hover:bg-gray-900 text-gray-100 py-1 px-2 text-xs rounded flex items-center transition">
              <CheckCircleIcon class="h-3 w-3 mr-2" />
              Submit
            </button>
          </div>
        </div>
        <div class="flex">
          <div class="flex item-center justify-center rounded-full relative group/avatar -mt-[64px] ml-[48px] w-[128px] h-[128px]">
            <img :src="avatarImageSrc || user.avatar_url || '/img/default_avatar.jpeg'" class="w-full h-full object-cover rounded-full" />
            <button v-if="!avatarImageSrc" class="absolute left-0 top-0 right-0 bottom-0 bg-black/50 text-gray-200 rounded-full flex items-center justify-center opacity-0 group-hover/avatar:opacity-100">
              <CameraIcon class="w-8 h-8" />
              <input type="file" class="absolute left-0 top-0 right-0 bottom-0 opacity-0" @change="onAvatarChange" />
            </button>
            <div v-else class="absolute top-1 right-0 flex flex-col gap-2">
              <button @click="cancleAvatarImage" class="w-7 h-7 flex item-center justify-center bg-red-500/80 text-white rounded-full">
                <XMarkIcon class="h-5 w-5" />
              </button>
              <button @click="submitAvatarImage" class="w-7 h-7 flex item-center justify-center bg-emerald-500/80 text-white rounded-full">
                <CheckCircleIcon class="h-5 w-5" />
              </button>
            </div>
          </div>
          <div class="flex justify-between items-center flex-1 p-4">
            <h2 class="font-bold text-lg">
              {{ user.name }}
            </h2>
          </div>
        </div>
      </div>
      <div class="border-t">
        <TabGroup>
          <TabList class="flex bg-white">
            <Tab v-slot="{ selected }" as="template">
              <TabItem :selected="selected" text="Posts" />
            </Tab>
            <Tab v-slot="{ selected }" as="template">
              <TabItem :selected="selected" text="Followers" />
            </Tab>
            <Tab v-slot="{ selected }" as="template">
              <TabItem :selected="selected" text="Followings" />
            </Tab>
            <Tab v-slot="{ selected }" as="template">
              <TabItem :selected="selected" text="Photos" />
            </Tab>
            <Tab v-if="isMyProfile" v-slot="{ selected }" as="template">
              <TabItem :selected="selected" text="My Profile" />
            </Tab>
          </TabList>

          <TabPanels class="mt-2">
            <TabPanel class="rounded-xl bg-white p-3 shadow">
              Posts
            </TabPanel>
            <TabPanel class="rounded-xl bg-white p-3 shadow">
              Followers
            </TabPanel>
            <TabPanel class="rounded-xl bg-white p-3 shadow">
              Followings
            </TabPanel>
            <TabPanel class="rounded-xl bg-white p-3 shadow">
              Photos
            </TabPanel>
            <TabPanel v-if="isMyProfile">
              <Edit :must-verify-email="mustVerifyEmail" :status="status" />
            </TabPanel>
          </TabPanels>
        </TabGroup>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
