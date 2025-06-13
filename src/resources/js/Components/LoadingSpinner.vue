<template>
  <div class="fixed top-0 left-0 w-full flex justify-center z-50">
    <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-8 w-8 my-4"></div>
  </div>
</template>

<style>
.loader {
  border-top-color: #3490dc;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
