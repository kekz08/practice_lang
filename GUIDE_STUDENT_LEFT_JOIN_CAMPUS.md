# Guide: Add LEFT JOIN (Campus) in StudentController

Use this as a checklist when you add the campus name to the students list. **CourseController** already does the same pattern for courses → campus; you can copy its structure and adapt column names.

---

## Before you start

- **Link column:** The `students` table must have a column that points to `campus` (e.g. `CampusID`). If your table uses another name (e.g. `Campus`), use that everywhere below instead of `CampusID`.
- **Right table:** `campus` (primary key `CampusID`, name column `CampusName`) — you already have this.

---

## Step 1: Build the query with LEFT JOIN and select

**Where:** `StudentController::index()`, right after `$query = Student::query();`

**What to do:**

1. Add a **LEFT JOIN** so each student row can attach one campus row:
   - Left table: `students`
   - Right table: `campus`
   - Condition: `students.CampusID` = `campus.CampusID` (use your actual column names if different)

2. Add a **select** so you get all student columns plus one extra from campus:
   - Keep all student columns: `students.*`
   - Add campus name with an alias: `campus.CampusName as campus_name`  
   So the API returns a flat field `campus_name` for each row.

**Reference (CourseController does it like this):**

```php
$query = Course::query()
    ->leftJoin('campus', 'courses.CampusID', '=', 'campus.CampusID')
    ->select('courses.*', 'campus.CampusName as campus_name');
```

For students you will have something like:

- `Student::query()`
- `->leftJoin('campus', 'students.CampusID', '=', 'campus.CampusID')`
- `->select('students.*', 'campus.CampusName as campus_name')`

Replace `CampusID` with the actual foreign key column on `students` if it’s different.

---

## Step 2: Prefix columns in the search (avoid ambiguity)

**Where:** Inside the `if ($search = ...)` block, in the `where(function ($q) use ($search) { ... })` closure.

**Why:** Once you join another table, Laravel needs to know which table each column belongs to. Use the table name as prefix: `students.StudentID`, `students.FirstName`, etc.

**What to do:**

- Change every column in the search from `'ColumnName'` to `'students.ColumnName'`.
- Add one more condition so search also matches campus name:  
  `->orWhere('campus.CampusName', 'like', "%{$search}%")`

So each line looks like:

- `$q->where('students.StudentID', 'like', "%{$search}%")`
- `$q->orWhere('students.FirstName', 'like', "%{$search}%")`
- … same for the other student columns …
- `$q->orWhere('campus.CampusName', 'like', "%{$search}%")`

---

## Step 3: Prefix sort column and handle campus_name

**Where:** Where you set `$sort` and call `$query->orderBy(...)`.

**Why:** With a join, the sort column must be unambiguous. When the user sorts by “Campus Name”, you must order by `campus.CampusName`, not by a column on `students`.

**What to do:**

1. When the requested sort key is `campus_name`, set the real sort column to `campus.CampusName`.
2. For any other sort (StudentID, FirstName, etc.), use `students.` + the sort key, e.g. `students.StudentID`, `students.FirstName`.

**Reference (CourseController):**

```php
$sortColumn = $sort === 'campus_name' ? 'campus.CampusName' : 'courses.' . $sort;
$query->orderBy($sortColumn, $order);
```

For students:

- If `$sort === 'campus_name'` → use `'campus.CampusName'`.
- Else → use `'students.' . $sort` (e.g. `students.StudentID`, `students.LastName`).

---

## Step 4: Expose campus_name in the API

You don’t need to change anything else in the controller for this. Because you used:

- `->select('students.*', 'campus.CampusName as campus_name')`  

each item in the paginated `data` array will already have a `campus_name` field (or null if there’s no matching campus). No extra code needed.

---

## Step 5: Show Campus Name in the frontend

**Where:** `resources/js/pages/StudentsPage.vue`, in the `studentColumns` array.

**What to do:** Add one column object for the joined campus name, e.g.:

- `key: 'campus_name'` (must match the alias you used in the controller).
- `label: 'Campus Name'` (or whatever you want in the header).
- `sortable: true` (you already handle sorting by `campus_name` in the controller).
- `width` as you like (e.g. `'140px'`).

Place it where you want it in the table (e.g. after `YearLevel` or `status`).

---

## Flow summary

1. **Controller – query:** `Student::query()->leftJoin('campus', ...)->select('students.*', 'campus.CampusName as campus_name')`.
2. **Controller – search:** Use `students.` prefix on all student columns and add `orWhere('campus.CampusName', 'like', ...)`.
3. **Controller – sort:** If sort is `campus_name`, order by `campus.CampusName`; otherwise order by `students.<sort>`.
4. **Frontend:** Add a column with `key: 'campus_name'` in `studentColumns`.

If your `students` table doesn’t have a campus link column yet, add it in the database (e.g. `CampusID` int, nullable) and optionally add it to the Student model’s `$fillable` and `$casts`; the controller steps above stay the same.
