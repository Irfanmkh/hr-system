<script setup lang="ts">
const config = useRuntimeConfig();
const { token } = useAuth();
definePageMeta({
  layout: "default",
  middleware: "auth",
});

// Query Params
const search = ref("");
const statusFilter = ref("");
const sortBy = ref("created_at");
const sortDir = ref("desc"); // 💡 Disesuaikan nama kuncinya ke sortDir
const page = ref(1);

const candidates = ref<any[]>([]);
const pagination = ref<any>({});
const loading = ref(false);

const fetchCandidates = async () => {
  loading.value = true;
  try {
    const res: any = await $fetch("/candidates", {
      baseURL: config.public.apiBase,
      headers: { Authorization: `Bearer ${token.value}` },
      params: {
        search: search.value || undefined,
        status: statusFilter.value || undefined, // Send undefined jika "" agar tidak terikutsertakan
        sort_by: sortBy.value,
        sort_dir: sortDir.value, // 💡 Kunci dikirim sebagai sort_dir sesuai controller
        page: page.value,
      },
    });

    candidates.value = res.data; // Data candidates dari CandidateResource::collection
    pagination.value = res.meta; // Meta pagination bawaan Laravel API Resource
  } catch (err) {
    console.error("Error fetching candidates:", err);
  } finally {
    loading.value = false;
  }
};

// Reset ke halaman 1 jika filter/search/sort berubah
watch([search, statusFilter, sortBy, sortDir], () => {
  page.value = 1;
  fetchCandidates();
});

// Watch pergantian halaman pagination
watch(page, () => {
  fetchCandidates();
});

onMounted(() => {
  fetchCandidates();
});
</script>

<template>
  <div class="p-4 md:p-6 max-w-7xl mx-auto space-y-4">
    <div class="border-b border-gray-100 pb-4">
      <h1 class="font-headline text-3xl font-bold text-primary">
        Daftar Kandidat
      </h1>
    </div>

    <!-- State Loading -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <div class="text-center space-y-2">
        <div
          class="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto"
        ></div>
        <p class="text-sm text-gray-400">Memuat data kandidat...</p>
      </div>
    </div>

    <!-- Control Bar (Filter & Search) -->
    <div
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 bg-white p-4 rounded-lg shadow-sm border"
    >
      <!-- Search Input -->
      <input
        v-model="search"
        type="text"
        placeholder="Cari nama, email, hp, alamat..."
        class="p-2 border rounded w-full text-sm"
      />

      <!-- Filter Status -->
      <select v-model="statusFilter" class="p-2 border rounded w-full text-sm">
        <option value="">Semua Status</option>
        <option value="applied">Applied</option>
        <option value="interviewed">Interviewed</option>
        <option value="hired">Hired</option>
        <option value="rejected">Rejected</option>
      </select>

      <!-- Sort By Column -->
      <select v-model="sortBy" class="p-2 border rounded w-full text-sm">
        <option value="created_at">Tanggal Daftar</option>
        <option value="full_name">Nama</option>
        <option value="email">Email</option>
      </select>

      <!-- Sort Direction -->
      <select v-model="sortDir" class="p-2 border rounded w-full text-sm">
        <option value="desc">Descending (Terbaru / Z-A)</option>
        <option value="asc">Ascending (Terlama / A-Z)</option>
      </select>
    </div>

    <!-- Table Data -->
    <div class="bg-white rounded-lg shadow border overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[600px]">
        <thead class="bg-gray-50 border-b text-sm">
          <tr>
            <th class="p-3">Nama</th>
            <th class="p-3">Email & Telp</th>
            <th class="p-3">Status</th>
            <th class="p-3">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-sm">
          <tr v-if="loading">
            <td colspan="4" class="p-4 text-center text-gray-500">
              Memuat data...
            </td>
          </tr>
          <tr v-else-if="candidates.length === 0">
            <td colspan="4" class="p-4 text-center text-gray-500">
              Data tidak ditemukan.
            </td>
          </tr>
          <tr
            v-for="item in candidates"
            :key="item.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="p-3 font-medium">{{ item.full_name }}</td>
            <td class="p-3 text-gray-600">
              <div>{{ item.email }}</div>
              <div class="text-xs text-gray-400">{{ item.phone }}</div>
            </td>
            <td class="p-3">
              <span
                class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800"
              >
                {{ item.status }}
              </span>
            </td>
            <td class="p-3">
              <NuxtLink
                :to="`/candidates/${item.id}`"
                class="text-blue-600 hover:underline font-medium"
              >
                Detail
              </NuxtLink>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div
      v-if="pagination && pagination.last_page > 1"
      class="flex justify-between items-center pt-2 text-sm"
    >
      <button
        :disabled="page <= 1"
        @click="page--"
        class="px-3 py-1 border rounded disabled:opacity-50 hover:bg-gray-50"
      >
        Prev
      </button>
      <span>Halaman {{ page }} dari {{ pagination.last_page }}</span>
      <button
        :disabled="page >= pagination.last_page"
        @click="page++"
        class="px-3 py-1 border rounded disabled:opacity-50 hover:bg-gray-50"
      >
        Next
      </button>
    </div>
  </div>
</template>
