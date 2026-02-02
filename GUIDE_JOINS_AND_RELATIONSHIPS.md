# Guide: LEFT JOINs and Relationships Between Tables

This guide explains **how to join tables** in Laravel (e.g. Courses with Campuses, Course Types, etc.) so you understand the flow and can do it yourself.

---

## 1. What a LEFT JOIN Does

- You have a **left** table (e.g. `courses`) and a **right** table (e.g. `campuses`).
- They are linked by a column (e.g. `courses.CampusID` = `campuses.id`).
- **LEFT JOIN** keeps **every row from the left table**. For each row, it attaches the matching row from the right table. If there is no match (e.g. `CampusID` is null), the right side is null; the course row is still returned.

So: “Give me all courses, and for each course include the campus info if it exists.”

---

## 2. Where Joins Happen in Your App

| Layer | Role |
|-------|------|
| **Model** | Defines *relationships* (e.g. “Course belongs to Campus”). Optional; used when you want Eloquent to load related data. |
| **Controller** | Builds the query: either uses relationships (e.g. `Course::with('campus')`) or uses Query Builder `leftJoin()`. |
| **API response** | Returns JSON. Joined/related data can be nested (e.g. `course.campus.name`) or flat (e.g. `campus_name`). |

The **flow** is: decide what data you need → choose relationship or manual join → implement in model (if relationship) and/or controller → return JSON → frontend shows it.

**Where the join lives in this project:** The Courses list uses a LEFT JOIN. See **`app/Http/Controllers/CourseController.php`** — the `index()` method uses `leftJoin('campuses', 'courses.CampusID', '=', 'campuses.id')` and `select('courses.*', 'campuses.name as campus_name')`. The Courses page then shows the `campus_name` column. If your `campuses` table has a different structure (e.g. column `CampusName` instead of `name`), change the `select()` to use that column and alias (e.g. `'campuses.CampusName as campus_name'`). If you don’t have a `campuses` table yet, remove or comment out the `leftJoin` and `select` lines in the controller.

---

## 3. Two Ways to “Join” in Laravel

### Approach A: Eloquent relationships (`with()`)

- You define a **relationship** on the model (e.g. Course **belongsTo** Campus).
- In the controller you do: `Course::with('campus')->...`
- Laravel runs the main query and then loads the related rows (either by extra queries or by join, depending on Laravel version/settings). Each course in the result has a `campus` object (or null).
- **Good for:** Clean code, reusing relationships everywhere, getting full related objects (e.g. `course.campus.name`, `course.campus.code`).

### Approach B: Query Builder `leftJoin()`

- In the controller you build the query and call `$query->leftJoin('campuses', 'courses.CampusID', '=', 'campuses.id')`.
- You **select** the columns you need from both tables (e.g. `courses.*` and `campuses.name as campus_name`) so there’s one SQL query and one flat row per course.
- **Good for:** Lists/tables where you only need a few columns from the right table (e.g. campus name in one column), or when you don’t want to define a relationship.

---

## 4. Step-by-Step Flow for a LEFT JOIN

### Step 1: Identify tables and the link column

- **Left table:** e.g. `courses` (the one you’re listing).
- **Right table:** e.g. `campuses` (the one you want to pull data from).
- **Link:** `courses.CampusID` = `campuses.id` (or whatever your campuses primary key is).

Same idea for:
- `courses.CourseTypeID` → `course_types.id`
- `courses.SchoolFeeTypeID` → `school_fee_types.id`

### Step 2: Choose approach

- Need the **full related object** (e.g. campus name, code, etc.) and want to reuse it in many places? → Use **relationships** (Approach A).
- Need only **one or two columns** in a flat list (e.g. “Campus” column with name)? → Either relationship or **manual leftJoin** (Approach B) is fine; join is one query and flat structure.

### Step 3: Implement

---

## 5. Approach A: Using Eloquent Relationships (Conceptual Flow)

**1. Define the relationship on the model**

Example: Course belongs to one Campus (each course has a `CampusID`).

In `app/Models/Course.php`:

```php
public function campus()
{
    // Course has CampusID → points to campuses.id (or whatever your PK is)
    return $this->belongsTo(Campus::class, 'CampusID', 'id');
}
```

If your campuses table uses a different primary key (e.g. `CampusID`), use:

```php
return $this->belongsTo(Campus::class, 'CampusID', 'CampusID');
```

**2. In the controller, load the relationship**

```php
$query = Course::query()->with('campus');
// ... search, sort, etc. ...
return response()->json($query->paginate($perPage));
```

**3. Resulting JSON**

Each course object will have a `campus` key (object or null):

```json
{
  "CourseID": 1,
  "CourseCode": "CS 101",
  "Description": "Intro to Programming",
  "CampusID": 5,
  "campus": {
    "id": 5,
    "name": "Main Campus"
  }
}
```

**4. Frontend (SimpleTable)**

If you want to show campus name in a column, use a column key that matches the nested structure, e.g. `campus.name` (if your SimpleTable supports dot notation), or in the API you can append an attribute (see “Appended attribute” below).

---

## 6. Approach B: Using Query Builder `leftJoin()`

**1. No relationship needed on the model** (you can add one later if you want).

**2. In the controller, build the query with a LEFT JOIN**

Example: join `campuses` so we have a single flat row with `campus_name`.

```php
$query = Course::query()
    ->leftJoin('campuses', 'courses.CampusID', '=', 'campuses.id')
    ->select(
        'courses.*',
        'campuses.name as campus_name'   // or whatever column, e.g. CampusName
    );
// ... search, sort (use courses. column names for sort), paginate ...
return response()->json($query->paginate($perPage));
```

**Important:**

- **Sort:** When you have joins, use the table prefix for the sort column to avoid ambiguity: `$query->orderBy('courses.CourseID', $order)`.
- **Search:** Keep search on `courses` columns unless you add where on joined columns: `$q->where('courses.CourseCode', 'like', ...)`.
- **Paginate:** Laravel will paginate the main table (courses) correctly.

**3. Resulting JSON**

Flat: each course has an extra field `campus_name`:

```json
{
  "CourseID": 1,
  "CourseCode": "CS 101",
  "Description": "Intro to Programming",
  "CampusID": 5,
  "campus_name": "Main Campus"
}
```

**4. Frontend (SimpleTable)**

Add a column with `key: 'campus_name'` and it will display.

---

## 7. Joining Multiple Tables (Same Idea)

Example: Course + Campus + CourseType.

**With relationships:**

```php
$query = Course::query()->with(['campus', 'courseType']);
```

Define on Course model:

```php
public function campus() {
    return $this->belongsTo(Campus::class, 'CampusID', 'id');
}
public function courseType() {
    return $this->belongsTo(CourseType::class, 'CourseTypeID', 'id');
}
```

**With manual joins:**

```php
$query = Course::query()
    ->leftJoin('campuses', 'courses.CampusID', '=', 'campuses.id')
    ->leftJoin('course_types', 'courses.CourseTypeID', '=', 'course_types.id')
    ->select(
        'courses.*',
        'campuses.name as campus_name',
        'course_types.name as course_type_name'
    );
```

Then in search/sort use `courses.` prefix where needed; in the frontend use `campus_name` and `course_type_name` as column keys.

---

## 8. When to Use Which

| Use case | Prefer |
|----------|--------|
| Need full related object in many places (e.g. forms, detail pages) | Eloquent relationship + `with()` |
| Table/list with one or two extra columns from another table | Either; `leftJoin()` + `select(... as alias)` is simple and one query |
| Many joins, complex filters on joined tables | Query Builder `leftJoin()` and explicit `select` |
| Want clean, reusable “Course has one Campus” in code | Eloquent relationship |

---

## 9. Summary Checklist (How You Actually Do It)

1. **Decide** which tables to join and which column links them (e.g. `courses.CampusID` = `campuses.id`).
2. **Relationship approach:**  
   - In the **model**: add `belongsTo(OtherModel::class, 'ForeignColumn', 'OtherTablePrimaryKey')`.  
   - In the **controller**: use `Model::query()->with('relationName')` then search/sort/paginate as usual.  
   - In the **frontend**: use nested keys (e.g. `campus.name`) or add an appended attribute on the model (e.g. `campus_name`) that the API and table can use.
3. **Join approach:**  
   - In the **controller**: `$query->leftJoin('other_table', 'main_table.foreign_key', '=', 'other_table.id')->select('main_table.*', 'other_table.name as other_name')`.  
   - Keep **search/sort** on `main_table` columns (with `main_table.` prefix if needed).  
   - In the **frontend**: add a column with `key: 'other_name'`.
4. **Test:** Hit the API (e.g. `/api/courses`) and check the JSON has the extra or nested data, then add/update columns in the Vue table.

Once you follow this flow (identify link → choose relationship or join → implement in model/controller → expose in JSON → show in frontend), you can apply it to any pair of tables (courses–campuses, courses–course_types, students–curricula, etc.).
