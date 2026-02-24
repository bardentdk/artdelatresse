<script setup>
import { computed, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import { PhSignOut, PhUser, PhCalendarBlank, PhList, PhStorefront } from '@phosphor-icons/vue';

// Récupération globale des données partagées par Inertia
const page = usePage();
const user = computed(() => page.props.auth.user);
const flash = computed(() => page.props.flash);

// Écouteur global pour afficher les notifications Toastify
watch(() => flash.value, (newFlash) => {
    if (newFlash.success) {
        toast.success(newFlash.success);
    }
    if (newFlash.error || Object.keys(page.props.errors).length > 0) {
        toast.error(newFlash.error || 'Une erreur est survenue.');
    }
}, { deep: true, immediate: true });
</script>

<template>
    <div class="min-h-screen bg-brand-bg text-brand-text selection:bg-brand-accent selection:text-white flex flex-col">
        <header class="bg-brand-surface shadow-sm border-b border-[#ebdaca] sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <Link :href="route('home')" class="font-season text-3xl font-bold tracking-tight text-brand-text">
                            L'Art de la Tresse
                        </Link>
                    </div>

                    <nav class="hidden md:flex space-x-8 items-center font-medium">
                        <Link :href="route('home')" class="hover:text-brand-accent transition-colors flex items-center gap-2">
                            <PhStorefront :size="20" /> Prestations
                        </Link>

                        <template v-if="user && user.role === 'client'">
                            <Link :href="route('client.dashboard')" class="hover:text-brand-accent transition-colors flex items-center gap-2">
                                <PhUser :size="20" /> Mon Espace
                            </Link>
                        </template>

                        <template v-if="user && user.role === 'admin'">
                            <Link :href="route('admin.dashboard')" class="hover:text-brand-accent transition-colors flex items-center gap-2">
                                Dashboard
                            </Link>
                            <Link :href="route('admin.services.index')" class="hover:text-brand-accent transition-colors flex items-center gap-2">
                                <PhList :size="20" /> Catalogue
                            </Link>
                            <Link :href="route('admin.planning.index')" class="hover:text-brand-accent transition-colors flex items-center gap-2">
                                <PhCalendarBlank :size="20" /> Planning
                            </Link>
                        </template>

                        <div class="flex items-center space-x-4 border-l border-[#ebdaca] pl-6 ml-2">
                            <template v-if="user">
                                <span class="text-sm opacity-70">Bonjour, {{ user.name }}</span>
                                <Link :href="route('logout')" method="post" as="button" class="text-brand-text hover:text-red-500 transition-colors flex items-center gap-1">
                                    <PhSignOut :size="20" /> Déconnexion
                                </Link>
                            </template>
                            <template v-else>
                                <Link :href="route('login')" class="hover:text-brand-accent transition-colors">Connexion</Link>
                                <Link :href="route('register')" class="bg-brand-text text-brand-surface px-5 py-2.5 rounded-full hover:bg-brand-accent transition-colors">
                                    Créer un compte
                                </Link>
                            </template>
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
            <slot />
        </main>

        <footer class="bg-brand-surface border-t border-[#ebdaca] py-8 text-center text-sm opacity-80">
            <p class="font-season text-lg">L'Art de la Tresse &copy; {{ new Date().getFullYear() }}</p>
            <p class="mt-2">Toutes nos prestations sont réalisées avec passion.</p>
        </footer>
    </div>
</template>