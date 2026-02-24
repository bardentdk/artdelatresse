<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { PhClock, PhCurrencyEur, PhCalendarPlus, PhWarningCircle } from '@phosphor-icons/vue';
import { computed } from 'vue';

const props = defineProps({
    services: {
        type: Array,
        required: true
    }
});

const page = usePage();
const user = computed(() => page.props.auth.user);

// Fonction pour formater le prix proprement en JS
const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};
</script>

<template>
    <Head title="Nos Prestations" />

    <AppLayout>
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h1 class="font-season text-5xl md:text-6xl text-brand-text mb-6">Nos Prestations</h1>
            <p class="text-lg opacity-80 leading-relaxed">
                Découvrez notre catalogue de tresses africaines et protectrices. 
                Chaque prestation est réalisée avec soin pour sublimer votre beauté naturelle.
            </p>
        </div>

        <div v-if="services.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <article 
                v-for="service in services" 
                :key="service.id" 
                class="bg-brand-surface rounded-2xl overflow-hidden shadow-sm border border-[#ebdaca] hover:shadow-md transition-shadow flex flex-col"
            >
                <div class="h-64 bg-[#f4ebe1] relative">
                    <img 
                        v-if="service.image_path" 
                        :src="`/storage/${service.image_path}`" 
                        :alt="service.name"
                        class="w-full h-full object-cover"
                    >
                    <div v-else class="w-full h-full flex items-center justify-center opacity-30">
                        <span class="font-season text-2xl">L'Art de la Tresse</span>
                    </div>
                    <div class="absolute top-4 right-4 bg-brand-surface/90 backdrop-blur-sm px-4 py-2 rounded-full font-bold text-brand-text border border-[#ebdaca]">
                        {{ formatPrice(service.price) }}
                    </div>
                </div>

                <div class="p-6 flex-grow flex flex-col">
                    <h2 class="font-season text-2xl font-bold mb-2">{{ service.name }}</h2>
                    <p class="text-sm opacity-75 mb-6 flex-grow line-clamp-3">
                        {{ service.description || 'Aucune description fournie pour cette prestation.' }}
                    </p>

                    <div class="flex items-center gap-6 mb-6 text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <PhClock :size="18" class="text-brand-accent" />
                            <span>{{ service.duration_minutes }} min</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <PhCurrencyEur :size="18" class="text-brand-accent" />
                            <span>Acompte : {{ formatPrice(service.deposit_amount) }}</span>
                        </div>
                    </div>

                    <Link 
                        :href="route('orders.create', service.id)"
                        class="w-full flex items-center justify-center gap-2 bg-brand-bg border border-brand-text text-brand-text py-3 rounded-xl hover:bg-brand-text hover:text-brand-surface transition-colors font-medium"
                    >
                        <PhCalendarPlus :size="20" />
                        Réserver cette prestation
                    </Link>
                </div>
            </article>
        </div>

        <div v-else class="text-center py-20 bg-brand-surface rounded-2xl border border-[#ebdaca]">
            <PhWarningCircle :size="48" class="mx-auto text-brand-accent mb-4" />
            <h3 class="font-season text-2xl mb-2">Catalogue en préparation</h3>
            <p class="opacity-70">Nous mettons actuellement à jour nos prestations. Revenez très vite !</p>
        </div>
    </AppLayout>
</template>