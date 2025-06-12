<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

export default {
  components: {
    AuthenticatedLayout,
    Head,
  },
  props: {
    boards: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      boardList: [...this.boards],
    };
  },
  methods: {
    logBoardTitles() {
      console.log(this.boardList.map(board => board.name));
    },
  },
};
</script>

<template>
  <Head title="Kanban Board" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kanban Board</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
          <div v-if="boardList.length">
            <div
              v-for="board in boardList"
              :key="board.id"
              class="mb-6 border p-4 rounded shadow"
            >
              <h3 class="text-lg font-bold mb-2">{{ board.name }}</h3>

              <div class="grid grid-cols-3 gap-4">
                <div
                  v-for="list in board.lists"
                  :key="list.id"
                  class="bg-gray-100 p-3 rounded"
                >
                  <h4 class="font-semibold">{{ list.name }}</h4>
                  <ul class="mt-2 space-y-1">
                    <li
                      v-for="card in list.cards"
                      :key="card.id"
                      class="bg-white p-2 rounded shadow text-sm"
                    >
                      {{ card.title }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div v-else>No boards found.</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>