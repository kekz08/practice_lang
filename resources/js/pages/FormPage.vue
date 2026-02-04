<template>
  <div class="space-y-6">
    <header class="mb-6">
      <h1 class="text-2xl font-semibold">Basic Usage</h1>
    </header>

    <!-- Basic Usage: single column form (from form-formatter README) -->
    <div class="grid grid-cols-1 gap-5.5 p-6.5 grid-auto-rows-[minmax(100px,_auto)]">
      <FormFormatter
        :sampledata="sampledata"
        :form="form"
        :parameters="parameters"
        @selectRefsReady="storeSelectRefs"
        @triggerCallback="executeCallback"
      />
    </div>

  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import FormFormatter from 'form-formatter';

// Parameters for selects (Basic Usage: parameters ref)
const parameters = ref({});

const selectRefs = ref({});
const storeSelectRefs = (refs) => {
  nextTick(() => {
    selectRefs.value = refs;
  });
};

// Basic Usage: executeCallback with callback map
const executeCallback = (callbackName) => {
  const callbackMap = {
    checkboxGroupCallback: () => console.log('Checkbox group callback'),
    checkboxCallback: () => console.log('Checkbox callback'),
    radioGroupCallback: () => console.log('Radio group callback'),
    radioCallback: () => console.log('Radio callback'),
    curriculumCallback: () => console.log('Curriculum callback'),
    courseCallback: () => console.log('Course callback'),
    employeeCallback: () => console.log('Employee callback'),
    programCallback: () => console.log('Program callback'),
  };
  if (callbackMap[callbackName]) {
    callbackMap[callbackName]();
  }
};

// Form data (Basic Usage: form ref)
const form = ref({
  id: null,
  firstName: '',
  lastName: '',
  email: '',
  yearLevel: null,
  birthDate: null,
  gender: null,
  checkboxGroup1: [],
  toggle1: null,
  programId: null,
  courseId: null,
});

// Basic Usage: sampledata — field configs (README style; fahadselect uses /api URLs for this app)
const sampledata = ref([
  { type: 'hidden', model: 'id', required: false },

  {
    type: 'checkbox-group',
    label: 'Checkbox group',
    placeholder: 'default',
    model: 'checkboxGroup1',
    required: false,
    callback: 'checkboxGroupCallback',
    options: [
      { value: '1', text: 'Option 1', color: 'teal' },
      { value: '2', text: 'Option 2', color: 'teal' },
      { value: '3', text: 'Option 3', color: 'teal' },
    ],
  },

  { type: 'toggle', label: 'Toggle', placeholder: 'red', model: 'toggle1', required: false, color: 'red', value: 'yes' },

  { type: 'text', model: 'firstName', label: 'First Name', placeholder: 'Enter First Name', required: true },
  { type: 'text', model: 'lastName', label: 'Last Name', placeholder: 'Enter Last Name', required: true },
  { type: 'text', model: 'email', label: 'Email', placeholder: 'Enter Email', required: true },
  { type: 'number', model: 'yearLevel', label: 'Year Level', placeholder: 'Enter Year Level', required: false },
  { type: 'date', model: 'birthDate', label: 'Birth Date', required: false },

  {
    type: 'select',
    model: 'gender',
    label: 'Gender',
    options: [
      { value: 'Male', text: 'Male' },
      { value: 'Female', text: 'Female' },
    ],
    required: false,
  },

  {
    type: 'fahadselect',
    model: 'programId',
    label: 'Program',
    route: '/api/programs',
    placeholder: 'Choose a program',
    callback: 'programCallback',
    required: false,
  },
  {
    type: 'fahadselect',
    model: 'courseId',
    label: 'Course',
    route: '/api/courses',
    placeholder: 'Choose a course',
    callback: 'courseCallback',
    required: false,
  },
]);
</script>
