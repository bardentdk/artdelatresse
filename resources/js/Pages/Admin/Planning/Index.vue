<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { PhPlus, PhTrash, PhFloppyDisk, PhCalendarCheck, PhClock } from '@phosphor-icons/vue';
import { toast } from 'vue3-toastify';

const props = defineProps({
    availabilities: {
        type: Array,
        required: true,
    }
});

// --- GESTION DE LA CRÉATION DE CRÉNEAUX EN MASSE ---
const pendingSlots = ref([]);
const newSlot = ref({
    date: '',
    start_time: '09:00',
    end_time: '12:00'
});

const addPendingSlot = () => {
    if (!newSlot.value.date || !newSlot.value.start_time || !newSlot.value.end_time) {
        toast.warning('Veuillez remplir tous les champs du créneau.');
        return;
    }
    
    // Vérifier si le début est bien avant la fin
    if (newSlot.value.start_time >= newSlot.value.end_time) {
        toast.error('L\'heure de fin doit être après l\'heure de début.');
        return;
    }

    pendingSlots.value.push({ ...newSlot.value });
    toast.info('Créneau ajouté à la liste d\'attente.');
};

const removePendingSlot = (index) => {
    pendingSlots.value.splice(index, 1);
};

const form = useForm({
    slots: []
});

const saveSlots = () => {
    if (pendingSlots.value.length === 0) return;
    
    form.slots = pendingSlots.value;
    form.post(route('admin.planning.store'), {
        onSuccess: () => {
            pendingSlots.value = [];
        }
    });
};

// --- AFFICHAGE DES CRÉNEAUX EXISTANTS ---
// Grouper les disponibilités par date pour un affichage plus clair
const groupedAvailabilities = computed(() => {
    const groups = {};
    props.availabilities.forEach(slot => {
        if (!groups[slot.date]) {
            groups[slot.date] = [];
        }
        groups[slot.date].push(slot);
    });
    return groups;
});

const deleteSlot = (id) => {
    if (confirm('Voulez-vous vraiment supprimer ce créneau ?')) {
        router.delete(route('admin.planning.destroy', id));
    }
};

// Formatage de la date en français
const formatDate = (dateString) => {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
};

// Formatage de l'heure (supprime les secondes)
const formatTime = (timeString) => {
    return timeString.substring(0, 5);
};
</script>

<template>
    <Head title="Gestion de l'Agenda" />

    <AdminLayout>
        <div class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="font-season text-4xl text-brand-text mb-2">Mon Agenda</h1>
                <p class="text-sm opacity-70">Générez vos disponibilités pour les prochaines semaines.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-brand-surface p-6 rounded-2xl shadow-sm border border-[#ebdaca]">
                    <h2 class="font-season text-2xl mb-4 flex items-center gap-2">
                        <PhCalendarCheck :size="24" class="text-brand-accent"/> 
                        Nouveau créneau
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Date</label>
                            <input type="date" v-model="newSlot.date" class="w-full px-4 py-2 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Début</label>
                                <input type="time" v-model="newSlot.start_time" class="w-full px-4 py-2 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Fin</label>
                                <input type="time" v-model="newSlot.end_time" class="w-full px-4 py-2 bg-[#fcf8f4] border border-[#ebdaca] rounded-lg focus:ring-2 focus:ring-brand-accent outline-none">
                            </div>
                        </div>
                        <button @click="addPendingSlot" class="w-full mt-2 flex items-center justify-center gap-2 bg-[#fcf8f4] border border-brand-text text-brand-text py-2 rounded-xl hover:bg-brand-text hover:text-brand-surface transition-colors font-medium">
                            <PhPlus :size="20" /> Ajouter à la liste
                        </button>
                    </div>
                </div>

                <div v-if="pendingSlots.length > 0" class="bg-[#fcf8f4] p-6 rounded-2xl border border-brand-accent/30">
                    <h3 class="font-medium mb-3 flex items-center justify-between">
                        Créneaux en attente
                        <span class="bg-brand-accent text-white text-xs px-2 py-1 rounded-full">{{ pendingSlots.length }}</span>
                    </h3>
                    <ul class="space-y-2 mb-4 max-h-60 overflow-y-auto pr-2">
                        <li v-for="(slot, index) in pendingSlots" :key="index" class="flex items-center justify-between bg-white p-3 rounded-lg border border-[#ebdaca] text-sm">
                            <div>
                                <strong class="block capitalize">{{ formatDate(slot.date) }}</strong>
                                <span class="opacity-70">{{ slot.start_time }} - {{ slot.end_time }}</span>
                            </div>
                            <button @click="removePendingSlot(index)" class="text-red-500 hover:text-red-700 p-1">
                                <PhTrash :size="18" />
                            </button>
                        </li>
                    </ul>
                    <button @click="saveSlots" :disabled="form.processing" class="w-full flex items-center justify-center gap-2 bg-brand-accent text-white py-3 rounded-xl hover:bg-[#a66a43] transition-colors font-medium disabled:opacity-50">
                        <PhFloppyDisk :size="20" /> Sauvegarder dans l'agenda
                    </button>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-brand-surface p-6 rounded-2xl shadow-sm border border-[#ebdaca] min-h-[500px]">
                    <h2 class="font-season text-2xl mb-6">Planning enregistré</h2>

                    <div v-if="Object.keys(groupedAvailabilities).length === 0" class="text-center py-12 opacity-50">
                        <PhCalendarBlank :size="48" class="mx-auto mb-3" />
                        <p>Aucun créneau enregistré pour le moment.</p>
                    </div>

                    <div v-else class="space-y-6">
                        <div v-for="(slots, date) in groupedAvailabilities" :key="date" class="border-b border-[#ebdaca] pb-4 last:border-0">
                            <h3 class="font-medium text-lg capitalize mb-3 text-brand-accent flex items-center gap-2">
                                <PhCalendarCheck :size="20" /> {{ formatDate(date) }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                <div v-for="slot in slots" :key="slot.id" 
                                     class="flex flex-col p-3 rounded-xl border relative overflow-hidden"
                                     :class="slot.is_booked ? 'bg-brand-bg border-brand-accent/20' : 'bg-white border-[#ebdaca]'">
                                    
                                    <div class="flex items-center gap-2 mb-1">
                                        <PhClock :size="16" class="opacity-70" />
                                        <span class="font-medium text-sm">{{ formatTime(slot.start_time) }} - {{ formatTime(slot.end_time) }}</span>
                                    </div>
                                    
                                    <div class="mt-2 flex justify-between items-center text-xs">
                                        <span v-if="slot.is_booked" class="text-brand-accent font-bold px-2 py-1 bg-brand-accent/10 rounded-md">
                                            Réservé
                                        </span>
                                        <span v-else class="text-green-600 font-medium px-2 py-1 bg-green-50 rounded-md">
                                            Libre
                                        </span>

                                        <button v-if="!slot.is_booked" @click="deleteSlot(slot.id)" class="text-red-400 hover:text-red-600 transition-colors p-1" title="Supprimer ce créneau">
                                            <PhTrash :size="16" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>