# Privacy Policy for G Sports Center (GSC) Mobile App

**Effective date:** 2026-05-16

## 1. Introduction
This Privacy Policy explains what personal data the GSC mobile app ("App") collects, how we use it, with whom we share it, how we store and protect it, and the rights you have regarding your data. By using the App you agree to the collection and use of information in accordance with this policy.

## 2. Data We Collect
- **Account & authentication:** Firebase Authentication user ID (`firebase_uid`), email address, display name (if provided).
- **Profile & usage:** optional profile details you provide and usage data (screens visited, actions taken) collected for analytics and improving the App.
- **Purchases & finance:** records related to package purchases stored in Firestore (`gsc_packages`) and mirrored to the website MySQL table `financial_transactions` (amount, `transaction_type`, description, `created_at`).
- **Payment proof:** images or screenshots you upload as payment proof are stored in Firebase/Cloud Storage (or Cloudinary if configured) as uploaded assets. The App may also cache the uploaded image locally on your device.
- **Device & diagnostics:** device information, app version, crash reports, and basic telemetry to help diagnose issues.
- **Optional sensors/permissions:** location (only if you use features that require location), camera and media library access (only to take or choose payment proof images), and push notification token (for notifications). The App does not collect contacts, SMS or call logs.

## 3. Why We Collect Data (Purposes)
- Authentication and account management.
- Process and validate package purchases, record financial transactions, and generate package QR codes.
- Store and display payment proof for administrative review.
- Provide and improve App features, analytics, and troubleshooting.
- Send transactional messages, booking confirmations, and push notifications if you opt-in.
- Comply with legal obligations and prevent fraud.

## 4. Legal Basis
For users in regions requiring legal bases, we rely on: your consent (for optional features like push notifications and camera), contract performance (to process purchases and bookings), and legitimate interests (to improve the App and detect abuse), where applicable.

## 5. Data Sharing and Third Parties
We may share data with:
- Firebase services (Authentication, Firestore, Storage) for user and app data storage.
- Cloudinary (if configured) for image hosting and delivery.
- Our website backend (CodeIgniter + MySQL) which stores `financial_transactions` and other administrative records.
- Analytics and crash-reporting providers (only aggregate and non-identifying data unless you opt-in).
We will not sell your personal data to third parties.

## 6. Data Storage, Retention & Security
- Data is stored in Firebase (Google Cloud) and/or our MySQL database hosted on our servers or hosting provider. Uploaded images may be stored in Firebase Storage or Cloudinary.
- We retain transactional and purchase records for business and accounting purposes (typically several years) unless you request deletion and it is legally allowed to remove them.
- We use standard industry protections (HTTPS/TLS, access controls, secure storage) to protect your data but cannot guarantee absolute security.

## 7. Permissions Explanation
- **Camera & Media Library:** required only when you upload payment proof or take photos; images are used to validate payments.
- **Location:** used only for features that require location (attendance radius checks or location-based services) and only when you grant permission.
- **Storage (local):** the App may cache images or data locally to improve performance.

## 8. Your Rights
Depending on your jurisdiction, you may request access to, correction of, or deletion of your personal data. You may also withdraw consent for optional features (e.g., push notifications) at any time. To exercise your rights, contact us (see Contact below).

## 9. Children
The App is not intended for children under 13. We do not knowingly collect personal data from children under 13.

## 10. Changes to This Privacy Policy
We may update this policy from time to time. The effective date at the top will change when we publish updates. Significant changes will be communicated through the App or other reasonable means.

## 11. Contact
If you have questions or requests about this Privacy Policy or your data, contact:

- **Email:** gmanagement2026@gmail.com
- **Address:** (optional) G Sports Center

## 12. Play Store Guidance
- Add a link to this Privacy Policy in your Play Console app listing and include required disclosures for permissions (camera, location, storage) in the App content section.
- If you use Firebase and third-party analytics, declare them in the Play Console Data safety form.

---
If you want wording adjusted, an HTML version, or a short Play Console summary, tell me which one.
