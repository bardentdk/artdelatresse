<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { PhCurrencyEur, PhUsers, PhCalendarStar, PhClock, PhUser } from '@phosphor-icons/vue';

const props = defineProps({
    totalRevenue: Number,
    todayAppointments: Array,
    upcomingAppointments: Array,
    totalBookings: Number,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};

const formatTime = (timeString) => {
    return timeString.substring(0, 5);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'short' });
};
</script>

<template>
    <Head title="Tableau de bord - Administration" />

    <AdminLayout>
        <div class="mb-10">
            <h1 class="font-season text-4xl text-brand-text mb-2">Vue d'ensemble</h1>
            <p class="text-sm opacity-70">Bienvenue dans votre espace de gestion. Voici un résumé de votre activité.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            
            <div class="bg-brand-surface p-6 rounded-2xl border border-[#ebdaca] shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-full bg-brand-bg flex items-center justify-center text-brand-accent flex-shrink-0">
                    <PhCurrencyEur :size="28" weight="bold" />
                </div>
                <div>
                    <p class="text-sm opacity-70 font-medium mb-1">Chiffre d'affaires encaissé</p>
                    <p class="font-season text-3xl font-bold">{{ formatPrice(totalRevenue) }}</p>
                </div>
            </div>

            <div class="bg-brand-surface p-6 rounded-2xl border border-[#ebdaca] shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-full bg-brand-bg flex items-center justify-center text-brand-text flex-shrink-0">
                    <PhUsers :size="28" weight="bold" />
                </div>
                <div>
                    <p class="text-sm opacity-70 font-medium mb-1">Réservations validées</p>
                    <p class="font-season text-3xl font-bold">{{ totalBookings }} <span class="text-lg font-inter font-normal opacity-50">clientes</span></p>
                </div>
            </div>

            <div class="bg-brand-text text-brand-surface p-6 rounded-2xl shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-full bg-brand-surface/20 flex items-center justify-center text-brand-surface flex-shrink-0">
                    <PhCalendarStar :size="28" weight="bold" />
                </div>
                <div>
                    <p class="text-sm opacity-80 font-medium mb-1">Vos rendez-vous du jour</p>
                    <p class="font-season text-3xl font-bold">{{ todayAppointments.length }} <span class="text-lg font-inter font-normal opacity-80">prévus</span></p>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="bg-brand-surface p-6 rounded-2xl border border-[#ebdaca] shadow-sm">
                <h2 class="font-season text-2xl mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-brand-accent animate-pulse"></span>
                    Aujourd'hui
                </h2>

                <div v-if="todayAppointments.length > 0" class="space-y-4">
                    <div v-for="order in todayAppointments" :key="order.id" class="p-4 rounded-xl border border-[#ebdaca] bg-[#fcf8f4] flex gap-4">
                        <div class="flex-shrink-0 text-center w-16 pt-1">
                            <span class="block font-bold text-lg text-brand-accent">{{ formatTime(order.availability.start_time) }}</span>
                            <span class="block text-xs opacity-50">{{ order.service.duration_minutes }} min</span>
                        </div>
                        <div class="border-l border-[#ebdaca] pl-4 flex-grow">
                            <p class="font-bold text-brand-text mb-1">{{ order.user.name }}</p>
                            <p class="text-sm opacity-80 mb-2">{{ order.service.name }}</p>
                            <div class="flex items-center gap-4 text-xs font-medium">
                                <span class="flex items-center gap-1 text-green-600">
                                    <PhCurrencyEur :size="14"/> Encaissé: {{ formatPrice(order.deposit_paid) }}
                                </span>
                                <span v-if="order.deposit_paid < order.total_price" class="flex items-center gap-1 text-brand-accent">
                                    À régler sur place: {{ formatPrice(order.total_price - order.deposit_paid) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-10 opacity-60">
                    <PhCalendarStar :size="48" class="mx-auto mb-3 opacity-50" />
                    <p>Aucune prestation prévue pour aujourd'hui. Profitez-en pour vous reposer !</p>
                </div>
            </div>

            <div class="bg-brand-surface p-6 rounded-2xl border border-[#ebdaca] shadow-sm">
                <h2 class="font-season text-2xl mb-6 text-brand-text opacity-80">À venir</h2>

                <div v-if="upcomingAppointments.length > 0" class="space-y-4">
                    <div v-for="order in upcomingAppointments" :key="order.id" class="flex items-center justify-between p-3 border-b border-[#ebdaca] last:border-0">
                        <div class="flex items-center gap-4">
                            <div class="bg-brand-bg w-12 h-12 rounded-lg flex flex-col items-center justify-center flex-shrink-0 border border-[#ebdaca]">
                                <span class="text-xs font-bold uppercase">{{ new Date(order.availability.date).toLocaleDateString('fr-FR', { month: 'short' }) }}</span>
                                <span class="font-season text-lg leading-none">{{ new Date(order.availability.date).getDate() }}</span>
                            </div>
                            <div>
                                <p class="font-bold text-sm">{{ order.user.name }}</p>
                                <p class="text-xs opacity-70">{{ order.service.name }}</p>
                            </div>
                        </div>
                        <div class="text-right text-sm">
                            <span class="flex items-center gap-1 opacity-80 justify-end"><PhClock :size="14"/> {{ formatTime(order.availability.start_time) }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-10 opacity-60">
                    <p>Aucun rendez-vous à venir pour le moment.</p>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>