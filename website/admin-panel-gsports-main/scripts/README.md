# Firebase Seeder Script

This seeder script populates your Firebase Firestore database with sample data for the G Sports Center Admin Panel.

## What it creates

### Collections and Sample Data:

1. **users** (3 users)
   - Admin user: `admin@gsportscenter.com`
   - Instructor: `instructor@gsportscenter.com` 
   - Coach: `coach@gsportscenter.com`

2. **facilities** (5 facilities)
   - Basketball Courts A & B
   - Fitness Studio
   - Weight Training Area
   - Swimming Pool

3. **members** (5 members)
   - Sample gym members with different membership types
   - Mix of active and inactive statuses

4. **classes** (5 classes)
   - Morning Yoga, HIIT Training, Pilates, Zumba Dance, Strength Training
   - Assigned to different instructors
   - Various schedules and pricing

5. **bookings** (5 bookings)
   - Sample facility bookings with different statuses
   - Pending, confirmed, and cancelled bookings

6. **attendance** (5 records)
   - Sample attendance records for classes
   - Present, late, and absent statuses

## Setup Instructions

### 1. Install Dependencies

```bash
npm install
```

### 2. Configure Environment Variables

Create a `.env.local` file in your project root with your Firebase configuration:

```bash
cp scripts/.env.example .env.local
```

Then edit `.env.local` with your actual Firebase values:

```env
NEXT_PUBLIC_FIREBASE_API_KEY=your_actual_api_key
NEXT_PUBLIC_FIREBASE_AUTH_DOMAIN=your_project_id.firebaseapp.com
NEXT_PUBLIC_FIREBASE_PROJECT_ID=your_actual_project_id
NEXT_PUBLIC_FIREBASE_STORAGE_BUCKET=your_project_id.appspot.com
NEXT_PUBLIC_FIREBASE_MESSAGING_SENDER_ID=your_actual_sender_id
NEXT_PUBLIC_FIREBASE_APP_ID=your_actual_app_id
```

### 3. Enable Firebase Services

Make sure these are enabled in your Firebase Console:

- **Authentication** > Sign-in method > Email/Password
- **Firestore Database** (in production mode)

### 4. Run the Seeder

```bash
npm run seed
```

## What the Seeder Does

1. **Creates Authentication Users**: Creates Firebase Auth accounts for admin and instructors
2. **Seeds Firestore Collections**: Populates all required collections with sample data
3. **Handles Duplicates**: Safely handles existing users and data
4. **Provides Feedback**: Shows progress and results during seeding

## Login Credentials

After seeding, you can login with these accounts:

- **Admin**: `admin@gsportscenter.com` / `Admin@123`
- **Instructor**: `instructor@gsportscenter.com` / `Instructor@123`
- **Coach**: `coach@gsportscenter.com` / `Coach@123`

## Sample Data Overview

### Facilities
- Basketball Court A (Available, 50 capacity)
- Basketball Court B (Busy, 35/50 occupancy)
- Fitness Studio (Available, 30 capacity)
- Weight Training Area (Busy, 25/40 occupancy)
- Swimming Pool (Maintenance, 60 capacity)

### Classes
- Morning Yoga (Monday 6:00 AM, John Smith)
- HIIT Training (Tuesday 7:00 PM, Sarah Johnson)
- Pilates (Wednesday 6:30 AM, John Smith)
- Zumba Dance (Thursday 7:30 PM, Sarah Johnson)
- Strength Training (Friday 6:00 PM, John Smith - Inactive)

### Members
- Michael Chen (Monthly, Active)
- Lisa Rodriguez (Yearly, Active)
- David Kim (Monthly, Active)
- Emma Wilson (Yearly, Active)
- James Brown (Monthly, Inactive)

### Bookings
- Mix of confirmed, pending, and cancelled bookings
- Different facilities and time slots
- Various pricing (100k - 150k IDR)

## Troubleshooting

### "Missing environment variables"
- Make sure `.env.local` exists and has all required variables
- Check that variable names match exactly (including NEXT_PUBLIC_ prefix)

### "Firebase configuration is invalid"
- Verify your Firebase config values are correct
- Make sure your Firebase project exists and is active

### "Permission denied"
- Check that Firestore Database is created
- Verify Authentication is enabled with Email/Password

### "Email already in use"
- The seeder handles this gracefully and will update user documents
- This is normal if you run the seeder multiple times

## Customizing the Data

You can modify the sample data by editing these arrays in `scripts/seed.js`:

- `users` - Authentication users and roles
- `facilities` - Gym facilities and equipment
- `members` - Gym members
- `classes` - Fitness classes and schedules
- `bookings` - Facility reservations
- `attendance` - Class attendance records

## Security Notes

- The seeder creates users with simple passwords for testing
- Change passwords in production
- Consider setting up proper Firestore security rules
- Don't commit your `.env.local` file to version control

## Next Steps

After seeding:

1. Login to the admin panel at `http://localhost:3000`
2. Explore the dashboard and different features
3. Test booking management and class scheduling
4. Customize the data for your specific needs
5. Set up proper security rules in production