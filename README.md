
# 🚚 Boxleo Transport & Logistics Management System

A powerful, full-featured **Transport and Logistics Management System** built for **Boxleo Courier** to streamline order processing, fleet management, dispatch operations, warehouse management, and business reporting. Built by **John Mwacharo**, the system integrates deeply with tools like **Google Maps** and **Google Sheets** to enhance operational efficiency and real-time tracking.

🔗 **Live Demo:** [tls.boxleocourier.com/login](https://tls.boxleocourier.com/login)  
🧑‍💻 **Login Credentials:**  
```plaintext
Email: john.boxleo@gmail.com  
Password: password
```

---

## 👨‍💼 Author

**John Mwacharo**  
_System Analyst & Fullstack Developer_

🔗 [LinkedIn Profile](https://www.linkedin.com/in/john-mwacharo-a12896227/)  
📧 support@solssa.com  
📞 +254 751 458 911  

I spearheaded the development of this system — gathering requirements, designing architecture, coding, testing, and deployment — while working closely with the Boxleo team to ensure the platform met real-world operational needs.

---

## 🧰 Features at a Glance

| Module                  | Description                                                                 |
|-------------------------|-----------------------------------------------------------------------------|
| ✅ Dashboard            | Business insights and operational overviews.                                |
| 📦 Orders               | Manage full order lifecycle – status, category, dispatch, returns, etc.     |
| 📋 Google Sheets Sync   | Real-time syncing of merchant orders via Google Sheets API.                 |
| 🗺️ Geofencing & Maps    | Route tracking, delivery zones, and dispatch visualization via Google Maps. |
| 🚚 Fleet Management     | Track drivers, riders, and vehicle assignments.                             |
| 🏢 Warehouse & Inventory| End-to-end management of warehouse stock and flows.                         |
| 👥 Vendors & Clients    | Manage merchant data and customer details.                                  |
| 🧑‍🤝‍🧑 User Access        | Role-based permissions with multi-role support.                             |
| 📊 Reports & Analytics  | PDF reports, real-time data charts, exportable insights.                   |

---

## 🗃️ Tech Stack

- **Backend:** Laravel 10+
- **Frontend:** Vue.js + Vuetify
- **Database:** MySQL
- **Auth:** Laravel Sanctum
- **APIs:** Google Maps, Google Sheets
- **Deployment:** DigitalOcean VPS

---

## 🗂️ System Menu (As per Business Requirements)

```json
"menu": [
  { "name": "Dashboard" },
  { "name": "Orders", "submenu": ["Orders", "Order Status", "Order Categories", "Picking", "Dispatch", "Return", "Clearance"] },
  { "name": "Vendors" },
  { "name": "Clients" },
  { "name": "Fleet Management", "submenu": ["Fleet Dashboard", "Vehicles", "Drivers", "Riders"] },
  { "name": "Zones" },
  { "name": "Google Sheets" },
  { "name": "Inventory Management" },
  { "name": "Warehouse" },
  { "name": "Reports" },
  { "name": "Users" },
  { "name": "Roles & Permissions" }
]
```

---

## 🔌 Integrations

| API/Service         | Purpose                                                     |
|---------------------|-------------------------------------------------------------|
| Google Sheets API   | Imports merchant orders in real-time                        |
| Google Maps API     | Enables geofencing, route optimization, location tracking   |
| Laravel Sanctum     | Secure authentication and multi-role login                  |
| PDF Exporting       | Auto-generate PDF dispatch sheets and reports               |

---

## 🔐 Authentication & Access

Supports role-based logins for:

- **Admins**
- **Dispatch Staff**
- **Warehouse Operators**
- **Fleet Supervisors**
- **Customer Care**

Each role is granted fine-tuned permissions for controlled access across modules.

---

## 🚀 Setup Instructions

```bash
# 1. Clone the repository
git clone https://github.com/your-repo/logistics-system.git

# 2. Navigate into the directory
cd logistics-system

# 3. Install PHP dependencies
composer install

# 4. Install frontend dependencies
npm install && npm run dev

# 5. Configure environment
cp .env.example .env
php artisan key:generate

# 6. Run database migrations
php artisan migrate

# 7. Serve the application
php artisan serve
```

---

## 📦 Folder Highlights

```bash
├── app/
│   ├── Http/
│   ├── Models/
│   └── Services/        # Google Sheets, Maps, PDF generation logic
├── resources/
│   └── js/              # Vue + Vuetify frontend SPA
├── routes/
│   └── web.php          # Route definitions
├── public/
│   └── assets/          # Images, PDFs, and JS/CSS assets
```

---

## 🤝 Collaboration & Stakeholder Engagement

Although this was a solo technical project, I worked collaboratively with multiple departments at Boxleo:

- 🚛 Logistics Team
- 🏢 Dispatch & Warehouse
- 💬 Customer Support
- 📈 Business Intelligence

Their real-world input ensured the system solved actual operational challenges.

---

## 📈 Future Enhancements

- 📍 Live Vehicle GPS Tracking (via IoT devices)
- 📱 Offline Rider App with Auto-Sync
- 🧾 Custom Invoice & Billing Engine
- 🔄 WooCommerce / Shopify Order Integrations
- 🔐 MFA & Biometric Support for Riders

---

## 📬 Contact

Have a project in mind or want to explore system licensing?

📧 **Email:** support@solssa.com  
📞 **Phone:** +254 751 458 911  
🌐 **LinkedIn:** [John Mwacharo](https://www.linkedin.com/in/john-mwacharo-a12896227/)  
🔒 **Demo Access:** [tls.boxleocourier.com/login](https://tls.boxleocourier.com/login)
# 🚚 Boxleo Transport & Logistics Management System

A powerful, full-featured **Transport and Logistics Management System** built for **Boxleo Courier** to streamline order processing, fleet management, dispatch operations, warehouse management, and business reporting. Built by **John Mwacharo**, the system integrates deeply with tools like **Google Maps** and **Google Sheets** to enhance operational efficiency and real-time tracking.

🔗 **Live Demo:** [tls.boxleocourier.com/login](https://tls.boxleocourier.com/login)  
🧑‍💻 **Login Credentials:**  password

---

> _"Technology should solve problems. I build systems that empower logistics, simplify operations, and drive business value."_  
> — **John Mwacharo**