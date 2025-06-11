<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kanban Board') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8 lg:py-12">
        <div class="max-w-full sm:max-w-3xl md:max-w-5xl lg:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Welcome to your Kanban Board</h3>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-100 p-4 rounded shadow">
                            <h4 class="font-semibold mb-2">To Do</h4>
                        </div>
                        <div class="bg-gray-100 p-4 rounded shadow">
                            <h4 class="font-semibold mb-2">In Progress</h4>
                        </div>
                        <div class="bg-gray-100 p-4 rounded shadow">
                            <h4 class="font-semibold mb-2">Done</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
