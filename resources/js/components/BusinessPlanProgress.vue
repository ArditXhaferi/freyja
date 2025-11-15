<template>
    <div class="w-full">
        <!-- Cute Header -->
        <div class="mb-4 bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400 rounded-2xl p-4 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="text-3xl">📋</div>
                    <h3 class="text-xl font-bold text-white">Business Plan</h3>
                </div>
                <div class="flex items-center gap-3 bg-white/30 backdrop-blur-sm rounded-full px-4 py-2">
                    <div class="text-sm font-bold text-white">
                        {{ completedCount }}/{{ totalFields }}
                    </div>
                    <div class="w-20 h-3 bg-white/50 rounded-full overflow-hidden">
                        <div 
                            class="h-full bg-white rounded-full transition-all duration-500 shadow-sm"
                            :style="{ width: `${completionPercentage}%` }"
                        ></div>
                    </div>
                    <div class="text-lg font-bold text-white w-10 text-right">
                        {{ Math.round(completionPercentage) }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Cute Grid Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div
                v-for="(section, sectionKey) in businessPlanSections"
                :key="sectionKey"
                :class="[
                    'rounded-2xl p-3 transition-all duration-300 hover:scale-105 cursor-pointer shadow-md',
                    section.completed 
                        ? 'bg-gradient-to-br from-green-100 to-emerald-200 border-2 border-green-300' 
                        : 'bg-gradient-to-br from-blue-50 to-purple-50 border-2 border-blue-200'
                ]"
            >
                <!-- Section Header -->
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div :class="[
                            'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shadow-sm',
                            section.completed 
                                ? 'bg-green-400 text-white' 
                                : 'bg-blue-300 text-blue-700'
                        ]">
                            <span v-if="section.completed">✨</span>
                            <span v-else>{{ section.filledCount }}</span>
                        </div>
                        <h4 class="font-bold text-xs text-gray-700">{{ section.title }}</h4>
                    </div>
                </div>

                <!-- Cute Field List -->
                <div class="space-y-1.5">
                    <div
                        v-for="fieldKey in section.fields"
                        :key="fieldKey"
                        :ref="el => setFieldRef(fieldKey, el)"
                        :id="`field-${fieldKey}`"
                        :class="[
                            'flex items-center gap-2 p-1.5 rounded-lg text-xs transition-all',
                            isFieldFilled(fieldKey) 
                                ? 'bg-green-200 border border-green-300' 
                                : 'bg-white/60 border border-blue-200',
                            fieldToHighlight === fieldKey ? 'field-highlight' : ''
                        ]"
                    >
                        <div :class="[
                            'w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0',
                            isFieldFilled(fieldKey) 
                                ? 'bg-green-400 text-white' 
                                : 'bg-blue-200 text-blue-600'
                        ]">
                            <span v-if="isFieldFilled(fieldKey)">✓</span>
                            <span v-else class="text-[8px]">○</span>
                        </div>
                        <div class="flex-1 min-w-0 font-medium truncate text-gray-700">
                            {{ getFieldLabel(fieldKey) }}
                        </div>
                    </div>
                </div>

                <!-- Cute Progress Bar -->
                <div class="mt-2 pt-2 border-t border-blue-200">
                    <div class="flex items-center justify-between text-[10px] font-bold text-gray-600 mb-1">
                        <span>{{ section.filledCount }}/{{ section.fields.length }}</span>
                    </div>
                    <div class="w-full h-2 bg-white/60 rounded-full overflow-hidden">
                        <div 
                            :class="[
                                'h-full rounded-full transition-all duration-500',
                                section.completed ? 'bg-gradient-to-r from-green-400 to-emerald-500' : 'bg-gradient-to-r from-blue-400 to-purple-400'
                            ]"
                            :style="{ width: `${(section.filledCount / section.fields.length) * 100}%` }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Celebration Banner -->
        <div 
            v-if="completionPercentage === 100"
            class="mt-4 bg-gradient-to-r from-yellow-300 via-pink-300 to-purple-300 rounded-2xl p-4 text-center shadow-lg animate-bounce"
        >
            <div class="text-4xl mb-2">🎉</div>
            <h3 class="text-lg font-bold text-gray-800">All Complete! 🎊</h3>
        </div>
    </div>
</template>

<script setup>
import { computed, watch, ref, nextTick, onMounted } from 'vue';

const props = defineProps({
    businessPlan: {
        type: Object,
        default: () => ({})
    },
    recentlyAnsweredFields: {
        type: Set,
        default: () => new Set()
    },
    fieldToHighlight: {
        type: String,
        default: null
    }
});

const updateTrigger = ref(0);
const fieldRefs = ref({});

const setFieldRef = (fieldKey, el) => {
    if (el) {
        fieldRefs.value[fieldKey] = el;
    }
};

// Watch for field to highlight and scroll to it
watch(() => props.fieldToHighlight, async (newField) => {
    if (newField) {
        // Wait for drawer to open and DOM to update
        await nextTick();
        // Small delay to ensure drawer is fully rendered
        setTimeout(async () => {
            await nextTick();
            const element = fieldRefs.value[newField];
            if (element) {
                // Scroll to the field with smooth behavior
                element.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center',
                    inline: 'nearest'
                });
            }
        }, 300);
    }
}, { immediate: true });

watch(() => props.businessPlan, () => {
    updateTrigger.value++;
}, { deep: true, immediate: true });

const businessPlanSections = computed(() => {
    const _ = updateTrigger.value;
    
    const sections = {
        basic: {
            title: 'Basic Info',
            fields: ['business_name', 'company_planned_name', 'company_type', 'industry', 'address', 'zip_code', 'postal_district', 'internet_address', 'business_id'],
            filledCount: 0,
            completed: false
        },
        company: {
            title: 'Company',
            fields: ['year_of_establishment', 'number_of_employees', 'company_owners_holdings', 'company_contact_info'],
            filledCount: 0,
            completed: false
        },
        business: {
            title: 'Concept',
            fields: ['business_idea', 'competence_skills', 'vision_long_term', 'my_business_comprehensive'],
            filledCount: 0,
            completed: false
        },
        analysis: {
            title: 'Analysis',
            fields: ['swot_analysis', 'competitors', 'competitive_situation', 'operating_environment_risks', 'industry_future_prospects'],
            filledCount: 0,
            completed: false
        },
        products: {
            title: 'Products',
            fields: ['products_services_general', 'products_services_detailed'],
            filledCount: 0,
            completed: false
        },
        market: {
            title: 'Market',
            fields: ['target_market_groups', 'sales_marketing', 'distribution_network', 'production_logistics'],
            filledCount: 0,
            completed: false
        },
        legal: {
            title: 'Legal',
            fields: ['permits_notices', 'insurance_contracts', 'intellectual_property_rights', 'support_network'],
            filledCount: 0,
            completed: false
        }
    };

    Object.keys(sections).forEach(sectionKey => {
        const section = sections[sectionKey];
        section.filledCount = section.fields.filter(field => isFieldFilled(field)).length;
        section.completed = section.filledCount === section.fields.length;
    });

    return sections;
});

const totalFields = computed(() => {
    return Object.values(businessPlanSections.value).reduce((sum, section) => sum + section.fields.length, 0);
});

const completedCount = computed(() => {
    return Object.values(businessPlanSections.value).reduce((sum, section) => sum + section.filledCount, 0);
});

const completionPercentage = computed(() => {
    if (totalFields.value === 0) return 0;
    return (completedCount.value / totalFields.value) * 100;
});

const isFieldFilled = (fieldKey) => {
    const _ = updateTrigger.value;
    const value = props.businessPlan?.[fieldKey];
    
    // Handle null/undefined
    if (value === null || value === undefined) return false;
    
    // Handle booleans - both true and false are considered filled
    if (typeof value === 'boolean') return true;
    
    // Handle numbers - including 0, which is a valid value
    if (typeof value === 'number') return true;
    
    // Handle strings - empty strings are not filled
    if (typeof value === 'string' && value.trim() === '') return false;
    if (typeof value === 'string') return true;
    
    // Handle arrays - empty arrays are not filled
    if (Array.isArray(value) && value.length === 0) return false;
    if (Array.isArray(value)) return true;
    
    // Handle objects - empty objects are not filled
    if (typeof value === 'object' && Object.keys(value).length === 0) return false;
    if (typeof value === 'object') return true;
    
    return false;
};

const getFieldLabel = (fieldKey) => {
    const labels = {
        business_name: 'Business Name',
        company_planned_name: 'Company Name',
        company_type: 'Company Type',
        industry: 'Industry',
        address: 'Address',
        zip_code: 'ZIP Code',
        postal_district: 'Postal District',
        internet_address: 'Website',
        business_id: 'Business ID',
        year_of_establishment: 'Year',
        number_of_employees: 'Employees',
        company_owners_holdings: 'Owners',
        company_contact_info: 'Contact',
        business_idea: 'Business Idea',
        competence_skills: 'Skills',
        vision_long_term: 'Vision',
        my_business_comprehensive: 'Overview',
        swot_analysis: 'SWOT',
        competitors: 'Competitors',
        competitive_situation: 'Advantage',
        operating_environment_risks: 'Risks',
        industry_future_prospects: 'Prospects',
        products_services_general: 'Products',
        products_services_detailed: 'Details',
        target_market_groups: 'Target Market',
        sales_marketing: 'Sales',
        distribution_network: 'Distribution',
        production_logistics: 'Logistics',
        permits_notices: 'Permits',
        insurance_contracts: 'Insurance',
        intellectual_property_rights: 'IP Rights',
        support_network: 'Support'
    };
    return labels[fieldKey] || fieldKey.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};
</script>

<style scoped>
/* Cute, playful animations */

/* Field highlight animation */
.field-highlight {
    animation: highlightPulse 5s ease-in-out;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fcd34d 100%) !important;
    border: 3px solid #f59e0b !important;
    box-shadow: 0 0 20px rgba(245, 158, 11, 0.5), 0 0 40px rgba(245, 158, 11, 0.3);
    transform: scale(1.05);
    z-index: 10;
    position: relative;
}

@keyframes highlightPulse {
    0% {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fcd34d 100%);
        border-color: #f59e0b;
        box-shadow: 0 0 20px rgba(245, 158, 11, 0.5), 0 0 40px rgba(245, 158, 11, 0.3);
        transform: scale(1.05);
    }
    50% {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fcd34d 100%);
        border-color: #f59e0b;
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.4), 0 0 30px rgba(245, 158, 11, 0.2);
        transform: scale(1.03);
    }
    100% {
        background: var(--original-bg, transparent);
        border-color: var(--original-border, transparent);
        box-shadow: none;
        transform: scale(1);
    }
}
</style>
