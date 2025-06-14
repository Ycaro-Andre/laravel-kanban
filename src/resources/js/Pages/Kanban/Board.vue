<template>
    <Head :title=board.title />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{boardData.title}}</h2>
        </template>

        <div class="py-12">
            <div v-if="editingCard.id" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white rounded shadow-lg p-6 w-full max-w-md">
                    <h2 class="text-lg font-bold mb-4">Edit Card</h2>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input
                        v-model="editingCard.title"
                        class="border border-gray-300 rounded px-3 py-2 w-full mb-4"
                    />

                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea
                        v-model="editingCard.description"
                        class="border border-gray-300 rounded px-3 py-2 w-full mb-4"
                        rows="4"
                    ></textarea>

                    <div class="flex justify-end gap-2">
                        <button @click="cancelCardEdit" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
                        <button @click="updateCard" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
                    </div>
                </div>
            </div>
            <LoadingSpinner v-if="loading" />
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div v-if="creatingNewList" class="mb-6">
                        <label class="block mb-1 font-medium text-gray-700">List title</label>
                        <input
                            type="text"
                            v-model="newListTitle"
                            class="border border-gray-300 rounded px-3 py-2 w-full"
                            placeholder="Enter list title"
                        />
                        <button
                            class="mt-2 mr-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                            @click="cancelCreatingNewList"
                        >
                            Cancel
                        </button>
                        <button
                            class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                            @click="createList"
                            :disabled="newListTitle.trim() === ''"
                        >
                            Create List
                        </button>
                    </div>
                    <div v-if="!creatingNewList" class="mb-6">
                        <button
                            class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                            @click="creatingNewListClick"
                        >
                            + New List
                        </button>
                    </div>
                    <draggable
                        v-model="boardData.lists"
                        group="lists"
                        class="grid grid-cols-4 gap-4"
                        item-key="id"
                        @change="onListChange"
                    >
                        <template #item="{ element: list, index: listIndex }">
                            <div
                                class="relative group flex flex-col col-span-1 border p-4 rounded shadow bg-white"
                            >
                                <div v-if="!editingList.id || editingList.id !== list.id">
                                    <h3 class="font-bold mb-2">{{ list.title }}</h3>
                                    <div class="absolute top-2 right-2 hidden group-hover:flex gap-2">
                                        <i class="fas fa-edit text-blue-600 cursor-pointer" @click="editList(list, listIndex)"></i>
                                        <i class="fas fa-trash text-red-600 cursor-pointer" @click="deleteList(list, listIndex)"></i>
                                    </div>
                                </div>
                                <div v-else>
                                    <input
                                        v-model="editingList.title"
                                        class="border border-gray-300 rounded px-2 py-1 w-full"
                                        @keyup.enter="updateList()"
                                        @blur="cancelListEdit"
                                    />
                                </div>

                                <draggable
                                    v-model="list.cards"
                                    group="cards"
                                    item-key="id"
                                    class="flex flex-col gap-2 mt-2"
                                    @change="onCardChange($event, list)"
                                >
                                    <template #item="{ element: card, index: cardIndex }">
                                        <div class="relative bg-gray-100 rounded p-2 my-1 hover:bg-gray-200">
                                            <div>
                                                <h5 class="font-bold mb-2">{{ card.title }}</h5>
                                                <div class="absolute inset-0 flex items-start justify-end top-2 right-2 flex gap-2 opacity-0 hover:opacity-100 transition-opacity">
                                                    <i class="fas fa-edit text-blue-600 cursor-pointer" @click="editCard(card, cardIndex, listIndex)"></i>
                                                    <i class="fas fa-trash text-red-600 cursor-pointer" @click="deleteCard(card, cardIndex, listIndex)"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                                <div v-if="creatingNewCard && creatingNewCardListIndex == listIndex" class="mb-6">
                                    <label class="block mb-1 font-medium text-gray-700">Card title</label>
                                    <input
                                        type="text"
                                        v-model="newCardTitle"
                                        class="border border-gray-300 rounded px-3 py-2 w-full"
                                        placeholder="Enter card title"
                                    />
                                    <button
                                        class="mt-2 mr-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                                        @click="cancelCreatingNewCard"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                                        @click="createCard(list, listIndex)"
                                        :disabled="newCardTitle.trim() === ''"
                                    >
                                        Create Card
                                    </button>
                                </div>
                                <div v-if="!creatingNewCard" class="mb-6">
                                    <button
                                        class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                                        @click="creatingNewCardClick(listIndex)"
                                    >
                                        + New Card
                                    </button>
                                </div>
                            </div>
                        </template>
                    </draggable>
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
import draggable from 'vuedraggable';
import debounce from 'lodash.debounce';

export default {
    build: {
        rollupOptions: {
            input: 'resources/js/app.js',
        }
    },
    components: {
        AuthenticatedLayout,
        Head,
        LoadingSpinner,
        draggable
    },
    props: {
        board: Object
    },
    data() {
        return {
            boardData: {...this.board},
            newListTitle: '',
            newCardTitle: '',
            creatingNewList: false,
            creatingNewCard: false,
            creatingNewCardListIndex: null,
            editingList: {},
            editingCard: {},
            loading: false,
            changedCardPositions: [],
            changedListPositions: [],
            changedCardInfo: null
        };
    },
    watch: {
    board(newVal) {
        this.boardData = {...newVal};
    }
    },
    created() {
        this.updateCardPositionsDebounce = debounce(this.updateCardPositions, 3000);
        this.updateListPositionsDebounce = debounce(this.updateListPositions, 3000);
    },
    methods: {
        creatingNewListClick() {
            this.creatingNewList = true;
        },
        cancelCreatingNewList() {
            this.creatingNewList = false;
            this.newListTitle = '';
        },
        editList(list, listIndex) {
            this.editingList.id = list.id;
            this.editingList.title = list.title;
            this.editingList.listIndex = listIndex
        },
        cancelListEdit() {
            this.editingList = {}
        },
        createList() {
            const title = this.newListTitle.trim();
            if (!title) return alert('Title cannot be null!');

            this.loading = true;

            axios.post(route('lists.store'), { title: title, board_id: this.boardData.id })
                .then(response => {
                    this.loading = false;
                    this.boardData = response.data.board;
                    this.cancelCreatingNewList();
                })
                .catch(errors => {
                    this.loading = false;
                    alert(errors.message)
                    this.cancelCreatingNewList();
                });
        },
        updateList() {
            const { listIndex, title, id } = this.editingList

            const trimmedTitle = title.trim();

            if (!trimmedTitle) return alert('Title cannot be null!');

            this.boardData.lists[listIndex].title = trimmedTitle;
            this.cancelListEdit();

            axios.put(route('lists.update', id), { 
                title: trimmedTitle,
            })
            .catch(errors => {
                alert(errors.message)
                window.location.reload()
            });
        },
        deleteList(list, listIndex) {
            this.boardData.lists.splice(listIndex, 1);
            this.boardData.lists.forEach((list, index) => {
                list.position = index;
            });

            axios.delete(route('lists.delete', list.id))
                .catch(errors => {
                    alert(errors.message)
                    window.location.reload
            });
        },
        updateListPositions() {
            this.loading = true;
            axios.post(route('lists.change-positions'), {changes: this.changedListPositions})
                .then(response => {
                    this.loading = false;
                })
                .catch(errors => {
                    this.loading = false;
                    this.changedListPositions = [];
                    alert(errors.message)
                    window.location.reload()
                })
                .finally(() => {
                    this.changedListPositions = [];
            });
        },
        creatingNewCardClick(index) {
            this.creatingNewCard = true;
            this.creatingNewCardListIndex = index;
        },
        cancelCreatingNewCard() {
            this.creatingNewCard = false;
            this.newCardTitle = '';
            this.creatingNewCardListIndex = null;
        },
        editCard(card, cardIndex, listIndex) {
            this.editingCard.id = card.id;
            this.editingCard.title = card.title;
            this.editingCard.description = card.description;
            this.editingCard.cardIndex = cardIndex
            this.editingCard.listIndex = listIndex
        },
        cancelCardEdit() {
            this.editingCard = {}
        },
        createCard(list, listIndex) {
            let title = this.newCardTitle.trim();
            if (!title) return alert('Title cannot be null!');

            let cardIndex = this.boardData.lists[listIndex].cards.length;

            this.boardData.lists[listIndex].cards.push({
                title: title,
                description: '',
                kanban_list_id: list.id
            })

            this.cancelCreatingNewCard();

            axios.post(route('cards.store'), { title: title, kanban_list_id: list.id})
                .then(response => {
                    this.boardData.lists[listIndex].cards[cardIndex].id = response.data.card.id;
                    this.boardData.lists[listIndex].cards[cardIndex].position = response.data.card.position;
                })
                .catch(errors => {
                    alert(errors.message);
                    window.location.reload()
                });
        },
        updateCard() {
            const { listIndex, cardIndex, title, description, id } = this.editingCard

            const trimmedTitle = title.trim();
            const trimmedDescription = description.trim();

            if (!trimmedTitle) return alert('Title cannot be null!');
            if (!trimmedDescription) return alert('Description cannot be null!');

            this.boardData.lists[listIndex].cards[cardIndex].title = trimmedTitle;
            this.boardData.lists[listIndex].cards[cardIndex].description = trimmedDescription;
            this.cancelCardEdit();

            axios.put(route('cards.update', id), { 
                title: trimmedTitle,
                description: trimmedDescription
            })
            .catch(errors => {
                alert(errors.message)
                window.pageXOffset.reload()
            });
        },
        deleteCard(card, cardIndex, listIndex) {

            this.boardData.lists[listIndex].cards.splice(cardIndex, 1);
            this.boardData.lists[listIndex].cards.forEach((card, index) => {
                card.position = index;
            });

            axios.delete(route('cards.delete', card.id))
                .catch(errors => {
                    alert(errors.message)
                    window.location.reload
            });
        },
        onListChange(event) {
            const { moved } = event;

            if (moved) {
                this.changedListPositions.push({
                    'kanban_list_id': moved.element.id,
                    'new_position': moved.newIndex,
                    'old_position': moved.oldIndex
                })
                this.updateListPositionsDebounce()
            }
        },
        onCardChange(event, list) {
            // moved means that a card was moved within the same list
            // added and removed means that a card was moved between lists
            const { moved, added, removed } = event;

            // Stayed at the same list
            if (moved) {
                this.changedCardPositions.push({
                    'old_list': list.id,
                    'new_list': list.id,
                    'card_id': moved.element.id,
                    'new_position': moved.newIndex,
                    'old_position': moved.oldIndex
                })
                this.updateCardPositionsDebounce()
            }

            // Added to a new list
            if (added) {
                this.changedCardInfo = {
                    'new_list': list.id,
                    'new_position': added.newIndex
                };
            }

            // Removed from original list
            if (removed) {
                this.changedCardPositions.push({
                    'old_list': list.id,
                    'new_list': this.changedCardInfo?.new_list,
                    'card_id': removed.element.id,
                    'new_position': this.changedCardInfo?.new_position,
                    'old_position': removed.oldIndex
                })
                this.changedCardInfo = null;
                this.updateCardPositionsDebounce()
            }
        },
        updateCardPositions() {
            this.loading = true;
            axios.post(route('cards.change-positions'), {changes: this.changedCardPositions})
                .then(response => {
                    this.loading = false;
                })
                .catch(errors => {
                    this.loading = false;
                    this.changedCardPositions = [];
                    alert(errors.message)
                    window.location.reload
                })
                .finally(() => {
                    this.changedCardPositions = [];
            });
        }
    }
};
</script>
