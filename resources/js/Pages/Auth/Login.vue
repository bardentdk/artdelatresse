<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import { PhEnvelope, PhLockKey, PhSignIn } from '@phosphor-icons/vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion" />

    <AppLayout>
        <div class="max-w-md mx-auto mt-16 bg-brand-surface p-8 rounded-2xl shadow-sm border border-[#ebdaca]">
            <div class="text-center mb-8">
                <h1 class="font-season text-4xl text-brand-text mb-2">Bon retour</h1>
                <p class="text-sm opacity-70">Connectez-vous pour gérer vos rendez-vous.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Adresse email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <PhEnvelope :size="20" class="text-brand-text opacity-50" />
                        </div>
                        <input 
                            id="email" 
                            v-model="form.email" 
                            type="email" 
                            required 
                            autofocus
                            class="w-full pl-10 pr-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-brand-accent transition-all outline-none"
                            placeholder="vous@exemple.com"
                        >
                    </div>
                    <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1">Mot de passe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <PhLockKey :size="20" class="text-brand-text opacity-50" />
                        </div>
                        <input 
                            id="password" 
                            v-model="form.password" 
                            type="password" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-brand-accent transition-all outline-none"
                            placeholder="••••••••"
                        >
                    </div>
                    <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" v-model="form.remember" class="rounded border-[#ebdaca] text-brand-accent focus:ring-brand-accent w-4 h-4">
                        <span class="ml-2 text-sm">Se souvenir de moi</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    :disabled="form.processing"
                    class="w-full flex items-center justify-center gap-2 bg-brand-text text-brand-surface py-3 rounded-xl hover:bg-brand-accent transition-colors font-medium disabled:opacity-50"
                >
                    <PhSignIn :size="20" /> 
                    <span>Se connecter</span>
                </button>

                <div class="text-center text-sm mt-6">
                    Pas encore de compte ? 
                    <Link :href="route('register')" class="font-medium text-brand-accent hover:underline">
                        Créer un compte
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>