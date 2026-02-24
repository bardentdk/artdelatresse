<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { PhCalendarCheck, PhClock, PhCurrencyEur, PhDownload, PhCheckCircle, PhWarningCircle } from '@phosphor-icons/vue';

const props = defineProps({
    orders: {
        type: Array,
        required: true
    }
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
};

const formatTime = (timeString) => {
    return timeString.substring(0, 5);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};
</script>

<template>
    <Head title="Mon Historique de Réservations" />

    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <div class="mb-10">
                <h1 class="font-season text-4xl text-brand-text mb-2">Mon Espace Client</h1>
                <p class="text-sm opacity-70">Retrouvez l'historique de vos rendez-vous et vos factures.</p>
            </div>

            <div v-if="orders.length > 0" class="space-y-6">
                <article 
                    v-for="order in orders" 
                    :key="order.id"
                    class="bg-brand-surface border border-[#ebdaca] rounded-2xl p-6 shadow-sm flex flex-col md:flex-row gap-6 md:items-center justify-between"
                >
                    <div class="flex-grow space-y-3">
                        <div class="flex items-center gap-3">
                            <span 
                                class="px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1"
                                :class="order.status === 'confirmed' ? 'bg-green-100 text-green-700' : (order.status === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700')"
                            >
                                <PhCheckCircle v-if="order.status === 'confirmed'" :size="16" />
                                <PhWarningCircle v-else :size="16" />
                                {{ order.status === 'confirmed' ? 'Confirmé' : (order.status === 'pending' ? 'En attente' : 'Annulé') }}
                            </span>
                            <span class="text-xs opacity-50 uppercase font-bold tracking-wider">Réf: {{ order.reference }}</span>
                        </div>

                        <h2 class="font-season text-2xl font-bold">{{ order.service.name }}</h2>
                        
                        <div class="flex flex-wrap items-center gap-6 text-sm font-medium opacity-80">
                            <div class="flex items-center gap-2 text-brand-accent">
                                <PhCalendarCheck :size="18" />
                                <span class="capitalize">{{ formatDate(order.availability.date) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <PhClock :size="18" />
                                <span>{{ formatTime(order.availability.start_time) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="md:text-right border-t md:border-t-0 md:border-l border-[#ebdaca] pt-4 md:pt-0 md:pl-6 min-w-[200px]">
                        <div class="mb-4">
                            <p class="text-xs opacity-60 mb-1">Montant réglé (Stripe)</p>
                            <p class="text-xl font-bold font-season flex items-center md:justify-end gap-1">
                                <PhCurrencyEur :size="20"/> {{ formatPrice(order.deposit_paid) }}
                            </p>
                            <p v-if="order.deposit_paid < order.total_price" class="text-xs text-brand-accent mt-1">
                                Reste à payer : {{ formatPrice(order.total_price - order.deposit_paid) }}
                            </p>
                            <p v-else class="text-xs text-green-600 mt-1 font-medium">Payé en totalité</p>
                        </div>

                        <a 
                            v-if="order.status === 'confirmed' || order.status === 'completed'"
                            :href="route('orders.invoice', order.id)" 
                            class="inline-flex items-center justify-center gap-2 w-full md:w-auto bg-[#fcf8f4] border border-brand-text text-brand-text px-4 py-2 rounded-xl hover:bg-brand-text hover:text-brand-surface transition-colors font-medium text-sm"
                            target="_blank"
                        >
                            <PhDownload :size="18" />
                            Facture PDF
                        </a>
                    </div>
                </article>
            </div>

            <div v-else class="text-center py-20 bg-brand-surface rounded-2xl border border-[#ebdaca]">
                <PhCalendarCheck :size="48" class="mx-auto text-brand-accent/50 mb-4" />
                <h3 class="font-season text-2xl mb-2">Aucune réservation</h3>
                <p class="opacity-70 mb-6">Vous n'avez pas encore réservé de prestation chez nous.</p>
                <Link :href="route('home')" class="inline-flex items-center gap-2 bg-brand-text text-brand-surface px-6 py-3 rounded-xl hover:bg-brand-accent transition-colors font-medium">
                    Voir le catalogue
                </Link>
            </div>
        </div>
    </AppLayout>
</template>