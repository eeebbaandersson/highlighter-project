<script setup>
import { RouterView } from 'vue-router'
</script>

<template>
    <main class="app-layout">
        <RouterView />
    </main>
</template>

<style>


.app-layout {
    width: 100%;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
</style>
