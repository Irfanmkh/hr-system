<script setup lang="ts">
const { token, user } = useAuth();
</script>

<template>
  <div>
    <h1>Halaman Test</h1>

    <h2>Data User</h2>

    <p>Nama: {{ user?.name }}</p>
    <p>Email: {{ user?.email }}</p>
    <p>Role: {{ user?.role }}</p>

    <h2>Token</h2>

    <p>{{ token }}</p>
  </div>
</template>
