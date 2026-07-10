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
                    <input v-model.trim="title" class="rounded-3xl border-1 border-gray-600 w-full p-2 mb-5" type="text" placeholder="title">
                </div>
                <div>
                    <textarea v-model.trim="content" class="rounded-3xl border-1 border-gray-600 w-full p-2 mb-7" placeholder="content"></textarea>
                </div>
                <div class="flex mb-7">
                    <div>

                        <input @change="handleFileSelect" multiple accept="image/*"  ref="fileInput" type="file" class="hidden">
                        <a href="#" @click.prevent="selectFile" class="w-32 p-2 bg-green-600 text-white rounded-lg hover:bg-green-800">Image</a>

                        <p v-if="imageFiles.length" class="my-4">Выбрано файлов: {{ imageFiles.length }}</p>

                        <div v-if="imageFiles.length" >
                            <div class="grid grid-cols-3 gap-4 w-full mb-9">
                                <div v-for="(item, index) in imageFiles" :key="item.id" class="aspect-[4/3] w-full relative">
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
            <div v-if="createdPost">
                <h3>{{ createdPost }}</h3>
                <div v-if="createdPost.images && createdPost.images.length">
                    <img
                        v-for="img in createdPost.images"
                        :key="img.id"
                        :src="'/storage/' + img.path"
                        alt="Изображение к посту"
                    />
                </div>
            </div>
        </div>

        <div>

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

const message = ref('');
const user = ref(null);
const createdPost = ref(null);


const getUserData = async () => {
    try {
        const response = await axios.get('/api/personal');
        user.value = response.data;
    } catch (error) {
        user.value = null; // Если 401 ошибка, значит пользователь не авторизован
    }
};

onMounted(() => {
    getUserData();
});


const canSubmit = computed(() => {
    return title.value.trim() && (content.value.trim() || imageFiles.value.length > 0);
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

        const response = await axios.post('/api/post', formDataPost, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        statusMessage.value = 'Данные успешно отправлены!';



        createdPost.value = response.data;

        message.value = response.data;

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
