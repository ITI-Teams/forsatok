# Google & External Auth Integration Guide

This guide explains how to integrate any frontend application (Angular, React, Vue) with the Universal Google Auth system built in the backend.

## 1. Login Entry Point
To initiate the OAuth flow, redirect the user to:
`GET: {BACKEND_URL}/auth/google?source={SOURCE}&type={TYPE}`

### Parameters:
*   **`source`**: (Required) Identifies the requesting application. Available values: `jobhub` (Candidates/Angular), `hive` (Employers/React), `livewire` (Admin Dashboard).
*   **`type`**: (Required) The user role. Available values: `candidate` or `employer`.

**Example (Angular/React):**
```typescript
const url = "http://localhost:8000/auth/google?source=jobhub&type=candidate";
window.location.href = url;
```

---

## 2. Handling the Callback
After successful authentication, the backend automatically redirects the user to your frontend callback page:
`{FRONTEND_URL}/auth/callback`

### Query Parameters Received:
*   **`token`**: The Sanctum PlainTextToken for subsequent API requests.
*   **`user`**: A URL-encoded JSON string containing user details.
*   **`error` / `message`**: Present if the authentication failed.

### Typical Logic (React/Angular):
1. Capture parameters from the URL.
2. Decode and parse the user data: `JSON.parse(decodeURIComponent(user))`.
3. Store the `token` and `user` object in `localStorage`.
4. Redirect the user to the initial page (Home/Dashboard).

---

## 3. Profile Picture (Avatar)
The system returns an `avatar` field within the user object. This is a "Smart Accessor":
*   **Google Users**: Contains the direct Google profile image URL.
*   **Local Users**: Contains the full URL pointing to your server's storage.
*   **Default**: Returns a default avatar URL if no image is set.

**Frontend Tip:** Use the `onerror` attribute on your `<img>` tag to provide a fallback image in case of loading failures.

---

## 4. Instant Activation (Auto-Verification)
Google-authenticated users are marked as `verified` immediately. You don't need to handle email verification flows for these users.

## 5. Requirements for Hive (React)
To set up the Hive application:
1. Create a route in React Router for `/auth/callback`.
2. Implement the storage logic mentioned in Step 2 within this route.

