# Guide: Multiple Pages and Components

This guide explains how the **Students page** and **reusable components** are set up so you can add more pages and components the same way.

---

## 1. Overview: What We Added

| Piece | Purpose |
|-------|--------|
| **Vue Router** | Switches between “pages” (Classes vs Students) without reloading the browser. |
| **AppLayout** | Shared layout: top nav (Classes / Students links) + area where the current page is rendered. |
| **Pages** | `ClassesPage.vue` and `StudentsPage.vue` — one Vue component per “page”. |
| **DataTablePage** | Reusable component: title + subtitle + SimpleTable so we don’t repeat the same markup. |
| **SPA fallback route** | Laravel returns the same `app` view for `/`, `/students`, etc., so Vue Router can handle the URL. |

---

## 2. File Structure

```
resources/js/
├── app.js                    # Creates Vue app and uses router
├── App.vue                   # Root: just <router-view />
├── router/
│   └── index.js             # Route definitions (/, /students)
├── components/
│   ├── AppLayout.vue         # Nav + slot for current page
│   └── DataTablePage.vue     # Reusable “page with table” (title + SimpleTable)
└── pages/
    ├── ClassesPage.vue       # Classes “page”
    └── StudentsPage.vue      # Students “page”
```

---

## 3. How Another Page (e.g. Students) Is Wired

### Step 1: Create the page component

**File:** `resources/js/pages/StudentsPage.vue`

- It’s a normal Vue component.
- It uses the shared **DataTablePage** and passes:
  - `title`, `subtitle`
  - `fetch-url="/api/students"`
  - `columns` (student columns)
  - `page-sizes`, `per-page`

So “another page” = one new file under `pages/` that composes your existing components.

### Step 2: Register the route

**File:** `resources/js/router/index.js`

- Import the new page: `import StudentsPage from '../pages/StudentsPage.vue';`
- Add a **child route** under the layout:

```js
children: [
  { path: '', name: 'classes', component: ClassesPage },
  { path: 'students', name: 'students', component: StudentsPage },
],
```

- Path `students` → URL `/students`, and the layout stays the same (nav + content area).

### Step 3: Laravel must serve the app for that URL

**File:** `routes/web.php`

- API routes (`/api/classes`, `/api/students`) are defined first.
- Then a **fallback** route sends every other GET request to the same Blade view that boots Vue:

```php
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
```

So when someone opens `http://localhost:8000/students`, Laravel returns the Vue app; Vue Router then shows the Students page.

---

## 4. How the Layout and Router Work Together

- **App.vue**  
  - Only contains `<router-view />`.  
  - Whatever route matches is rendered here (the layout for our app is the first matched route).

- **Router**  
  - Route `/` uses `AppLayout` and has **children**: `''` (Classes) and `'students'` (Students).  
  - So the structure is: **AppLayout** wraps a **second** `<router-view />` (in `AppLayout.vue`) where the child (ClassesPage or StudentsPage) is rendered.

- **AppLayout.vue**  
  - Renders the **nav** (Classes / Students links using `<router-link>`).  
  - Renders **`<router-view />`** in the main area.  
  - That inner `<router-view />` is where `ClassesPage` or `StudentsPage` appears.

So:

1. User goes to `/` or `/students`.
2. Laravel serves the app (thanks to the fallback route).
3. Vue Router matches the path → AppLayout + the right child (ClassesPage or StudentsPage).
4. AppLayout shows the nav and the current page in the main area.

---

## 5. When and How to Make a Component

**When it makes sense:**

- The same **structure or behavior** is used in more than one place (e.g. “page with title + table” for both Classes and Students).
- You want a clearer **structure** (e.g. a dedicated “layout” or “page” component).

**How we used it here:**

- **AppLayout**  
  - Used once, but it’s the shared shell (nav + main area). So it’s a component to keep the root simple and to have one place to change the nav.

- **DataTablePage**  
  - Used by both Classes and Students: same “title + subtitle + SimpleTable” pattern, different `title`, `fetch-url`, and `columns`. So we extracted that into a **reusable component** that receives props.

**How to add another similar page (e.g. “Instructors”):**

1. Add **InstructorsPage.vue** in `pages/` (use `DataTablePage` with `fetch-url="/api/instructors"` and the right columns).
2. In **router/index.js**, add a child: `{ path: 'instructors', name: 'instructors', component: InstructorsPage }`.
3. In **AppLayout.vue**, add a `<router-link to="/instructors">Instructors</router-link>` in the nav.
4. On the backend, add the Laravel route and controller for `/api/instructors` (same idea as students).

No new “component” is required unless you see repeated UI or logic again; then extract that into a component and use it from the page.

---

## 6. Checklist: Adding a New Page

- [ ] Create **`resources/js/pages/YourPage.vue`** (e.g. use `DataTablePage` or your own markup).
- [ ] In **`resources/js/router/index.js`**: import the page and add a child route (e.g. `path: 'your-path', name: 'your-name', component: YourPage`).
- [ ] In **`resources/js/components/AppLayout.vue`**: add a `<router-link to="/your-path">Your Label</router-link>` in the nav.
- [ ] If the page needs data from the backend: add a Laravel route (e.g. `Route::get('/api/your-resource', ...)`) and use that URL in your page/component (e.g. `fetch-url="/api/your-resource"`).

After that, visiting `/your-path` will show your new page with the shared layout and nav.
