<template>
  <Head title="Death Certificate" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <FlashMessage />
    <div class="flex min-h-screen w-full flex-col bg-gray-50">
      <!-- Main Content -->
      <main class="flex flex-1 flex-col gap-4 p-2 md:gap-8 md:p-8">
        <!-- Tab Content -->
        <div class="p-4">
          <div v-if="activeTab === 'all'">
            <div class="rounded-lg">
              <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-medium leading-6 text-gray-900">All Death Certificates</h3>
                  <p class="mt-1 text-sm text-gray-500">Manage and view all death certificates in the system.</p>
                </div>

                <button @click="openModal"
                  class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="mr-2 h-4 w-4">
                    <path d="M5 12h14" />
                    <path d="M12 5v14" />
                  </svg>
                  New Certificate
                </button>
              </div>
              <div class="px-4 py-5 sm:p-6">
                <!-- Certificate Table -->
                <div class="w-full">
                  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-4 gap-4">
                    <div class="relative">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                      </svg>
                      <form @submit.prevent="performSearch">
                        <input type="text" placeholder="Search by name or cause..." v-model="search"
                          @input="debounceSearch"
                          class="w-full sm:max-w-sm rounded-md border border-gray-300 pl-10 pr-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors duration-200" />
                      </form>
                    </div>
                    <div class="text-sm text-gray-500">
                      Total: <span class="font-medium">{{ deaths?.total || 0 }}</span> certificates
                    </div>
                  </div>

                  <div v-if="!deaths?.data || deaths.data.length === 0"
                    class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="rounded-full bg-gray-100 p-3 mb-4">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="h-6 w-6 text-gray-500">
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                        <polyline points="14 2 14 8 20 8" />
                        <path d="M12 18v-6" />
                        <path d="M8 15h8" />
                      </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">
                      {{ search ? 'No certificates found' : 'No certificates available' }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                      {{ search ? 'Try adjusting your search to find what you\'re looking for.' : 'Get started by creating your first death certificate.' }}
                    </p>
                    <button v-if="search" @click="clearSearch"
                      class="mt-4 inline-flex items-center justify-center rounded-md bg-blue-50 px-3 py-2 text-sm font-medium text-blue-600 transition-colors duration-200 hover:bg-blue-100">
                      Clear search
                    </button>
                  </div>

                  <div v-else class="rounded-md border overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                      <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                          <tr>
                            <th scope="col"
                              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              <div class="flex items-center cursor-pointer">
                                Name
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round" class="ml-1 h-4 w-4">
                                  <path d="m7 15 5 5 5-5" />
                                  <path d="m7 9 5-5 5 5" />
                                </svg>
                              </div>
                            </th>
                            <th scope="col"
                              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              <div class="flex items-center cursor-pointer">
                                Date of Death
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round" class="ml-1 h-4 w-4">
                                  <path d="m7 15 5 5 5-5" />
                                  <path d="m7 9 5-5 5 5" />
                                </svg>
                              </div>
                            </th>
                            <th scope="col"
                              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Age
                            </th>
                            <th scope="col"
                              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Place of Death
                            </th>
                            <th scope="col"
                              class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Actions
                            </th>
                          </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" style="text-transform: uppercase;">
                          <tr v-for="(death, index) in deaths.data" :key="death.id"
                            :class="[index % 2 === 0 ? 'bg-white' : 'bg-gray-50', 'hover:bg-blue-50 transition-colors duration-150']">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800">
                              {{ death.first_name }} {{ death.middle_name }} {{ death.last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                              {{ formatDate(death.date_of_death) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                              {{ calculateAge(death.birthdate, death.date_of_death) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                              {{ death.place_of_death }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                              <div class="relative inline-block text-left" :ref="`dropdown-${death.id}`">
                                <button @click.stop="toggleDropdown(death.id)"
                                  class="h-8 w-8 p-0 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-150">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="h-4 w-4 mx-auto">
                                    <circle cx="12" cy="12" r="1" />
                                    <circle cx="12" cy="5" r="1" />
                                    <circle cx="12" cy="19" r="1" />
                                  </svg>
                                </button>
                                
                                <!-- Dropdown Menu with improved positioning and z-index -->
                                <transition
                                  enter-active-class="transition ease-out duration-100"
                                  enter-from-class="transform opacity-0 scale-95"
                                  enter-to-class="transform opacity-100 scale-100"
                                  leave-active-class="transition ease-in duration-75"
                                  leave-from-class="transform opacity-100 scale-100"
                                  leave-to-class="transform opacity-0 scale-95">
                                  <div v-if="activeDropdown === death.id" @click.stop
                                    class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                    style="z-index: 1000;">
                                    <div class="py-1">
                                      <div class="px-4 py-2 text-sm text-gray-700 font-medium border-b border-gray-100">Actions</div>
                                      <button @click="viewDetails(death)"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center transition-colors duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                          viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"
                                          class="mr-2 h-4 w-4 text-gray-500">
                                          <path
                                            d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                          <polyline points="14 2 14 8 20 8" />
                                          <line x1="16" y1="13" x2="8" y2="13" />
                                          <line x1="16" y1="17" x2="8" y2="17" />
                                          <line x1="10" y1="9" x2="8" y2="9" />
                                        </svg>
                                        View Details
                                      </button>
                                      <button @click="editCertificate(death)"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center transition-colors duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                          viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"
                                          class="mr-2 h-4 w-4 text-gray-500">
                                          <path d="M12 20h9" />
                                          <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                                        </svg>
                                        Edit Certificate
                                      </button>
                                      <button @click="printCertificate(death.id)"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center transition-colors duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                          viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"
                                          class="mr-2 h-4 w-4 text-gray-500">
                                          <polyline points="6 9 6 2 18 2 18 9" />
                                          <path
                                            d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                          <rect x="6" y="14" width="12" height="8" />
                                        </svg>
                                        Print Certificate
                                      </button>
                                      <div class="border-t border-gray-100"></div>
                                      <button @click="confirmDelete(death.id)"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center transition-colors duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                          viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"
                                          class="mr-2 h-4 w-4 text-red-500">
                                          <path d="M3 6h18" />
                                          <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                          <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                          <line x1="10" y1="11" x2="10" y2="17" />
                                          <line x1="14" y1="11" x2="14" y2="17" />
                                        </svg>
                                        Delete
                                      </button>
                                    </div>
                                  </div>
                                </transition>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                      <div class="flex-1 flex justify-between sm:hidden">
                        <button 
                          @click="goToPage(deaths.current_page - 1)" 
                          :disabled="!deaths.prev_page_url"
                          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                          Previous
                        </button>
                        <button 
                          @click="goToPage(deaths.current_page + 1)" 
                          :disabled="!deaths.next_page_url"
                          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                          Next
                        </button>
                      </div>
                      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                          <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ deaths.from || 0 }}</span>
                            to
                            <span class="font-medium">{{ deaths.to || 0 }}</span>
                            of
                            <span class="font-medium">{{ deaths.total || 0 }}</span>
                            results
                          </p>
                        </div>
                        <div>
                          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button 
                              @click="goToPage(deaths.current_page - 1)" 
                              :disabled="!deaths.prev_page_url"
                              class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                              <span class="sr-only">Previous</span>
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="h-5 w-5">
                                <path d="m15 18-6-6 6-6" />
                              </svg>
                            </button>
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                              {{ deaths.current_page }} of {{ deaths.last_page }}
                            </span>
                            <button 
                              @click="goToPage(deaths.current_page + 1)" 
                              :disabled="!deaths.next_page_url"
                              class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                              <span class="sr-only">Next</span>
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="h-5 w-5">
                                <path d="m9 18 6-6-6-6" />
                              </svg>
                            </button>
                          </nav>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>

      <!-- Certificate Details Modal -->
      <transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="detailsModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
          role="dialog" aria-modal="true">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="detailsModalOpen = false">
            </div>

            <!-- Modal panel -->
            <div
              class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
              <div class="sticky top-0 bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 z-10">
                <div class="sm:flex sm:items-start">
                  <div
                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="h-6 w-6 text-blue-600">
                      <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                      <polyline points="14 2 14 8 20 8" />
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                      Death Certificate #{{ selectedCertificate?.id }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                      Certificate details for {{ selectedCertificate?.first_name }} {{ selectedCertificate?.middle_name
                      }} {{ selectedCertificate?.last_name }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="max-h-[80vh] overflow-y-auto px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div v-for="(section, index) in certificateSections" :key="index" class="mb-8">
                  <div class="flex items-center mb-3">
                    <h3 class="text-lg font-medium text-gray-800">{{ section.title }}</h3>
                    <div class="ml-3 flex-grow h-px bg-gray-200"></div>
                  </div>
                  <div class="grid gap-4 sm:grid-cols-2">
                    <div v-for="(field, fieldIndex) in section.fields" :key="fieldIndex"
                      class="space-y-1 p-3 rounded-md bg-gray-50 hover:bg-gray-100 transition-colors duration-150">
                      <div class="text-sm font-medium text-gray-500">{{ field.label }}</div>
                      <div class="font-medium text-gray-800">{{ field.value || 'N/A' }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                <button type="button" @click="printCertificate(selectedCertificate.id)"
                  class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="mr-2 h-4 w-4">
                    <polyline points="6 9 6 2 18 2 18 9" />
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                    <rect x="6" y="14" width="12" height="8" />
                  </svg>
                  Print Certificate
                </button>

                <button type="button"
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                  @click="detailsModalOpen = false">
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <!-- Delete Confirmation Modal -->
      <transition name="modal">
        <div v-if="isDeleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
          role="dialog" aria-modal="true">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="isDeleteModalOpen = false">
            </div>
            <div
              class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
              <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div
                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-red-600">
                      <path d="M3 6h18" />
                      <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                      <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                      Confirm Deletion
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                      Are you sure you want to delete this death record? This action cannot be undone.
                    </p>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                <button type="button"
                  class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                  @click="deleteDeath">
                  Delete
                </button>
                <button type="button"
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                  @click="isDeleteModalOpen = false">
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <!-- New Certificate Modal -->
      <AddDeathModal v-if="showModal" :show="showModal" :death-record="selectedDeathRecord" :is-editing="isEditing"
        @close="closeModal" />
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import {
  FileTextIcon,
  CalendarIcon,
  AlertCircleIcon,
  UsersIcon
} from 'lucide-vue-next';
import { defineProps, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import AddDeathModal from "@/components/AddDeathModal.vue";
import FlashMessage from '@/components/FlashMessage.vue';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Death Certificate', href: '/death-certificate' },
];

// Props from the controller
const props = defineProps({
  deaths: Object,
  search: String
});

// State variables
const showModal = ref(false);
const selectedDeathRecord = ref({});
const isEditing = ref(false);
const selectedCertificate = ref(null);
const detailsModalOpen = ref(false);
const activeTab = ref('all');
const activeDropdown = ref(null);
const search = ref(props.search || '');
const deathToDeleteId = ref(null);
const isDeleteModalOpen = ref(false);
const searchTimeout = ref(null);

// Modal functions
const openModal = () => {
  selectedDeathRecord.value = {};
  isEditing.value = false;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  // Optional: Reset after animation completes
  setTimeout(() => {
    selectedDeathRecord.value = {};
    isEditing.value = false;
  }, 300);
};

// Search and pagination functions with debouncing
const debounceSearch = () => {
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  searchTimeout.value = setTimeout(() => {
    performSearch();
  }, 500); // 500ms delay
};

const performSearch = () => {
  router.get(route('death.index'), {
    search: search.value
  }, {
    preserveState: true,
    replace: true,
    only: ['deaths'] // Only reload the deaths data
  });
};

const clearSearch = () => {
  search.value = '';
  performSearch();
};

const goToPage = (page) => {
  if (page < 1 || (props.deaths?.last_page && page > props.deaths.last_page)) {
    return;
  }
  
  router.get(route('death.index'), {
    search: search.value,
    page: page
  }, {
    preserveState: true,
    replace: true,
    only: ['deaths']
  });
};

// Initialize data
onMounted(() => {
  // Add click outside listener to close dropdown
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
});

// Age calculation function
function calculateAge(birthdate, dateOfDeath) {
  if (!birthdate || !dateOfDeath) return 'N/A';
  
  const birth = new Date(birthdate);
  const death = new Date(dateOfDeath);

  let age = death.getFullYear() - birth.getFullYear();
  const hasBirthdayPassed =
    death.getMonth() > birth.getMonth() ||
    (death.getMonth() === birth.getMonth() && death.getDate() >= birth.getDate());

  if (!hasBirthdayPassed) {
    age--;
  }

  return age;
}

// Certificate sections for detail view
const certificateSections = computed(() => {
  if (!selectedCertificate.value) return [];

  return [
    {
      title: "Personal Information",
      fields: [
        { label: "Full Name", value: `${selectedCertificate.value.first_name} ${selectedCertificate.value.middle_name} ${selectedCertificate.value.last_name}` },
        { label: "Sex/Gender", value: selectedCertificate.value.sex },
        { label: "Date of Birth", value: formatDate(selectedCertificate.value.birthdate) },
        {
          label: "Age",
          value: calculateAge(selectedCertificate.value.birthdate, selectedCertificate.value.date_of_death)
        },
        { label: "Civil Status", value: selectedCertificate.value.civil_status },
        { label: "Religion", value: selectedCertificate.value.religion },
        { label: "Citizenship", value: selectedCertificate.value.citizenship },
        { label: "Residence", value: selectedCertificate.value.residence },
        { label: "Occupation", value: selectedCertificate.value.occupation },
      ],
    },
    {
      title: "Death Information",
      fields: [
        { label: "Date of Death", value: formatDate(selectedCertificate.value.date_of_death) },
        { label: "Place of Death", value: selectedCertificate.value.place_of_death },
        { label: "Province", value: selectedCertificate.value.province },
        { label: "Municipality", value: selectedCertificate.value.municipality },
      ],
    },
    {
      title: "Cause of Death",
      fields: [
        { label: "Immediate Cause", value: selectedCertificate.value.cause_of_death_a },
        { label: "Antecedent Cause", value: selectedCertificate.value.cause_of_death_b },
        { label: "Underlying Cause", value: selectedCertificate.value.cause_of_death_c },
        { label: "Underlying Cause other", value: selectedCertificate.value.cause_of_death_d },
      ],
    },
    {
      title: "Family Information",
      fields: [
        { label: "Father's Name", value: selectedCertificate.value.name_of_father },
        { label: "Mother's Name", value: selectedCertificate.value.name_of_mother },
      ],
    },
    {
      title: "Medical Certification",
      fields: [
        { label: "Attending Physician", value: selectedCertificate.value.doctor },
        { label: "Position", value: selectedCertificate.value.doctor_position },
        { label: "Address", value: selectedCertificate.value.address },
      ],
    },
    {
      title: "Informant Information",
      fields: [
        { label: "Informant Name", value: selectedCertificate.value.informant_name },
        { label: "Relationship", value: selectedCertificate.value.relationship },
        { label: "Address", value: selectedCertificate.value.informant_address },
      ],
    },
    {
      title: "Administrative Information",
      fields: [
        { label: "Prepared By", value: selectedCertificate.value.prepared_by_name },
        { label: "Position", value: selectedCertificate.value.prepared_by_position },
        { label: "Reviewed By", value: selectedCertificate.value.reviewed_by },
        { label: "Position", value: selectedCertificate.value.reviewed_position },
        { label: "Remarks", value: selectedCertificate.value.remarks },
        { label: "Date", value: formatDate(selectedCertificate.value.date) },
      ],
    },
  ];
});

// Helper methods
const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString();
};

const capitalize = (str) => {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1);
};

const toggleDropdown = (id) => {
  activeDropdown.value = activeDropdown.value === id ? null : id;
};

const handleClickOutside = (event) => {
  // Check if the click is outside any dropdown
  if (activeDropdown.value !== null && !event.target.closest('[class*="dropdown-"]')) {
    activeDropdown.value = null;
  }
};

const viewDetails = (certificate) => {
  selectedCertificate.value = certificate;
  detailsModalOpen.value = true;
  activeDropdown.value = null;
};

const editCertificate = (certificate) => {
  selectedDeathRecord.value = certificate;
  isEditing.value = true;
  showModal.value = true;
  activeDropdown.value = null;
};

const printCertificate = (deathId) => {
  activeDropdown.value = null;
  
  if (!window.Laravel?.routes?.death_generate_pdf) {
    console.error("Laravel routes not defined.");
    return;
  }

  const url = window.Laravel.routes.death_generate_pdf.replace('__ID__', deathId);
  window.open(url, '_blank');
};

// Function to open confirmation modal
const confirmDelete = (id) => {
  deathToDeleteId.value = id;
  isDeleteModalOpen.value = true;
  activeDropdown.value = null;
};

// Function to delete death record using Inertia
const deleteDeath = () => {
  if (!deathToDeleteId.value) return;

  Inertia.delete(route('death-record.delete', deathToDeleteId.value), {
    onSuccess: () => {
      isDeleteModalOpen.value = false;
      deathToDeleteId.value = null;
    },
    onError: (errors) => {
      alert('Failed to delete the death record.');
    }
  });
};
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>