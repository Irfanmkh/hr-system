<script setup lang="ts">
const route = useRoute();
const config = useRuntimeConfig();
const { token } = useAuth();
definePageMeta({
  layout: "default",
  middleware: "auth",
});

const candidate = ref<any>(null);
const loading = ref(true);

const fetchDetail = async () => {
  try {
    const res: any = await $fetch(`/candidates/${route.params.id}`, {
      baseURL: config.public.apiBase,
      headers: { Authorization: `Bearer ${token.value}` },
    });
    candidate.value = res.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const updateStatus = async (newStatus: string) => {
  try {
    await $fetch(`/candidates/${route.params.id}/status`, {
      baseURL: config.public.apiBase,
      method: "PATCH",
      headers: { Authorization: `Bearer ${token.value}` },
      body: { status: newStatus },
    });
    candidate.value.status = newStatus;
    alert("Status berhasil diperbarui!");
  } catch (err) {
    alert("Gagal update status");
  }
};

onMounted(() => fetchDetail());
</script>

<template>
  <div class="p-4 md:p-6 max-w-4xl mx-auto space-y-6">
    <NuxtLink to="/candidates" class="text-sm text-gray-500 hover:underline"
      >← Kembali ke daftar</NuxtLink
    >

    <div v-if="loading">Loading detail kandidat...</div>
    <div v-else-if="candidate" class="space-y-6">
      <!-- Card Biodata -->
      <div class="bg-white p-6 rounded-lg shadow border space-y-4">
        <div
          class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"
        >
          <div>
            <h1 class="text-2xl font-bold">{{ candidate.full_name }}</h1>
            <p class="text-gray-500">
              {{ candidate.email }} • {{ candidate.phone }}
            </p>
          </div>
          <!-- Update Status Select -->
          <div class="flex items-center gap-2">
            <label class="text-sm font-semibold">Status:</label>
            <select
              :value="candidate.status"
              @change="(e: any) => updateStatus(e.target.value)"
              class="p-2 border rounded font-medium"
            >
              <option value="applied">Applied</option>
              <option value="interviewed">Interviewed</option>
              <option value="hired">Hired</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
        </div>

        <hr />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
          <div>
            <p class="font-semibold text-gray-600">Alamat</p>
            <p>{{ candidate.address }}</p>
          </div>
          <div>
            <p class="font-semibold text-gray-600">Tanggal Lahir</p>
            <p>{{ candidate.birth_date }}</p>
          </div>
        </div>
      </div>

      <!-- Card Riwayat Lamaran -->
      <div class="bg-white p-6 rounded-lg shadow border space-y-4">
        <h2 class="text-lg font-bold">Riwayat Lamaran</h2>
        <div
          v-if="!candidate.applications || candidate.applications.length === 0"
          class="text-gray-500 text-sm"
        >
          Belum ada riwayat lamaran.
        </div>
        <ul v-else class="divide-y">
          <li
            v-for="app in candidate.applications"
            :key="app.id"
            class="py-3 flex justify-between items-center"
          >
            <div>
              <p class="font-semibold">
                {{ app.job?.title || "Posisi Tidak Diketahui" }}
              </p>
              <p class="text-xs text-gray-500">
                Dilamar pada: {{ app.created_at }}
              </p>
            </div>
            <span class="text-xs px-2 py-1 bg-gray-100 rounded border">{{
              app.status
            }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>
