# UCC Hostel Booking System — User Training & Tutorial Guide

Welcome to the **UCC Hostel Booking System** training guide. This comprehensive manual is designed for training purposes and outlines the precise step-by-step journeys for each of the four system personas: **Students**, **Hostel Agents**, **Hostel Managers**, and **Administrators**.

For visual demonstration, corresponding tutorial walkthrough videos have been recorded using Playwright, and key interface screenshots are preserved within the repository at `/home/jules/verification/screenshots/` and `/home/jules/verification/videos/`.

---

## Table of Contents
1. [Student User Journey Tutorial](#1-student-user-journey-tutorial)
2. [Hostel Agent User Journey Tutorial](#2-hostel-agent-user-journey-tutorial)
3. [Hostel Manager User Journey Tutorial](#3-hostel-manager-user-journey-tutorial)
4. [System Administrator User Journey Tutorial](#4-system-administrator-user-journey-tutorial)
5. [Fee & Split Payment Calculation Model](#5-fee--split-payment-calculation-model)

---

## 1. Student User Journey Tutorial
*Recorded Walkthrough: `/home/jules/verification/videos/` (Student WebM File)*

Students use the platform to search for nearby campus housing, compare features, choose compatible roommates using the lifestyle preference matchmaking system, book rooms, and contact 24/7 support.

### Step-by-Step Walkthrough
1. **Explore & Search Hostels:**
   - Go to the homepage (`/`).
   - Use the sticky, mobile-responsive search bar to filter hostels by neighborhood (e.g., *Amamoma*, *Kwaprow*).
   - Use category tags to filter by amenities (e.g., *AC*, *Wifi*, *Study Room*).
   - **Screenshot:** `/home/jules/verification/screenshots/student_home.png`

2. **Hostel Details & Roommate Matching:**
   - Click on any hostel (e.g., **Royal Gardens**) to view its detailed page.
   - View dynamic media galleries (hover-to-play videos, image carousels).
   - If logged in, the system displays compatibility scores based on lifestyle preference comparisons (cleanliness, sleep schedule).
   - **Screenshot:** `/home/jules/verification/screenshots/student_hostel_details.png`

3. **Floating 24/7 Support Center:**
   - Click the floating **24/7 Support Hub** bubble in the bottom-right corner.
   - Open a Live Chat ticket, search through the FAQs accordion, or find campus emergency hotlines.
   - **Screenshot:** `/home/jules/verification/screenshots/student_support_submitted.png`

4. **Complete a Room Booking:**
   - Select a room (e.g., *Room A1*) and fill out the Booking Form.
   - Both guest users (requiring basic sign-up parameters) and authenticated students can initiate bookings securely.
   - Complete checkout securely via the **Paystack** checkout window. Upon success, guest users are automatically registered and logged in.
   - **Screenshot:** `/home/jules/verification/screenshots/student_bookings_terminal.png`

---

## 2. Hostel Agent User Journey Tutorial
*Recorded Walkthrough: `/home/jules/verification/videos/` (Agent WebM File)*

Agents register on the portal to list properties, manage rooms, track commission payouts, and request balance withdrawals.

### Step-by-Step Walkthrough
1. **Agent Registration:**
   - Navigate to `/agent/register`.
   - Submit personal information along with verification files (Ghanacard ID number and digital scan upload).
   - **Screenshot:** `/home/jules/verification/screenshots/agent_register.png`

2. **Dashboard & Performance Statistics:**
   - Upon administrative approval, log in to access the specialized **Agent Portal Dashboard**.
   - Track key indicators: *Total Commission Earned*, *Available Balance*, and *Withdrawn Amount*.
   - **Screenshot:** `/home/jules/verification/screenshots/agent_dashboard.png`

3. **Manage Listed Properties:**
   - Navigate to **My Hostels** (`/agent/hostels`) to list a new property or update an existing one.
   - Manage room counts, edit capacities, specify gender designations (*Male*, *Female*, or *Any*), and view active bookings.
   - **Screenshot:** `/home/jules/verification/screenshots/agent_hostels.png`

4. **Withdraw Commissions:**
   - Navigate to **Withdrawals** under settings.
   - View transaction logs or submit a new bank withdrawal request (minimum GHS 50).
   - **Screenshot:** `/home/jules/verification/screenshots/agent_settings_terminal.png`

---

## 3. Hostel Manager User Journey Tutorial
*Recorded Walkthrough: `/home/jules/verification/videos/` (Manager WebM File)*

Hostel Managers are assigned to specific approved properties. They oversee student occupants, manage room statuses, track resident complaints, and generate operational/revenue reports.

### Step-by-Step Walkthrough
1. **Manager Portal Dashboard:**
   - Log in using manager credentials (e.g., `manager@example.com`).
   - The central dashboard displays real-time occupancy rates, pending bookings, active occupants, and unresolved resident complaints.
   - **Screenshot:** `/home/jules/verification/screenshots/manager_dashboard.png`

2. **Manage Hostel Settings:**
   - Edit hostel profiles, select featured images, manage high-limit photo galleries with interactive drag-and-drop interfaces, and control the active/inactive status of properties.
   - **Screenshot:** `/home/jules/verification/screenshots/manager_hostels.png`

3. **Room Availability & Occupancy Control:**
   - Track specific rooms (e.g., shared vs. single), update statuses manually, and review active residents.
   - **Screenshot:** `/home/jules/verification/screenshots/manager_rooms.png`

4. **Occupant Rosters & Direct Messaging:**
   - Search the resident list, export CSV sheets, or view comprehensive profile histories.
   - **Screenshot:** `/home/jules/verification/screenshots/manager_occupants.png`

5. **Resident Complaints & Workorders:**
   - Track utility, security, or facility complaints submitted by students.
   - Update complaint statuses (e.g., *In Progress*, *Resolved*) and log internal worknotes.
   - **Screenshot:** `/home/jules/verification/screenshots/manager_complaints_terminal.png`

---

## 4. System Administrator User Journey Tutorial
*Recorded Walkthrough: `/home/jules/verification/videos/` (Admin WebM File)*

Administrators are the high-level supervisors of the platform. They manage user permissions, verify agent credentials, process withdrawal bank transfers, configure split-payment bank codes, and resolve support tickets.

### Step-by-Step Walkthrough
1. **Central Admin Command Center:**
   - Log in as an administrator (e.g., `admin@example.com`).
   - Monitor total platform revenue, overall booking statistics, active/inactive ratios, and recent system audit logs.
   - **Screenshot:** `/home/jules/verification/screenshots/admin_dashboard.png`

2. **User & Role Administration:**
   - Browse the platform users roster. Administrators can edit profile fields, toggle active statuses, or reassign roles.
   - **Screenshot:** `/home/jules/verification/screenshots/admin_users.png`

3. **Hostel Verification & Split-Payment Subaccounts:**
   - Review pending agent properties.
   - Link approved hostels to verified Paystack subaccounts to enable secure split payments.
   - **Screenshot:** `/home/jules/verification/screenshots/admin_hostels.png`

4. **24/7 Help Desk & Ticket Management:**
   - Track all open support tickets across the platform.
   - Assign tickets, reply to student queries, or close completed sessions.
   - **Screenshot:** `/home/jules/verification/screenshots/admin_support_terminal.png`

---

## 5. Fee & Split Payment Calculation Model

The booking engine enforces a rigorous, server-side-calculated fee structure to ensure transparent and reliable distributions during split payment processing:

- **Base Room Cost:** Set directly by the hostel manager or agent.
- **Platform Fee (2.80%):** Retained by the platform main account to cover hosting, maintenance, and security.
- **Paystack Buffer (1.97%):** Added to ensure that the hostel receives the full expected amount after standard Paystack transaction deductions.
- **Banking Charge (0.35%):** Operational/banking buffer for transactions.
- **Total Surcharge Rate:** **5.12%** of the base room cost.
- **Final Checkout Amount:** $Room Cost \times 1.0512$
