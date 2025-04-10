<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Municipal Services">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#F5F7FA] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] lg:justify-center lg:p-8">
        <header class="not-has-[nav]:hidden mb-6 w-full max-w-[335px] text-sm lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div class="duration-750 starting:opacity-0 flex w-full items-center justify-center opacity-100 transition-opacity lg:grow">
            <main class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div
                    class="flex-1 rounded-bl-lg rounded-br-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] lg:rounded-br-none lg:rounded-tl-lg lg:p-20"
                >
                    <h1 class="mb-1 text-2xl font-medium text-emerald-700 dark:text-emerald-400">Municipal Services Portal</h1>
                    <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                        Access essential documents and permits for your needs.<br />Choose from the following services:
                    </p>
                    <ul class="mb-6 flex flex-col lg:mb-8">
                        <li
                            class="relative flex items-center gap-4 py-3 before:absolute before:bottom-0 before:left-[0.4rem] before:top-1/2 before:border-l before:border-emerald-200 dark:before:border-emerald-900"
                        >
                            <span class="relative bg-white py-1 dark:bg-[#161615]">
                                <span
                                    class="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-emerald-200 bg-[#FDFDFC] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-emerald-800 dark:bg-[#161615]"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400" />
                                </span>
                            </span>
                            <span class="font-medium">
                                Sanitary Permits
                                <p class="font-normal text-xs text-[#706f6c] dark:text-[#A1A09A]">For businesses and establishments</p>
                            </span>
                        </li>
                        <li
                            class="relative flex items-center gap-4 py-3 before:absolute before:bottom-1/2 before:left-[0.4rem] before:top-0 before:border-l before:border-emerald-200 before:border-b-0 dark:before:border-emerald-900"
                        >
                            <span class="relative bg-white py-1 dark:bg-[#161615]">
                                <span
                                    class="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-emerald-200 bg-[#FDFDFC] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-emerald-800 dark:bg-[#161615]"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400" />
                                </span>
                            </span>
                            <span class="font-medium">
                                Health Cards
                                <p class="font-normal text-xs text-[#706f6c] dark:text-[#A1A09A]">For food handlers and service workers</p>
                            </span>
                        </li>
                        <li
                            class="relative flex items-center gap-4 py-3 before:absolute before:bottom-1/2 before:left-[0.4rem] before:top-0 before:border-l before:border-emerald-200 dark:before:border-emerald-900"
                        >
                            <span class="relative bg-white py-1 dark:bg-[#161615]">
                                <span
                                    class="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-emerald-200 bg-[#FDFDFC] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-emerald-800 dark:bg-[#161615]"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400" />
                                </span>
                            </span>
                            <span class="font-medium">
                                Death Certificates
                                <p class="font-normal text-xs text-[#706f6c] dark:text-[#A1A09A]">For legal and administrative purposes</p>
                            </span>
                        </li>
                    </ul>
                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            <a
                                href="#"
                                class="inline-block rounded-sm border border-emerald-700 bg-emerald-700 px-5 py-1.5 text-sm leading-normal text-white hover:bg-emerald-800 dark:border-emerald-600 dark:bg-emerald-600 dark:text-white dark:hover:border-emerald-500 dark:hover:bg-emerald-500"
                            >
                                Apply Now
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="inline-block rounded-sm border border-emerald-700 px-5 py-1.5 text-sm leading-normal text-emerald-700 hover:bg-emerald-50 dark:border-emerald-600 dark:text-emerald-400 dark:hover:bg-emerald-900/30"
                            >
                                Learn More
                            </a>
                        </li>
                    </ul>
                </div>
                <div
                    class="relative -mb-px aspect-[335/376] w-full shrink-0 overflow-hidden rounded-t-lg bg-emerald-50 dark:bg-emerald-950 lg:-ml-px lg:mb-0 lg:aspect-auto lg:w-[438px] lg:rounded-r-lg lg:rounded-t-none"
                >
                    <div class="flex h-full items-center justify-center p-8">
                        <div class="text-center">
                            <div class="mb-6 flex justify-center">
                                <div class="rounded-full bg-emerald-100 p-4 dark:bg-emerald-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                                    </svg>
                                </div>
                            </div>
                            <h2 class="text-xl font-semibold text-emerald-700 dark:text-emerald-400 mb-2">Health & Community Services</h2>
                            <p class="text-emerald-600 dark:text-emerald-300 max-w-xs mx-auto">
                                Ensuring public health and safety through accessible documentation and permits
                            </p>
                        </div>
                    </div>
                    <div
                        class="absolute inset-0 rounded-t-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] lg:overflow-hidden lg:rounded-r-lg lg:rounded-t-none"
                    />
                </div>
            </main>
        </div>
        <div class="h-14.5 hidden lg:block"></div>
    </div>
</template>