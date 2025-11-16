<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay" @click.self="close">
        <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto modal-content border border-gray-200">
            <div class="sticky top-0 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-lg z-10">
                <h2 class="text-2xl font-bold text-gray-900">Schedule Advisor Meeting</h2>
                <button @click="close" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <!-- Loading State -->
                <div v-if="checkingReadiness" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-[#5cc094] mb-4"></div>
                    <p class="text-lg font-medium text-gray-700">Checking your readiness...</p>
                </div>

                <!-- Not Ready State -->
                <div v-else-if="!readinessData.is_ready" class="space-y-6">
                    <div class="bg-white border-l-4 border-yellow-400 p-4 rounded-lg border border-yellow-200">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Not Quite Ready Yet</h3>
                                <p class="text-sm text-gray-700 mb-3">
                                    You're {{ readinessData.readiness_score }}% ready. Let's get you prepared for your advisor meeting!
                                </p>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-gray-200">
                                    <p class="text-sm font-semibold text-gray-900 mb-2">Progress:</p>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 mb-2">
                                        <div 
                                            class="bg-yellow-400 h-2.5 rounded-full transition-all duration-300" 
                                            :style="{ width: readinessData.readiness_score + '%' }"
                                        ></div>
                                    </div>
                                    <p class="text-xs text-gray-600">
                                        {{ readinessData.filled_fields_count }} of {{ readinessData.total_fields_count }} business plan fields completed
                                        <span v-if="!readinessData.has_roadmap" class="text-gray-500"> • No roadmap created yet</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border-l-4 border-[#5cc094] p-4 rounded-lg border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-2">What You Need to Do:</h4>
                        <ul class="space-y-2">
                            <li v-for="(rec, index) in readinessData.recommendations" :key="index" class="flex items-start text-sm text-gray-700">
                                <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0 text-[#5cc094]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                                {{ rec }}
                            </li>
                        </ul>
                    </div>

                    <div class="flex gap-4 justify-end">
                        <button
                            @click="close"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            I'll Prepare First
                        </button>
                        <button
                            @click="proceedAnyway"
                            class="px-6 py-2 bg-[#5cc094] text-white rounded-lg hover:bg-[#4a9d7a] transition-colors font-semibold"
                        >
                            Schedule Anyway
                        </button>
                    </div>
                </div>

                <!-- Ready State - Scheduling Form -->
                <div v-else class="space-y-6">
                    <div class="bg-green-50 border-l-4 border-[#5cc094] p-4 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-[#5cc094] mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-gray-800 font-semibold">Great! You're ready to schedule a meeting with an advisor.</p>
                        </div>
                    </div>

                    <!-- Advisor Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Select Advisor <span class="text-red-400">*</span>
                        </label>
                        <select
                            v-model="selectedAdvisorId"
                            class="w-full px-4 py-2 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#5cc094] focus:border-[#5cc094] transition-colors"
                            :disabled="loadingAdvisors || scheduling"
                        >
                            <option value="" class="bg-white text-gray-900">-- Select an advisor --</option>
                            <option 
                                v-for="advisor in filteredAdvisors" 
                                :key="advisor.id" 
                                :value="advisor.id"
                                class="bg-white text-gray-900"
                            >
                                {{ advisor.name }} 
                                <span v-if="advisor.title">- {{ advisor.title }}</span>
                                <span v-if="advisor.specialization"> ({{ getSpecializationLabel(advisor.specialization) }})</span>
                            </option>
                        </select>
                        <p v-if="selectedAdvisor" class="mt-2 text-sm text-gray-600">
                            <span v-if="selectedAdvisor.email" class="block">📧 {{ selectedAdvisor.email }}</span>
                            <span v-if="selectedAdvisor.bio" class="block mt-1 text-gray-700">{{ selectedAdvisor.bio }}</span>
                        </p>
                    </div>

                    <!-- Meeting Topic -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Meeting Topic
                        </label>
                        <input
                            v-model="meetingTopic"
                            type="text"
                            placeholder="e.g., Funding application, Business registration, etc."
                            class="w-full px-4 py-2 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#5cc094] focus:border-[#5cc094] placeholder:text-gray-400 transition-colors"
                            :disabled="scheduling"
                        />
                    </div>

                    <!-- Date Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Date <span class="text-red-400">*</span>
                        </label>
                        <input
                            v-model="meetingDate"
                            type="date"
                            :min="minDate"
                            class="w-full px-4 py-2 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#5cc094] focus:border-[#5cc094] transition-colors"
                            :disabled="scheduling"
                        />
                    </div>

                    <!-- Time Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Time <span class="text-red-400">*</span>
                        </label>
                        <input
                            v-model="meetingTime"
                            type="time"
                            class="w-full px-4 py-2 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#5cc094] focus:border-[#5cc094] transition-colors"
                            :disabled="scheduling"
                        />
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Additional Notes (Optional)
                        </label>
                        <textarea
                            v-model="meetingNotes"
                            rows="3"
                            placeholder="Any specific questions or topics you'd like to discuss..."
                            class="w-full px-4 py-2 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#5cc094] focus:border-[#5cc094] placeholder:text-gray-400 transition-colors resize-none"
                            :disabled="scheduling"
                        ></textarea>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg border border-red-200">
                        <p class="text-sm text-red-700">{{ errorMessage }}</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 justify-end">
                        <button
                            @click="close"
                            :disabled="scheduling"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Cancel
                        </button>
                        <button
                            @click="scheduleMeeting"
                            :disabled="!canSchedule || scheduling"
                            class="px-6 py-2 bg-[#5cc094] text-white rounded-lg hover:bg-[#4a9d7a] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 font-semibold"
                        >
                            <span v-if="scheduling">
                                <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                                Scheduling...
                            </span>
                            <span v-else>📅 Schedule Meeting</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    preselectedAdvisorId: {
        type: Number,
        default: null
    },
    specialization: {
        type: String,
        default: null
    },
    topic: {
        type: String,
        default: null
    },
    advisors: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'scheduled']);

const checkingReadiness = ref(true);
const readinessData = ref({
    is_ready: false,
    readiness_score: 0,
    filled_fields_count: 0,
    total_fields_count: 0,
    has_roadmap: false,
    recommendations: []
});
const loadingAdvisors = ref(false);
const advisorsList = ref(props.advisors || []);
const selectedAdvisorId = ref(props.preselectedAdvisorId || null);
const meetingTopic = ref(props.topic || '');
const meetingDate = ref('');
const meetingTime = ref('');
const meetingNotes = ref('');
const scheduling = ref(false);
const errorMessage = ref('');
const proceedAnywayFlag = ref(false);

const minDate = computed(() => {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    return tomorrow.toISOString().split('T')[0];
});

const filteredAdvisors = computed(() => {
    if (!props.specialization) {
        return advisorsList.value;
    }
    return advisorsList.value.filter(advisor => advisor.specialization === props.specialization);
});

const selectedAdvisor = computed(() => {
    if (!selectedAdvisorId.value) return null;
    return advisorsList.value.find(a => a.id === selectedAdvisorId.value);
});

const canSchedule = computed(() => {
    return selectedAdvisorId.value && meetingDate.value && meetingTime.value && !scheduling.value;
});

const getSpecializationLabel = (specialization) => {
    const labels = {
        'residence_permit': 'Residence Permit',
        'business_registration': 'Business Registration',
        'tax': 'Tax & Accounting',
        'funding': 'Funding & Finance',
        'legal': 'Legal & IP',
        'marketing': 'Marketing & Sales'
    };
    return labels[specialization] || specialization;
};

const checkReadiness = async () => {
    checkingReadiness.value = true;
    errorMessage.value = '';
    
    try {
        const response = await axios.get('/api/meetings/readiness');
        readinessData.value = response.data;
    } catch (error) {
        console.error('Error checking readiness:', error);
        errorMessage.value = 'Failed to check readiness. Please try again.';
        // Default to ready if check fails
        readinessData.value = {
            is_ready: true,
            readiness_score: 100,
            filled_fields_count: 0,
            total_fields_count: 0,
            has_roadmap: true,
            recommendations: []
        };
    } finally {
        checkingReadiness.value = false;
    }
};

const loadAdvisors = async () => {
    if (advisorsList.value.length > 0) {
        return; // Already loaded
    }
    
    loadingAdvisors.value = true;
    try {
        const response = await axios.get('/api/advisors');
        if (response.data && response.data.advisors) {
            advisorsList.value = response.data.advisors;
        }
    } catch (error) {
        console.error('Error loading advisors:', error);
    } finally {
        loadingAdvisors.value = false;
    }
};

const proceedAnyway = () => {
    proceedAnywayFlag.value = true;
    readinessData.value.is_ready = true;
};

const scheduleMeeting = async () => {
    if (!canSchedule.value) return;
    
    scheduling.value = true;
    errorMessage.value = '';
    
    try {
        // Combine date and time
        const scheduledAt = new Date(`${meetingDate.value}T${meetingTime.value}`);
        
        const response = await axios.post('/api/meetings', {
            advisor_id: selectedAdvisorId.value,
            scheduled_at: scheduledAt.toISOString(),
            topic: meetingTopic.value || null,
            notes: meetingNotes.value || null
        });
        
        // Success - emit event and close
        emit('scheduled', response.data.meeting);
        
        // Add to calendar (create .ics file)
        addToCalendar(response.data.meeting);
        
        setTimeout(() => {
            close();
        }, 1000);
    } catch (error) {
        console.error('Error scheduling meeting:', error);
        if (error.response && error.response.data && error.response.data.messages) {
            const messages = error.response.data.messages;
            errorMessage.value = Object.values(messages).flat().join(', ');
        } else {
            errorMessage.value = error.response?.data?.error || 'Failed to schedule meeting. Please try again.';
        }
    } finally {
        scheduling.value = false;
    }
};

const addToCalendar = (meeting) => {
    try {
        const advisor = meeting.advisor;
        const startDate = new Date(meeting.scheduled_at);
        const endDate = new Date(startDate.getTime() + 60 * 60 * 1000); // 1 hour later
        
        const formatDate = (date) => {
            return date.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
        };
        
        const icsContent = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Freyja//Advisor Meeting//EN',
            'BEGIN:VEVENT',
            `UID:meeting-${meeting.id}@freyja.fi`,
            `DTSTART:${formatDate(startDate)}`,
            `DTEND:${formatDate(endDate)}`,
            `SUMMARY:Meeting with ${advisor.name}${meeting.topic ? ' - ' + meeting.topic : ''}`,
            `DESCRIPTION:${meeting.notes || 'Advisor meeting'}`,
            `LOCATION:Online/Espoo`,
            `ORGANIZER;CN=${advisor.name}:MAILTO:${advisor.email}`,
            'END:VEVENT',
            'END:VCALENDAR'
        ].join('\r\n');
        
        const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `meeting-${meeting.id}.ics`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error creating calendar file:', error);
    }
};

const close = () => {
    if (!scheduling.value) {
        emit('close');
    }
};

// Watch for modal opening
watch(() => props.show, (isShowing) => {
    if (isShowing) {
        checkingReadiness.value = true;
        proceedAnywayFlag.value = false;
        selectedAdvisorId.value = props.preselectedAdvisorId || null;
        meetingTopic.value = props.topic || '';
        meetingDate.value = '';
        meetingTime.value = '';
        meetingNotes.value = '';
        errorMessage.value = '';
        
        // Load advisors and check readiness
        Promise.all([
            loadAdvisors(),
            checkReadiness()
        ]);
    }
});

// Initialize
onMounted(() => {
    if (props.advisors && props.advisors.length > 0) {
        advisorsList.value = props.advisors;
    }
});
</script>

<style scoped>
.modal-overlay {
    background-color: rgba(247, 250, 248, 0.85);
    backdrop-filter: blur(2px);
    animation: fadeIn 0.2s ease-out;
}

.modal-content {
    animation: slideUp 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

