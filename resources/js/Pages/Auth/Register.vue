<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import { PhUser, PhEnvelope, PhPhone, PhLockKey, PhUserPlus } from '@phosphor-icons/vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Créer un compte" />

    <AppLayout>
        <div class="max-w-md mx-auto mt-12 bg-brand-surface p-8 rounded-2xl shadow-sm border border-[#ebdaca]">
            <div class="text-center mb-8">
                <h1 class="font-season text-4xl text-brand-text mb-2">Bienvenue</h1>
                <p class="text-sm opacity-70">Créez votre compte pour réserver une prestation.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium mb-1">Nom complet</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <PhUser :size="20" class="text-brand-text opacity-50" />
                        </div>
                        <input id="name" v-model="form.name" type="text" required autofocus class="w-full pl-10 pr-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none" placeholder="Jeanne Dupont">
                    </div>
                    <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Adresse email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <PhEnvelope :size="20" class="text-brand-text opacity-50" />
                        </div>
                        <input id="email" v-model="form.email" type="email" required class="w-full pl-10 pr-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none" placeholder="vous@exemple.com">
                    </div>
                    <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium mb-1">Téléphone</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <PhPhone :size="20" class="text-brand-text opacity-50" />
                        </div>
                        <input id="phone" v-model="form.phone" type="text" class="w-full pl-10 pr-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none" placeholder="06 12 34 56 78">
                    </div>
                    <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1">Mot de passe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <PhLockKey :size="20" class="text-brand-text opacity-50" />
                        </div>
                        <input id="password" v-model="form.password" type="password" required class="w-full pl-10 pr-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none" placeholder="••••••••">
                    </div>
                    <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirmer le mot de passe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <PhLockKey :size="20" class="text-brand-text opacity-50" />
                        </div>
                        <input id="password_confirmation" v-model="form.password_confirmation" type="password" required class="w-full pl-10 pr-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none" placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" :disabled="form.processing" class="w-full flex items-center justify-center gap-2 bg-brand-text text-brand-surface py-3 rounded-xl hover:bg-brand-accent transition-colors font-medium mt-4 disabled:opacity-50">
                    <PhUserPlus :size="20" /> 
                    <span>S'inscrire</span>
                </button>

                <div class="text-center text-sm mt-4">
                    Déjà un compte ? 
                    <Link :href="route('login')" class="font-medium text-brand-accent hover:underline">
                        Se connecter
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>