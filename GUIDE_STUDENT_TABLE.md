# Guide: Displaying the Student Table

This guide walks you through how the Student table is displayed in your Laravel + Vue app. The same pattern is used for your Classes table.

---

## Overview: The 4 Steps

| Step | What | Where |
|------|------|--------|
| 1 | **Model** – Eloquent model for the `students` table | `app/Models/Student.php` |
| 2 | **Controller** – API that returns paginated, searchable, sortable JSON | `app/Http/Controllers/StudentController.php` |
| 3 | **Route** – URL that calls the controller | `routes/web.php` → `/api/students` |
| 4 | **Frontend** – Vue + SimpleTable that fetches and displays the data | `resources/js/App.vue` (or a separate page) |

---

## Step 1: The Model

**File:** `app/Models/Student.php`

**Purpose:** Tells Laravel how to talk to your `students` table (table name, primary key, fillable fields, and type casts).

**What to check:**

- **Table name** – The model uses `protected $table = 'students';`. If your actual database table is named something else (e.g. `student`), change it:
  ```php
  protected $table = 'student';  // use your real table name
  ```
- **Primary key** – Set to `StudentID`. Matches your schema.
- **Fillable** – List of columns that can be mass-assigned. Add/remove columns to match your table.
- **Casts** – Ensure dates (`BirthDate`, `created_at`, `updated_at`) and numeric columns are cast so they come out as the right type in JSON.

**You can test the model in Tinker:**

```bash
php artisan tinker
>>> App\Models\Student::count()
>>> App\Models\Student::first()
```

---

## Step 2: The Controller

**File:** `app/Http/Controllers/StudentController.php`

**Purpose:** Exposes an API that:

1. **Paginates** – Returns a page of rows (e.g. 10 per page).
2. **Searches** – If the client sends `?search=...`, filters by StudentID, FirstName, LastName, Email, etc.
3. **Sorts** – If the client sends `?sort=LastName&order=asc`, orders by that column.

**How it works:**

- `Student::query()` builds the query.
- `$request->input('search')` adds `LIKE` conditions when present.
- `$request->input('sort')` and `$request->input('order')` set `orderBy()`.
- `$query->paginate($perPage)` returns a Laravel paginator, which becomes JSON with `data`, `total`, `current_page`, etc.

**Testing the API:**

Open in browser or use curl:

- `http://localhost:8000/api/students`
- `http://localhost:8000/api/students?search=Juan`
- `http://localhost:8000/api/students?sort=LastName&order=asc&per_page=5`

You should see JSON with a `data` array of students and pagination info.

---

## Step 3: The Route

**File:** `routes/web.php`

**What was added:**

```php
use App\Http\Controllers\StudentController;

Route::get('/api/students', [StudentController::class, 'index']);
```

**Meaning:** A GET request to `/api/students` runs `StudentController::index()` and returns the JSON response.

---

## Step 4: The Frontend (Displaying the Table)

Your app uses **Vue 3** and **SimpleTable** and loads data from `/api/classes`. You do the same for students: point SimpleTable at `/api/students` and define columns that match the API.

### Option A: Add a Students section on the same page as Classes

In `resources/js/App.vue` you can:

1. Add a second `<SimpleTable>` that uses `fetch-url="/api/students"`.
2. Define a `studentColumns` array (same idea as `columns` for classes).
3. Optionally add a simple way to switch between “Classes” and “Students” (tabs or two sections).

**Example – add a second table below Classes:**

```vue
<template>
  <div class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] p-6">
    <header class="mb-6">
      <h1 class="text-2xl font-semibold">Classes</h1>
      <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">Classes table from database</p>
    </header>

    <SimpleTable
      fetch-url="/api/classes"
      :columns="columns"
      :page-sizes="[100, 200, 500, 1000]"
      :per-page="100"
      searchable
      odd-row-color="bg-white dark:bg-[#161615]"
      even-row-color="bg-stone-50 dark:bg-[#1a1a18]"
      hover-color="hover:bg-stone-200 dark:hover:bg-stone-700"
    />

    <!-- Students section -->
    <header class="mb-6 mt-12">
      <h1 class="text-2xl font-semibold">Students</h1>
      <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">Students table from database</p>
    </header>

    <SimpleTable
      fetch-url="/api/students"
      :columns="studentColumns"
      :page-sizes="[10, 25, 50, 100]"
      :per-page="25"
      searchable
      odd-row-color="bg-white dark:bg-[#161615]"
      even-row-color="bg-stone-50 dark:bg-[#1a1a18]"
      hover-color="hover:bg-stone-200 dark:hover:bg-stone-700"
    />
  </div>
</template>

<script setup>
import SimpleTable from '@kikiloaw/simple-table'

const columns = [
  { key: 'ClassID', label: 'ID', sortable: true, width: '80px' },
  // ... rest of your class columns
]

const studentColumns = [
  { key: 'StudentID', label: 'ID', sortable: true, width: '80px' },
  { key: 'StudentYear', label: 'Year', sortable: true, width: '80px' },
  { key: 'FirstName', label: 'First Name', sortable: true, width: '120px' },
  { key: 'MiddleName', label: 'Middle Name', sortable: true, width: '120px' },
  { key: 'LastName', label: 'Last Name', sortable: true, width: '120px' },
  { key: 'Email', label: 'Email', sortable: true, width: '180px' },
  { key: 'PhoneNumber', label: 'Phone', sortable: true, width: '120px' },
  { key: 'Gender', label: 'Gender', sortable: true, width: '90px' },
  { key: 'YearLevel', label: 'Year Level', sortable: true, width: '90px' },
  { key: 'status', label: 'Status', sortable: true, width: '100px' },
  { key: 'created_at', label: 'Created', sortable: true, width: '160px' },
]
</script>
```

- **`fetch-url="/api/students"`** – Same as your new route; SimpleTable will call this and expect the same paginated JSON shape as `/api/classes`.
- **`studentColumns`** – Each `key` must match a column name returned by the API (same as your Student model/table). Add or remove columns to match what you want to show.

### Option B: Separate page for Students

If you use a router (e.g. Vue Router or Inertia), create a Students view/component that only has the Students `<SimpleTable>` and `studentColumns`, and set its route to load that component. The flow is the same: `fetch-url="/api/students"` and columns that match the API.

---

## Summary Checklist

- [ ] **Model** – `app/Models/Student.php` exists; `$table` matches your DB table name.
- [ ] **Controller** – `app/Http/Controllers/StudentController.php` has `index()` that returns paginated JSON.
- [ ] **Route** – `routes/web.php` has `GET /api/students` → `StudentController@index`.
- [ ] **API** – Visiting `http://localhost:8000/api/students` returns JSON with a `data` array of students.
- [ ] **Frontend** – A `<SimpleTable>` uses `fetch-url="/api/students"` and a `columns` (or `studentColumns`) array whose `key` values match the API fields.

Once these are in place, the Student table is “hooked up” end to end: database → Model → Controller → Route → SimpleTable.
