<template>
    <Head title="Kanban Board" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kanban Board</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div v-if="creatingNewBoard" class="mb-6">
                        <label class="block mb-1 font-medium text-gray-700">Board Title</label>
                        <input
                            type="text"
                            v-model="newBoardTitle"
                            class="border border-gray-300 rounded px-3 py-2 w-full"
                            placeholder="Enter board title"
                        />
                        <button
                            class="mt-2 mr-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                            @click="cancelCreatingNewBoard"
                        >
                            Cancel
                        </button>
                        <button
                            class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                            @click="createBoard"
                            :disabled="newBoardTitle.trim() === ''"
                        >
                            + Create Board
                        </button>
                    </div>
                    <div v-if="!creatingNewBoard" class="mb-6">
                        <button
                            class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                            @click="creatingNewBoardClick"
                        >
                            + New Board
                        </button>
                    </div>

                    <div class="grid grid-cols-4 gap-4">
                        <div
                            v-for="board in boardList"
                            :key="board.id"
                            class="col-span-1 border p-4 rounded shadow"
                        >
                            {{ board.title }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { route } from 'ziggy-js';

export default {
    build: {
        rollupOptions: {
            input: 'resources/js/app.js',
        }
    },
    components: {
        AuthenticatedLayout,
        Head
    },
    props: {
        boards: Array,
    },
    data() {
        return {
            boardList: [...this.boards],
            newBoardTitle: '',
            creatingNewBoard: false
        };
    },
    methods: {
        createBoard() {
            const title = this.newBoardTitle.trim();
            if (!title) return alert('Title cannot be null!');

            axios.post(route('boards.store'), { title: title })
                .then(response => {
                    this.boardList = response.data.boards;
                    this.creatingNewBoard = false;
                })
                .catch(errors => {
                    alert(errors.message)
                    this.creatingNewBoard = false;
                });
        },
        creatingNewBoardClick() {
            this.creatingNewBoard = true;
        },
        cancelCreatingNewBoard() {
            this.creatingNewBoard = false;
        }
    }
};
</script>
