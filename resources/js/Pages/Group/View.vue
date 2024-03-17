<script setup>
import { ref } from "vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { XMarkIcon, CheckCircleIcon, CameraIcon } from "@heroicons/vue/24/outline";
import { Head,useForm } from "@inertiajs/vue3";
import PrimaryButtonVue from "@/Components/PrimaryButton.vue";
import InviteUserModalVue from "./InviteUserModal.vue";

const imagesForm = useForm({
  thumbnail: null,
  cover: null,
});

const showNotification = ref(true);
const coverImageSrc = ref("");
const thumbnailImageSrc = ref("");
const showInviteUserModal = ref(false);
const authUser = usePage().props.auth.user;
const isCurrentUserAdmin = computed(() => props.group.role === "admin");


const props = defineProps({
  errors: Object,
  success: {
    type: String,
  },
  group: {
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
function onThumbnailChange(e) {
  imagesForm.thumbnail = e.target.files[0];
  if (imagesForm.thumbnail) {
    const reader = new FileReader();
    reader.onload = () => {
      thumbnailImageSrc.value = reader.result;
    };
    reader.readAsDataURL(imagesForm.thumbnail);
  }
}

function cancleCoverImage() {
  imagesForm.cover = null;
  coverImageSrc.value = null;
}
function cancleThumbnailImage() {
  imagesForm.thumbnail = null;
  thumbnailImageSrc.value = null;
}

function submitCoverImage() {
  imagesForm.post(route("group.updateImages", props.group.slug), {
    onSuccess: () => {
        showNotification.value = true
      cancleCoverImage();
      setTimeout(() => {
        showNotification.value = false;
      }, 3000);
    },
  });
}
function submitThumbnailImage() {
  imagesForm.post(route("group.updateImages", props.group.slug), {
    onSuccess: () => {
        showNotification.value = true
      cancleThumbnailImage();
      setTimeout(() => {
        showNotification.value = false;
      }, 3000);
    },
  });
}

function joinToGroup()
{
    const form = useForm({})

    form.post(route('group.join', props.group.slug))
}
</script>

<template>
    <Head title="Group"/>
  <AuthenticatedLayout>
    <div class="max-w-[768px] mx-auto h-full overflow-auto">
      <div v-show="showNotification && success" class="my-2 py-2 px-3 font-medium text-sm text-white bg-emerald-500">
        {{ success }}
      </div>
      <div v-if="errors.thumbnail" class="my-2 py-2 px-3 font-medium text-sm text-white bg-red-400">
        {{ errors.thumbnail }}
      </div>
      <div class="group relative bg-white">
        <img :src="coverImageSrc || group.cover_url || '/img/default_cover.jpeg'" class="w-full h-[200px] object-cover" />
        <div v-if="isCurrentUserAdmin" class="absolute top-2 right-2">
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
          <div class="flex item-center justify-center rounded-full relative group/thumbnail -mt-[64px] ml-[48px] w-[128px] h-[128px]">
            <img :src="thumbnailImageSrc || group.thumbnail_url" class="w-full h-full object-cover rounded-full" />
            <button v-if="isCurrentUserAdmin && !thumbnailImageSrc" class="absolute left-0 top-0 right-0 bottom-0 bg-black/50 text-gray-200 rounded-full flex items-center justify-center opacity-0 group-hover/thumbnail:opacity-100">
              <CameraIcon class="w-8 h-8" />
              <input type="file" class="absolute left-0 top-0 right-0 bottom-0 opacity-0" @change="onThumbnailChange" />
            </button>
            <div v-else-if="isCurrentUserAdmin" class="absolute top-1 right-0 flex flex-col gap-2">
              <button @click="cancleThumbnailImage" class="w-7 h-7 flex item-center justify-center bg-red-500/80 text-white rounded-full">
                <XMarkIcon class="h-5 w-5" />
              </button>
              <button @click="submitThumbnailImage" class="w-7 h-7 flex item-center justify-center bg-emerald-500/80 text-white rounded-full">
                <CheckCircleIcon class="h-5 w-5" />
              </button>
            </div>
          </div>
          <div class="flex justify-between items-center flex-1 p-4">
            <h2 class="font-bold text-lg">
              {{ group.name }}
            </h2>

            <PrimaryButtonVue v-if="!authUser" :href="route('login')">
                Login to Join to this Group
            </PrimaryButtonVue>
            <PrimaryButtonVue v-if="isCurrentUserAdmin"
                @click="showInviteUserModal = true">
                Invite User
            </PrimaryButtonVue>
            <PrimaryButtonVue v-else-if="authUser && !group.role && group.auto_approval"
                @click="joinToGroup">
                Join to Group
            </PrimaryButtonVue>
            <PrimaryButtonVue v-else-if="authUser && !group.role && !group.auto_approval"
                @click="joinToGroup">
                Request to Join
            </PrimaryButtonVue>
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
          </TabPanels>
        </TabGroup>
      </div>
    </div>
  </AuthenticatedLayout>
  <InviteUserModalVue v-model="showInviteUserModal" />
</template>
