<template>
    <Head title="Kanban Board" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kanban Board</h2>
        </template>

        <div class="py-12">
            <LoadingSpinner v-if="loading" />
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
                            Create Board
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
                            class="relative group col-span-1 border p-4 rounded shadow"
                        >
                            <div v-if="!editingBoardId || editingBoardId !== board.id">
                                <h3 class="text-lg font-semibold">{{ board.title }}</h3>
                                <div class="absolute top-2 right-2 hidden group-hover:flex gap-2">
                                    <i class="fas fa-eye text-gray-500 cursor-pointer" @click="showBoard(board)"></i>
                                    <i class="fas fa-edit text-blue-600 cursor-pointer" @click="editBoard(board)"></i>
                                    <i class="fas fa-trash text-red-600 cursor-pointer" @click="deleteBoard(board)"></i>
                                </div>
                            </div>
                            <div v-else>
                                <input
                                    v-model="editingTitle"
                                    class="border border-gray-300 rounded px-2 py-1 w-full"
                                    @keyup.enter="updateBoard()"
                                    @blur="cancelEdit"
                                />
                            </div>
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
import LoadingSpinner from '@/Components/LoadingSpinner.vue';

export default {
    build: {
        rollupOptions: {
            input: 'resources/js/app.js',
        }
    },
    components: {
        AuthenticatedLayout,
        Head,
        LoadingSpinner
    },
    props: {
        boards: Array,
    },
    data() {
        return {
            boardList: [...this.boards],
            newBoardTitle: '',
            creatingNewBoard: false,
            editingBoardId: null,
            editingTitle: '',
            loading: false
        };
    },
    methods: {
        createBoard() {
            const title = this.newBoardTitle.trim();
            if (!title) return alert('Title cannot be null!');

            this.loading = true;

            axios.post(route('boards.store'), { title: title })
                .then(response => {
                    this.loading = false;
                    this.boardList = response.data.boards;
                    this.cancelCreatingNewBoard();
                })
                .catch(errors => {
                    alert(errors.message)
                    this.cancelCreatingNewBoard();
                });
        },
        creatingNewBoardClick() {
            this.creatingNewBoard = true;
        },
        cancelCreatingNewBoard() {
            this.creatingNewBoard = false;
            this.newBoardTitle = '';
        },
        showBoard(board) {
            this.$inertia.visit(route('boards.show', board.id));
        },
        editBoard(board) {
            this.editingBoardId = board.id;
            this.editingTitle = board.title;
        },
        cancelEdit() {
            this.editingBoardId = null;
            this.editingTitle = '';
        },
        updateBoard() {
            const title = this.editingTitle.trim();
            if (!title) return alert('Title cannot be null!');

            this.loading = true;

            axios.put(route('boards.update', this.editingBoardId), { title: title })
                .then(response => {
                    this.loading = false;
                    this.boardList = response.data.boards;
                    this.cancelEdit();
                })
                .catch(errors => {
                    alert(errors.message)
                    this.loading = false;
                    this.cancelEdit();
                });
        },
        deleteBoard(board) {
            this.loading = true;
            axios.delete(route('boards.delete', board.id))
                .then(response => {
                    this.loading = false;
                    this.boardList = response.data.boards;
                })
                .catch(errors => {
                    this.loading = false;
                    alert(errors.message)
                });
        }
    }
};
</script>
