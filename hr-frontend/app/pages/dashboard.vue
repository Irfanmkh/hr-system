<script setup lang="ts">
import { Bar } from "vue-chartjs";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from "chart.js";

// Register Chart.js
ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
);

const config = useRuntimeConfig();
const { token } = useAuth();

definePageMeta({
  layout: "default",
});

// Proteksi Sederhana: Jika tidak ada token saat refresh, kembalikan ke login
if (!token.value) {
  await navigateTo("/login");
}

const dashboard = ref<any>(null);
const loading = ref(true);

const getDashboard = async () => {
  try {
    const response: any = await $fetch("/dashboard", {
      baseURL: config.public.apiBase,
      headers: {
        Authorization: `Bearer ${token.value}`,
        Accept: "application/json",
      },
    });

    dashboard.value = response.data;
  } catch (error) {
    console.log("Error dashboard:", error);
  } finally {
    loading.value = false;
  }
};

// Data Komputasi untuk Chart dengan Styling Warna Brand
const chartData = computed(() => {
  if (!dashboard.value?.candidates_by_status)
    return { labels: [], datasets: [] };

  return {
    labels: dashboard.value.candidates_by_status.map((item: any) =>
      item.status.toUpperCase(),
    ),
    datasets: [
      {
        label: "Jumlah Kandidat",
        backgroundColor: "#164A82", // Accent Blue
        hoverBackgroundColor: "#2B6A8C",
        borderRadius: 6,
        data: dashboard.value.candidates_by_status.map(
          (item: any) => item.total,
        ),
      },
    ],
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false, // Disembunyikan agar tampilan chart lebih bersih
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        stepSize: 1,
      },
    },
  },
};

onMounted(() => {
  getDashboard();
});
</script>

<template>
  <div class="p-6 max-w-7xl mx-auto space-y-6 font-sans">
    <!-- Header Section -->
    <div class="border-b border-gray-100 pb-4">
      <h1 class="font-headline text-3xl font-bold text-primary">
        Dashboard Overview
      </h1>
      <p class="text-sm text-gray-500 mt-1">
        Ringkasan statistik kandidat dan lowongan kerja aktif.
      </p>
    </div>

    <!-- State Loading -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <div class="text-center space-y-2">
        <div
          class="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto"
        ></div>
        <p class="text-sm text-gray-400">Memuat data dashboard...</p>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else-if="dashboard" class="space-y-6">
      <!-- Stat Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <!-- Card Total Candidate -->
        <div
          class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition"
        >
          <div>
            <p
              class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
            >
              Total Candidate
            </p>
            <p class="font-headline text-4xl font-extrabold text-primary mt-1">
              {{ dashboard.total_candidates }}
            </p>
          </div>
          <div
            class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary text-xl"
          >
            👥
          </div>
        </div>

        <!-- Card Total Lowongan -->
        <div
          class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition"
        >
          <div>
            <p
              class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
            >
              Total Lowongan
            </p>
            <p
              class="font-headline text-4xl font-extrabold text-accent-blue mt-1"
            >
              {{ dashboard.total_jobs }}
            </p>
          </div>
          <div
            class="w-12 h-12 bg-accent-blue/10 rounded-full flex items-center justify-center text-accent-blue text-xl"
          >
            💼
          </div>
        </div>
      </div>

      <!-- Section Chart & Status Details -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Chart Bar -->
        <div
          class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4"
        >
          <div class="flex items-center justify-between">
            <h2 class="font-headline text-lg font-bold text-primary">
              Grafik Kandidat per Status
            </h2>
            <span class="text-xs text-gray-400 font-medium"
              >Real-time Data</span
            >
          </div>

          <div class="h-72 w-full pt-2">
            <Bar :data="chartData" :options="chartOptions" />
          </div>
        </div>

        <!-- Rincian Status Candidate List -->
        <div
          class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4"
        >
          <h2 class="font-headline text-lg font-bold text-primary">
            Rincian Status
          </h2>

          <div class="divide-y divide-gray-100">
            <div
              v-for="status in dashboard.candidates_by_status"
              :key="status.status"
              class="py-3 flex items-center justify-between"
            >
              <span class="text-sm font-semibold capitalize text-gray-700">
                {{ status.status }}
              </span>
              <span
                class="px-3 py-1 text-xs font-bold rounded-full bg-primary/10 text-primary"
              >
                {{ status.total }} Kandidat
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
