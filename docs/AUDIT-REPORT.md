# AUDIT REPORT — IT SERVICE DESK
**Disusun oleh:** Kiro AI (Senior Software Architect + Senior Laravel Developer + UI/UX Engineer + Business Analyst)  
**Tanggal:** 7 November 2026  
**Versi PRD:** v1.0  
**Status:** Draft untuk Review  

---

## DAFTAR ISI

1. [Ringkasan Eksekutif](#1-ringkasan-eksekutif)
2. [Analisis PRD](#2-analisis-prd)
3. [Analisis Desain Stitch](#3-analisis-desain-stitch)
4. [Cross Analysis: PRD vs Desain](#4-cross-analysis-prd-vs-desain)
5. [Arsitektur Laravel](#5-arsitektur-laravel)
6. [Implementation Plan](#6-implementation-plan)
7. [Risiko & Rekomendasi](#7-risiko--rekomendasi)

---

## 1. RINGKASAN EKSEKUTIF

| Item | Detail |
|------|--------|
| Proyek | IT Service Desk |
| Framework | Laravel 12, PHP 8.3 |
| Database | MySQL |
| Frontend | Blade + Tailwind CSS + Vite |
| PRD | Terbaca lengkap (10 halaman, v1.0) |
| Desain Stitch | 26 screen/komponen terbaca lengkap |
| Kesesuaian PRD–Desain | **Tinggi** — hampir semua fitur utama PRD memiliki desain |
| Fitur PRD tanpa desain | 3 item minor |
| Fitur desain tanpa PRD | 3 item enterprise (di luar scope MVP) |
| Rekomendasi | Lanjut implementasi dengan catatan di Bagian 7 |

---

## 2. ANALISIS PRD

### 2.1 Tujuan Aplikasi

IT Service Desk adalah platform berbasis web untuk mengelola permintaan layanan TI dan insiden secara terstruktur. Sistem memusatkan seluruh permintaan TI dalam satu kanal, mempercepat respons melalui assignment & SLA, menyediakan audit trail, menghubungkan tiket dengan aset TI, serta menyediakan laporan operasional untuk IT Manager.

### 2.2 User Roles & Permissions

| Role | Akses Inti |
|------|------------|
| **Employee / User** | Create ticket, track ticket, reply, feedback, knowledge base |
| **Technician** | Assigned tickets, update status, notes, resolution, assets |
| **IT Manager** | Dashboard, SLA, reports, workload, approval |
| **Admin** | Users, roles, categories, SLA, services, assets, settings |
| **Approver** | Review, approve/reject, comment pada service request |

### 2.3 Modul Aplikasi (dari PRD)

| No | Modul | Prioritas |
|----|-------|-----------|
| 5.1 | Authentication & User Management | Must (MVP) |
| 5.2 | Dashboard (per role) | Must (MVP) |
| 5.3 | Ticket Management | Must (MVP) |
| 5.4 | SLA Management | Must (MVP) |
| 5.5 | Service Catalog & Approval | Should |
| 5.6 | Asset Management | Should |
| 5.7 | Knowledge Base | Should |
| 5.8 | Notification (in-app) | Must (MVP) |
| 5.9 | Feedback & Reporting | Should |
| 5.10 | Audit Log | Must (MVP) |

### 2.4 Workflow Tiket

```
New → Assigned → In Progress → Pending → Resolved → Closed
                                               ↕
                                           Reopened
```

- **New**: Tiket baru dibuat oleh user
- **Assigned**: IT melakukan triage dan assign technician
- **In Progress**: Technician sedang mengerjakan
- **Pending**: Menunggu informasi, user, vendor, atau dependensi
- **Resolved**: Technician mengisi resolusi
- **Closed**: User mengonfirmasi selesai atau auto-close
- **Reopened**: User menilai masalah belum selesai

### 2.5 Status Tiket

`New` | `Assigned` | `In Progress` | `Pending` | `Resolved` | `Closed` | `Reopened`

### 2.6 Prioritas & SLA Baseline

| Priority | Target Response | Target Resolution | Contoh |
|----------|----------------|-------------------|--------|
| Critical | 15 menit | 2 jam | Layanan/sistem kritis down |
| High | 30 menit | 4 jam | Gangguan pekerjaan penting |
| Medium | 2 jam | 8 jam | Gangguan satu/sebagian user |
| Low | 4 jam | 24 jam | Request atau gangguan minor |

> Catatan: angka SLA dapat dikonfigurasi Admin sesuai kebijakan perusahaan.

### 2.7 Functional Requirements

| ID | Requirement | Prioritas | Acceptance Criteria |
|----|-------------|-----------|---------------------|
| FR-01 | Authentication | Must | Login/logout; akses sesuai role |
| FR-02 | Create Ticket | Must | Form valid; sistem hasilkan Ticket ID |
| FR-03 | Ticket Tracking | Must | User lihat status & timeline tiket sendiri |
| FR-04 | Assignment | Must | IT assign/reassign; tercatat di history |
| FR-05 | Status Workflow | Must | Status hanya berubah melalui alur izin |
| FR-06 | Comments | Must | User/technician diskusi sesuai visibility |
| FR-07 | Attachment | Must | File divalidasi ukuran & tipe sebelum simpan |
| FR-08 | SLA | Must | Deadline otomatis, breach terdeteksi |
| FR-09 | Dashboard | Must | Metric sesuai role, dapat difilter |
| FR-10 | Service Catalog | Should | User buat service request |
| FR-11 | Approval | Should | Approver approve/reject dengan alasan |
| FR-12 | Asset | Should | Asset dikaitkan ticket & user |
| FR-13 | Knowledge Base | Should | Pencarian artikel + detail |
| FR-14 | Feedback | Should | Rating setelah resolution |
| FR-15 | Audit Log | Must | Aktivitas penting tercatat actor + timestamp |
| FR-16 | Reporting | Should | Manager buat laporan dengan filter |

### 2.8 Non-Functional Requirements

- **Security**: Password hashing, CSRF, authorization/policy, validation, secure upload, rate limiting, session security
- **Performance**: Pagination, indexing, eager loading, query terukur
- **Availability**: Backup database & storage berkala
- **Usability**: Responsive desktop/tablet/mobile; navigasi konsisten; error/success feedback jelas
- **Maintainability**: MVC Laravel, service/repository layer, naming convention konsisten
- **Scalability**: Queue, email, API, multi-department, integrasi eksternal
- **Auditability**: Perubahan tiket, approval, assignment, konfigurasi dapat ditelusuri

### 2.9 Entitas Database (dari PRD)

```
users, roles, permissions, departments, locations,
tickets, ticket_comments, ticket_attachments, ticket_histories, ticket_assignments,
categories, subcategories, priorities, sla_policies,
services, service_requests, approvals,
assets, asset_categories, asset_assignments, asset_maintenance,
knowledge_articles, notifications, feedbacks, audit_logs
```

### 2.10 Menu Navigasi (dari PRD)

| Employee | Technician | Manager/Admin |
|----------|------------|---------------|
| Dashboard | Dashboard | Dashboard |
| My Tickets | My Tickets | All Tickets |
| Create Ticket | Assigned Tickets | SLA Monitoring |
| Service Catalog | Knowledge Base | Reports |
| Knowledge Base | Assets | Assets |
| Profile | Profile | Users & Roles |
| — | — | Categories / Services / SLA / Settings |

### 2.11 Scope MVP

**Termasuk MVP:**
- Authentication + role
- Dashboard dasar
- Ticket CRUD & lifecycle
- Category, priority, assignment
- Comment, attachment, history
- SLA dasar
- Notifikasi in-app dasar
- Reporting dasar

**Di luar MVP:**
- WhatsApp integration
- AI ticket classification
- Advanced predictive analytics
- Complex multi-level approval
- Mobile native application
- External ITSM integrations

### 2.12 KPI Produk

- First Response Time (FRT)
- Average Resolution Time (ART)
- SLA Compliance Rate
- Ticket Backlog
- First Contact Resolution (FCR)
- Reopen Rate
- Customer Satisfaction (CSAT)
- Ticket volume per category/department/period
- Technician workload & resolution rate

### 2.13 Acceptance Criteria MVP

1. User login sesuai role; tidak bisa akses menu di luar izin
2. User buat ticket dengan validasi; memperoleh Ticket ID unik
3. Technician lihat ticket assigned; ubah status sesuai workflow
4. Assignment, komentar, status change, priority change tercatat di history
5. SLA dihitung dari aturan aktif; ticket overdue teridentifikasi
6. User lihat perkembangan ticket miliknya sampai closed
7. Manager lihat statistik dasar dan filter laporan
8. Data ticket konsisten di MySQL; tidak hilang setelah refresh/logout
9. Attachment disimpan aman; hanya bisa diakses sesuai authorization
10. Test utama auth, authorization, ticket lifecycle, SLA, validation lulus

---

## 3. ANALISIS DESAIN STITCH

### 3.1 Inventaris Halaman Desain

| No | Folder | Nama Halaman | Deskripsi |
|----|--------|--------------|-----------|
| 1 | `apex_service_desk` | Design System | Visual foundation & design token |
| 2 | `it_service_desk_enterprise_sign_in` | Sign In | Halaman login enterprise |
| 3 | `it_service_desk_employee_dashboard` | Employee Dashboard | Dashboard karyawan |
| 4 | `it_service_desk_technician_dashboard` | Technician Dashboard | Dashboard teknisi |
| 5 | `it_manager_dashboard` | IT Manager Dashboard | Dashboard manager |
| 6 | `it_service_desk_my_tickets` | My Tickets | Daftar tiket milik user |
| 7 | `it_service_desk_all_tickets` | All Tickets | Daftar semua tiket (admin/manager) |
| 8 | `create_ticket` | Create New Ticket | Form buat tiket baru |
| 9 | `it_service_desk_ticket_detail` | Ticket Detail | Detail tiket + SLA + komentar |
| 10 | `it_service_desk_assign_technician_modal` | Assign Technician Modal | Modal assign teknisi ke tiket |
| 11 | `it_service_desk_service_catalog` | Service Catalog | Katalog layanan |
| 12 | `it_service_desk_service_request_form` | Service Request Form | Form request layanan |
| 13 | `it_service_desk_approval_management` | Pending Approvals | Manajemen approval |
| 14 | `it_service_desk_sla_monitoring` | SLA Monitoring | Monitoring SLA |
| 15 | `it_service_desk_it_assets` | IT Assets | Daftar aset IT |
| 16 | `it_service_desk_asset_detail` | Asset Detail | Detail aset + riwayat |
| 17 | `it_service_desk_knowledge_base` | Knowledge Base | Halaman KB + pencarian |
| 18 | `it_service_desk_knowledge_article_detail` | Knowledge Article Detail | Detail artikel KB |
| 19 | `it_service_desk_reports` | Reports | Laporan & statistik |
| 20 | `notification_center` | Notification Center | Pusat notifikasi |
| 21 | `audit_log` | Audit Log | Log aktivitas sistem |
| 22 | `it_service_desk_admin_users_roles` | Users & Access Control | Manajemen user & role |
| 23 | `it_service_desk_admin_categories` | Categories & Taxonomies | Manajemen kategori |
| 24 | `it_service_desk_admin_sla_policies` | SLA Policies | Konfigurasi SLA |
| 25 | `it_service_desk_admin_services` | Service Catalog Management | Manajemen service catalog |
| 26 | `it_service_desk_user_profile_settings` | Account Settings & Profile | Profil & pengaturan user |
| 27 | `it_service_desk_visual_foundation_ui_specification` | UI Specification | Spesifikasi UI & komponen |

**Total: 27 screens** (termasuk 1 design system doc + 1 UI spec)

### 3.2 Visual Foundation & Design System

**Nama Design System:** Apex Service Desk  
**Tema:** Dark Navy Enterprise — "Cockpit-inspired" untuk IT Operations  

#### Palet Warna Utama

| Token | Hex/RGBA | Penggunaan |
|-------|----------|------------|
| `app-bg` | `#0B1220` | Background utama viewport |
| `sidebar-surface` | `#10182A` | Background sidebar navigasi |
| `card-surface` | `#121C31` | Background card & widget |
| `card-elevated` | `#16233F` | Card hover / elevated state |
| `input-surface` | `#0F1930` | Background form input |
| `primary-container` | `#2563EB` | Tombol primary, active nav |
| `accent-electric` | `#4F8CFF` | Highlights, active indicator |
| `text-primary` | `#F8FAFC` | Teks utama |
| `text-secondary` | `#94A3B8` | Teks sekunder, label kolom |
| `text-tertiary` | `#64748B` | Hint, placeholder |
| `status-success` | `#22C55E` | Resolved, healthy |
| `status-warning` | `#F59E0B` | SLA warning, at-risk |
| `status-danger` | `#EF4444` | SLA breach, critical/P1 |
| `status-info` | `#38BDF8` | In progress, assigned |
| `status-pending` | `#A78BFA` | Pending, deferred |

#### Tipografi

- **Font Family:** Inter (single typeface)
- **Scale:** headline-xl (36px/700) → headline-lg (22px/600) → headline-md (18px/600) → headline-sm (15px/600) → body-lg (15px/400) → body-md (14px/400) → body-sm (13px/400) → label-md (13px/600) → label-sm (12px/500) → caption (11px/400)

#### Spacing & Layout

- Sistem grid 4px (4, 8, 12, 16, 20, 24, 32, 40, 48, 64)
- Sidebar: 240–260px lebar, fixed left
- Responsive breakpoints: Mobile (<768px), Tablet (768–1023px), Desktop (1024px+)

#### Border Radius

| Token | Value | Penggunaan |
|-------|-------|------------|
| `full` | 9999px | Status badges, SLA pills |
| `xl` | 1.5rem / 24px | Modal, drawer |
| `lg` | 1rem / 16px | Card container |
| `md` | 0.75rem / 12px | Input, button |
| `sm` | 0.25rem | Micro elements |

#### Komponen Utama Teridentifikasi

- **Buttons**: Primary (blue), Secondary/Ghost, Danger
- **Form Inputs**: Height 40px, rounded-xl, dark input-surface
- **Status Chips/Badges**: Pill shape, color-coded per status
- **Cards & Tables**: Rounded-lg, dark surface, hover elevation
- **SLA Progress Bar**: Animated pulse on breach/warning
- **Modals**: Glassmorphism backdrop blur, elevation level 3
- **Sidebar Nav**: Active state dengan electric blue glow
- **Checkboxes**: 18px, custom styled
- **Data Tables**: Hairline borders, hover row highlight

### 3.3 Komponen Sidebar Navigasi

Berdasarkan analisis HTML desain:

**Sidebar Umum (semua role):**
- Logo "Apex Desk" + subtitle "Tier 1-3 Operations"
- Dashboard
- My Tickets
- All Tickets
- Create Ticket
- Notification Center
- Service Catalog
- Assets
- Knowledge Base
- Reports
- Profile / Settings

**Khusus Manager:**
- Manager Dashboard (sub-item)
- SLA Monitoring
- Users & Access Control *(admin section)*
- Categories *(admin section)*
- SLA Policies *(admin section)*
- Service Catalog Management *(admin section)*
- Audit Log *(admin section)*

### 3.4 Detail Konten per Halaman Kunci

#### Sign In
- Header dengan status "OPERATIONAL" + latency indicator
- Form: Work Email + Password dengan toggle visibility
- SSO: Okta Verify + Microsoft Entra ID
- Checkbox "Remember device 30d" + Forgot password
- Compliance badges: 256-Bit TLS, SOC-2 Type II, Zero Trust

#### Employee Dashboard
- Welcome section dengan quick stats
- Stat cards: Open, In Progress, Resolved, Closed
- Recent Tickets list dengan status badges

#### IT Manager Dashboard
- Metric cards: Total Tickets (1,248), SLA Compliance (97.4%), Avg Resolution (3h 42m), CSAT (4.8)
- Charts: Ticket Volume & Resolution, Technician Allocation, Tickets by Category, Tickets by Priority
- Tables: High-Impact Active Incidents (P1 & P2), SLA Breached Tickets, Urgent SLA Alerts
- Panels: Core Service Health, Top Demand Vectors

#### Technician Dashboard
- SLA Urgent Alerts panel
- Workload indicator
- Assigned tickets queue

#### All Tickets
- Header dengan "Smart SLA Load Router"
- Filter: status, priority, category, technician, date range
- Table dengan kolom: Ticket ID, Title, Status, Priority, Assignee, SLA, Updated
- Status badges: Active, Breached, Healthy, Escalated L3

#### Create Ticket
- Section: "Incident Specifications"
- Fields: Title, Category, Sub-category, Priority, Urgency, Impact Scope, Description, Affected Asset, Physical Location, Preferred Contact, Attachments
- Warning/impact fields

#### Ticket Detail
- Header: Ticket ID, Title, Priority badge, Status badge
- SLA countdown timer (prominent)
- Sections: Incident Properties, SLA Targets & Compliance, Incident Command & War Room, Related Records & Runbooks
- Timeline/activity feed
- Comment thread (public reply + internal note)
- Assign Technician button → modal

#### SLA Monitoring
- Metric: SLA Compliance (96.4%), Avg Response (6m 42s), Avg Resolution (1h 48m)
- SLA Tier Policy Matrix table
- War Room Bridge panel
- Per-ticket SLA countdown

#### Reports
- Metric cards: Total Tickets (3,842), SLA Rate (96.4%), Avg Response (14m 20s), CSAT (4.86)
- Charts: Ticket Trend (Inflow vs Resolution), Ticket by Category, Ticket by Priority & SLA Adherence
- Table: Technician Performance Matrix, Technician Performance Breakdown
- Export options: PDF, CSV, PPTX

#### Notification Center
- Categories: SLA Breached, SLA Warning, Approval Required, Ticket Assigned, New Comment, Status Changed, Request Approved, Ticket Resolved, Request Rejected
- Urgency grouping
- Panels: Urgent SLA Watchlist, Dispatch Channels, Weekly Alert Volume

#### Audit Log
- Header stats: 148,291 total events, 4,120 (filtered)
- Label: "Continuous Immutable Audit Trail"
- Table: Timestamp, Actor, Action, Entity, Data Before/After, IP

#### Users & Access Control
- Role badges: Admin, IT Manager, Technician, Employee, Approver
- User count: 1,402
- Table: Name, Email, Role, Department, Status, Last Active
- "Create Enterprise User" button

#### Categories & Taxonomies
- Stats: 12 Categories, 3,842/mo tickets
- Category cards: Network & Connectivity, Workplace Hardware, Software & Dev Tooling, Identity & SecOps IAM, Enterprise Apps & ERP, Telephony & A/V Systems, Legacy On-Premise Printing
- Subcategory management per category
- Create/Edit Category form

#### SLA Policies
- Policies: P1 Critical, P2 High, P3 Standard, P4 Routine, VIP Executive, Off-Premises Hardware
- Per policy: Response time, Resolution time, Business hours, Escalation rules

#### IT Assets
- Stats: 1,248 total, 1,082 active
- Sections: Fleet Inventory Register, Fleet Distribution, Warranty Watchlist, IT Depot Operations
- Asset table dengan kolom: Asset ID, Name, Category, Assigned To, Status, Warranty

#### Asset Detail
- Header: Asset name, model, OS, compliance status
- Sections: Asset Specifications & Procurement, Related Incidents & Service Requests, Maintenance & Diagnostic History, Custody Lifecycle & Custodian Audit
- Current Custodian info, Lifecycle & Warranty dates

#### Knowledge Base
- Search bar prominent
- Category filter: Network, Hardware, Software, Account, Security, Applications
- Stats: 4.2 min avg read, 97.8% resolution rate, 82.4% helpful
- Popular Articles list
- "Still can't find?" CTA → Create Ticket

#### Knowledge Article Detail
- Article title, category, author, date, read time
- Content sections: Symptom & Root Cause Analysis, Step-by-Step Resolution Guide
- Helpful/Not Helpful rating
- Related articles panel

#### Approval Management
- Pending Approvals list
- Approval cards: Title, requester, cost, urgency
- Actions: Approve / Reject
- Reject modal dengan alasan text
- Examples: Developer Laptop Provisioning, Production DB Access, AWS Spend Expansion

#### User Profile Settings
- Sections: Profile Information, Change Password, Notification Channels, Appearance & Display, Security & Active Sessions, Authenticated Endpoints

#### Service Catalog
- Grid layout layanan dengan icon & description
- Kategori layanan
- "Request" CTA per item
- SLA info per layanan

#### Service Request Form
- Dynamic form sesuai jenis layanan
- Example: Hardware Provisioning → fields: Department, Keyboard Layout, Peripherals Bundle

---

## 4. CROSS ANALYSIS: PRD VS DESAIN

### 4.1 Tabel Kesesuaian Fitur

| Fitur PRD | ID FR | Halaman Desain | Status | Catatan |
|-----------|-------|----------------|--------|---------|
| Authentication / Login | FR-01 | `enterprise_sign_in` | ✅ Lengkap | Desain lengkap dengan SSO, form, alert |
| Employee Dashboard | FR-09 | `employee_dashboard` | ✅ Lengkap | Stats + recent tickets tersedia |
| Technician Dashboard | FR-09 | `technician_dashboard` | ✅ Lengkap | SLA alerts + workload |
| IT Manager Dashboard | FR-09 | `it_manager_dashboard` | ✅ Lengkap | Semua KPI + charts tersedia |
| Create Ticket | FR-02 | `create_ticket` | ✅ Lengkap | Form fields lengkap |
| My Tickets | FR-03 | `my_tickets` | ✅ Lengkap | List dengan filter |
| All Tickets | FR-03 | `all_tickets` | ✅ Lengkap | Smart SLA + filter |
| Ticket Detail | FR-03, FR-05, FR-06 | `ticket_detail` | ✅ Lengkap | Timeline, komentar, SLA |
| Assign Technician | FR-04 | `assign_technician_modal` | ✅ Lengkap | Modal dengan SLA info |
| SLA Management | FR-08 | `sla_monitoring`, `admin_sla_policies` | ✅ Lengkap | Monitor + konfigurasi |
| Service Catalog | FR-10 | `service_catalog`, `service_request_form` | ✅ Lengkap | Grid + dynamic form |
| Approval Workflow | FR-11 | `approval_management` | ✅ Lengkap | List + approve/reject modal |
| Asset Management | FR-12 | `it_assets`, `asset_detail` | ✅ Lengkap | List + detail + history |
| Knowledge Base | FR-13 | `knowledge_base`, `knowledge_article_detail` | ✅ Lengkap | Search + kategori + artikel |
| Notification | FR-08, FR-11 | `notification_center` | ✅ Lengkap | Semua tipe notifikasi |
| Reporting | FR-16 | `reports` | ✅ Lengkap | Charts + tabel + export |
| Audit Log | FR-15 | `audit_log` | ✅ Lengkap | Immutable trail + filter |
| User & Role Management | FR-01 | `admin_users_roles` | ✅ Lengkap | 5 role dengan CRUD |
| Category Management | — | `admin_categories` | ✅ Lengkap | 7 kategori + subkategori |
| SLA Policy Config | FR-08 | `admin_sla_policies` | ✅ Lengkap | 6 policy tier |
| Service Admin | FR-10 | `admin_services` | ✅ Lengkap | CRUD service catalog |
| User Profile | FR-01 | `user_profile_settings` | ✅ Lengkap | Profil + notif + security |
| Feedback/Rating | FR-14 | *Artikel KB (helpful rating)* | ⚠️ Parsial | Hanya KB rating, tidak ada tiket CSAT screen khusus |
| Attachment | FR-07 | `create_ticket` (ada file upload area) | ⚠️ Parsial | Tidak ada dedicated attachment viewer page |
| Admin Dashboard | FR-09 | *Tidak ada screen khusus* | ⚠️ Parsial | Admin menggunakan Manager dashboard |
| Department / Location | FR-01 | *Tidak ditemukan screen* | ❌ Tidak ada desain | PRD menyebut department & location management |
| Password Reset Flow | FR-01 | *Hanya "Forgot password?" link* | ❌ Tidak ada desain | Tidak ada halaman reset password |
| Re-open Ticket | FR-05 | *Tidak ada screen khusus* | ❌ Tidak ada desain | Workflow reopen belum divisualisasikan |

### 4.2 Ringkasan Status Kesesuaian

| Kategori | Jumlah |
|----------|--------|
| ✅ Lengkap (desain memadai) | 22 |
| ⚠️ Parsial (desain ada tapi kurang lengkap) | 3 |
| ❌ Tidak ada desain | 3 |

### 4.3 Fitur Desain yang Melampaui PRD (Enterprise Extras)

Beberapa elemen desain ada di Stitch tetapi **tidak disebutkan dalam PRD** — ini adalah fitur enterprise yang perlu dikonfirmasi apakah akan diimplementasi:

| Fitur | Halaman | Rekomendasi |
|-------|---------|-------------|
| SSO (Okta / Microsoft Entra) | Sign In | Simpan sebagai future feature (out of MVP) |
| "War Room" / Incident Command | Ticket Detail, SLA Monitoring | Simpan sebagai future feature |
| SOC-2 / Zero Trust branding | Sign In | UI cosmetic, tidak perlu backend |
| VIP Executive SLA Policy | Admin SLA | Dapat dikonfigurasi di SLA admin |
| IT Concierge chatbot | Knowledge Base | Out of scope MVP (AI) |

### 4.4 Rekomendasi untuk Gap

| Gap | Rekomendasi |
|-----|-------------|
| Tiket CSAT/feedback screen | Buat halaman/modal rating sederhana setelah tiket di-resolved |
| Department & Location management | Tambahkan screen admin sederhana (table + CRUD modal) |
| Password reset flow | Tambahkan 2 screen: request reset + form password baru |
| Reopen ticket UI | Tambahkan tombol "Reopen" di ticket detail dengan konfirmasi modal |

---

## 5. ARSITEKTUR LARAVEL

### 5.1 Database ERD (Ringkasan Entitas)

```
┌─────────────┐    ┌──────────────┐    ┌─────────────────┐
│    users    │    │    roles     │    │   departments   │
│─────────────│    │──────────────│    │─────────────────│
│ id          │───▶│ id           │    │ id              │
│ name        │    │ name         │    │ name            │
│ email       │    │ slug         │    │ description     │
│ password    │    └──────────────┘    └─────────────────┘
│ role_id     │─────────────────────────────────────────▶ │
│ department  │                                           │
│ location_id │    ┌──────────────┐    ┌─────────────────┐
│ active      │    │  locations   │    │   categories    │
└─────────────┘    │──────────────│    │─────────────────│
       │           │ id, name,    │    │ id, name, icon, │
       │           │ address, ... │    │ parent_id, ...  │
       │           └──────────────┘    └─────────────────┘
       │
       ▼
┌─────────────────────────────────────────────────────────┐
│                        tickets                          │
│─────────────────────────────────────────────────────────│
│ id, ticket_number (INC-XXXXX), title, description       │
│ status (new|assigned|in_progress|pending|resolved|closed│
│         |reopened)                                      │
│ priority_id, category_id, subcategory_id                │
│ requester_id (FK users), assignee_id (FK users)         │
│ location_id, asset_id                                   │
│ sla_policy_id, response_due_at, resolution_due_at       │
│ sla_response_breached, sla_resolution_breached          │
│ resolved_at, closed_at, first_response_at               │
│ impact, urgency, source                                 │
│ created_at, updated_at                                  │
└─────────────────────────────────────────────────────────┘
       │ 1
       ├──────────────────────────────────────────────────
       │ n               │ n              │ n
       ▼                 ▼                ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────────┐
│ticket_comment│  │ticket_attach.│  │  ticket_history  │
│──────────────│  │──────────────│  │──────────────────│
│ id           │  │ id           │  │ id               │
│ ticket_id    │  │ ticket_id    │  │ ticket_id        │
│ user_id      │  │ uploaded_by  │  │ user_id          │
│ body         │  │ filename     │  │ action           │
│ type (public │  │ filepath     │  │ field_changed    │
│  |internal)  │  │ mime_type    │  │ old_value        │
│ created_at   │  │ file_size    │  │ new_value        │
└──────────────┘  └──────────────┘  │ created_at       │
                                     └──────────────────┘

┌──────────────┐  ┌──────────────────┐  ┌──────────────┐
│ sla_policies │  │  service_requests│  │   approvals  │
│──────────────│  │──────────────────│  │──────────────│
│ id, name     │  │ id, service_id   │  │ id           │
│ priority_id  │  │ requester_id     │  │ request_id   │
│ response_min │  │ status           │  │ approver_id  │
│ resolution_hr│  │ form_data (JSON) │  │ status       │
│ business_hrs │  │ ...              │  │ comment      │
└──────────────┘  └──────────────────┘  └──────────────┘

┌──────────────┐  ┌──────────────────┐  ┌──────────────┐
│    assets    │  │knowledge_articles│  │  feedbacks   │
│──────────────│  │──────────────────│  │──────────────│
│ id, asset_no │  │ id, title, body  │  │ id           │
│ category_id  │  │ category_id      │  │ ticket_id    │
│ brand, model │  │ author_id        │  │ user_id      │
│ serial_num   │  │ status (published│  │ rating (1-5) │
│ status       │  │  |draft)         │  │ comment      │
│ assigned_to  │  │ views, helpful_  │  └──────────────┘
│ warranty_end │  │  count, ...      │
└──────────────┘  └──────────────────┘

┌──────────────────────────────────────────────────────┐
│                     audit_logs                       │
│──────────────────────────────────────────────────────│
│ id, user_id, action, auditable_type, auditable_id    │
│ old_values (JSON), new_values (JSON)                 │
│ ip_address, user_agent, created_at                   │
└──────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────┐
│                   notifications                      │
│──────────────────────────────────────────────────────│
│ id, user_id, type, title, body, data (JSON)          │
│ read_at, created_at                                  │
└──────────────────────────────────────────────────────┘
```

### 5.2 Database Migrations (Urutan)

1. `create_roles_table`
2. `create_departments_table`
3. `create_locations_table`
4. `create_users_table` *(modifikasi default)*
5. `create_categories_table` *(self-referencing untuk subkategori)*
6. `create_priorities_table`
7. `create_sla_policies_table`
8. `create_tickets_table`
9. `create_ticket_comments_table`
10. `create_ticket_attachments_table`
11. `create_ticket_histories_table`
12. `create_services_table`
13. `create_service_requests_table`
14. `create_approvals_table`
15. `create_asset_categories_table`
16. `create_assets_table`
17. `create_asset_assignments_table`
18. `create_asset_maintenance_table`
19. `create_knowledge_articles_table`
20. `create_feedbacks_table`
21. `create_audit_logs_table`
22. `create_notifications_table`

### 5.3 Models

```
app/Models/
├── User.php
├── Role.php
├── Department.php
├── Location.php
├── Category.php              # self-referencing (parent/children)
├── Priority.php
├── SlaPolicy.php
├── Ticket.php                # relasi ke User, Category, Priority, SlaPolicy, Asset
├── TicketComment.php
├── TicketAttachment.php
├── TicketHistory.php
├── Service.php
├── ServiceRequest.php
├── Approval.php
├── AssetCategory.php
├── Asset.php
├── AssetAssignment.php
├── AssetMaintenance.php
├── KnowledgeArticle.php
├── Feedback.php
├── AuditLog.php
└── Notification.php          # Gunakan Laravel built-in atau custom
```

### 5.4 Controllers

```
app/Http/Controllers/
├── Auth/
│   ├── LoginController.php
│   ├── LogoutController.php
│   └── PasswordResetController.php
├── DashboardController.php           # redirect berdasarkan role
├── TicketController.php              # CRUD + workflow
├── TicketCommentController.php
├── TicketAttachmentController.php
├── ServiceCatalogController.php
├── ServiceRequestController.php
├── ApprovalController.php
├── SlaMonitoringController.php
├── AssetController.php
├── KnowledgeBaseController.php
├── KnowledgeArticleController.php
├── ReportController.php
├── NotificationController.php
├── UserProfileController.php
└── Admin/
    ├── UserController.php
    ├── RoleController.php
    ├── CategoryController.php
    ├── SlaPolicy Controller.php
    ├── ServiceAdminController.php
    ├── DepartmentController.php
    ├── LocationController.php
    └── AuditLogController.php
```

### 5.5 Services

```
app/Services/
├── SlaService.php              # kalkulasi SLA deadline, cek breach
├── TicketAssignmentService.php # logika assign/reassign
├── NotificationService.php     # kirim notifikasi in-app
├── AuditLogService.php         # catat aktivitas penting
├── ReportService.php           # generate data laporan
├── AttachmentService.php       # upload, validasi, simpan file
└── ApprovalService.php         # alur approve/reject
```

### 5.6 Middleware

```
app/Http/Middleware/
├── CheckRole.php               # cek role user sebelum akses route
├── CheckUserActive.php         # cek user aktif (tidak dinonaktifkan)
└── TrackLastActive.php         # update last_active_at user
```

### 5.7 Policies

```
app/Policies/
├── TicketPolicy.php            # siapa bisa lihat/edit/assign/close tiket
├── ServiceRequestPolicy.php   # siapa bisa buat/approve request
├── AssetPolicy.php             # akses data aset
├── KnowledgeArticlePolicy.php # siapa bisa publish/edit artikel
├── UserPolicy.php              # manajemen user (admin only)
├── CategoryPolicy.php          # konfigurasi kategori
└── ReportPolicy.php            # akses laporan (manager/admin)
```

### 5.8 Routes

```
routes/web.php
├── Auth routes (login, logout, password reset)
├── Dashboard (redirect by role)
├── Tickets (CRUD + actions: assign, comment, resolve, close, reopen)
├── Service Catalog & Requests
├── Approvals
├── SLA Monitoring
├── Assets
├── Knowledge Base
├── Reports
├── Notifications
├── User Profile
└── Admin prefix
    ├── Users & Roles
    ├── Categories
    ├── SLA Policies
    ├── Services
    ├── Departments & Locations
    └── Audit Log
```

### 5.9 Blade Components

```
resources/views/
├── layouts/
│   ├── app.blade.php           # layout utama (sidebar + topbar + content)
│   └── auth.blade.php          # layout halaman auth
├── components/
│   ├── sidebar.blade.php
│   ├── topbar.blade.php
│   ├── breadcrumb.blade.php
│   ├── stat-card.blade.php
│   ├── ticket-row.blade.php
│   ├── status-badge.blade.php  # badge dengan warna per status
│   ├── priority-badge.blade.php
│   ├── sla-countdown.blade.php # timer SLA dengan indikator warna
│   ├── modal.blade.php         # base modal glassmorphism
│   ├── notification-item.blade.php
│   ├── empty-state.blade.php
│   └── pagination.blade.php
├── auth/
│   ├── login.blade.php
│   └── reset-password.blade.php
├── dashboard/
│   ├── employee.blade.php
│   ├── technician.blade.php
│   └── manager.blade.php
├── tickets/
│   ├── index.blade.php         # all tickets
│   ├── my-tickets.blade.php
│   ├── create.blade.php
│   ├── show.blade.php          # ticket detail
│   └── partials/
│       ├── comments.blade.php
│       ├── history-timeline.blade.php
│       └── sla-panel.blade.php
├── service-catalog/
│   ├── index.blade.php
│   ├── request-form.blade.php
│   └── show.blade.php
├── approvals/
│   └── index.blade.php
├── sla/
│   └── monitoring.blade.php
├── assets/
│   ├── index.blade.php
│   └── show.blade.php
├── knowledge-base/
│   ├── index.blade.php
│   └── show.blade.php
├── reports/
│   └── index.blade.php
├── notifications/
│   └── index.blade.php
├── profile/
│   └── settings.blade.php
└── admin/
    ├── users/index.blade.php
    ├── categories/index.blade.php
    ├── sla-policies/index.blade.php
    ├── services/index.blade.php
    └── audit-log/index.blade.php
```

### 5.10 Authentication & Authorization

- **Authentication**: Laravel Breeze (Blade stack) atau custom Auth
- **Role System**: Single role per user (FK ke tabel `roles`)
- **Authorization**: Laravel Policies + Gate
- **Middleware**: `CheckRole` diaplikasikan di route group
- **Session**: Standard Laravel session (MySQL driver)

### 5.11 Notification Architecture

- Gunakan Laravel `Notification` + channel `database` untuk in-app
- Tabel `notifications` menggunakan format Laravel built-in
- Trigger notifikasi di: TicketController, ApprovalController, SlaService
- **Future**: Email via Laravel Mail; Queue via Laravel Queue + Redis

### 5.12 Testing

```
tests/
├── Unit/
│   ├── SlaServiceTest.php
│   ├── TicketWorkflowTest.php
│   └── AttachmentServiceTest.php
└── Feature/
    ├── AuthTest.php
    ├── TicketCrudTest.php
    ├── TicketCommentTest.php
    ├── AssignmentTest.php
    ├── ApprovalTest.php
    ├── SlaCalculationTest.php
    └── ReportTest.php
```

---

## 6. IMPLEMENTATION PLAN

### PHASE 1 — Foundation (Prerequisite untuk semua)

#### Task 1.1 — Project Setup & Configuration
- **Deskripsi**: Setup Laravel 12, Tailwind CSS, Vite, database MySQL, .env
- **Files**: `composer.json`, `package.json`, `vite.config.js`, `tailwind.config.js`, `.env`
- **Dependencies**: Tidak ada
- **Acceptance Criteria**: `php artisan serve` jalan, Tailwind compile sukses
- **Testing**: Manual

#### Task 1.2 — Design System Token & Tailwind Config
- **Deskripsi**: Implementasi warna, tipografi, spacing dari DESIGN.md ke `tailwind.config.js`
- **Files**: `tailwind.config.js`, `resources/css/app.css`
- **Dependencies**: Task 1.1
- **Acceptance Criteria**: Semua token warna `apex_service_desk/DESIGN.md` tersedia sebagai Tailwind class
- **Testing**: Visual check

#### Task 1.3 — Database Migrations (Core)
- **Deskripsi**: Buat migrations untuk roles, departments, locations, users (modifikasi), categories (dengan self-reference), priorities, sla_policies
- **Files**: `database/migrations/` (7 file baru)
- **Dependencies**: Task 1.1
- **Acceptance Criteria**: `php artisan migrate` sukses tanpa error
- **Testing**: Migration test

#### Task 1.4 — Database Seeders (Core)
- **Deskripsi**: Seed default roles (Employee, Technician, IT Manager, Admin, Approver), priorities (Critical, High, Medium, Low), SLA baseline, admin user
- **Files**: `database/seeders/`
- **Dependencies**: Task 1.3
- **Acceptance Criteria**: `php artisan db:seed` menghasilkan data default yang benar
- **Testing**: Database assertion

#### Task 1.5 — Authentication (Login, Logout, Password Reset)
- **Deskripsi**: Implementasi auth menggunakan Breeze atau custom, sesuaikan view dengan desain sign_in
- **Files**: Auth controllers, views/auth/, middleware
- **Dependencies**: Task 1.3, 1.4
- **Acceptance Criteria**: FR-01 terpenuhi — user login/logout; redirect sesuai role; akses dibatasi
- **Testing**: `tests/Feature/AuthTest.php`

#### Task 1.6 — Layout & Navigation Shell
- **Deskripsi**: Buat `layouts/app.blade.php` dengan sidebar, topbar, breadcrumb sesuai desain Stitch
- **Files**: `resources/views/layouts/`, `resources/views/components/`
- **Dependencies**: Task 1.2, 1.5
- **Acceptance Criteria**: Sidebar navigasi render dengan benar per role; responsive (mobile sidebar collapse)
- **Testing**: Browser visual check, responsiveness test

### PHASE 2 — Core Ticket Management

#### Task 2.1 — Ticket Migrations & Models
- **Deskripsi**: Migrations untuk tickets, ticket_comments, ticket_attachments, ticket_histories
- **Files**: `database/migrations/`, `app/Models/`
- **Dependencies**: Task 1.3
- **Acceptance Criteria**: Schema sesuai ERD; relasi Model berjalan
- **Testing**: Unit test relasi Model

#### Task 2.2 — Create Ticket
- **Deskripsi**: Form buat tiket sesuai desain `create_ticket`, validasi input, generate Ticket ID (`INC-XXXXX`), simpan ke DB
- **Files**: `TicketController.php`, `tickets/create.blade.php`, `TicketPolicy.php`
- **Dependencies**: Task 2.1, 1.5
- **Acceptance Criteria**: FR-02 terpenuhi; Ticket ID unik dihasilkan; semua field tervalidasi
- **Testing**: `tests/Feature/TicketCrudTest.php`

#### Task 2.3 — Ticket Detail & Timeline
- **Deskripsi**: Halaman detail tiket sesuai desain `ticket_detail`; ticket history timeline
- **Files**: `tickets/show.blade.php`, `partials/history-timeline.blade.php`
- **Dependencies**: Task 2.2
- **Acceptance Criteria**: FR-03 terpenuhi; seluruh perubahan tiket terdokumentasi di timeline
- **Testing**: Feature test

#### Task 2.4 — Ticket Comments (Public & Internal)
- **Deskripsi**: Reply thread sesuai visibility; public vs internal note
- **Files**: `TicketCommentController.php`, `partials/comments.blade.php`
- **Dependencies**: Task 2.3
- **Acceptance Criteria**: FR-06 terpenuhi; Internal note tidak terlihat oleh Employee
- **Testing**: `tests/Feature/TicketCommentTest.php`

#### Task 2.5 — Ticket Attachment
- **Deskripsi**: Upload attachment; validasi MIME type & ukuran; simpan di Laravel Storage (private)
- **Files**: `AttachmentService.php`, `TicketAttachmentController.php`
- **Dependencies**: Task 2.3
- **Acceptance Criteria**: FR-07 terpenuhi; file tidak bisa diakses langsung tanpa auth
- **Testing**: Upload test dengan file valid dan invalid

#### Task 2.6 — Ticket Assignment & Status Workflow
- **Deskripsi**: Assign/reassign technician; workflow status (New→Assigned→In Progress→Pending→Resolved→Closed→Reopened); catat di history
- **Files**: `TicketAssignmentService.php`, `tickets/show.blade.php` (tombol actions)
- **Dependencies**: Task 2.4, 2.5
- **Acceptance Criteria**: FR-04, FR-05 terpenuhi; status hanya berubah melalui alur yang diizinkan
- **Testing**: `tests/Feature/AssignmentTest.php`, `tests/Unit/TicketWorkflowTest.php`

#### Task 2.7 — My Tickets & All Tickets
- **Deskripsi**: Listing tickets dengan filter, sort, search, pagination sesuai desain
- **Files**: `tickets/index.blade.php`, `tickets/my-tickets.blade.php`
- **Dependencies**: Task 2.6
- **Acceptance Criteria**: Employee hanya lihat tiket miliknya; Manager/Admin lihat semua; filter berfungsi
- **Testing**: Authorization test, filter test

### PHASE 3 — Dashboard & SLA

#### Task 3.1 — SLA Service & Migrations
- **Deskripsi**: `SlaService.php` untuk kalkulasi deadline (response_due_at, resolution_due_at) saat tiket dibuat/diprioritaskan; deteksi breach
- **Files**: `SlaService.php`, `database/migrations/create_sla_policies_table.php`
- **Dependencies**: Task 2.2
- **Acceptance Criteria**: FR-08 terpenuhi; deadline dihitung tepat; breach terdeteksi
- **Testing**: `tests/Unit/SlaServiceTest.php`, `tests/Feature/SlaCalculationTest.php`

#### Task 3.2 — Employee Dashboard
- **Deskripsi**: Statistik tiket user + recent tickets sesuai desain `employee_dashboard`
- **Files**: `DashboardController.php`, `dashboard/employee.blade.php`
- **Dependencies**: Task 2.7
- **Acceptance Criteria**: FR-09 terpenuhi; angka statistik akurat berdasarkan data real
- **Testing**: Feature test

#### Task 3.3 — Technician Dashboard
- **Deskripsi**: Workload technician + SLA urgent alerts + assigned queue sesuai desain `technician_dashboard`
- **Files**: `dashboard/technician.blade.php`
- **Dependencies**: Task 3.1
- **Acceptance Criteria**: Technician hanya lihat data dirinya; SLA alert muncul untuk tiket at-risk
- **Testing**: Feature test

#### Task 3.4 — Manager Dashboard
- **Deskripsi**: Semua KPI (total tickets, SLA compliance, avg resolution, CSAT) + charts sesuai desain `it_manager_dashboard`
- **Files**: `dashboard/manager.blade.php`, `ReportService.php`
- **Dependencies**: Task 3.1, 3.2
- **Acceptance Criteria**: FR-09 terpenuhi; semua metric akurat; chart responsive
- **Testing**: Feature test

#### Task 3.5 — SLA Monitoring Page
- **Deskripsi**: Halaman SLA monitoring sesuai desain `sla_monitoring`; SLA tier matrix; per-ticket countdown
- **Files**: `sla/monitoring.blade.php`, `SlaMonitoringController.php`
- **Dependencies**: Task 3.1
- **Acceptance Criteria**: Seluruh tiket aktif dengan SLA ditampilkan dengan status yang akurat
- **Testing**: Feature test

### PHASE 4 — Enterprise Modules

#### Task 4.1 — Service Catalog & Request Form
- **Deskripsi**: Katalog layanan; dynamic form request; simpan service_request
- **Files**: `ServiceCatalogController.php`, `service-catalog/`, migrations, models
- **Dependencies**: Task 1.5
- **Acceptance Criteria**: FR-10 terpenuhi; user bisa browse dan submit request
- **Testing**: Feature test

#### Task 4.2 — Approval Workflow
- **Deskripsi**: Request yang memerlukan approval trigger notifikasi ke Approver; halaman approval management sesuai desain; approve/reject dengan komentar
- **Files**: `ApprovalController.php`, `ApprovalService.php`, `approvals/`
- **Dependencies**: Task 4.1
- **Acceptance Criteria**: FR-11 terpenuhi; status approval tercatat; notifikasi terkirim
- **Testing**: `tests/Feature/ApprovalTest.php`

#### Task 4.3 — Asset Management
- **Deskripsi**: CRUD asset; assign asset ke user/department; kaitkan asset ke tiket; sesuai desain `it_assets` dan `asset_detail`
- **Files**: `AssetController.php`, `assets/`, migrations, models
- **Dependencies**: Task 2.6
- **Acceptance Criteria**: FR-12 terpenuhi; asset dapat dikaitkan ke tiket; riwayat maintenance tercatat
- **Testing**: Feature test

#### Task 4.4 — Knowledge Base
- **Deskripsi**: Artikel KB dengan kategori; pencarian; helpful/not helpful rating; detail artikel; sesuai desain `knowledge_base` dan `knowledge_article_detail`
- **Files**: `KnowledgeBaseController.php`, `knowledge-base/`
- **Dependencies**: Task 1.5
- **Acceptance Criteria**: FR-13 terpenuhi; pencarian berfungsi; artikel dapat ditautkan ke tiket
- **Testing**: Feature test

#### Task 4.5 — Notification System
- **Deskripsi**: In-app notifications menggunakan Laravel Notification (database channel); notification center sesuai desain; read/unread state
- **Files**: `NotificationService.php`, `app/Notifications/`, `notifications/`
- **Dependencies**: Task 2.6, 4.2
- **Acceptance Criteria**: FR-08 (notif) terpenuhi; 9 tipe notifikasi berfungsi sesuai desain
- **Testing**: Feature test

#### Task 4.6 — Feedback / CSAT
- **Deskripsi**: Rating 1-5 setelah tiket resolved; simpan ke feedbacks; tampil di laporan
- **Files**: `FeedbackController.php`, modal di ticket detail view
- **Dependencies**: Task 2.6
- **Acceptance Criteria**: FR-14 terpenuhi; user hanya bisa beri rating 1x per tiket
- **Testing**: Feature test

#### Task 4.7 — Reports
- **Deskripsi**: Halaman laporan sesuai desain `reports`; filter periode, kategori, teknisi; data chart; export CSV
- **Files**: `ReportController.php`, `reports/`
- **Dependencies**: Task 3.4
- **Acceptance Criteria**: FR-16 terpenuhi; data akurat sesuai filter; export CSV berfungsi
- **Testing**: `tests/Feature/ReportTest.php`

### PHASE 5 — Admin & Hardening

#### Task 5.1 — Admin User & Role Management
- **Deskripsi**: CRUD user; assign role; aktif/nonaktif; sesuai desain `admin_users_roles`
- **Files**: `Admin/UserController.php`, `admin/users/`
- **Dependencies**: Task 1.4
- **Acceptance Criteria**: Admin bisa buat, edit, nonaktifkan user; role tersimpan benar
- **Testing**: Authorization + feature test

#### Task 5.2 — Admin Category Management
- **Deskripsi**: CRUD kategori dan subkategori; sesuai desain `admin_categories`
- **Files**: `Admin/CategoryController.php`, `admin/categories/`
- **Dependencies**: Task 1.3
- **Acceptance Criteria**: Subkategori terhubung ke kategori parent dengan benar
- **Testing**: Feature test

#### Task 5.3 — Admin SLA Policy Management
- **Deskripsi**: CRUD SLA policy; assign ke priority/service; sesuai desain `admin_sla_policies`
- **Files**: `Admin/SlaPolicyController.php`, `admin/sla-policies/`
- **Dependencies**: Task 3.1
- **Acceptance Criteria**: Perubahan SLA policy langsung berpengaruh ke tiket baru
- **Testing**: Feature test

#### Task 5.4 — Admin Service Management
- **Deskripsi**: CRUD service catalog; sesuai desain `admin_services`
- **Files**: `Admin/ServiceAdminController.php`, `admin/services/`
- **Dependencies**: Task 4.1
- **Acceptance Criteria**: Admin bisa tambah/edit/nonaktifkan service dari catalog
- **Testing**: Feature test

#### Task 5.5 — Audit Log
- **Deskripsi**: `AuditLogService.php` mencatat login, perubahan tiket, assignment, approval, konfigurasi; halaman sesuai desain `audit_log`
- **Files**: `AuditLogService.php`, `Admin/AuditLogController.php`, `admin/audit-log/`
- **Dependencies**: Semua controller Phase 1-4
- **Acceptance Criteria**: FR-15 terpenuhi; setiap aksi penting tercatat dengan actor + timestamp + IP
- **Testing**: Feature test

#### Task 5.6 — User Profile Settings
- **Deskripsi**: Edit profil, ganti password, preferensi notifikasi; sesuai desain `user_profile_settings`
- **Files**: `UserProfileController.php`, `profile/settings.blade.php`
- **Dependencies**: Task 1.5
- **Acceptance Criteria**: User bisa update profil; ganti password dengan validasi; preferensi notif tersimpan
- **Testing**: Feature test

#### Task 5.7 — Security Hardening
- **Deskripsi**: Rate limiting login, CSRF semua form, input sanitization, secure file storage, authorization check di semua endpoint sensitif
- **Files**: `Middleware/`, `Policies/`, route config
- **Dependencies**: Semua phase sebelumnya
- **Acceptance Criteria**: Semua NFR Security terpenuhi
- **Testing**: Manual security review + automated policy tests

#### Task 5.8 — Performance Optimization
- **Deskripsi**: Eager loading relasi Eloquent, query indexing, pagination semua listing, caching kalkulasi dashboard
- **Files**: Semua controllers + migrations (tambah index)
- **Dependencies**: Semua phase sebelumnya
- **Acceptance Criteria**: Halaman dashboard & ticket list load < 2s pada data 1000+ ticket
- **Testing**: Manual profiling

---

## 7. RISIKO & REKOMENDASI

### 7.1 Risiko Teknis

| Risiko | Dampak | Probabilitas | Mitigasi |
|--------|--------|-------------|----------|
| SLA calculation tidak memperhitungkan business hours & timezone | Laporan tidak akurat | Tinggi | Definisikan business hours di SLA policy; gunakan Carbon untuk kalkulasi; unit test lengkap |
| Attachment: file berbahaya terupload | Security incident | Sedang | Whitelist MIME + extension; scan ukuran; simpan di storage private; akses melalui signed URL |
| Dashboard query lambat pada data besar | UX buruk | Sedang | Eager load, indexing FK, database view untuk statistik, cache per 5-15 menit |
| Status workflow tidak konsisten | Data korup | Sedang | Implementasi state machine atau array allowed transitions di model |
| Concurrent assignment (2 IT assign ke tiket yang sama) | Data inkonsisten | Rendah | DB transaction + optimistic locking atau lock tiket saat proses assign |
| Scope creep dari fitur enterprise desain | Proyek molor | Tinggi | Ikuti MVP scope PRD; simpan enterprise features sebagai backlog |

### 7.2 Rekomendasi Arsitektur

1. **SLA Engine**: Gunakan `Carbon` untuk kalkulasi waktu; buat `SlaService` yang dipanggil saat tiket dibuat atau priority berubah. Pertimbangkan scheduled job (cron) setiap 5 menit untuk deteksi breach secara batch.

2. **Ticket ID**: Format `INC-00001` — gunakan database sequence atau zero-padded auto-increment. Buat scope/helper di model untuk konsistensi.

3. **Status Transitions**: Definisikan array `ALLOWED_TRANSITIONS` di `Ticket` model:
   ```php
   const ALLOWED_TRANSITIONS = [
       'new'         => ['assigned'],
       'assigned'    => ['in_progress'],
       'in_progress' => ['pending', 'resolved'],
       'pending'     => ['in_progress'],
       'resolved'    => ['closed', 'reopened'],
       'closed'      => ['reopened'],
       'reopened'    => ['assigned'],
   ];
   ```

4. **Notifications**: Gunakan Laravel Notification dengan `DatabaseChannel` untuk MVP. Buat `Notifiable` trait pada `User`. Tipe notifikasi sebagai konstanta class.

5. **File Storage**: Gunakan `Storage::disk('private')` untuk attachment. Akses melalui temporary signed URL (`Storage::temporaryUrl()`).

6. **Role & Permission**: Untuk MVP, single role FK cukup. Jika di masa depan butuh granular permission, pertimbangkan migrasi ke Spatie Laravel-Permission.

7. **Alpine.js**: Gunakan untuk interaksi UI sederhana (toggle sidebar mobile, modal open/close, tab switching) tanpa perlu Vue/React.

### 7.3 Rekomendasi Desain

1. **Admin Dashboard**: Tambahkan 1 screen admin dashboard sederhana yang menampilkan ringkasan sistem (total users, total tiket, SLA health, recent activity). Bisa dibangun di atas Manager Dashboard dengan tambahan shortcut admin.

2. **Department & Location Pages**: Tambahkan halaman CRUD sederhana untuk Department dan Location di admin section. Tidak memerlukan desain baru yang kompleks — cukup tabel + modal form.

3. **Password Reset Pages**: Buat 2 view sederhana sesuai visual identity Stitch (background dark, card centered):
   - `forgot-password.blade.php` — input email
   - `reset-password.blade.php` — form password baru

4. **Ticket Feedback Modal**: Tambahkan modal 5-star rating setelah tiket di-resolved yang muncul otomatis atau saat user mengunjungi tiket resolved pertama kali.

5. **Reopen Ticket**: Tambahkan tombol "Reopen Issue" di ticket detail dengan konfirmasi modal — tidak memerlukan desain baru, cukup gunakan komponen modal yang sudah ada.

### 7.4 Rekomendasi Umum

- Mulai dari Phase 1 secara berurutan; jangan mulai Phase 2 sebelum Phase 1 selesai dan disetujui.
- Setiap task memiliki acceptance criteria yang harus diverifikasi sebelum merge ke branch utama.
- SLA Service harus di-test dengan unit test yang komprehensif sebelum digunakan di production.
- Gunakan Laravel seeders untuk semua data master (roles, priorities, SLA baseline, categories) agar environment mudah di-reset.
- Dokumentasikan `.env.example` dengan semua variable yang dibutuhkan sebelum deployment.

---

## STATUS AUDIT

| Tahap | Status | Catatan |
|-------|--------|---------|
| ✅ Baca PRD | Selesai | PDF berhasil dibaca (10 halaman, v1.0) |
| ✅ Analisis PRD | Selesai | Semua elemen PRD teridentifikasi |
| ✅ Analisis Desain Stitch | Selesai | 27 screen/komponen teranalisis |
| ✅ Cross Analysis | Selesai | 22 match, 3 parsial, 3 gap |
| ✅ Arsitektur Laravel | Selesai | ERD, struktur file, arsitektur lengkap |
| ✅ Implementation Plan | Selesai | 25 task terurut dengan dependensi |
| 🟡 Menunggu persetujuan | Pending | Implementasi BELUM dimulai |

---

*Dokumen ini disiapkan sebagai acuan audit dan perencanaan. Implementasi tidak akan dimulai sampai mendapat persetujuan dari stakeholder.*

*IT Service Desk — AUDIT-REPORT.md — v1.0 — 7 November 2026*
