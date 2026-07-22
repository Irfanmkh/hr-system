<script setup lang="ts">
const config = useRuntimeConfig();
const { token } = useAuth();

const jobs = ref<any[]>([]);
const form = ref({ id: null, title: "", department: "", description: "" });
const isEditing = ref(false);

const fetchJobs = async () => {
  const res: any = await $fetch("/jobs", {
    baseURL: config.public.apiBase,
    headers: { Authorization: `Bearer ${token.value}` },
  });
  jobs.value = res.data;
};

const saveJob = async () => {
  const url = isEditing.value ? `/jobs/${form.value.id}` : "/jobs";
  const method = isEditing.value ? "PUT" : "POST";

  await $fetch(url, {
    baseURL: config.public.apiBase,
    method,
    headers: { Authorization: `Bearer ${token.value}` },
    body: form.value,
  });

  resetForm();
  fetchJobs();
};

const editJob = (job: any) => {
  form.value = { ...job };
  isEditing.value = true;
};

const deleteJob = async (id: number) => {
  if (!confirm("Yakin ingin menghapus lowongan ini?")) return;
  await $fetch(`/jobs/${id}`, {
    baseURL: config.public.apiBase,
    method: "DELETE",
    headers: { Authorization: `Bearer ${token.value}` },
  });
  fetchJobs();
};

const resetForm = () => {
  form.value = { id: null, title: "", department: "", description: "" };
  isEditing.value = false;
};

onMounted(() => fetchJobs());
</script>

<template>
  <div
    class="p-4 md:p-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6"
  >
    <!-- Form Create/Edit Job -->
    <div class="bg-white p-5 rounded-lg shadow border h-fit space-y-4">
      <h2 class="text-lg font-bold">
        {{ isEditing ? "Edit Lowongan" : "Tambah Lowongan Baru" }}
      </h2>
      <form @submit.prevent="saveJob" class="space-y-3">
        <div>
          <label class="block text-sm font-medium">Judul Posisi</label>
          <input
            v-model="form.title"
            type="text"
            class="w-full p-2 border rounded"
            required
          />
        </div>
        <div>
          <label class="block text-sm font-medium">Departemen</label>
          <input
            v-model="form.department"
            type="text"
            class="w-full p-2 border rounded"
            required
          />
        </div>
        <div>
          <label class="block text-sm font-medium">Deskripsi</label>
          <textarea
            v-model="form.description"
            class="w-full p-2 border rounded"
            rows="3"
          ></textarea>
        </div>
        <div class="flex gap-2">
          <button
            type="submit"
            class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700"
          >
            Simpan
          </button>
          <button
            v-if="isEditing"
            type="button"
            @click="resetForm"
            class="p-2 border rounded"
          >
            Batal
          </button>
        </div>
      </form>
    </div>

    <!-- Table Jobs -->
    <div
      class="lg:col-span-2 bg-white rounded-lg shadow border overflow-x-auto"
    >
      <table class="w-full text-left min-w-[500px]">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="p-3">Posisi</th>
            <th class="p-3">Departemen</th>
            <th class="p-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="job in jobs" :key="job.id" class="border-b">
            <td class="p-3 font-semibold">{{ job.title }}</td>
            <td class="p-3 text-sm text-gray-600">{{ job.department }}</td>
            <td class="p-3 text-right space-x-2">
              <button
                @click="editJob(job)"
                class="text-blue-600 hover:underline text-sm"
              >
                Edit
              </button>
              <button
                @click="deleteJob(job.id)"
                class="text-red-600 hover:underline text-sm"
              >
                Hapus
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
