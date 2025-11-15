<template>
    <nav :class="[
        'fixed top-0 left-0 right-0 z-50 bg-[#fff5f5] transition-all duration-200',
        isScrolled ? 'border-b border-gray-200' : 'border-b border-transparent'
    ]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-12 md:h-16">
                <!-- Logo - Visible on all screens -->
                <div class="flex items-center">
                    <button
                        @click="navigate('roadmap')"
                        class="flex items-center gap-3 hover:opacity-80 transition-opacity"
                    >
                        <img
                            src="/images/Logo.webp"
                            alt="Logo"
                            class="h-6 md:h-8 w-auto object-contain"
                        />
                    </button>
                </div>

                <!-- Desktop Navigation - Hidden on mobile -->
                <div class="hidden md:flex items-center gap-1">
                    <button
                        v-for="item in navigationItems"
                        :key="item.key"
                        @click="navigate(item.key)"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                            activeTab === item.key
                                ? 'bg-gray-100 text-gray-900'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                        ]"
                    >
                        {{ item.label }}
                    </button>
                </div>

                <!-- Mobile Menu Button - Hidden on mobile, only logo shown -->
                <div class="hidden md:block"></div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    activeTab: {
        type: String,
        default: 'roadmap'
    }
});

const emit = defineEmits(['navigate']);

const mobileMenuOpen = ref(false);
const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 0;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Check initial scroll position
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const navigationItems = [
    {
        key: 'roadmap',
        label: 'Home'
    },
    {
        key: 'business',
        label: 'Business'
    },
    {
        key: 'roadmap-tab',
        label: 'Roadmap'
    },
    {
        key: 'advisors',
        label: 'Advisors'
    },
    {
        key: 'calendar',
        label: 'Calendar'
    },
    {
        key: 'network',
        label: 'Network'
    },
    {
        key: 'add',
        label: 'Add'
    }
];

const navigate = (tab) => {
    emit('navigate', tab);
};

const handleMobileNavigate = (tab) => {
    navigate(tab);
    mobileMenuOpen.value = false;
};
</script>

<style scoped>
/* Additional styles if needed */
</style>

