<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay" @click.self="close">
        <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto modal-content">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-lg">
                <h2 class="text-2xl font-bold text-gray-800">Generate Business Plan PDF</h2>
                <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div v-if="!hasEnoughInfo" class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <p class="text-sm text-yellow-700">
                        <strong>Note:</strong> You may want to fill in more business plan information before generating the PDF for best results.
                    </p>
                </div>

                <div v-if="generating" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                    <p class="text-lg font-medium text-gray-700">Generating your business plan PDF...</p>
                    <p class="text-sm text-gray-500 mt-2">Please wait, this may take a moment.</p>
                </div>

                <div v-else class="space-y-6">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                        <p class="text-sm text-blue-700">
                            <strong>Ready to generate?</strong> We'll create a PDF from your business plan using the template. The download will start automatically when ready.
                        </p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-600 mb-2"><strong>Your business plan includes:</strong></p>
                        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                            <li>{{ filledFieldsCount }} filled fields</li>
                            <li v-if="businessPlan.business_name">Business Name: {{ businessPlan.business_name }}</li>
                            <li v-if="businessPlan.industry">Industry: {{ businessPlan.industry }}</li>
                            <li v-if="businessPlan.business_idea">Business Idea: Available</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 flex gap-4 justify-end">
                    <button
                        @click="close"
                        :disabled="generating"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Cancel
                    </button>
                    <button
                        @click="generatePDF"
                        :disabled="generating"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <span v-if="generating">
                            <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                            Generating...
                        </span>
                        <span v-else>Generate & Download PDF</span>
                    </button>
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
    businessPlan: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close']);

const generating = ref(false);

const hasEnoughInfo = computed(() => {
    const bp = props.businessPlan || {};
    const filledFields = Object.values(bp).filter(v => 
        v !== null && v !== undefined && v !== ''
    ).length;
    return filledFields >= 5; // At least 5 fields filled
});

const filledFieldsCount = computed(() => {
    const bp = props.businessPlan || {};
    return Object.values(bp).filter(v => 
        v !== null && v !== undefined && v !== ''
    ).length;
});

const close = () => {
    if (!generating.value) {
        emit('close');
    }
};

const generatePDF = async () => {
    generating.value = true;
    
    try {
        // Call the API to generate PDF
        const response = await axios.post('/api/business-plan/generate-pdf', {}, {
            responseType: 'blob', // Important for binary data
            headers: {
                'Accept': 'application/pdf'
            }
        });

        // Create a blob URL and trigger download
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        
        // Extract filename from Content-Disposition header if available
        const contentDisposition = response.headers['content-disposition'];
        let filename = 'business-plan-' + new Date().toISOString().split('T')[0] + '.pdf';
        if (contentDisposition) {
            // Try to extract filename from Content-Disposition header
            const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/i);
            if (filenameMatch && filenameMatch[1]) {
                // Remove quotes if present and trim
                filename = filenameMatch[1].replace(/['"]/g, '').trim();
                // Ensure it ends with .pdf
                if (!filename.endsWith('.pdf')) {
                    filename = filename.replace(/\.pdf_?$/i, '') + '.pdf';
                }
            }
        }
        
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Clean up the blob URL
        window.URL.revokeObjectURL(url);

        // Close modal after successful download
        setTimeout(() => {
            generating.value = false;
            emit('close');
        }, 500);
    } catch (error) {
        console.error('Error generating PDF:', error);
        generating.value = false;
        
        let errorMessage = 'Failed to generate PDF. Please try again.';
        if (error.response) {
            if (error.response.status === 404) {
                errorMessage = 'Template file not found. Please contact support.';
            } else if (error.response.data && error.response.data.message) {
                errorMessage = error.response.data.message;
            }
        }
        
        alert(errorMessage);
    }
};

// Auto-generate when modal is shown (optional - can be removed if you want manual trigger)
watch(() => props.show, (isShowing) => {
    if (isShowing && hasEnoughInfo.value) {
        // Optionally auto-generate when modal opens
        // Uncomment the line below if you want auto-generation
        // generatePDF();
    }
});
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

