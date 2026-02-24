<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { 
    PhReceipt, PhCheckCircle, PhWarningCircle, PhXCircle, 
    PhCalendarCheck, PhClock, PhCurrencyEur, PhUser
} from '@phosphor-icons/vue';

const props = defineProps({
    orders: {
        type: Array,
        required: true,
    }
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
};

const formatTime = (timeString) => {
    return timeString.substring(0, 5);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};
</script>

<template>
    <Head title="Gestion des Commandes" />

    <AdminLayout>
        <div class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="font-season text-4xl text-brand-text mb-2">Commandes & Réservations</h1>
                <p class="text-sm opacity-70">Retrouvez l'historique complet de tous les rendez-vous pris par vos clientes.</p>
            </div>
        </div>

        <div class="bg-brand-surface rounded-2xl shadow-sm border border-[#ebdaca] overflow-hidden">
            <div v-if="orders.length > 0" class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#fcf8f4] border-b border-[#ebdaca] text-brand-text/70 uppercase text-xs font-bold tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4">Réf / Statut</th>
                            <th scope="col" class="px-6 py-4">Cliente</th>
                            <th scope="col" class="px-6 py-4">Prestation</th>
                            <th scope="col" class="px-6 py-4">Rendez-vous</th>
                            <th scope="col" class="px-6 py-4 text-right">Paiement Stripe</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ebdaca]">
                        <tr v-for="order in orders" :key="order.id" class="hover:bg-brand-bg/50 transition-colors">
                            
                            <td class="px-6 py-4">
                                <div class="font-bold font-inter text-brand-text mb-2">{{ order.reference }}</div>
                                <span 
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold"
                                    :class="{
                                        'bg-green-100 text-green-700': order.status === 'confirmed' || order.status === 'completed',
                                        'bg-orange-100 text-orange-700': order.status === 'pending',
                                        'bg-red-100 text-red-700': order.status === 'cancelled'
                                    }"
                                >
                                    <PhCheckCircle v-if="order.status === 'confirmed' || order.status === 'completed'" :size="14" />
                                    <PhWarningCircle v-if="order.status === 'pending'" :size="14" />
                                    <PhXCircle v-if="order.status === 'cancelled'" :size="14" />
                                    {{ 
                                        order.status === 'confirmed' ? 'Confirmé' : 
                                        (order.status === 'completed' ? 'Terminé' : 
                                        (order.status === 'pending' ? 'En attente' : 'Annulé')) 
                                    }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-text text-brand-surface flex items-center justify-center flex-shrink-0">
                                        <PhUser :size="16" weight="bold" />
                                    </div>
                                    <div>
                                        <p class="font-bold text-brand-text">{{ order.user.name }}</p>
                                        <p class="text-xs opacity-70">{{ order.user.phone || order.user.email }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <p class="font-medium text-brand-text">{{ order.service.name }}</p>
                                <p class="text-xs opacity-70">Total : {{ formatPrice(order.total_price) }}</p>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2 font-medium text-brand-accent mb-1 capitalize">
                                    <PhCalendarCheck :size="16" /> {{ formatDate(order.availability.date) }}
                                </div>
                                <div class="flex items-center gap-2 text-xs opacity-70">
                                    <PhClock :size="14" /> {{ formatTime(order.availability.start_time) }} - {{ formatTime(order.availability.end_time) }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <p class="font-season text-lg font-bold text-brand-text mb-1 flex items-center justify-end gap-1">
                                    <PhCurrencyEur :size="16" /> {{ formatPrice(order.deposit_paid) }}
                                </p>
                                <p v-if="order.deposit_paid < order.total_price" class="text-xs text-brand-accent font-medium">
                                    Reste : {{ formatPrice(order.total_price - order.deposit_paid) }}
                                </p>
                                <p v-else class="text-xs text-green-600 font-medium">
                                    Réglé en totalité
                                </p>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="text-center py-20">
                <PhReceipt :size="48" class="mx-auto text-brand-accent/50 mb-4" />
                <h3 class="font-season text-2xl mb-2">Aucune commande</h3>
                <p class="opacity-70">Les réservations de vos clientes apparaîtront ici.</p>
            </div>
        </div>
    </AdminLayout>
</template>