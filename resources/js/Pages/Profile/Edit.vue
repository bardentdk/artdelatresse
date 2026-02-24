<script setup>
import { computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { PhUser, PhEnvelope, PhPhone, PhLockKey, PhFloppyDisk } from '@phosphor-icons/vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Sélection dynamique du Layout
const layoutComponent = computed(() => {
    return user.value.role === 'admin' ? AdminLayout : AppLayout;
});

// Formulaire d'informations
const infoForm = useForm({
    name: user.value.name,
    email: user.value.email,
    phone: user.value.phone || '',
});

const updateInfo = () => {
    infoForm.patch(route('profile.update.info'), {
        preserveScroll: true,
    });
};

// Formulaire de mot de passe
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    passwordForm.put(route('profile.update.password'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
            }
        },
    });
};
</script>

<template>
    <Head title="Mon Profil" />

    <component :is="layoutComponent">
        <div class="max-w-4xl mx-auto space-y-8">
            <div class="mb-10">
                <h1 class="font-season text-4xl text-brand-text mb-2">Mon Profil</h1>
                <p class="text-sm opacity-70">Gérez vos informations personnelles et paramètres de sécurité.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <section class="bg-brand-surface p-8 rounded-2xl shadow-sm border border-[#ebdaca]">
                    <h2 class="font-season text-2xl mb-6 flex items-center gap-2">
                        <PhUser :size="24" class="text-brand-accent"/> Informations
                    </h2>

                    <form @submit.prevent="updateInfo" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nom complet</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <PhUser :size="20" class="text-brand-text opacity-50" />
                                </div>
                                <input v-model="infoForm.name" type="text" required class="w-full pl-10 pr-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                            </div>
                            <p v-if="infoForm.errors.name" class="text-red-500 text-xs mt-1">{{ infoForm.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Adresse email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <PhEnvelope :size="20" class="text-brand-text opacity-50" />
                                </div>
                                <input v-model="infoForm.email" type="email" required class="w-full pl-10 pr-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                            </div>
                            <p v-if="infoForm.errors.email" class="text-red-500 text-xs mt-1">{{ infoForm.errors.email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Téléphone</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <PhPhone :size="20" class="text-brand-text opacity-50" />
                                </div>
                                <input v-model="infoForm.phone" type="text" class="w-full pl-10 pr-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                            </div>
                            <p v-if="infoForm.errors.phone" class="text-red-500 text-xs mt-1">{{ infoForm.errors.phone }}</p>
                        </div>

                        <button type="submit" :disabled="infoForm.processing" class="w-full flex items-center justify-center gap-2 bg-brand-text text-brand-surface py-3 rounded-xl hover:bg-brand-accent transition-colors font-medium mt-4 disabled:opacity-50">
                            <PhFloppyDisk :size="20" /> Enregistrer les modifications
                        </button>
                    </form>
                </section>

                <section class="bg-brand-surface p-8 rounded-2xl shadow-sm border border-[#ebdaca]">
                    <h2 class="font-season text-2xl mb-6 flex items-center gap-2">
                        <PhLockKey :size="24" class="text-brand-accent"/> Sécurité
                    </h2>

                    <form @submit.prevent="updatePassword" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium mb-1">Mot de passe actuel</label>
                            <input v-model="passwordForm.current_password" type="password" required class="w-full px-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                            <p v-if="passwordForm.errors.current_password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.current_password }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Nouveau mot de passe</label>
                            <input v-model="passwordForm.password" type="password" required class="w-full px-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                            <p v-if="passwordForm.errors.password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.password }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Confirmer le nouveau mot de passe</label>
                            <input v-model="passwordForm.password_confirmation" type="password" required class="w-full px-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                        </div>

                        <button type="submit" :disabled="passwordForm.processing" class="w-full flex items-center justify-center gap-2 bg-brand-bg border border-brand-text text-brand-text py-3 rounded-xl hover:bg-brand-text hover:text-brand-surface transition-colors font-medium mt-4 disabled:opacity-50">
                            <PhLockKey :size="20" /> Mettre à jour le mot de passe
                        </button>
                    </form>
                </section>

            </div>
        </div>
    </component>
</template>