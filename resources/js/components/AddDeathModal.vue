<template>
    <transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

                <!-- Modal panel -->
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <form @submit.prevent="submit">
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
                                        {{ props.isEditing ? 'Edit Death Certificate' : 'New Death Certificate' }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ props.isEditing ? 'Update the information for this death certificate.' : 'Enter all required information to create a new death certificate.' }}
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

                                    <button type="button" @click="activeFormTab = 'medical'" :class="[
                                        'px-3 py-2 text-sm font-medium rounded-md flex items-center',
                                        activeFormTab === 'medical'
                                            ? 'bg-blue-50 text-blue-700'
                                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
                                    ]">
                                        <span
                                            class="flex items-center justify-center w-6 h-6 rounded-full mr-2 text-xs font-bold"
                                            :class="activeFormTab === 'medical' ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-700'">3</span>
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
                                            :class="activeFormTab === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-700'">4</span>
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
                                        <input type="text" v-model="form.first_name" placeholder="First Name"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Middle Name
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="form.middle_name" placeholder="Middle Name"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Last Name
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="form.last_name" placeholder="Last Name"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Gender
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <select v-model="form.sex"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required>
                                            <option value="" disabled selected>Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Date of Death
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="date" v-model="form.date_of_death"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Date of Birth
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="datetime-local" v-model="form.birthdate"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Place of Death
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="form.place_of_death"
                                            placeholder="Hospital, Home, etc."
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Civil Status
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <select v-model="form.civil_status"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required>
                                            <option value="" disabled selected>Select Civil Status</option>
                                            <option value="single">SINGLE</option>
                                            <option value="married">MARRIED</option>
                                            <option value="widow">WIDOW</option>
                                            <option value="widower">WIDOWER</option>
                                            <option value="annulled">ANNULLed</option>
                                        </select>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Religion
                                        </label>
                                        <input type="text" v-model="form.religion" placeholder="Religion"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Citizenship
                                        </label>
                                        <select v-model="form.citizenship"  @change="handleOthers"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="" disabled selected>Select Citizenship</option>
                                            <option value="FILIPINO">FILIPINO</option>
                                            <option value="OTHERS">Others</option>
                                        </select>
                                        <input v-if="isOthersSelectedCitizenship" type="text" v-model="form.citizenship"
                                            :placeholder="form.citizenship === 'OTHERS' ? 'Enter Citizenship' : ''"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Residence
                                        </label>
                                        <input type="text" v-model="form.residence"
                                            placeholder="Medical facility address"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Occupation
                                        </label>
                                        <input type="text" v-model="form.occupation" placeholder="Occupation"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Remarks
                                        </label>
                                        <select v-model="form.remarks" 
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="" disabled>Select a Remarks</option>
                                            <option value="WITH INSURANCE">WITH INSURANCE</option>
                                            <option value="WITHOUT INSURANCE">WITHOUT INSURANCE</option>
                                    
                                        </select>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Province
                                        </label>
                                        <input type="text" v-model="form.province" value="TARLAC" 
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Municipality
                                        </label>
                                        <input type="text" v-model="form.municipality" value="CONCEPCION" 
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
                                        <input type="text" v-model="form.name_of_father"
                                            placeholder="Father's full name"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Mother's Name
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="form.name_of_mother"
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
                                        <input type="text" v-model="form.informant_name" placeholder="Informant's name"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Informant's Address
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="text" v-model="form.informant_address"
                                            placeholder="Informant's address"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Relationship
                                        </label>
                                        <select v-model="form.relationship" @change="handleOthers"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="" disabled selected>Select Relationship</option>
                                            <option value="MOTHER">MOTHER</option>
                                            <option value="FATHER">FATHER</option>
                                            <option value="SPOUSE">SPOUSE</option>
                                            <option value="GRANDMOTHER">GRANDMOTHER</option>
                                            <option value="GRANDFATHER">GRANDFATHER</option>
                                            <option value="SON">SON</option>
                                            <option value="DAUGHTER">DAUGHTER</option>
                                            <option value="SIBLING">SIBLING</option>
                                            <option value="OTHERS">OTHERS</option>
                                        </select>
                                        <input v-if="isOthersSelectedRelationship" type="text" v-model="form.relationship"
                                            :placeholder="form.relationship === 'OTHERS' ? 'Enter Relationship' : ''"
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
                                        <h4 class="text-md font-medium text-gray-800 mb-3 pb-2 border-b mt-6">Cause of
                                            Death</h4>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Cause of Death A
                                        </label>
                                        <select v-model="form.cause_of_death_a" @change="handleOthers"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="" disabled selected>Select Cause of Death</option>
                                            <option value="CARDIO-RESPIRATORY ARREST">CARDIO-RESPIRATORY ARREST</option>
                                            <option value="OTHERS">Others</option>
                                        </select>
                                        <input v-if="isOthersSelectedCause" type="text"
                                            v-model="form.cause_of_death_a" placeholder="Enter cause of death A"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Cause of Death B
                                        </label>
                                        <input type="text" v-model="form.cause_of_death_b"
                                            placeholder="Antecedent cause"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Cause of Death C
                                        </label>
                                        <input type="text" v-model="form.cause_of_death_c"
                                            placeholder="Underlying cause"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Cause of Death D
                                        </label>
                                        <input type="text" v-model="form.cause_of_death_d"
                                            placeholder="Underlying cause"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>
                                    <div class="col-span-3">
                                        <h4 class="text-md font-medium text-gray-800 mb-3 pb-2 border-b">Medical
                                            Information</h4>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Doctor
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>

                                        <select v-model="form.doctor" @change="handleOthers"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="" disabled>Select a doctor</option>
                                            <option value="BENJAMIN Q. BENGCO III, M.D.">BENJAMIN Q. BENGCO III, M.D.</option>
                                            <option value="GLADYS LOURDES B. BENGCO, M.D.">GLADYS LOURDES B. BENGCO, M.D.</option>
                                            <option value="KRISTINE JOY MENDOZA-RIVERA, M.D.">KRISTINE JOY MENDOZA-RIVERA, M.D.</option>
                                            <option value="DOLORES C. CUNANAN, M.D.">DOLORES C. CUNANAN, M.D.</option>
                                            <option value="ORGIE I. FELICIANO, M.D.">ORGIE I. FELICIANO, M.D.</option>
                                            <option value="OTHERS">OTHERS</option>
                                        </select>

                                        <!-- Input field appears if 'OTHERS' is selected -->
                                        <input v-if="isOthersSelectedDoctor" type="text" v-model="form.doctor"
                                            :placeholder="form.doctor === 'OTHERS' ? 'Enter doctor\'s name' : ''"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Doctor Position
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>

                                        <select v-model="form.doctor_position" @change="handleOthers"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="" disabled>Select a doctor position</option>
                                            <option value="MUNICIPAL HEALTH OFFICER">MUNICIPAL HEALTH OFFICER</option>
                                            <option value="RURAL HEALTH PHYSICIAN">RURAL HEALTH PHYSICIAN</option>
                                            <option value="OTHERS">OTHERS</option>
                                        </select>

                                        <!-- Input field appears if 'OTHERS' is selected -->
                                        <input v-if="isOthersSelectedDoctorPosition" type="text" v-model="form.doctor_position"
                                            :placeholder="form.doctor === 'OTHERS' ? 'Enter doctor\'s Position' : ''"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Address
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>

                                        <select v-model="form.address" @change="handleOthers"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="" disabled>Select a Address</option>
                                            <option value="SAN NICOLAS POBLACION, CONCEPCION, TARLAC">SAN NICOLAS POBLACION, CONCEPCION, TARLAC</option>
                                            <option value="BALUTO, CONCEPCION, TARLAC">BALUTO, CONCEPCION, TARLAC</option>
                                            <option value="STA. CRUZ, CONCEPCION, TARLAC">STA. CRUZ, CONCEPCION, TARLAC</option>
                                            <option value="TINANG, CONCEPCION, TARLAC">TINANG, CONCEPCION, TARLAC</option>
                                            <option value="OTHERS">OTHERS</option>
                                        </select>

                                        <!-- Input field appears if 'OTHERS' is selected -->
                                        <input v-if="isOthersSelectedAddress" type="text" v-model="form.address"
                                            :placeholder="form.address === 'OTHERS' ? 'Enter doctor\'s name' : ''"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Reviewed By
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>

                                        <select v-model="form.reviewed_by" @change="handleOthers"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="" disabled>Select a doctor</option>
                                            <option value="BENJAMIN Q. BENGCO III, M.D.">BENJAMIN Q. BENGCO III, M.D.</option>
                                            <option value="GLADYS LOURDES B. BENGCO, M.D.">GLADYS LOURDES B. BENGCO, M.D.</option>
                                            <option value="KRISTINE JOY MENDOZA-RIVERA, M.D.">KRISTINE JOY MENDOZA-RIVERA, M.D.</option>
                                            <option value="DOLORES C. CUNANAN, M.D.">DOLORES C. CUNANAN, M.D.</option>
                                            <option value="ORGIE I. FELICIANO, M.D.">ORGIE I. FELICIANO, M.D.</option>
                                            <option value="OTHERS">OTHERS</option>
                                        </select>

                                        <!-- Input field appears if 'OTHERS' is selected -->
                                        <input v-if="isOthersSelectedReviewedBy" type="text" v-model="form.reviewed_by"
                                            :placeholder="form.reviewed_by === 'OTHERS' ? 'Enter doctor\'s name' : ''"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Reviewed Position
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>

                                        <select v-model="form.reviewed_position" @change="handleOthers"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="" disabled>Select a doctor position</option>
                                            <option value="MUNICIPAL HEALTH OFFICER">MUNICIPAL HEALTH OFFICER</option>
                                            <option value="RURAL HEALTH PHYSICIAN">RURAL HEALTH PHYSICIAN</option>
                                            <option value="OTHERS">OTHERS</option>
                                        </select>

                                        <!-- Input field appears if 'OTHERS' is selected -->
                                        <input v-if="isOthersSelectedReviewedPosition" type="text" v-model="form.reviewed_position"
                                            :placeholder="form.reviewed_position === 'OTHERS' ? 'Enter doctor\'s Position' : ''"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
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
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Prepared By
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>

                                        <select v-model="form.prepared_by_name" @change="handleOthers"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="" disabled>Select a Name</option>
                                            <option value="AARON JAY C. GONZALES">AARON JAY C. GONZALES</option>
                                            <option value="GERALD B. CASTRO">GERALD B. CASTRO</option>
                                            <option value="HEIDY D. PADERE">HEIDY D. PADERE</option>
                                            <option value="OTHERS">OTHERS</option>
                                        </select>

                                        <!-- Input field appears if 'OTHERS' is selected -->
                                        <input v-if="isOthersSelectedPreparedBy" type="text" v-model="form.prepared_by_name"
                                            :placeholder="form.prepared_by_name === 'OTHERS' ? 'Enter doctor\'s Position' : ''"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Preparer Position
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>

                                        <select v-model="form.prepared_by_position" @change="handleOthers"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                                            <option value="" disabled>Select a Position</option>
                                            <option value="SANITARY INSPECTOR">SANITARY INSPECTOR</option>
                                            <option value="ADMINISTRATIVE AIDE III">ADMINISTRATIVE AIDE III</option>
                                            <option value="OTHERS">OTHERS</option>
                                        </select>

                                        <!-- Input field appears if 'OTHERS' is selected -->
                                        <input v-if="isOthersSelectedPrreparedPosition" type="text" v-model="form.prepared_by_position"
                                            :placeholder="form.prepared_by_position === 'OTHERS' ? 'Enter doctor\'s Position' : ''"
                                            class="mt-2 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200" />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-gray-700 flex items-center">
                                            Date Today
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input type="date" v-model="form.date"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors duration-200"
                                            required />
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
                                {{ props.isEditing ? 'Update Certificate' : 'Save Certificate' }}
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
import { ref, watch, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({ 
  show: Boolean,
  deathRecord: {
    type: Object,
    default: () => ({})
  },
  isEditing: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(["close"]);

const activeFormTab = ref('personal');
const citizenship = ref('FILIPINO');
const relationship = ref('MOTHER');
const cause_of_death_a = ref('CARDIO-RESPIRATORY ARREST');
const doctor = ref('');

// Track whether "OTHERS" is selected
const isOthersSelectedDoctor = ref(false);
const isOthersSelectedRelationship = ref(false);
const isOthersSelectedCause = ref(false);
const isOthersSelectedCitizenship = ref(false);
const isOthersSelectedDoctorPosition = ref(false);
const isOthersSelectedAddress = ref(false);
const isOthersSelectedReviewedBy = ref(false);
const isOthersSelectedReviewedPosition = ref(false);
const isOthersSelectedPreparedBy = ref(false);
const isOthersSelectedPrreparedPosition = ref(false);

// Initialize the form
const form = useForm({
    province: 'TARLAC',
    municipality: 'CONCEPCION',
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
    cause_of_death_d: '',
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

// Function to format date to YYYY-MM-DD for input fields
const formatDateForInput = (dateString, isDateTimeLocal = false) => {
  if (!dateString) return '';
  
  // Try to parse the date
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return '';
  
  if (isDateTimeLocal) {
    // Format as YYYY-MM-DDThh:mm for datetime-local inputs
    // Pad with zeros to ensure proper format
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${year}-${month}-${day}T${hours}:${minutes}`;
  } else {
    // Format as YYYY-MM-DD for date inputs
    return date.toISOString().split('T')[0];
  }
};

// Function to check if a value is not in the predefined options
const isCustomValue = (value, options) => {
  if (!value) return false;
  return !options.includes(value);
};

// Function to populate form with existing data
const populateForm = () => {
  if (!props.deathRecord) return;
  
  // Populate form fields
  Object.keys(props.deathRecord).forEach(key => {
    if (form[key] !== undefined) {
      // Format dates for input fields
      if (key === 'birthdate') {
        form[key] = formatDateForInput(props.deathRecord[key], true); // Use datetime-local format
      } else if (['date_of_death', 'date'].includes(key)) {
        form[key] = formatDateForInput(props.deathRecord[key]); // Use date format
      } else {
        form[key] = props.deathRecord[key];
      }
    }
  });
  
  // Check for custom values in dropdown fields
  const doctorOptions = [
    "BENJAMIN Q. BENGCO III, M.D.", 
    "GLADYS LOURDES B. BENGCO, M.D.", 
    "KRISTINE JOY MENDOZA-RIVERA, M.D.", 
    "DOLORES C. CUNANAN, M.D.", 
    "ORGIE I. FELICIANO, M.D."
  ];
  
  const doctorPositionOptions = [
    "MUNICIPAL HEALTH OFFICER", 
    "RURAL HEALTH PHYSICIAN"
  ];
  
  const addressOptions = [
    "SAN NICOLAS POBLACION, CONCEPCION, TARLAC", 
    "BALUTO, CONCEPCION, TARLAC", 
    "STA. CRUZ, CONCEPCION, TARLAC", 
    "TINANG, CONCEPCION, TARLAC"
  ];
  
  const preparedByOptions = [
    "AARON JAY C. GONZALES", 
    "GERALD B. CASTRO", 
    "HEIDY D. PADERE"
  ];
  
  const preparedPositionOptions = [
    "SANITARY INSPECTOR", 
    "ADMINISTRATIVE AIDE III"
  ];
  
  const relationshipOptions = [
    "MOTHER", 
    "FATHER", 
    "SPOUSE", 
    "GRANDMOTHER", 
    "GRANDFATHER", 
    "SON", 
    "DAUGHTER", 
    "SIBLING"
  ];
  
  const causeOfDeathOptions = [
    "CARDIO-RESPIRATORY ARREST"
  ];
  
  const citizenshipOptions = [
    "FILIPINO"
  ];
  
  // Set "OTHERS" for custom values
  if (isCustomValue(form.doctor, doctorOptions)) {
    isOthersSelectedDoctor.value = true;
  }
  
  if (isCustomValue(form.doctor_position, doctorPositionOptions)) {
    isOthersSelectedDoctorPosition.value = true;
  }
  
  if (isCustomValue(form.address, addressOptions)) {
    isOthersSelectedAddress.value = true;
  }
  
  if (isCustomValue(form.reviewed_by, doctorOptions)) {
    isOthersSelectedReviewedBy.value = true;
  }
  
  if (isCustomValue(form.reviewed_position, doctorPositionOptions)) {
    isOthersSelectedReviewedPosition.value = true;
  }
  
  if (isCustomValue(form.prepared_by_name, preparedByOptions)) {
    isOthersSelectedPreparedBy.value = true;
  }
  
  if (isCustomValue(form.prepared_by_position, preparedPositionOptions)) {
    isOthersSelectedPrreparedPosition.value = true;
  }
  
  if (isCustomValue(form.relationship, relationshipOptions)) {
    isOthersSelectedRelationship.value = true;
  }
  
  if (isCustomValue(form.cause_of_death_a, causeOfDeathOptions)) {
    isOthersSelectedCause.value = true;
  }
  
  if (isCustomValue(form.citizenship, citizenshipOptions)) {
    isOthersSelectedCitizenship.value = true;
  }
};

onMounted(() => {
  if (props.isEditing && props.deathRecord) {
    populateForm();
  }
});

watch(() => props.deathRecord, (newValue) => {
  if (props.isEditing && newValue) {
    populateForm();
  }
}, { deep: true });

watch(() => props.isEditing, (newValue) => {
  if (newValue && props.deathRecord) {
    populateForm();
  }
}, { immediate: true });

// Handle doctor change
const handleOthers = () => {
    // Update the flag based on whether "OTHERS" is selected
    isOthersSelectedDoctor.value = form.doctor === 'OTHERS';
    isOthersSelectedRelationship.value = form.relationship === 'OTHERS';
    isOthersSelectedCause.value = form.cause_of_death_a === 'OTHERS';
    isOthersSelectedCitizenship.value = form.citizenship === 'OTHERS';
    isOthersSelectedDoctorPosition.value = form.doctor_position === 'OTHERS';
    isOthersSelectedAddress.value = form.address === 'OTHERS';
    isOthersSelectedReviewedBy.value = form.reviewed_by === 'OTHERS';
    isOthersSelectedReviewedPosition.value = form.reviewed_position === 'OTHERS';
    isOthersSelectedPreparedBy.value = form.prepared_by_name === 'OTHERS';
    isOthersSelectedPrreparedPosition.value = form.prepared_by_position === 'OTHERS';
};

// Submit function
const submit = () => {
  if (props.isEditing) {
    // Update existing record
    form.put(route("death.update", props.deathRecord.id), {
      onSuccess: () => {
        emit("close");
        window.location.reload();
      },
    });
  } else {
    // Create new record
    form.post(route("death.store"), {
      onSuccess: () => {
        emit("close");
        window.location.reload();
      },
    });
  }
};

// Close modal function
const closeModal = () => {
  emit("close");
};
</script>


<!-- <script setup>
import { ref, watch, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({ 
  show: Boolean,
  deathRecord: {
    type: Object,
    default: () => ({})
  },
  isEditing: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(["close"]);

const activeFormTab = ref('personal');
const citizenship = ref('FILIPINO');
const relationship = ref('MOTHER');
const cause_of_death_a = ref('CARDIO-RESPIRATORY ARREST');
const doctor = ref('');

// Track whether "OTHERS" is selected
const isOthersSelectedDoctor = ref(false);
const isOthersSelectedRelationship = ref(false);
const isOthersSelectedCause = ref(false);
const isOthersSelectedCitizenship = ref(false);
const isOthersSelectedDoctorPosition = ref(false);
const isOthersSelectedAddress = ref(false);
const isOthersSelectedReviewedBy = ref(false);
const isOthersSelectedReviewedPosition = ref(false);
const isOthersSelectedPreparedBy = ref(false);
const isOthersSelectedPrreparedPosition = ref(false);

// Initialize the form
const form = useForm({
    province: 'TARLAC',
    municipality: 'CONCEPCION',
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

// Function to format date to YYYY-MM-DD for input fields
const formatDateForInput = (dateString) => {
  if (!dateString) return '';
  
  // If it's already in YYYY-MM-DD format, return it
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
    return dateString;
  }
  
  // Try to parse the date
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return '';
  
  // Format as YYYY-MM-DD
  return date.toISOString().split('T')[0];
};

// Function to check if a value is not in the predefined options
const isCustomValue = (value, options) => {
  if (!value) return false;
  return !options.includes(value);
};

// Function to populate form with existing data
const populateForm = () => {
  if (!props.deathRecord) return;
  
  // Populate form fields
  Object.keys(props.deathRecord).forEach(key => {
    if (form[key] !== undefined) {
      // Format dates for input fields
      if (['date_of_death', 'birthdate', 'date'].includes(key)) {
        form[key] = formatDateForInput(props.deathRecord[key]);
      } else {
        form[key] = props.deathRecord[key];
      }
    }
  });
  
  // Check for custom values in dropdown fields
  const doctorOptions = [
    "BENJAMIN Q. BENGCO III, M.D.", 
    "GLADYS LOURDES B. BENGCO, M.D.", 
    "KRISTINE JOY MENDOZA-RIVERA, M.D.", 
    "DOLORES C. CUNANAN, M.D.", 
    "ORGIE I. FELICIANO, M.D."
  ];
  
  const doctorPositionOptions = [
    "MUNICIPAL HEALTH OFFICER", 
    "RURAL HEALTH PHYSICIAN"
  ];
  
  const addressOptions = [
    "SAN NICOLAS POBLACION, CONCEPCION, TARLAC", 
    "BALUTO, CONCEPCION, TARLAC", 
    "STA. CRUZ, CONCEPCION, TARLAC", 
    "TINANG, CONCEPCION, TARLAC"
  ];
  
  const preparedByOptions = [
    "AARON JAY C. GONZALES", 
    "GERALD B. CASTRO", 
    "HEIDY D. PADERE"
  ];
  
  const preparedPositionOptions = [
    "SANITARY INSPECTOR", 
    "ADMINISTRATIVE AIDE III"
  ];
  
  const relationshipOptions = [
    "MOTHER", 
    "FATHER", 
    "SPOUSE", 
    "GRANDMOTHER", 
    "GRANDFATHER", 
    "SON", 
    "DAUGHTER", 
    "SIBLING"
  ];
  
  const causeOfDeathOptions = [
    "CARDIO-RESPIRATORY ARREST"
  ];
  
  const citizenshipOptions = [
    "FILIPINO"
  ];
  
  // Set "OTHERS" for custom values
  if (isCustomValue(form.doctor, doctorOptions)) {
    isOthersSelectedDoctor.value = true;
  }
  
  if (isCustomValue(form.doctor_position, doctorPositionOptions)) {
    isOthersSelectedDoctorPosition.value = true;
  }
  
  if (isCustomValue(form.address, addressOptions)) {
    isOthersSelectedAddress.value = true;
  }
  
  if (isCustomValue(form.reviewed_by, doctorOptions)) {
    isOthersSelectedReviewedBy.value = true;
  }
  
  if (isCustomValue(form.reviewed_position, doctorPositionOptions)) {
    isOthersSelectedReviewedPosition.value = true;
  }
  
  if (isCustomValue(form.prepared_by_name, preparedByOptions)) {
    isOthersSelectedPreparedBy.value = true;
  }
  
  if (isCustomValue(form.prepared_by_position, preparedPositionOptions)) {
    isOthersSelectedPrreparedPosition.value = true;
  }
  
  if (isCustomValue(form.relationship, relationshipOptions)) {
    isOthersSelectedRelationship.value = true;
  }
  
  if (isCustomValue(form.cause_of_death_a, causeOfDeathOptions)) {
    isOthersSelectedCause.value = true;
  }
  
  if (isCustomValue(form.citizenship, citizenshipOptions)) {
    isOthersSelectedCitizenship.value = true;
  }
};

onMounted(() => {
  if (props.isEditing && props.deathRecord) {
    populateForm();
  }
});

watch(() => props.deathRecord, (newValue) => {
  if (props.isEditing && newValue) {
    populateForm();
  }
}, { deep: true });

watch(() => props.isEditing, (newValue) => {
  if (newValue && props.deathRecord) {
    populateForm();
  }
}, { immediate: true });

// Handle doctor change
const handleOthers = () => {
    // Update the flag based on whether "OTHERS" is selected
    isOthersSelectedDoctor.value = form.doctor === 'OTHERS';
    isOthersSelectedRelationship.value = form.relationship === 'OTHERS';
    isOthersSelectedCause.value = form.cause_of_death_a === 'OTHERS';
    isOthersSelectedCitizenship.value = form.citizenship === 'OTHERS';
    isOthersSelectedDoctorPosition.value = form.doctor_position === 'OTHERS';
    isOthersSelectedAddress.value = form.address === 'OTHERS';
    isOthersSelectedReviewedBy.value = form.reviewed_by === 'OTHERS';
    isOthersSelectedReviewedPosition.value = form.reviewed_position === 'OTHERS';
    isOthersSelectedPreparedBy.value = form.prepared_by_name === 'OTHERS';
    isOthersSelectedPrreparedPosition.value = form.prepared_by_position === 'OTHERS';
};

// Submit function
const submit = () => {
  if (props.isEditing) {
    // Update existing record
    form.put(route("death.update", props.deathRecord.id), {
      onSuccess: () => {
        emit("close");
        window.location.reload();
      },
    });
  } else {
    // Create new record
    form.post(route("death.store"), {
      onSuccess: () => {
        emit("close");
        window.location.reload();
      },
    });
  }
};

// Close modal function
const closeModal = () => {
  emit("close");
};
</script> -->

