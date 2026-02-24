<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    PhPlus, PhPencilSimple, PhTrash, PhImage, 
    PhX, PhCheckCircle, PhXCircle, PhCurrencyEur, PhClock 
} from '@phosphor-icons/vue';

const props = defineProps({
    services: {
        type: Array,
        required: true,
    }
});

// --- GESTION DE LA MODAL ET DU FORMULAIRE ---
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const imagePreview = ref(null);

const form = useForm({
    name: '',
    description: '',
    price: '',
    deposit_amount: '',
    duration_minutes: 60,
    is_active: true,
    image: null,
    _method: 'post', // Nécessaire pour Inertia lors de l'envoi de fichiers en PUT
});

const openCreateModal = () => {
    isEditing.value = false;
    editingId.value = null;
    imagePreview.value = null;
    form.reset();
    form.clearErrors();
    form._method = 'post';
    isModalOpen.value = true;
};

const openEditModal = (service) => {
    isEditing.value = true;
    editingId.value = service.id;
    imagePreview.value = service.image_path ? `/storage/${service.image_path}` : null;
    
    form.name = service.name;
    form.description = service.description || '';
    form.price = service.price;
    form.deposit_amount = service.deposit_amount;
    form.duration_minutes = service.duration_minutes;
    form.is_active = service.is_active;
    form.image = null; // On ne remplit pas l'input file par défaut
    form._method = 'put'; // Pour forcer la méthode PUT via POST (requis pour les fichiers)
    
    form.clearErrors();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => form.reset(), 300); // Laisse le temps à l'animation de se terminer
};

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    if (isEditing.value) {
        form.post(route('admin.services.update', editingId.value), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.services.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteService = (id) => {
    if (confirm('Voulez-vous vraiment supprimer cette prestation ? Cette action est irréversible.')) {
        router.delete(route('admin.services.destroy', id), {
            preserveScroll: true,
        });
    }
};

// --- UTILITAIRES ---
const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};
</script>

<template>
    <Head title="Catalogue des prestations" />

    <AdminLayout>
        <div class="mb-10 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
            <div>
                <h1 class="font-season text-4xl text-brand-text mb-2">Catalogue</h1>
                <p class="text-sm opacity-70">Gérez vos prestations, prix et acomptes.</p>
            </div>
            <button @click="openCreateModal" class="flex items-center justify-center gap-2 bg-brand-text text-brand-surface px-6 py-3 rounded-xl hover:bg-brand-accent transition-colors font-medium">
                <PhPlus :size="20" /> Nouvelle prestation
            </button>
        </div>

        <div v-if="services.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <article v-for="service in services" :key="service.id" class="bg-brand-surface rounded-2xl overflow-hidden shadow-sm border border-[#ebdaca] flex flex-col">
                <div class="h-48 bg-[#fcf8f4] relative">
                    <img v-if="service.image_path" :src="`/storage/${service.image_path}`" class="w-full h-full object-cover">
                    <div v-else class="w-full h-full flex items-center justify-center opacity-30">
                        <PhImage :size="48" />
                    </div>
                    <div class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 shadow-sm"
                         :class="service.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                        <component :is="service.is_active ? PhCheckCircle : PhXCircle" :size="16" />
                        {{ service.is_active ? 'Actif' : 'Inactif' }}
                    </div>
                </div>

                <div class="p-5 flex-grow flex flex-col">
                    <h2 class="font-season text-xl font-bold mb-2">{{ service.name }}</h2>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm bg-[#fcf8f4] p-3 rounded-xl border border-[#ebdaca]/50">
                        <div>
                            <span class="opacity-70 block text-xs">Prix total</span>
                            <span class="font-bold flex items-center gap-1"><PhCurrencyEur :size="16"/> {{ formatPrice(service.price) }}</span>
                        </div>
                        <div>
                            <span class="opacity-70 block text-xs">Acompte</span>
                            <span class="font-bold text-brand-accent">{{ formatPrice(service.deposit_amount) }}</span>
                        </div>
                        <div class="col-span-2 flex items-center gap-2">
                            <PhClock :size="16" class="opacity-70"/> 
                            <span>{{ service.duration_minutes }} minutes</span>
                        </div>
                    </div>

                    <div class="mt-auto flex gap-2 pt-4 border-t border-[#ebdaca]">
                        <button @click="openEditModal(service)" class="flex-1 flex items-center justify-center gap-2 bg-[#fcf8f4] text-brand-text py-2 rounded-lg hover:bg-brand-bg border border-[#ebdaca] transition-colors text-sm font-medium">
                            <PhPencilSimple :size="18" /> Modifier
                        </button>
                        <button @click="deleteService(service.id)" class="px-3 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                            <PhTrash :size="18" />
                        </button>
                    </div>
                </div>
            </article>
        </div>

        <div v-else class="text-center py-20 bg-brand-surface rounded-2xl border border-[#ebdaca]">
            <PhImage :size="48" class="mx-auto text-brand-accent/50 mb-4" />
            <h3 class="font-season text-2xl mb-2">Aucune prestation</h3>
            <p class="opacity-70 mb-6">Commencez par ajouter votre première prestation à votre catalogue.</p>
            <button @click="openCreateModal" class="inline-flex items-center gap-2 bg-brand-text text-brand-surface px-6 py-3 rounded-xl hover:bg-brand-accent transition-colors">
                <PhPlus :size="20" /> Ajouter une prestation
            </button>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeModal"></div>
            
            <div class="bg-brand-surface w-full max-w-2xl rounded-2xl shadow-xl z-10 overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-6 border-b border-[#ebdaca] flex justify-between items-center bg-[#fcf8f4]">
                    <h3 class="font-season text-2xl">{{ isEditing ? 'Modifier la prestation' : 'Nouvelle prestation' }}</h3>
                    <button @click="closeModal" class="text-brand-text hover:text-red-500 transition-colors">
                        <PhX :size="24" />
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form @submit.prevent="submitForm" class="space-y-6">
                        
                        <div class="flex gap-4 items-start">
                            <div class="flex-grow">
                                <label class="block text-sm font-medium mb-1">Nom de la prestation</label>
                                <input v-model="form.name" type="text" required class="w-full px-4 py-2 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                                <span v-if="form.errors.name" class="text-red-500 text-xs">{{ form.errors.name }}</span>
                            </div>
                            <div class="pt-7">
                                <label class="flex items-center cursor-pointer gap-2">
                                    <input type="checkbox" v-model="form.is_active" class="rounded border-[#ebdaca] text-brand-accent focus:ring-brand-accent w-5 h-5">
                                    <span class="text-sm font-medium">En ligne</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Description</label>
                            <textarea v-model="form.description" rows="3" class="w-full px-4 py-2 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none resize-none"></textarea>
                            <span v-if="form.errors.description" class="text-red-500 text-xs">{{ form.errors.description }}</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Prix total (€)</label>
                                <input v-model="form.price" type="number" step="0.01" required class="w-full px-4 py-2 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                                <span v-if="form.errors.price" class="text-red-500 text-xs">{{ form.errors.price }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Acompte (€)</label>
                                <input v-model="form.deposit_amount" type="number" step="0.01" required class="w-full px-4 py-2 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                                <span v-if="form.errors.deposit_amount" class="text-red-500 text-xs">{{ form.errors.deposit_amount }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Durée (min)</label>
                                <input v-model="form.duration_minutes" type="number" step="15" required class="w-full px-4 py-2 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                                <span v-if="form.errors.duration_minutes" class="text-red-500 text-xs">{{ form.errors.duration_minutes }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Image d'illustration</label>
                            <div class="flex items-center gap-6">
                                <div class="w-24 h-24 rounded-xl border-2 border-dashed border-[#ebdaca] overflow-hidden flex items-center justify-center bg-[#fcf8f4] flex-shrink-0">
                                    <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover">
                                    <PhImage v-else :size="32" class="opacity-30" />
                                </div>
                                <div class="flex-grow">
                                    <input @change="handleImageChange" type="file" accept="image/*" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-accent/10 file:text-brand-accent hover:file:bg-brand-accent/20 cursor-pointer">
                                    <p class="text-xs opacity-60 mt-2">Formats acceptés : JPG, PNG, WEBP. Max: 2Mo.</p>
                                    <span v-if="form.errors.image" class="text-red-500 text-xs">{{ form.errors.image }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-[#ebdaca] flex justify-end gap-3">
                            <button type="button" @click="closeModal" class="px-6 py-2.5 rounded-xl font-medium hover:bg-[#fcf8f4] transition-colors">
                                Annuler
                            </button>
                            <button type="submit" :disabled="form.processing" class="bg-brand-text text-brand-surface px-6 py-2.5 rounded-xl font-medium hover:bg-brand-accent transition-colors disabled:opacity-50 flex items-center gap-2">
                                <span v-if="form.processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                {{ isEditing ? 'Mettre à jour' : 'Enregistrer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>