<template>
    <div  class="p-6 w-130 mx-auto">
        <div v-if="user">
            <h1 class="mb-10 w-full text-[18px] text-center">Hello, {{ user.name }}</h1>
        </div>
        <div v-if="user">
            <form @submit.prevent="submitForm" class="mb-13">
                <div>
                    <h1 class="mb-3 w-full text-[17px]">Post</h1>
                </div>
                <div>
                    <input v-model.trim="title" class="rounded-3xl border-1 border-gray-600 w-full px-3 py-2 mb-5" type="text" placeholder="title">
                </div>
                <div>
                    <textarea v-model.trim="content" class="rounded-3xl border-1 border-gray-600 w-full px-3 py-2 mb-7" placeholder="content"></textarea>
                </div>
                <div class="flex mb-7">
                    <div>

                        <input @change="handleFileSelect" multiple accept="image/*"  ref="fileInput" type="file" class="hidden">
                        <a href="#" @click.prevent="selectFile" class="w-32 p-2 bg-green-600 text-white rounded-lg hover:bg-green-800">Image</a>

                        <p v-if="imageFiles.length" class="my-4">Выбрано файлов: {{ imageFiles.length }}</p>

                        <div v-if="imageFiles.length" >
                            <div class="grid grid-cols-3 gap-4 w-full mb-9">
                                <div v-for="(item, index) in imageFiles" :key="item?.id || index" class="aspect-[4/3] w-full relative">
                                    <div class="">
                                        <button type="button" @click="removeImage(item.id)" class="w-8 h-8 bg-gray-400 hover:bg-gray-500 text-white rounded-full text-center cursor-pointer absolute -top-3 -right-3 font-bold">×</button>
                                    </div>
                                    <img :src="item.preview" alt="Предпросмотр" class="w-full h-full object-cover">
                                </div>
                            </div>


                            <button type="button" class="w-32 p-2 bg-red-400 text-white rounded-lg hover:bg-red-200 cursor-pointer " @click="removeImagesAll">Удалить все</button>


                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <button :disabled="isUploading || !canSubmit"  type="submit" class="block rounded-3xl p-2 w-32 text-white text-center bg-sky-500 hover:bg-sky-700 cursor-pointer">
                        {{ isUploading ? 'Отправка...' : 'Опубликовать' }}
                    </button>
                </div>

                <div>
                    <p v-if="statusMessage">{{ statusMessage }}</p>
                </div>
            </form>

        </div>

        <div v-if="posts && posts.length">
            <div v-for="post in posts" :key="post?.id" class="mb-7 pb-7 border-b border-gray-300">
                <h1 class="text-xl font-bold mb-2">{{ post?.title }}</h1>
                <div v-if="post?.images && post.images.length" class="flex flex-wrap gap-2 mb-3">
                    <img v-for="(img, imgIndex) in post.images" :src="img?.url" :key="img?.id || imgIndex" alt="Изображение">
                </div>
                <p class="mb-4 text-gray-700 whitespace-pre-line">{{ post?.content}}</p>
                <p class="text-slate-500 text-sm text-right">{{ post?.created_at}}</p>
            </div>

        </div>

    </div>
</template>

<script setup>
import axios from "../../axios.js";
import {ref, onMounted, computed} from 'vue';

const title = ref('');
const content = ref('');

const imageFiles  = ref([]);

const fileInput = ref(null);
const isUploading = ref(false);
const statusMessage = ref('');

const user = ref(null);

const posts = ref([]);
const getUserData = async () => {
    try {
        const response = await axios.get('/api/personal');
        user.value = response.data;
    } catch (error) {
        user.value = null;
    }
};

onMounted(() => {
    getUserData();
    getPosts();
});

const getPosts = async() => {
    const response = await axios.get('/api/posts');
    posts.value = response.data.data
}

const canSubmit = computed(() => {
    return title.value && title.value.trim() && (content.value.trim() || imageFiles.value.length > 0);
});

const handleFileSelect = (event) => {
    const selectedFiles  = Array.from(event.target.files);
    if (!selectedFiles.length) return;


    selectedFiles.forEach(file => {
        const uniqueId = Date.now() + Math.random().toString(36).substring(2, 9);
        const previewUrl = URL.createObjectURL(file);

        imageFiles.value.push({
            id: uniqueId,
            file: file,
            name: file.name,
            preview: previewUrl
        })
    });
    event.target.value = '';
};

const selectFile = () => {
    fileInput.value.click();
};

const removeImagesAll = () => {
    imageFiles.value.forEach(item => URL.revokeObjectURL(item.preview));
    imageFiles.value = [];
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const removeImage = (id) => {

    const index = imageFiles.value.findIndex(item => item.id === id);

    if (index !== -1) {
        URL.revokeObjectURL(imageFiles.value[index].preview);
        imageFiles.value.splice(index, 1);
    }
};

// Post and File
const submitForm  = async () => {
    if (!canSubmit.value) return;

    const formDataPost = new FormData();
    formDataPost.append('title', title.value);
    formDataPost.append('content', content.value);
    imageFiles.value.forEach((item) => {
        formDataPost.append('images[]', item.file);
    });


    try {
        isUploading.value = true;
        statusMessage.value = 'Отправка данных на Laravel...';

        const response = await axios.post('/api/posts', formDataPost, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        statusMessage.value = 'Данные успешно отправлены!';

        const newPost = response.data.data;
        posts.value.unshift(newPost);


        title.value = '';
        content.value = '';

        removeImagesAll();


    } catch (error) {
        statusMessage.value = 'Ошибка при отправке данных';
        console.error(error);
    }finally {
        isUploading.value = false;
    }



};


</script>

<style scoped>

</style>
