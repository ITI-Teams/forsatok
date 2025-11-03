# Laravel Job Board – Nov 2025

**web-based Job Board system** built with **Laravel 12**, enabling employers to post jobs, admins to review and approve them, and candidates to search and apply for job listings easily due to their specialities.

---

## Features Overview

### Employers
- Register and manage their profile. 
- Create Users accounts 
- Post job listings with details such as:
  - Job title, description, skills, and qualifications.
  - Salary range, benefits, category, and location.
  - Work type wherther be (Remote / On-site / Hybrid).
  - Technologies used and application deadline.
- Edit and delete job listings.  
- Review and respond to applications with Accept / Reject.  
- View basic analytics.  
- Comment on job posts.
- contact candidate if they are accepted directly throw the info provided.
- Receive notifications if the posted job is accepted or rejected and the ability to show more details of that single notification *the whole msg* 

### Candidates
- Register and manage their profile also the ability to delete their account.  
- Search and filter jobs by:
  - Keyword, category, salary, experience level, and location.
- Apply for jobs by:
  - Uploading a resume.
  - Providing contact details.
  - Ability of LinkedIn integration for auto-filling forms instead of doing it manual.
- Manage applications with Apply / Cancel.  
- Receive notifications on application status if it's accepted or rejected and the ability to show more details of that single notification *the whole msg* .

### Admins
- Approve or reject job posts submitted by employers.  
- ability of creating and removing mails
- Manage users (Employers & Candidates).  
- Monitor overall system activity.  
- Remove inappropriate comments.

---

## System Modules

| Module | Description |
|--------|-------------|
| **Authentication** | Laravel Breeze-based login/registration for Employers, Candidates, and Admins. |
| **Job Listings** | Employers can create, edit, and manage job posts. |
| **Applications** | Candidates can apply for jobs , upload resumes filter job search. |
| **Admin Panel** | Manage users and job approvals / full control of the site. |
| **Search & Filters** | Dynamic filters for finding relevant job listings. |
| **Notifications** | Email or dashboard notifications for job status. |
| **Analytics** | Employer-side analytics on job views and applications. |

---

## Technical Stack

| Component | Technology |
|------------|-------------|
| **Framework** | Laravel 12+ / Angular 20|
| **Frontend** | Bootstrap 5 / Tailwind CSS /Flowbite /PrimeNg |
| **Role-based** | Spatie permission|
| **Notification** | Pusher|
| **Authentication** | Laravel Breeze |
| **Database** | MySQL  |
| **Version Control** | Git & GitHub |
| **Optional Integration** | LinkedIn API for profile autofill |

---

## Eloquent Relationships Used

- **User Roles** → `Admin`, `Employer`, `Candidate` (Role-based access)  
- **Job ↔ Employer** → One-to-Many  
- **Application ↔ Job ↔ Candidate** → Many-to-Many (with pivot). 
- **Comments** → Polymorphic Relation.  

---

## UI / UX
- Responsive layout with **Bootstrap 5** and **Tailwind CSS**.
- Clean color palette and easy navigation.

