<template>
    <div class="space-y-6">
        <div v-show="showFilters" class="flex flex-wrap items-end gap-4">
        <FahadSelect
            class="flex-1 min-w-[200px]"
            placeholder="Search by Campus"
            search-route="/api/campus"
            @triggerChange="onCampusSelect" />
        </div>
        <header class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
            <h1 class="text-2xl font-semibold">Campus</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">Campus table from database</p>
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Add Campus
            </button>
        </header>
        <SimpleTable
            ref="tableRef"
            fetch-url="/api/campus"
            :columns="columns"
            :page-sizes="[10, 20, 50, 100]"
            :per-page="10"
            :query-params="tableQueryParams"
            selectable
            searchable
            odd-row-color="bg-white dark:bg-[#161615]"
            even-row-color="bg-stone-50 dark:bg-[#1a1a18]"
            hover-color="hover:bg-stone-200 dark:hover:bg-stone-700"

        >
        <template #actions="{ rows }">
            <TableBatchActions
                :rows="rows"
                @export="handleExport"
                @bulk-delete="handleBulkDelete"
                @toggle-filters="showFilters = !showFilters"
            />
        </template>
        <template #cell-actions="{ row }">
            <TableActions
                :row="row"
                @edit="handleEdit"
                @delete="handleDelete"
                @view="handleView"
            />
        </template>
        <template #cell-status="{ row }">
            <span :class="statusClass(row.status)" class="font-semibold capitalize">
                {{ row.status || 'active' }}
            </span>
        </template>
        </SimpleTable>
        <!-- CRUD Modal with FormFormatter -->
        <Teleport to="body">
            <div
                v-if="modalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50"
                @click.self="closeModal"
            >
                <div class="relative w-full max-w-7xl mx-4 my-8 bg-white dark:bg-[#1a1a18] rounded-lg shadow-xl">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-stone-200 dark:border-stone-700">
                        <h2 class="text-lg font-semibold">
                            {{ isViewMode ? 'View Campus' : isEditMode ? 'Edit Campus' : 'Add Campus' }}
                        </h2>
                        <button
                            @click="closeModal"
                            class="p-1 rounded hover:bg-stone-100 dark:hover:bg-stone-800"
                            aria-label="Close"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form @submit.prevent="handleSubmit" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5.5 grid-auto-rows-[minmax(80px,_auto)]">
                            <FormFormatter
                                :sampledata="formFieldsForDisplay"
                                :form="form"
                                :parameters="parameters"
                                @selectRefsReady="storeSelectRefs"
                                @triggerCallback="executeCallback"
                            />
                        </div>
                        <div v-if="!isViewMode" class="flex justify-end gap-2 mt-6 pt-4 border-t border-stone-200 dark:border-stone-700">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-stone-700 bg-white border border-stone-300 rounded-md hover:bg-stone-50 dark:bg-stone-800 dark:text-stone-200 dark:border-stone-600"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="formSubmitting"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ formSubmitting ? 'Saving...' : (isEditMode ? 'Update' : 'Create') }}
                            </button>
                        </div>
                        <div v-else class="flex justify-end mt-6 pt-4 border-t border-stone-200 dark:border-stone-700">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-stone-700 bg-white border border-stone-300 rounded-md hover:bg-stone-50 dark:bg-stone-800 dark:text-stone-200 dark:border-stone-600"
                            >
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <ConfirmModal
            v-model="deleteModalOpen"
            :title="deleteModalTitle"
            :message="deleteModalMessage"
            confirm-text="Delete"
            variant="danger"
            @confirm="executeDelete"
        />
    </div>
</template>

<script setup>
import {ref, computed, watch, nextTick} from 'vue';
import axios from 'axios';
import { useToast } from '../../composables/useToast';
import FahadSelect from 'fahad-select';
import 'fahad-select/dist/style.css';
import FormFormatter from 'form-formatter';
import SimpleTable from '@kikiloaw/simple-table';
import TableActions from "@/components/TableActions.vue";
import TableBatchActions from "@/components/TableBatchActions.vue";
import ConfirmModal from '../../components/ConfirmModal.vue';

const { show: showToast } = useToast();
const tableRef = ref(null);
const selectedCampusID = ref(null);
const showFilters = ref(false);
const modalOpen = ref(false);
const isEditMode = ref(false);
const isViewMode = ref(false);
const deleteModalOpen = ref(false);
const deleteTarget = ref(null);
const formSubmitting = ref(false);

const deleteModalTitle = computed(() => {
    return deleteTarget.value?.type === 'bulk' ? 'Bulk Delete Campus' : 'Delete Campus';
});

const deleteModalMessage = computed(() => {
    if (deleteTarget.value?.type === 'bulk') {
        return `Are you sure you want to delete ${deleteTarget.value.rows.length} campuses? This action cannot be undone.`;
    }
    return `Are you sure you want to delete campus "${deleteTarget.value?.row?.CampusName}"? This action cannot be undone.`;
});

const parameters = ref({});
const selectRefs = ref({});
const storeSelectRefs = (refs) => {
    nextTick(() => {
        selectRefs.value = refs;
    });
};

const executeCallback = (callbackName) => {
    if (callbackName) console.log('Callback:', callbackName);
};

const form = ref({
    CampusID: null,
    CampusCode: '',
    CampusName: '',
    Location: '',
    CampusHead: null,
    OfficeCode: null,
    status: null,
});

const formFields = [
    { type: 'hidden', model: 'CampusID', required: false },
    { type: 'text', model: 'CampusCode', label: 'Campus Code', placeholder: 'Enter Campus Code', required: true },
    { type: 'text', model: 'CampusName', label: 'Campus Name', placeholder: 'Enter Campus Name', required: true },
    { type: 'text', model: 'Location', label: 'Location', placeholder: 'Enter Location', required: false },
    { type: 'number', model: 'CampusHead', label: 'Campus Head', placeholder: 'Enter Campus Head ID', required: false },
    { type: 'number', model: 'OfficeCode', label: 'Office Code', placeholder: 'Enter Office Code', required: false },
    {
        type: 'select',
        model: 'status',
        label: 'Status',
        options: [
            { value: 'active', text: 'Active' },
            { value: 'inactive', text: 'Inactive' },
            { value: 'locked', text: 'Locked' },
            { value: 'unlocked', text: 'Unlocked' },
        ],
        required: true
    },
];

const formFieldsForDisplay = computed(() => {
    return formFields.map(field => ({
        ...field,
        disabled: isViewMode.value
    }));
});
const onCampusSelect = (selected) => {
    selectedCampusID.value = selected?.id ?? null;
};

watch([selectedCampusID], () => {
    tableRef.value?.refresh?.();
})

const tableQueryParams = computed(() => {
    const params = {};
    if (selectedCampusID.value != null) params.CampusID = selectedCampusID.value;
    return params;
});

const columns = [
    { key: '#', label: 'ID', sortable: true, width: '80px', autonumber: true },
    { key: 'CampusCode', label: 'Campus Code', sortable: true, width: '100px' },
    { key: 'CampusName', label: 'Campus Name', sortable: true, width: '120px' },
    { key: 'Location', label: 'Location', sortable: true, width: '100px' },
    { key: 'CampusHead', label: 'CampusHead', sortable: true, width: '100px' },
    { key: 'OfficeCode', label: 'Office Code', sortable: true, width: '100px' },
    { key: 'status', label: 'Status', sortable: true, width: '100px' },
    { key: 'created_at', label: 'Created', sortable: true, width: '160px' },
    { key: 'actions', label: 'Action', sortable: false, width: '100px' },
];
const statusClass = (status) => {
    status = status?.toLowerCase();
    return {
        active: 'text-green-500',
        inactive: 'text-gray-500',
        locked: 'text-red-600',
        unlocked: 'text-green-600',
    }[status] || 'text-gray-400';
};

const openCreate = () => {
    isEditMode.value = false;
    isViewMode.value = false;
    form.value = {
        CampusID: null,
        CampusCode: '',
        CampusName: '',
        Location: '',
        CampusHead: null,
        OfficeCode: null,
        status: 'active',
    };
    modalOpen.value = true;
};

const openEdit = (row) => {
    isEditMode.value = true;
    isViewMode.value = false;
    form.value = {
        ...row,
        status: row.status || 'active'
    };
    modalOpen.value = true;
};

const openView = (row) => {
    isEditMode.value = false;
    isViewMode.value = true;
    form.value = {
        ...row,
        status: row.status || 'active'
    };
    modalOpen.value = true;
};

const closeModal = () => {
    modalOpen.value = false;
};

const handleSubmit = async () => {
    formSubmitting.value = true;
    try {
        if (isEditMode.value) {
            await axios.put(`/api/campus/${form.value.CampusID}`, form.value);
            showToast('Campus updated successfully', 'success');
        } else {
            await axios.post('/api/campus', form.value);
            showToast('Campus created successfully', 'success');
        }
        modalOpen.value = false;
        tableRef.value?.refresh?.();
    } catch (error) {
        console.error(error);
        showToast(error.response?.data?.message || 'An error occurred', 'danger');
    } finally {
        formSubmitting.value = false;
    }
};

const handleEdit = (row) => {
    openEdit(row);
};

const handleDelete = (row) => {
    deleteTarget.value = { type: 'single', row };
    deleteModalOpen.value = true;
};

const handleView = (row) => {
    openView(row);
};

const handleExport = () => {
    showToast('Exporting campus data...', 'info');
};

const handleBulkDelete = (rows) => {
    deleteTarget.value = { type: 'bulk', rows };
    deleteModalOpen.value = true;
};

const executeDelete = async () => {
    try {
        if (deleteTarget.value.type === 'single') {
            await axios.delete(`/api/campus/${deleteTarget.value.row.CampusID}`);
            showToast('Campus deleted successfully', 'success');
        } else {
            const ids = deleteTarget.value.rows.map(r => r.CampusID);
            await axios.post('/api/campus/bulk-delete', { ids });
            showToast(`${ids.length} campuses deleted successfully`, 'success');
        }
        tableRef.value?.refresh?.();
    } catch (error) {
        console.error(error);
        showToast('Failed to delete campus', 'danger');
    } finally {
        deleteModalOpen.value = false;
    }
};
</script>
