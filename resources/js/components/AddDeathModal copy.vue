<template>
    <transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                @click="showModal = false"></div>

                <!-- Modal panel -->
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <form @submit.prevent="handleSubmit">
                        <div class="sticky top-0 bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 z-10">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="h-6 w-6 text-blue-600">
                                        <path
                                            d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                        <polyline points="14 2 14 8 20 8" />
                                        <line x1="12" y1="18" x2="12" y2="12" />
                                        <line x1="9" y1="15" x2="15" y2="15" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-xl leading-6 font-semibold text-gray-900">
                                        New Death Certificate
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Enter all required information to create a new death certificate.
                                    </p>
                                </div>
                            </div>

                            <!-- Form Steps -->
                            <div class="mt-4 border-t border-b border-gray-200 py-3">
                                <div class="flex justify-between">
                                    <button type="button" @click="activeFormTab = 'personal'" :class="[
                                        'px-3 py-2 text-sm font-medium rounded-md flex items-center',
                                        activeFormTab === 'personal'
                                            ? 'bg-blue-50 text-blue-700'
                                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
                                    ]">
                                        <span
                                            class="flex items-center justify-center w-6 h-6 rounded-full mr-2 text-xs font-bold"
                                            :class="activeFormTab === 'personal' ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-700'">1</span>
                                        Personal
                                    </button>
                                    <button type="button" @click="activeFormTab = 'family'" :class="[
                                        'px-3 py-2 text-sm font-medium rounded-md flex items-center',
                                        activeFormTab === 'family'
                                            ? 'bg-blue-50 text-blue-700'
                                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
                                    ]">
                                        <span
                                            class="flex items-center justify-center w-6 h-6 rounded-full mr-2 text-xs font-bold"
                                            :class="activeFormTab === 'family' ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-700'">2</span>
                                        Family
                                    </button>
                                    <button type="button" @click="activeFormTab = 'death'" :class="[
                                        'px-3 py-2 text-sm font-medium rounded-md flex items-center',
                                        activeFormTab === 'death'
                                            ? 'bg-blue-50 text-blue-700'
                                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
                                    ]">
                                        <span
                                            class="flex items-center justify-center w-6 h-6 rounded-full mr-2 text-xs font-bold"
                                            :class="activeFormTab === 'death' ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-700'">3</span>
                                        Death
                                    </button>
                                    <button type="button" @click="activeFormTab = 'medical'" :class="[
                                        'px-3 py-2 text-sm font-medium rounded-md flex items-center',
                                        activeFormTab === 'medical'
                                            ? 'bg-blue-50 text-blue-700'
                                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
                                    ]">
                                        <span
                                            class="flex items-center justify-center w-6 h-6 rounded-full mr-2 text-xs font-bold"
                                            :class="activeFormTab === 'medical' ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-700'">4</span>
                                        Medical
                                    </button>
                                    <button type="button" @click="activeFormTab = 'admin'" :class="[
                                        'px-3 py-2 text-sm font-medium rounded-md flex items-center',
                                        activeFormTab === 'admin'
                                            ? 'bg-blue-50 text-blue-700'
                                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
                                    ]">
                                        <span
                                            class="flex items-center justify-center w-6 h-6 rounded-full mr-2 text-xs font-bold"
                                            :class="activeFormTab === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-700'">5</span>
                                        Admin
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="max-h-[80vh] overflow-y-auto px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <!-- Personal Information Tab -->
                            <div v-if="activeFormTab === 'personal'" class="animate-fadeIn">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="col-span-3">
                                        <h4 class="text-md font-medium text-gray-800 mb-3 pb-2 border-b">Personal
                                            Information</h4>
                                    </div>

                                    <!-- Personal Information Fields -->
                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            First Name
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="formData.firstName" placeholder="First Name"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Middle Name
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="formData.middleName" placeholder="Middle Name"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Last Name
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="formData.lastName" placeholder="Last Name"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Sex
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <select v-model="formData.sex"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required>
                                            <option value="" disabled selected>Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Birthdate
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="date" v-model="formData.birthdate"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Civil Status
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <select v-model="formData.civilStatus"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required>
                                            <option value="" disabled selected>Select Civil Status</option>
                                            <option value="single">SINGLE</option>
                                            <option value="married">MARRIED</option>
                                            <option value="widow">WIDOW</option>
                                            <option value="widower">WIDOWER</option>
                                            <option value="annulled">ANNULLED</option>
                                        </select>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Religion
                                        </label>
                                        <input type="text" v-model="formData.religion" placeholder="Religion"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Citizenship
                                        </label>
                                        <select v-model="citizenshipType"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="FILIPINO">FILIPINO</option>
                                            <option value="OTHERS">Others</option>
                                        </select>
                                        <input v-if="citizenshipType === 'OTHERS'" type="text"
                                            v-model="formData.citizenship" placeholder="Enter citizenship"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Occupation
                                        </label>
                                        <input type="text" v-model="formData.occupation" placeholder="Occupation"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="col-span-3 space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Remarks
                                        </label>
                                        <input type="text" v-model="formData.remarks" placeholder="remarks"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200 resize-none"></textarea>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Province
                                        </label>
                                        <input type="text" v-model="formData.province" placeholder="Province"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Municipality
                                        </label>
                                        <input type="text" v-model="formData.municipality" placeholder="Municipality"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="button" @click="activeFormTab = 'family'"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        Next: Family Information
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Family Information Tab -->
                            <div v-if="activeFormTab === 'family'" class="animate-fadeIn">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="col-span-3">
                                        <h4 class="text-md font-medium text-gray-800 mb-3 pb-2 border-b">Family
                                            Information</h4>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Father's Name
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="formData.nameOfFather"
                                            placeholder="Father's full name"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Mother's Name
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="formData.nameOfMother"
                                            placeholder="Mother's full name"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="col-span-3">
                                        <h4 class="text-md font-medium text-gray-800 mb-3 pb-2 border-b mt-6">Informant
                                            Information</h4>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Informant's Name
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="formData.informantName"
                                            placeholder="Informant's name"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Informant's Address
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="formData.informantAddress"
                                            placeholder="Informant's address"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Relationship
                                        </label>
                                        <select v-model="relationshipType"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="MOTHER">MOTHER</option>
                                            <option value="FATHER">FATHER</option>
                                            <option value="WIFE">WIFE</option>
                                            <option value="SPOUSE">SPOUSE</option>
                                            <option value="GRANDMOTHER">GRANDMOTHER</option>
                                            <option value="GRANDFATHER">GRANDFATHER</option>
                                            <option value="SON">SON</option>
                                            <option value="DAUGHTER">DAUGHTER</option>
                                            <option value="SIBLING">SIBLING</option>
                                            <option value="OTHERS">OTHERS</option>
                                        </select>
                                        <input v-if="relationshipType === 'OTHERS'" type="text"
                                            v-model="formData.relationship" placeholder="Enter relationship"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between">
                                    <button type="button" @click="activeFormTab = 'personal'"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Previous: Personal
                                    </button>
                                    <button type="button" @click="activeFormTab = 'death'"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        Next: Death Information
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Death Information Tab -->
                            <div v-if="activeFormTab === 'death'" class="animate-fadeIn">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="col-span-3">
                                        <h4 class="text-md font-medium text-gray-800 mb-3 pb-2 border-b">Death
                                            Information</h4>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Date of Death
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="date" v-model="formData.dateOfDeath"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Place of Death
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="formData.placeOfDeath"
                                            placeholder="Hospital, Home, etc."
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                  

                                

                                    <div class="col-span-3">
                                        <h4 class="text-md font-medium text-gray-800 mb-3 pb-2 border-b mt-6">Cause of
                                            Death</h4>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Cause of Death A
                                        </label>
                                        <select v-model="causeOfDeathAType"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="CARDIO-RESPIRATORY ARREST">CARDIO-RESPIRATORY ARREST</option>
                                            <option value="OTHERS">Others</option>
                                        </select>
                                        <input v-if="causeOfDeathAType === 'OTHERS'" type="text"
                                            v-model="formData.causeOfDeathA" placeholder="Enter cause of death A"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Cause of Death B
                                        </label>
                                        <input type="text" v-model="formData.causeOfDeathB"
                                            placeholder="Antecedent cause"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Cause of Death C
                                        </label>
                                        <input type="text" v-model="formData.causeOfDeathC"
                                            placeholder="Underlying cause"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between">
                                    <button type="button" @click="activeFormTab = 'family'"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Previous: Family
                                    </button>
                                    <button type="button" @click="activeFormTab = 'medical'"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        Next: Medical Information
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Medical Information Tab -->
                            <div v-if="activeFormTab === 'medical'" class="animate-fadeIn">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="col-span-3">
                                        <h4 class="text-md font-medium text-gray-800 mb-3 pb-2 border-b">Medical
                                            Information</h4>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Doctor
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <select v-model="doctorType"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required>
                                            <option value="" disabled selected>Select a doctor</option>
                                            <option value="BENJAMIN Q. BENGCO III, M.D.">BENJAMIN Q. BENGCO III, M.D.
                                            </option>
                                            <option value="GLADYS LOURDES B. BENGCO, M.D.">GLADYS LOURDES B. BENGCO,
                                                M.D.</option>
                                            <option value="KRISTINE JOY MENDOZA-RIVERA, M.D.">KRISTINE JOY
                                                MENDOZA-RIVERA, M.D.</option>
                                            <option value="DOLORES C. CUNANAN, M.D.">DOLORES C. CUNANAN, M.D.</option>
                                            <option value="ORGIE I. FELICIANO, M.D.">ORGIE I. FELICIANO, M.D.</option>
                                            <option value="OTHERS">OTHERS</option>
                                        </select>
                                        <input v-if="doctorType === 'OTHERS'" type="text" v-model="formData.doctor"
                                            placeholder="Enter doctor's name"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Doctor Position
                                        </label>
                                        <input type="text" v-model="formData.doctorPosition"
                                            placeholder="Doctor's position"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Address
                                        </label>
                                        <input type="text" v-model="formData.address"
                                            placeholder="Medical facility address"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between">
                                    <button type="button" @click="activeFormTab = 'death'"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Previous: Death
                                    </button>
                                    <button type="button" @click="activeFormTab = 'admin'"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        Next: Administrative
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Administrative Information Tab -->
                            <div v-if="activeFormTab === 'admin'" class="animate-fadeIn">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="col-span-3">
                                        <h4 class="text-md font-medium text-gray-800 mb-3 pb-2 border-b">Administrative
                                            Information</h4>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Reviewed By
                                        </label>
                                        <input type="text" v-model="formData.reviewedBy" placeholder="Name of reviewer"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Reviewer Position
                                        </label>
                                        <input type="text" v-model="formData.reviewedPosition"
                                            placeholder="Position of reviewer"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Prepared By
                                        </label>
                                        <input type="text" v-model="formData.preparedByName"
                                            placeholder="Name of preparer"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Preparer Position
                                        </label>
                                        <input type="text" v-model="formData.preparedByPosition"
                                            placeholder="Position of preparer"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="col-span-3 space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Remarks
                                        </label>
                                        <textarea v-model="formData.remarks" placeholder="Additional remarks or notes"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200 resize-none"></textarea>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between">
                                    <button type="button" @click="activeFormTab = 'medical'"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Previous: Medical
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="mr-2 h-4 w-4">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                    <polyline points="17 21 17 13 7 13 7 21" />
                                    <polyline points="7 3 7 8 15 8" />
                                </svg>
                                Save Certificate
                            </button>
                            <button type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                                @click="closeModal">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { defineProps, defineEmits } from "vue";

defineProps({ show: Boolean });
const emit = defineEmits(["close"]);

const activeFormTab = ref('personal');
const citizenshipType = ref('FILIPINO');
const relationshipType = ref('MOTHER');
const causeOfDeathAType = ref('CARDIO-RESPIRATORY ARREST');
const doctorType = ref('');


const form = useForm({
    province: '',
    municipality: '',
    first_name: '',
    middle_name: '',
    last_name: '',
    sex: '',
    date_of_death: '',
    birthdate: '',
    age: '',
    gender: '',
    place_of_death: '',
    civil_status: '',
    religion: '',
    citizenship: '',
    residence: '',
    occupation: '',
    name_of_father: '',
    name_of_mother: '',
    cause_of_death_a: '',
    cause_of_death_b: '',
    cause_of_death_c: '',
    doctor: '',
    doctor_position: '',
    address: '',
    date: '',
    reviewed_position: '',
    reviewed_by: '',
    informant_name: '',
    relationship: '',
    informant_address: '',
    prepared_by_name: '',
    prepared_by_position: '',
    remarks: '',
});

const submit = () => {
    console.log("Submitting form:", JSON.stringify(form)); // Debugging Output

    form.post(route("death.store"), {
        onSuccess: () => {
            emit("close");
            window.location.reload();
        },
    });
};


const closeModal = () => {
    emit("close");
};

</script>