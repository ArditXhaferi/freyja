<template>
    <div class="w-full">
        <!-- Header -->
        <div class="mb-4 bg-[#5cc094] rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h3 class="text-xl font-bold text-white">Business Plan</h3>
                </div>
                <div class="flex items-center gap-3 bg-[#4a9d7a] rounded-full px-4 py-2 border border-white/20">
                    <div class="text-sm font-bold text-white">
                        {{ completedCount }}/{{ totalFields }}
                    </div>
                    <div class="w-20 h-3 bg-white/10 rounded-full overflow-hidden">
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

        <!-- Sections Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="section in sectionsArray"
                :key="section.key"
                :class="[
                    'bg-white rounded-xl border-2 transition-all hover:shadow-lg cursor-pointer overflow-hidden',
                    section.completed 
                        ? 'border-[#5cc094] shadow-md' 
                        : 'border-gray-200 hover:border-gray-300'
                ]"
            >
                <!-- Card Header -->
                <div :class="[
                    'px-6 py-5 border-b-2',
                    section.completed 
                        ? 'bg-gradient-to-br from-[#5cc094]/10 to-[#5cc094]/5 border-[#5cc094]/20' 
                        : 'bg-gray-50 border-gray-200'
                ]">
                    <div class="flex items-center justify-between mb-3">
                        <div :class="[
                            'w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold border-2 flex-shrink-0',
                            section.completed 
                                ? 'bg-[#5cc094] text-white border-[#5cc094] shadow-sm' 
                                : 'bg-white text-gray-600 border-gray-300'
                        ]">
                            <span v-if="section.completed" class="text-xl">✓</span>
                            <span v-else>{{ section.filledCount }}</span>
                        </div>
                        <div :class="[
                            'px-3 py-1 rounded-full text-xs font-bold',
                            section.completed 
                                ? 'bg-[#5cc094] text-white' 
                                : 'bg-gray-200 text-gray-600'
                        ]">
                            {{ Math.round((section.filledCount / section.fields.length) * 100) }}%
                        </div>
                    </div>
                    <h4 class="font-bold text-lg text-gray-900 mb-1">{{ section.title }}</h4>
                    <p class="text-xs text-gray-500">{{ section.filledCount }} of {{ section.fields.length }} fields completed</p>
                </div>

                <!-- Field List (Compact) -->
                <div class="px-6 py-4 max-h-[400px] overflow-y-auto">
                    <div class="space-y-3">
                        <div
                            v-for="fieldKey in section.fields"
                            :key="fieldKey"
                            :ref="el => setFieldRef(fieldKey, el)"
                            :id="`field-${fieldKey}`"
                            :class="[
                                'p-3 rounded-lg transition-all cursor-pointer border',
                                isFieldFilled(fieldKey) 
                                    ? 'border-[#5cc094] bg-green-50/50' 
                                    : 'border-gray-200 bg-gray-50/50 hover:bg-gray-100',
                                fieldToHighlight === fieldKey ? 'field-highlight border-blue-400 bg-blue-50' : '',
                                editingField === fieldKey ? 'border-[#5cc094] bg-blue-50' : ''
                            ]"
                            @click.stop="startEditing(fieldKey)"
                        >
                            <div class="flex items-start gap-3">
                                <!-- Status Indicator -->
                                <div :class="[
                                    'w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 border-2 mt-0.5',
                                    isFieldFilled(fieldKey) 
                                        ? 'bg-[#5cc094] border-[#5cc094]' 
                                        : 'bg-white border-gray-300'
                                ]">
                                    <span v-if="isFieldFilled(fieldKey)" class="text-white text-[10px] font-bold">✓</span>
                                    <span v-else class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                </div>
                                
                                <!-- Field Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h5 class="font-semibold text-xs text-gray-900">{{ getFieldLabel(fieldKey) }}</h5>
                                        <i class="fa-solid fa-pen text-[10px] text-gray-400"></i>
                                    </div>
                                    
                                    <!-- Editing Mode -->
                                    <div v-if="editingField === fieldKey" class="mt-2" @click.stop>
                                        <textarea
                                            v-if="isTextField(fieldKey)"
                                            v-model="editValue"
                                            @blur="saveField(fieldKey)"
                                            @keydown.enter.ctrl="saveField(fieldKey)"
                                            @keydown.esc="cancelEditing"
                                            @click.stop
                                            class="w-full bg-white text-gray-900 text-xs p-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#5cc094] focus:border-[#5cc094] resize-none"
                                            rows="3"
                                            ref="editInput"
                                        ></textarea>
                                        <input
                                            v-else
                                            v-model="editValue"
                                            @blur="saveField(fieldKey)"
                                            @keydown.enter="saveField(fieldKey)"
                                            @keydown.esc="cancelEditing"
                                            @click.stop
                                            type="text"
                                            class="w-full bg-white text-gray-900 text-xs p-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#5cc094] focus:border-[#5cc094]"
                                            ref="editInput"
                                        />
                                    </div>
                                    
                                    <!-- Display Mode -->
                                    <div v-else>
                                        <p v-if="isFieldFilled(fieldKey)" :class="['text-xs mt-1 line-clamp-2', isFieldFilled(fieldKey) ? 'text-[#5cc094] font-medium' : 'text-gray-600']">
                                            {{ formatFieldValue(fieldKey) }}
                                        </p>
                                        <p v-else class="text-xs text-gray-400 italic mt-1">
                                            Not provided
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Footer Progress -->
                <div :class="[
                    'px-6 py-4 border-t-2',
                    section.completed 
                        ? 'bg-gradient-to-br from-[#5cc094]/10 to-[#5cc094]/5 border-[#5cc094]/20' 
                        : 'bg-gray-50 border-gray-200'
                ]">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-gray-600">{{ section.filledCount }}/{{ section.fields.length }}</span>
                        <div class="flex-1 h-2.5 bg-gray-200 rounded-full overflow-hidden">
                            <div 
                                :class="[
                                    'h-full rounded-full transition-all duration-500',
                                    section.completed ? 'bg-[#5cc094]' : 'bg-gray-400'
                                ]"
                                :style="{ width: `${(section.filledCount / section.fields.length) * 100}%` }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Celebration Banner -->
        <div 
            v-if="completionPercentage === 100"
            class="mt-6 bg-gradient-to-r from-[#5cc094] to-[#4a9d7a] rounded-lg p-6 text-center border border-[#5cc094]/20"
        >
            <div class="text-5xl mb-3">🎉</div>
            <h3 class="text-xl font-bold text-white">All Complete! 🎊</h3>
            <p class="text-white/90 text-sm mt-1">Great job completing your business plan!</p>
        </div>
    </div>
</template>

<script setup>
import { computed, watch, ref, nextTick, onMounted } from 'vue';
import axios from 'axios';

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

const emit = defineEmits(['update']);

const updateTrigger = ref(0);
const fieldRefs = ref({});
const editingField = ref(null);
const editValue = ref('');
const editInput = ref(null);

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
            key: 'basic',
            title: 'Basic Info',
            fields: ['business_name', 'company_planned_name', 'company_type', 'industry', 'address', 'zip_code', 'postal_district', 'internet_address', 'business_id'],
            filledCount: 0,
            completed: false
        },
        company: {
            key: 'company',
            title: 'Company',
            fields: ['year_of_establishment', 'number_of_employees', 'company_owners_holdings', 'company_contact_info'],
            filledCount: 0,
            completed: false
        },
        business: {
            key: 'business',
            title: 'Concept',
            fields: ['business_idea', 'competence_skills', 'vision_long_term', 'my_business_comprehensive'],
            filledCount: 0,
            completed: false
        },
        analysis: {
            key: 'analysis',
            title: 'Analysis',
            fields: ['swot_analysis', 'competitors', 'competitive_situation', 'operating_environment_risks', 'industry_future_prospects'],
            filledCount: 0,
            completed: false
        },
        products: {
            key: 'products',
            title: 'Products',
            fields: ['products_services_general', 'products_services_detailed'],
            filledCount: 0,
            completed: false
        },
        market: {
            key: 'market',
            title: 'Market',
            fields: ['target_market_groups', 'sales_marketing', 'distribution_network', 'production_logistics'],
            filledCount: 0,
            completed: false
        },
        legal: {
            key: 'legal',
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

const sectionsArray = computed(() => {
    return Object.values(businessPlanSections.value);
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

const formatFieldValue = (fieldKey) => {
    const _ = updateTrigger.value;
    const value = props.businessPlan?.[fieldKey];
    
    if (value === null || value === undefined) return '';
    
    // Handle booleans
    if (typeof value === 'boolean') {
        return value ? 'Yes' : 'No';
    }
    
    // Handle numbers
    if (typeof value === 'number') {
        // Special formatting for certain fields
        if (fieldKey === 'year_of_establishment') {
            return value.toString();
        }
        if (fieldKey === 'number_of_employees') {
            return `${value} ${value === 1 ? 'employee' : 'employees'}`;
        }
        if (fieldKey === 'years_in_finland') {
            return `${value} ${value === 1 ? 'year' : 'years'}`;
        }
        return value.toString();
    }
    
    // Handle arrays
    if (Array.isArray(value)) {
        if (value.length === 0) return '';
        // For languages array
        if (fieldKey === 'languages') {
            return value.join(', ');
        }
        // For products_services_detailed
        if (fieldKey === 'products_services_detailed') {
            return `${value.length} ${value.length === 1 ? 'item' : 'items'}`;
        }
        return value.join(', ');
    }
    
    // Handle objects
    if (typeof value === 'object') {
        // For SWOT analysis
        if (fieldKey === 'swot_analysis') {
            const parts = [];
            if (value.strengths) parts.push('Strengths');
            if (value.weaknesses) parts.push('Weaknesses');
            if (value.opportunities) parts.push('Opportunities');
            if (value.threats) parts.push('Threats');
            return parts.length > 0 ? parts.join(', ') : 'SWOT analysis';
        }
        return Object.keys(value).length > 0 ? 'See details' : '';
    }
    
    // Handle strings
    if (typeof value === 'string') {
        // Truncate long strings
        if (value.length > 60) {
            return value.substring(0, 57) + '...';
        }
        return value;
    }
    
    return String(value);
};

const isTextField = (fieldKey) => {
    // Fields that should use textarea (long text fields)
    const textareaFields = [
        'business_idea', 'competence_skills', 'swot_analysis', 'products_services_general',
        'products_services_detailed', 'sales_marketing', 'production_logistics',
        'distribution_network', 'target_market_groups', 'competitors', 'competitive_situation',
        'operating_environment_risks', 'vision_long_term', 'industry_future_prospects',
        'permits_notices', 'insurance_contracts', 'intellectual_property_rights',
        'support_network', 'my_business_comprehensive', 'company_owners_holdings',
        'company_contact_info'
    ];
    return textareaFields.includes(fieldKey);
};

const startEditing = async (fieldKey) => {
    if (editingField.value === fieldKey) return; // Already editing this field
    
    editingField.value = fieldKey;
    const value = props.businessPlan?.[fieldKey];
    editValue.value = value !== null && value !== undefined ? String(value) : '';
    
    await nextTick();
    if (editInput.value) {
        if (Array.isArray(editInput.value)) {
            editInput.value[0]?.focus();
        } else {
            editInput.value.focus();
        }
    }
};

const cancelEditing = () => {
    editingField.value = null;
    editValue.value = '';
};

const saveField = async (fieldKey) => {
    if (editingField.value !== fieldKey) return;
    
    const newValue = editValue.value.trim();
    const oldValue = props.businessPlan?.[fieldKey];
    
    // Only save if value changed
    if (newValue !== String(oldValue || '')) {
        try {
            const updateData = {
                [fieldKey]: newValue || null
            };
            
            // Convert to number if it's a numeric field
            const numericFields = ['year_of_establishment', 'number_of_employees', 'years_in_finland'];
            if (numericFields.includes(fieldKey) && newValue) {
                const numValue = parseInt(newValue);
                if (!isNaN(numValue)) {
                    updateData[fieldKey] = numValue;
                }
            }
            
            // Emit update event to parent
            emit('update', updateData);
            
            // Also save to backend
            await axios.post('/api/business-plan/update', updateData);
        } catch (err) {
            console.error('Failed to save field:', err);
            alert('Failed to save. Please try again.');
        }
    }
    
    cancelEditing();
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Field highlight animation */
.field-highlight {
    animation: highlightPulse 3s ease-in-out;
    border-left: 4px solid #3b82f6 !important;
    background: #eff6ff !important;
    z-index: 10;
    position: relative;
}

@keyframes highlightPulse {
    0% {
        background: #dbeafe;
        border-left-color: #3b82f6;
    }
    50% {
        background: #eff6ff;
        border-left-color: #60a5fa;
    }
    100% {
        background: #eff6ff;
        border-left-color: #3b82f6;
    }
}
</style>
