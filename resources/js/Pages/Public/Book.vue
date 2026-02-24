<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { PhCalendarBlank, PhClock, PhCreditCard, PhCheckCircle } from '@phosphor-icons/vue';
import { toast } from 'vue3-toastify';

const props = defineProps({
    service: Object,
    availabilities: Object, // Format: { 'YYYY-MM-DD': [{id, start_time...}, ...] }
});

const form = useForm({
    service_id: props.service.id,
    availability_id: null,
    payment_type: 'deposit', // 'deposit' (50%) ou 'full' (100%)
});

// État pour la sélection UI
const selectedDate = ref(null);
const selectedSlot = ref(null);

// Formatage JS
const formatPrice = (price) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });
const formatTime = (timeStr) => timeStr.substring(0, 5);

// Calculs des prix
const depositAmount = computed(() => props.service.price / 2);

const selectSlot = (date, slot) => {
    selectedDate.value = date;
    selectedSlot.value = slot;
    form.availability_id = slot.id;
};

const proceedToCheckout = () => {
    if (!form.availability_id) {
        toast.warning("Veuillez choisir un créneau horaire.");
        return;
    }
    form.post(route('orders.checkout'));
};
</script>

<template>
    <Head :title="`Réserver - ${service.name}`" />

    <AppLayout>
        <div class="max-w-5xl mx-auto flex flex-col lg:flex-row gap-10">
            
            <div class="flex-grow space-y-8">
                
                <section class="bg-brand-surface p-8 rounded-3xl shadow-sm border border-[#ebdaca]">
                    <h2 class="font-season text-2xl mb-6 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-brand-text text-brand-surface flex items-center justify-center text-sm font-bold font-inter">1</span>
                        Choisissez votre créneau
                    </h2>

                    <div v-if="Object.keys(availabilities).length > 0" class="space-y-6">
                        <div v-for="(slots, date) in availabilities" :key="date">
                            <h3 class="font-medium mb-3 capitalize text-brand-accent flex items-center gap-2">
                                <PhCalendarBlank :size="20"/> {{ formatDate(date) }}
                            </h3>
                            <div class="flex flex-wrap gap-3">
                                <button 
                                    v-for="slot in slots" 
                                    :key="slot.id"
                                    @click="selectSlot(date, slot)"
                                    class="px-4 py-2 rounded-xl border transition-all flex items-center gap-2 text-sm"
                                    :class="selectedSlot?.id === slot.id 
                                        ? 'bg-brand-text text-brand-surface border-brand-text ring-2 ring-brand-text ring-offset-2 ring-offset-brand-surface' 
                                        : 'bg-white border-[#ebdaca] hover:border-brand-accent text-brand-text'"
                                >
                                    <PhClock :size="16" /> {{ formatTime(slot.start_time) }}
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-10 opacity-70">
                        <PhCalendarBlank :size="48" class="mx-auto mb-3" />
                        <p>Aucun créneau n'est disponible pour le moment. Revenez bientôt !</p>
                    </div>
                </section>

                <section class="bg-brand-surface p-8 rounded-3xl shadow-sm border border-[#ebdaca]" :class="{'opacity-50 pointer-events-none': !form.availability_id}">
                    <h2 class="font-season text-2xl mb-6 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-brand-text text-brand-surface flex items-center justify-center text-sm font-bold font-inter">2</span>
                        Option de règlement
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" v-model="form.payment_type" value="deposit" class="peer sr-only">
                            <div class="p-5 rounded-2xl border-2 transition-all peer-checked:border-brand-accent peer-checked:bg-brand-bg bg-white border-[#ebdaca] h-full flex flex-col">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-bold text-lg">Payer l'acompte (50%)</span>
                                    <PhCheckCircle v-if="form.payment_type === 'deposit'" :size="24" weight="fill" class="text-brand-accent" />
                                </div>
                                <p class="text-sm opacity-70 mb-4 flex-grow">Réservez votre place en réglant la moitié maintenant. Le reste sera à régler le jour J.</p>
                                <span class="text-2xl font-bold font-season">{{ formatPrice(depositAmount) }}</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" v-model="form.payment_type" value="full" class="peer sr-only">
                            <div class="p-5 rounded-2xl border-2 transition-all peer-checked:border-brand-accent peer-checked:bg-brand-bg bg-white border-[#ebdaca] h-full flex flex-col">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-bold text-lg">Paiement intégral</span>
                                    <PhCheckCircle v-if="form.payment_type === 'full'" :size="24" weight="fill" class="text-brand-accent" />
                                </div>
                                <p class="text-sm opacity-70 mb-4 flex-grow">Réglez la totalité de la prestation dès aujourd'hui pour être l'esprit tranquille le jour J.</p>
                                <span class="text-2xl font-bold font-season">{{ formatPrice(service.price) }}</span>
                            </div>
                        </label>
                    </div>
                </section>
            </div>

            <div class="lg:w-96 flex-shrink-0">
                <div class="bg-brand-text text-brand-surface p-8 rounded-3xl sticky top-28 shadow-lg">
                    <h3 class="font-season text-2xl mb-6 text-brand-accent">Récapitulatif</h3>
                    
                    <div class="space-y-4 mb-6 pb-6 border-b border-brand-surface/20">
                        <div>
                            <span class="block text-xs uppercase tracking-wider opacity-60 mb-1">Prestation</span>
                            <span class="font-medium text-lg">{{ service.name }}</span>
                        </div>
                        <div v-if="selectedDate && selectedSlot">
                            <span class="block text-xs uppercase tracking-wider opacity-60 mb-1">Rendez-vous le</span>
                            <span class="font-medium capitalize">{{ formatDate(selectedDate) }} à {{ formatTime(selectedSlot.start_time) }}</span>
                        </div>
                        <div v-else>
                            <span class="text-sm italic opacity-60">Veuillez choisir un créneau</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-end mb-8">
                        <span class="opacity-80">Total à payer aujourd'hui</span>
                        <span class="font-season text-3xl font-bold text-brand-accent">
                            {{ form.payment_type === 'deposit' ? formatPrice(depositAmount) : formatPrice(service.price) }}
                        </span>
                    </div>

                    <button 
                        @click="proceedToCheckout"
                        :disabled="!form.availability_id || form.processing"
                        class="w-full flex items-center justify-center gap-2 bg-brand-accent text-white py-4 rounded-xl hover:bg-[#a66a43] transition-colors font-bold disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <template v-else>
                            <PhCreditCard :size="22" />
                            Payer en toute sécurité
                        </template>
                    </button>
                    
                    <p class="text-center text-xs opacity-50 mt-4 flex items-center justify-center gap-1">
                        Paiement sécurisé par Stripe
                    </p>
                </div>
            </div>

        </div>
    </AppLayout>
</template>