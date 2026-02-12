<template>
    <div class="space-y-6">
        <header class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Students</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">Students table from database</p>
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Add Student
            </button>
        </header>

        <div v-show="showFilters" class="flex flex-wrap items-end gap-4">
            <FahadSelect
                class="flex-1 min-w-[200px]"
                placeholder="Search by Student"
                search-route="/api/students"
                @triggerChange="onStudentSelect"
            />
            <FahadSelect
                class="flex-1 min-w-[200px]"
                placeholder="Search by Program"
                search-route="/api/programs"
                @triggerChange="onProgramSelect"
            />
            <FahadSelect
                class="flex-1 min-w-[200px]"
                placeholder="Search by Campus"
                search-route="/api/campus"
                @triggerChange="onCampusSelect"
            />
        </div>

        <SimpleTable
            ref="tableRef"
            fetch-url="/api/students"
            :columns="studentColumns"
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
          {{ row.status }}
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
                        <div class="flex items-center gap-4">
                            <div v-if="form.avatar_url" class="w-12 h-12 overflow-hidden rounded-full border border-stone-200 dark:border-stone-700">
                                <img :src="form.avatar_url" alt="Student Avatar" class="w-full h-full object-cover" />
                            </div>
                            <h2 class="text-lg font-semibold">
                                {{ isViewMode ? 'View Student' : isEditMode ? 'Edit Student' : 'Add Student' }}
                            </h2>
                        </div>
                        <button
                            @click="closeModal"
                            class="p-1 rounded hover:bg-stone-100 dark:hovr:bg-stone-800"
                            aria-label="Close"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form @submit.prevent="handleSubmit" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5.5 grid-auto-rows-[minmax(80px,_auto)]">
                            <div v-if="isViewMode" class="md:col-span-2 space-y-4">
                                <!-- Avatar Preview -->
                                <div v-if="form.avatar_url" class="flex justify-center mb-4">
                                    <div class="w-32 h-32 overflow-hidden rounded-lg border-2 border-stone-200 dark:border-stone-700 shadow-sm flex items-center justify-center bg-stone-50 dark:bg-stone-800">
                                        <img
                                            :src="form.avatar_url"
                                            alt="Student Avatar"
                                            class="w-full h-full object-cover"
                                        />
                                    </div>
                                </div>

                                <!-- Attachments List -->
                                <div v-if="form.attachment_urls && form.attachment_urls.length > 0" class="border rounded-lg p-4 bg-stone-50 dark:bg-stone-800/50">
                                    <h3 class="text-sm font-semibold mb-3">Attachments</h3>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                        <div v-for="(url, idx) in form.attachment_urls" :key="idx" class="flex flex-col items-center">
                                            <div class="w-20 h-20 overflow-hidden rounded border border-stone-200 dark:border-stone-700 bg-white dark:bg-stone-800 flex items-center justify-center">
                                                <img v-if="isImage(url)" :src="url" class="w-full h-full object-cover" />
                                                <svg v-else class="w-10 h-10 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <a :href="url" target="_blank" class="text-[10px] text-blue-600 hover:underline mt-1 text-center line-clamp-1">
                                                {{ url.split('/').pop().split('?')[0] }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
import { ref, reactive, computed, watch, nextTick } from 'vue';
import axios from 'axios';
import { useToast } from '../../composables/useToast';
import FahadSelect from 'fahad-select';
import 'fahad-select/dist/style.css';
import FormFormatter from 'form-formatter';
import SimpleTable from '@kikiloaw/simple-table';
import TableBatchActions from '../../components/TableBatchActions.vue';
import TableActions from '@/components/TableActions.vue';
import ConfirmModal from '../../components/ConfirmModal.vue';

const { show: showToast } = useToast();
const tableRef = ref(null);
const showFilters = ref(false);
const selectedStudentId = ref(null);
const selectedProgramId = ref(null);
const selectedCampusID = ref(null);
const modalOpen = ref(false);
const isEditMode = ref(false);
const isViewMode = ref(false);
const deleteModalOpen = ref(false);
const deleteTarget = ref(null);
const formSubmitting = ref(false);

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

const form = reactive({
    StudentID: null,
    StudentYear: null,
    FirstName: '',
    MiddleName: '',
    LastName: '',
    Email: '',
    PhoneNumber: '',
    Gender: null,
    BirthDate: null,
    Address: '',
    CurriculumID: null,
    YearLevel: null,
    status: null,
    avatar: [],
    avatar_url: null,
    attachments: [],
    attachment_urls: [],
});

const formFields = [
    { type: 'hidden', model: 'StudentID', required: false },
    { type: 'number', model: 'StudentYear', label: 'Student Year', placeholder: 'e.g. 2024', required: false },
    { type: 'text', model: 'FirstName', label: 'First Name', placeholder: 'Enter First Name', required: true },
    { type: 'text', model: 'MiddleName', label: 'Middle Name', placeholder: 'Enter Middle Name', required: false },
    { type: 'text', model: 'LastName', label: 'Last Name', placeholder: 'Enter Last Name', required: true },
    { type: 'text', model: 'Email', label: 'Email', placeholder: 'Enter Email', required: false },
    { type: 'text', model: 'PhoneNumber', label: 'Phone Number', placeholder: 'Enter Phone', required: false },
    {
        type: 'select',
        model: 'Gender',
        label: 'Gender',
        placeholder: 'Choose gender',
        options: [
            { value: 'Male', text: 'Male' },
            { value: 'Female', text: 'Female' },
            { value: 'Other', text: 'Other' },
        ],
        required: false,
    },
    { type: 'date', model: 'BirthDate', label: 'Birth Date', required: false },
    { type: 'textarea', model: 'Address', label: 'Address', placeholder: 'Enter Address', required: false },
    {
        type: 'fahadselect',
        model: 'CurriculumID',
        label: 'Program / Curriculum',
        route: '/api/curricula',
        placeholder: 'Choose program',
        required: false,
    },
    {
        type: 'select',
        model: 'YearLevel',
        label: 'Year Level',
        placeholder: 'Choose year level',
        required: false,
        options: [
            { value: 1, text: '1' },
            { value: 2, text: '2' },
            { value: 3, text: '3' },
            { value: 4, text: '4' },
            { value: 5, text: '5' },
        ]
    },
    {
        type: 'select',
        model: 'status',
        label: 'Status',
        options: [
            { value: 'active', text: 'Active' },
            { value: 'inactive', text: 'Inactive' },
            { value: 'locked', text: 'Locked' },
            { value: 'unlocked', text: 'Unlocked' },
            { value: 'filled', text: 'Filled' },
            { value: 'pre-enroll', text: 'Pre-enroll' },
        ],
        required: false,
    },
    [
        {
            type: 'file',
            model: 'avatar',
            label: 'Profile Picture',
            placeholder: 'Choose avatar',
            multiple: false,
            accept: '.jpg,.png,.jpeg',
            required: false
        },
        {
            type: 'file',
            model: 'attachments',
            label: 'Documents',
            placeholder: 'Choose files',
            multiple: true,
            accept: '.jpg,.png,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.txt',
            required: false
        },
    ],
];

const deleteModalTitle = computed(() =>
    deleteTarget.value?.type === 'bulk'
        ? 'Delete Students'
        : 'Delete Student'
);

const deleteModalMessage = computed(() => {
    const t = deleteTarget.value;
    if (!t) return '';
    if (t.type === 'bulk') return `Are you sure you want to delete ${t.ids.length} student(s)?`;
    return `Are you sure you want to delete ${t.row.FirstName} ${t.row.LastName}?`;
});

const formFieldsForDisplay = computed(() =>
    formFields.map((field) => {
        if (Array.isArray(field)) {
            return field.map(f => ({ ...f, disabled: isViewMode.value }));
        }
        return {
            ...field,
            disabled: isViewMode.value,
        };
    })
);

const resetForm = () => {
    Object.assign(form, {
        StudentID: null,
        StudentYear: null,
        FirstName: '',
        MiddleName: '',
        LastName: '',
        Email: '',
        PhoneNumber: '',
        Gender: null,
        BirthDate: null,
        Address: '',
        CurriculumID: null,
        YearLevel: null,
        status: null,
        avatar: [],
        avatar_url: null,
        attachments: [],
        attachment_urls: [],
    });
};

const openCreate = () => {
    resetForm();
    isEditMode.value = false;
    isViewMode.value = false;
    modalOpen.value = true;
};

const openEdit = (row) => {
    const birthDate = row.BirthDate
        ? (typeof row.BirthDate === 'string' ? row.BirthDate.split('T')[0] : row.BirthDate)
        : null;
    Object.assign(form, {
        StudentID: row.StudentID,
        StudentYear: row.StudentYear ?? null,
        FirstName: row.FirstName ?? '',
        MiddleName: row.MiddleName ?? '',
        LastName: row.LastName ?? '',
        Email: row.Email ?? '',
        PhoneNumber: row.PhoneNumber ?? '',
        Gender: row.Gender ?? null,
        BirthDate: birthDate,
        Address: row.Address ?? '',
        CurriculumID: row.CurriculumID
            ? { id: row.CurriculumID, label: row.program_name ?? `Curriculum ${row.CurriculumID}` }
            : null,
        YearLevel: row.YearLevel ?? null,
        status: row.status ?? null,
        avatar: [],
        avatar_url: row.avatar_url ?? null,
        attachments: [],
        attachment_urls: row.attachment_urls ?? [],
    });
    isEditMode.value = true;
    isViewMode.value = false;
    modalOpen.value = true;
};

const openView = (row) => {
    openEdit(row);
    isEditMode.value = false;
    isViewMode.value = true;
};

const closeModal = () => {
    modalOpen.value = false;
};

// No-op fileToBase64 as form-formatter already handles it
const fileToBase64 = (file) => {
    return Promise.resolve(file);
};

const handleSubmit = async () => {
    formSubmitting.value = true;
    try {
        const extractBase64 = (files) => {
            if (!files || files.length === 0) return [];
            return files.map(fileObj => {
                if (typeof fileObj === 'string') return fileObj;
                if (fileObj && fileObj.content) return fileObj.content;
                return null;
            }).filter(Boolean);
        };

        const avatarBase64 = extractBase64(form.avatar);
        const attachmentsBase64 = extractBase64(form.attachments);

        const payload = {
            StudentYear: form.StudentYear,
            FirstName: form.FirstName,
            MiddleName: form.MiddleName || null,
            LastName: form.LastName,
            Email: form.Email || null,
            PhoneNumber: form.PhoneNumber || null,
            Gender: form.Gender || null,
            BirthDate: form.BirthDate || null,
            Address: form.Address || null,
            CurriculumID: (form.CurriculumID?.id ?? form.CurriculumID) || null,
            YearLevel: form.YearLevel || null,
            status: form.status || null,
            avatar: avatarBase64[0] || null,
            attachments: attachmentsBase64,
        };

        if (isEditMode.value && form.StudentID) {
            await axios.put(`/api/students/${form.StudentID}`, payload);
            showToast('Student updated successfully');
        } else {
            await axios.post('/api/students', payload);
            showToast('Student created successfully');
        }
        closeModal();
        tableRef.value?.refresh?.();
    } catch (error) {
        console.error('Save failed:', error);
        const msg = error.response?.data?.message || error.response?.data?.errors
            ? Object.values(error.response.data.errors || {}).flat().join(', ')
            : (error instanceof TypeError ? error.message : 'Failed to save student');
        showToast(msg, 'error');
    } finally {
        formSubmitting.value = false;
    }
};

const onStudentSelect = (selected) => {
    selectedStudentId.value = selected?.id ?? null;
};

const onProgramSelect = (selected) => {
    selectedProgramId.value = selected?.id ?? null;
};

const onCampusSelect = (selected) => {
    selectedCampusID.value = selected?.id ?? null;
};

watch([selectedStudentId, selectedProgramId, selectedCampusID], () => {
    tableRef.value?.refresh?.();
});

const tableQueryParams = computed(() => {
    const params = {};
    if (selectedStudentId.value != null) params.StudentID = selectedStudentId.value;
    if (selectedProgramId.value != null) params.ProgramID = selectedProgramId.value;
    if (selectedCampusID.value != null) params.CampusID = selectedCampusID.value;
    return params;
});

const studentColumns = [
    { key: '#', label: 'ID', sortable: true, width: '80px', autonumber: true, fixed: true },
    { key: 'FirstName', label: 'First Name', sortable: true, width: '120px', fixed: 'left' },
    { key: 'MiddleName', label: 'Middle Name', sortable: true, width: '120px' },
    { key: 'LastName', label: 'Last Name', sortable: true, width: '120px' },
    { key: 'Email', label: 'Email', sortable: true, width: '180px' },
    { key: 'StudentYear', label: 'Year', sortable: true, width: '80px' },
    { key: 'PhoneNumber', label: 'Phone', sortable: true, width: '120px' },
    { key: 'Gender', label: 'Gender', sortable: true, width: '90px' },
    { key: 'YearLevel', label: 'Year Level', sortable: true, width: '90px' },
    { key: 'program_name', label: 'Program Name', sortable: true, width: '120px' },
    { key: 'campus_name', label: 'Campus', sortable: true, width: '140px' },
    { key: 'status', label: 'Status', sortable: true, width: '100px' },
    { key: 'created_at', label: 'Created', sortable: true, width: '160px' },
    { key: 'actions', label: 'Actions', sortable: false, width: '120px', fixed: 'right' },
];

const statusClass = (status) => {
    status = status?.toLowerCase();
    return {
        active: 'text-green-500',
        inactive: 'text-gray-500',
        locked: 'text-red-600',
        unlocked: 'text-green-600',
        filled: 'text-red-500',
        'pre-enroll': 'text-yellow-500',
    }[status] || 'text-gray-400';
};

const handleExport = (type, rows) => {
    if (type === 'csv') {
        const csv =
            'StudentID,FirstName,LastName,Email,Campus,Program\n' +
            rows
                .map(
                    (row) =>
                        `${row.StudentID},${row.FirstName},${row.LastName},${row.Email ?? ''},${row.campus_name ?? ''},${row.program_name ?? ''}`
                )
                .join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'students.csv';
        a.click();
        URL.revokeObjectURL(url);
    }
};

const executeDelete = () => {
    const t = deleteTarget.value;
    if (!t) return;
    if (t.type === 'bulk') {
        Promise.all(t.ids.map((id) => axios.delete(`/api/students/${id}`)))
            .then(() => {
                showToast(`Deleted ${t.ids.length} student(s) successfully`);
                tableRef.value?.refresh?.();
            })
            .catch((error) => {
                console.error('Bulk delete failed:', error);
                showToast('Failed to delete some or all students', 'error');
            });
    } else {
        axios
            .delete(`/api/students/${t.row.StudentID}`)
            .then(() => {
                showToast('Student deleted successfully');
                tableRef.value?.refresh?.();
            })
            .catch((error) => {
                console.error('Delete failed:', error);
                showToast('Failed to delete student', 'error');
            });
    }
    deleteTarget.value = null;
};

const handleBulkDelete = (rows) => {
    const ids = rows.map((r) => r.StudentID);
    deleteTarget.value = { type: 'bulk', ids };
    deleteModalOpen.value = true;
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

const isImage = (url) => {
    if (!url) return false;
    // Handle both direct filenames and route URLs
    const parts = url.split('/');
    const filenameWithQuery = parts[parts.length - 1];
    const filename = filenameWithQuery.split('?')[0];
    const extension = filename.split('.').pop().toLowerCase();
    return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension);
};
</script>
