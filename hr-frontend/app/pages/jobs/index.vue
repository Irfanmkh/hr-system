<script setup lang="ts">
const config = useRuntimeConfig();
const { token } = useAuth();

definePageMeta({
  layout: "default",
  middleware: "auth",
});

const jobs = ref<any[]>([]);
const loading = ref(false);
const submitting = ref(false);

const form = ref({ id: null, title: "", department: "", description: "" });
const isEditing = ref(false);

const fetchJobs = async () => {
  loading.value = true;
  try {
    const res: any = await $fetch("/jobs", {
      baseURL: config.public.apiBase,
      headers: { Authorization: `Bearer ${token.value}` },
    });
    jobs.value = res.data;
  } catch (err) {
    console.error("Gagal ambil data jobs:", err);
  } finally {
    loading.value = false;
  }
};

const saveJob = async () => {
  submitting.value = true;
  const url = isEditing.value ? `/jobs/${form.value.id}` : "/jobs";
  const method = isEditing.value ? "PUT" : "POST";

  try {
    await $fetch(url, {
      baseURL: config.public.apiBase,
      method,
      headers: { Authorization: `Bearer ${token.value}` },
      body: form.value,
    });

    resetForm();
    fetchJobs();
  } catch (err) {
    console.error("Gagal simpan job:", err);
  } finally {
    submitting.value = false;
  }
};

const editJob = (job: any) => {
  form.value = { ...job };
  isEditing.value = true;
};

const deleteJob = async (id: number) => {
  if (!confirm("Yakin ingin menghapus lowongan ini?")) return;

  try {
    await $fetch(`/jobs/${id}`, {
      baseURL: config.public.apiBase,
      method: "DELETE",
      headers: { Authorization: `Bearer ${token.value}` },
    });
    fetchJobs();
  } catch (err) {
    console.error("Gagal hapus job:", err);
  }
};

const resetForm = () => {
  form.value = { id: null, title: "", department: "", description: "" };
  isEditing.value = false;
};

onMounted(() => fetchJobs());
</script>

<template>
  <div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Title Section -->
    <div class="border-b border-gray-100 pb-4">
      <h1 class="font-headline text-3xl font-bold text-primary">
        Manajemen Lowongan Kerja
      </h1>
    </div>

    <!-- State Loading -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <div class="text-center space-y-2">
        <div
          class="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto"
        ></div>
        <p class="text-sm text-gray-400">Memuat data loker...</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Form Input / Edit -->
      <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
        <h2 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b">
          {{ isEditing ? "Edit Lowongan" : "Tambah Lowongan Baru" }}
        </h2>

        <form @submit.prevent="saveJob" class="space-y-4">
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1"
              >Judul Posisi</label
            >
            <input
              v-model="form.title"
              type="text"
              placeholder="Contoh: Frontend Developer"
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-500"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1"
              >Departemen</label
            >
            <input
              v-model="form.department"
              type="text"
              placeholder="Contoh: IT / Engineering"
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-500"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1"
              >Deskripsi Pekerjaan</label
            >
            <textarea
              v-model="form.description"
              placeholder="Tuliskan kualifikasi atau deskripsi singkat..."
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-500"
              rows="4"
            ></textarea>
          </div>

          <div class="flex items-center gap-2 pt-2">
            <button
              type="submit"
              :disabled="submitting"
              class="flex-1 bg-primary hover:bg-accent-gold text-white text-sm font-medium py-2 px-4 rounded disabled:opacity-50"
            >
              {{
                submitting ? "Menyimpan..." : isEditing ? "Update" : "Simpan"
              }}
            </button>
            <button
              v-if="isEditing"
              type="button"
              @click="resetForm"
              class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium py-2 px-4 rounded"
            >
              Batal
            </button>
          </div>
        </form>
      </div>

      <!-- Tabel Data Jobs -->
      <div
        class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden"
      >
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
          <h3 class="font-medium text-gray-700 text-sm">Daftar Lowongan</h3>
          <span class="text-xs text-gray-500"
            >Total: {{ jobs.length }} data</span
          >
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b text-gray-600 text-xs uppercase">
              <tr>
                <th class="py-3 px-4">Posisi</th>
                <th class="py-3 px-4">Departemen</th>
                <th class="py-3 px-4 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="loading">
                <td colspan="3" class="py-8 text-center text-gray-400">
                  Memuat data...
                </td>
              </tr>

              <tr v-else-if="jobs.length === 0">
                <td colspan="3" class="py-8 text-center text-gray-400">
                  Belum ada data lowongan.
                </td>
              </tr>

              <tr
                v-else
                v-for="job in jobs"
                :key="job.id"
                class="hover:bg-gray-50/50"
              >
                <td class="py-3 px-4">
                  <p class="font-medium text-gray-800">{{ job.title }}</p>
                  <p
                    class="text-xs text-gray-400 line-clamp-1"
                    v-if="job.description"
                  >
                    {{ job.description }}
                  </p>
                </td>
                <td class="py-3 px-4 text-gray-600">
                  <span
                    class="inline-block px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded"
                  >
                    {{ job.department }}
                  </span>
                </td>
                <td class="py-3 px-4 text-center space-x-3">
                  <button
                    @click="editJob(job)"
                    class="text-blue-600 hover:text-blue-800 font-medium text-xs"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteJob(job.id)"
                    class="text-red-600 hover:text-red-800 font-medium text-xs"
                  >
                    Hapus
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
