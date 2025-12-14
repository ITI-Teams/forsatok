# Dashboard API Reference

All endpoints live under the `/api/dashboard` prefix. Unless stated otherwise they require a valid Sanctum token that belongs to an `admin` or `employer` user and will enforce the same role/permission checks used in the Blade dashboards.

Use the new authentication endpoints to create or log in an admin/employer user, then send the returned bearer token in the `Authorization: Bearer {token}` header for every protected request.

## Authentication

| Method | Path | Body | Response |
| --- | --- | --- | --- |
| `POST` | `/api/dashboard/auth/register` | `name`, `email`, `password`, `password_confirmation`, `type` (`admin` or `employer`) | `201` with `{ status, message, data: { user, token } }` |
| `POST` | `/api/dashboard/auth/login` | `email`, `password` | `200` with `{ status, message, data: { user, token } }` |
| `POST` | `/api/dashboard/auth/logout` | _none_ (requires token) | `200` with `{ status, message }` |
| `POST` | `/api/dashboard/auth/forgot-password` | `email` | `200` on success, otherwise 404/500 with `message`. |
| `POST` | `/api/dashboard/auth/reset-password` | `email`, `token`, `password` | `200` with `{ status, message }` once the password is reset. |

## Profile & Notifications

| Method | Path | Description |
| --- | --- | --- |
| `GET` | `/api/dashboard/profile` | Returns the authenticated user, attached roles, and permissions. |
| `GET` | `/api/dashboard/notifications` | Paginated notifications for the current user. |
| `GET` | `/api/dashboard/notifications/unread` | Only unread notifications. |
| `POST` | `/api/dashboard/notifications/{id}/read` | Mark a single notification as read. |
| `POST` | `/api/dashboard/notifications/read-all` | Mark every notification as read. |
| `DELETE` | `/api/dashboard/notifications/{id}` | Delete one notification. |
| `DELETE` | `/api/dashboard/notifications` | Delete all notifications. |

## Admin Analytics & Audit Log

| Method | Path | Description |
| --- | --- | --- |
| `GET` | `/api/dashboard/stats/admin` | Dashboard KPIs, recent users, and recent audit log entries. |
| `GET` | `/api/dashboard/audit-logs` | Paginated audit logs with user info. |

## Catalog Management (Admin)

### Categories

| Method | Path | Body |
| --- | --- | --- |
| `GET` | `/api/dashboard/categories` | Pagination, `search`, `fields[]`. |
| `POST` | `/api/dashboard/categories` | `name`. |
| `PUT` | `/api/dashboard/categories/{category}` | `name`. |
| `DELETE` | `/api/dashboard/categories/{category}` | _soft delete_. |
| `GET` | `/api/dashboard/categories/trashed` | View soft deleted records. |
| `POST` | `/api/dashboard/categories/{id}/restore` | Restore from trash. |
| `DELETE` | `/api/dashboard/categories/{id}/force` | Permanently delete. |

### Skills

Same pattern as categories with `/skills` prefix (`name`, `category_id`).

### Countries & Cities

Identical CRUD + trash flows under `/countries` and `/cities`. Provide `name`, `code` (countries) or `name`, `country_id` (cities).

## User, Role, and Permission Admin

| Method | Path | Description |
| --- | --- | --- |
| `GET/POST/PUT/DELETE` | `/api/dashboard/users[...]` | Full CRUD + trash/restore/force for admin/employer/candidate records. Assign roles/permissions via request payload arrays. |
| `GET/POST/PUT/DELETE` | `/api/dashboard/roles[...]` | Manage Spatie roles. |
| `GET/POST/PUT/DELETE` | `/api/dashboard/permissions[...]` | Manage permissions. |
| `GET/POST` | `/api/dashboard/role-permissions` | Fetch or sync permissions for a role. |
| `GET/POST` | `/api/dashboard/user-access` | Inspect or append roles/permissions for a user (mirrors the Livewire UI). |

## Job Management (Admin & Employer)

Routes live under `/api/dashboard/jobs`:

- `POST /` (permission `jobs.manage`): create via `StoreJobRequest` fields.
- `PUT /{job}` (permission `jobs.manage`): update via `UpdateJobRequest` fields.
- `DELETE /{job}`: soft delete.
- `GET /trashed`, `POST /{id}/restore`, `DELETE /{id}/force`: trash controls.
- `POST /{job}/approve` / `POST /{job}/reject`: toggle moderation state.
- `GET /` (permission `jobs.view`): paginated listing with optional `search` and `fields`. Employers are auto-filtered to their own jobs.
- `GET /{job}`: show including relations.

## Employer Dashboard Extras

| Method | Path | Description |
| --- | --- | --- |
| `GET` | `/api/dashboard/stats/employer` | KPI cards, charts, and top jobs for current employer. |
| `GET` | `/api/dashboard/applications` | Employer-scoped paginated applications with filtering. |
| `POST` | `/api/dashboard/applications` | Create a new application (enforces uniqueness per candidate/job). Supports file upload as `resume`. |
| `GET/PUT/DELETE` | `/api/dashboard/applications/{application}` | Show, update, soft delete. |
| `GET` | `/api/dashboard/applications/trashed` | Employer-only trash. |
| `POST` | `/api/dashboard/applications/{id}/restore` | Restore. |
| `DELETE` | `/api/dashboard/applications/{id}/force` | Force delete + resume cleanup. |
| `DELETE` | `/api/dashboard/applications/trash/empty` | Empty employer trash. |
| `GET` | `/api/dashboard/company-reviews` | Employer reviews with search. |
| `POST` | `/api/dashboard/company-reviews/{review}/approve` | Approve review (sends notification). |
| `POST` | `/api/dashboard/company-reviews/{review}/reject` | Reject review. |
| `DELETE` | `/api/dashboard/company-reviews/{review}` | Soft delete. |
| `GET` | `/api/dashboard/company-reviews/trashed` | View trash. |
| `POST` | `/api/dashboard/company-reviews/{id}/restore` | Restore. |
| `DELETE` | `/api/dashboard/company-reviews/{id}/force` | Force delete. |
| `GET` | `/api/dashboard/employer/profile` | Current employer info + location. |
| `PUT` | `/api/dashboard/employer/profile` | Update employer info, email, avatar, and location fields. |

## Shared Contact Routes

| Method | Path | Description |
| --- | --- | --- |
| `GET` | `/api/dashboard/contact-messages` | Admin/employer view of contact messages with optional `search`. |
| `DELETE` | `/api/dashboard/contact-messages/{message}` | Admin only hard delete (admin routes section). |

## Testing with Postman

1. Import `backend/postman/dashboard-api.postman_collection.json` into Postman.
2. Create an environment with a `base_url` variable (e.g. `http://localhost:8000`) and a `token` variable.
3. Hit `Dashboard Auth / Register` or `Dashboard Auth / Login` to obtain a token. Copy the token into the `token` variable.
4. Every protected request in the collection already sends the header `Authorization: Bearer {{token}}` and `Accept: application/json`.
5. Adjust request bodies as needed (sample payloads are included in the collection). For file uploads (applications, employer avatar) switch Postman to `form-data` and attach files.
6. For resources that support pagination or search, include the documented query parameters (`per_page`, `search`, `fields[]`).

Typical response envelope:

```json
{
  "status": true,
  "message": "Optional human readable message",
  "data": { "...": "resource payload" },
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 10,
    "total": 25
  }
}
```

Errors follow standard Laravel JSON validation errors or `{ "status": false, "message": "..." }`.
