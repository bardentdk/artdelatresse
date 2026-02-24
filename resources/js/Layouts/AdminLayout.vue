<script setup>
import { computed, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import { 
    PhSignOut, PhCalendarBlank, PhList, PhStorefront, 
    PhSquaresFour, PhReceipt 
} from '@phosphor-icons/vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const flash = computed(() => page.props.flash);

// Écouteur global pour les notifications
watch(() => flash.value, (newFlash) => {
    if (newFlash.success) toast.success(newFlash.success);
    if (newFlash.error || Object.keys(page.props.errors).length > 0) {
        toast.error(newFlash.error || 'Une erreur est survenue.');
    }
}, { deep: true, immediate: true });
</script>

<template>
    <div class="min-h-screen bg-brand-bg text-brand-text flex selection:bg-brand-accent selection:text-white">
        
        <aside class="w-64 bg-brand-surface border-r border-[#ebdaca] flex flex-col hidden md:flex fixed h-full z-10">
            <div class="h-20 flex items-center justify-center border-b border-[#ebdaca]">
                <Link :href="route('home')" class="font-season text-2xl font-bold text-brand-text text-center">
                    L'Art de la Tresse<br><span class="text-sm font-inter font-normal opacity-70">Espace Pro</span>
                </Link>
            </div>

            <nav class="flex-grow p-4 space-y-2 font-medium">
                <Link :href="route('admin.dashboard')" 
                      class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors"
                      :class="route().current('admin.dashboard') ? 'bg-brand-bg text-brand-accent' : 'hover:bg-[#fcf8f4]'">
                    <PhSquaresFour :size="22" /> Tableau de bord
                </Link>

                <Link :href="route('admin.services.index')" 
                      class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors"
                      :class="route().current('admin.services.*') ? 'bg-brand-bg text-brand-accent' : 'hover:bg-[#fcf8f4]'">
                    <PhList :size="22" /> Catalogue Prestations
                </Link>

                <Link :href="route('admin.planning.index')" 
                      class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors"
                      :class="route().current('admin.planning.*') ? 'bg-brand-bg text-brand-accent' : 'hover:bg-[#fcf8f4]'">
                    <PhCalendarBlank :size="22" /> Mon Agenda
                </Link>

                <Link :href="route('admin.orders.index')" 
                      class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors"
                      :class="route().current('admin.orders.*') ? 'bg-brand-bg text-brand-accent' : 'hover:bg-[#fcf8f4]'">
                    <PhReceipt :size="22" /> Commandes
                </Link>
            </nav>

            <div class="p-4 border-t border-[#ebdaca]">
                <div class="px-4 mb-4 text-sm opacity-70">
                    Connectée en tant que<br>
                    <strong class="text-brand-text">{{ user.name }}</strong>
                </div>
                <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center justify-center gap-2 bg-[#fcf8f4] text-brand-text py-2.5 rounded-xl hover:bg-red-50 hover:text-red-600 transition-colors border border-[#ebdaca]">
                    <PhSignOut :size="20" /> Déconnexion
                </Link>
            </div>
        </aside>

        <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
            <header class="md:hidden bg-brand-surface border-b border-[#ebdaca] h-16 flex items-center justify-between px-4 sticky top-0 z-20">
                <Link :href="route('home')" class="font-season text-xl font-bold">L'Art de la Tresse</Link>
                <button class="p-2"><PhList :size="24" /></button>
            </header>

            <main class="flex-grow p-6 md:p-10 w-full max-w-6xl mx-auto">
                <slot />
            </main>
        </div>
    </div>
</template>