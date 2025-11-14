<template>
    <Head title="Sign Up" />
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-4xl font-extrabold text-gray-900">
                    Create Your Account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Start building your startup roadmap today
                </p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div v-if="Object.keys($page.props.errors).length > 0" 
                         class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <span class="text-red-500 text-xl">⚠️</span>
                            </div>
                            <div class="ml-3">
                                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                    <li v-for="(error, key) in $page.props.errors" :key="key">
                                        {{ error }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="John Doe"
                        />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="you@example.com"
                        />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Create a password"
                        />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Confirm your password"
                        />
                    </div>

                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Language
                        </label>
                        <select
                            id="language"
                            v-model="form.language"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        >
                            <option value="en">English</option>
                            <option value="fi">Finnish</option>
                            <option value="sv">Swedish</option>
                        </select>
                    </div>

                    <div>
                        <label for="country_of_origin" class="block text-sm font-medium text-gray-700 mb-2">
                            Country of Origin (Optional)
                        </label>
                        <input
                            id="country_of_origin"
                            v-model="form.country_of_origin"
                            type="text"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="e.g., Finland, USA, etc."
                        />
                    </div>

                    <div class="flex items-center">
                        <input
                            id="has_business_experience"
                            v-model="form.has_business_experience"
                            type="checkbox"
                            value="1"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label for="has_business_experience" class="ml-2 block text-sm text-gray-700">
                            I have previous business experience
                        </label>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 disabled:opacity-50"
                        >
                            Create Account
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <Link :href="route('login')" class="font-medium text-blue-600 hover:text-blue-500">
                                Sign in
                            </Link>
                        </p>
                    </div>
                </form>
            </div>

            <div class="text-center">
                <Link :href="route('home')" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Back to home
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    language: 'en',
    country_of_origin: '',
    has_business_experience: false,
});

const submit = () => {
    form.post(route('register'));
};
</script>

