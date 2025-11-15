<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay" @click.self="close">
        <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto modal-content">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-lg">
                <h2 class="text-2xl font-bold text-gray-800">Meeting Preparation Document</h2>
                <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div v-if="!hasEnoughInfo" class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <p class="text-sm text-yellow-700">
                        <strong>Note:</strong> You may want to fill in more business plan information before generating the meeting prep document for best results.
                    </p>
                </div>

                <div class="space-y-6">
                    <!-- Business Idea Summary -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Idea Summary</label>
                        <textarea
                            v-model="formData.businessIdea"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            rows="3"
                            placeholder="Describe your business idea..."
                        ></textarea>
                    </div>

                    <!-- Target Customers -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Customers</label>
                        <textarea
                            v-model="formData.targetCustomers"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            rows="2"
                            placeholder="Who are your target customers?"
                        ></textarea>
                    </div>

                    <!-- Funding Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Funding Status</label>
                        <textarea
                            v-model="formData.fundingStatus"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            rows="2"
                            placeholder="Have you secured any funding? What are your funding needs?"
                        ></textarea>
                    </div>

                    <!-- Questions for Advisor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Questions for Advisor</label>
                        <textarea
                            v-model="formData.questions"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            rows="4"
                            placeholder="What questions do you have for your advisor? (e.g., How do I register my company? What permits do I need?)"
                        ></textarea>
                    </div>

                    <!-- Additional Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                        <textarea
                            v-model="formData.notes"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            rows="3"
                            placeholder="Any other information you'd like to share with your advisor..."
                        ></textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-4 justify-end">
                    <button
                        @click="close"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="generatePDF"
                        :disabled="generating"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <span v-if="generating">Generating...</span>
                        <span v-else>Generate & Download PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import jsPDF from 'jspdf';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    businessPlan: {
        type: Object,
        default: () => ({})
    },
    prepData: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close']);

const formData = ref({
    businessIdea: '',
    targetCustomers: '',
    fundingStatus: '',
    questions: '',
    notes: ''
});

const generating = ref(false);

const hasEnoughInfo = computed(() => {
    const bp = props.businessPlan || {};
    const filledFields = Object.values(bp).filter(v => 
        v !== null && v !== undefined && v !== ''
    ).length;
    return filledFields >= 5; // At least 5 fields filled
});

watch(() => props.prepData, (newData) => {
    if (newData && typeof newData === 'object') {
        formData.value = {
            businessIdea: newData.business_idea || newData.businessIdea || formData.value.businessIdea || props.businessPlan?.business_idea || '',
            targetCustomers: newData.target_customers || newData.targetCustomers || formData.value.targetCustomers || props.businessPlan?.target_market_groups || '',
            fundingStatus: newData.funding_status || newData.fundingStatus || formData.value.fundingStatus || '',
            questions: newData.questions || formData.value.questions || '',
            notes: newData.notes || formData.value.notes || ''
        };
    }
}, { immediate: true });

watch(() => props.businessPlan, (newPlan) => {
    if (newPlan && Object.keys(newPlan).length > 0) {
        if (!formData.value.businessIdea && newPlan.business_idea) {
            formData.value.businessIdea = newPlan.business_idea;
        }
        if (!formData.value.targetCustomers && newPlan.target_market_groups) {
            formData.value.targetCustomers = newPlan.target_market_groups;
        }
    }
}, { immediate: true, deep: true });

const close = () => {
    emit('close');
};

const generatePDF = () => {
    generating.value = true;
    
    try {
        const doc = new jsPDF();
        const pageWidth = doc.internal.pageSize.getWidth();
        const margin = 20;
        const maxWidth = pageWidth - (margin * 2);
        let yPos = margin;

        // Helper function to add text with word wrap
        const addText = (text, fontSize = 12, isBold = false, color = [0, 0, 0]) => {
            doc.setFontSize(fontSize);
            doc.setTextColor(color[0], color[1], color[2]);
            if (isBold) {
                doc.setFont(undefined, 'bold');
            } else {
                doc.setFont(undefined, 'normal');
            }
            
            const lines = doc.splitTextToSize(text || '', maxWidth);
            lines.forEach(line => {
                if (yPos > doc.internal.pageSize.getHeight() - 30) {
                    doc.addPage();
                    yPos = margin;
                }
                doc.text(line, margin, yPos);
                yPos += fontSize * 0.5;
            });
            yPos += 5;
        };

        // Title
        addText('Meeting Preparation Document', 20, true, [0, 0, 0]);
        addText(`Generated: ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}`, 10, false, [100, 100, 100]);
        yPos += 10;

        // Business Idea
        if (formData.value.businessIdea) {
            addText('Business Idea Summary', 14, true);
            addText(formData.value.businessIdea, 11);
            yPos += 5;
        }

        // Target Customers
        if (formData.value.targetCustomers) {
            addText('Target Customers', 14, true);
            addText(formData.value.targetCustomers, 11);
            yPos += 5;
        }

        // Funding Status
        if (formData.value.fundingStatus) {
            addText('Funding Status', 14, true);
            addText(formData.value.fundingStatus, 11);
            yPos += 5;
        }

        // Questions for Advisor
        if (formData.value.questions) {
            addText('Questions for Advisor', 14, true);
            addText(formData.value.questions, 11);
            yPos += 5;
        }

        // Additional Notes
        if (formData.value.notes) {
            addText('Additional Notes', 14, true);
            addText(formData.value.notes, 11);
        }

        // Business Plan Summary (if available)
        const bp = props.businessPlan || {};
        const filledFields = Object.entries(bp).filter(([_, v]) => v !== null && v !== undefined && v !== '');
        if (filledFields.length > 0) {
            yPos += 10;
            addText('Business Plan Information', 14, true);
            filledFields.slice(0, 10).forEach(([key, value]) => {
                const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                const valueStr = typeof value === 'object' ? JSON.stringify(value) : String(value);
                addText(`${label}: ${valueStr.substring(0, 100)}${valueStr.length > 100 ? '...' : ''}`, 10);
            });
        }

        // Footer
        const pageCount = doc.internal.pages.length - 1;
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150, 150, 150);
            doc.text(`Page ${i} of ${pageCount}`, pageWidth - margin, doc.internal.pageSize.getHeight() - 10, { align: 'right' });
        }

        // Generate filename
        const timestamp = new Date().toISOString().split('T')[0];
        const filename = `meeting-prep-${timestamp}.pdf`;

        // Save PDF
        doc.save(filename);
        
        generating.value = false;
    } catch (error) {
        console.error('Error generating PDF:', error);
        generating.value = false;
        alert('Failed to generate PDF. Please try again.');
    }
};
</script>

<style scoped>
.modal-overlay {
    background-color: rgba(0, 0, 0, 0.5);
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

