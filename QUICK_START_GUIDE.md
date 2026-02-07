# Quick Start Guide - AgroCraft Updates

## 🚀 Fast Setup (5 Minutes)

### Step 1: Create Database Table (2 minutes)

**Easiest Method - Use the Setup Script:**

1. Open your browser
2. Go to: `http://localhost/agrocraft/setup_database.php`
3. Click the **"Create Messages Table"** button
4. You should see a success message ✓

**Alternative Method - Using phpMyAdmin:**

1. Go to: `http://localhost/phpmyadmin`
2. Click on database **"impulse101"** (left sidebar)
3. Click **"SQL"** tab
4. Copy and paste this code:

```sql
CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `sender_type` varchar(20) NOT NULL COMMENT 'farmer or buyer',
  `receiver_id` int(11) NOT NULL,
  `receiver_type` varchar(20) NOT NULL COMMENT 'farmer or buyer',
  `message_text` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```

5. Click **"Go"**

---

### Step 2: Get Weather API Key (2 minutes)

1. **Sign Up** (free):
   - Visit: https://openweathermap.org/api
   - Click **"Sign Up"**
   - Fill the form and verify email

2. **Get Your Key**:
   - Login and go to: https://home.openweathermap.org/api_keys
   - Copy your API key (long string of letters/numbers)

3. **Add to Your Project**:
   - Open: `FarmerPortal/Messages.php`
   - Find line: `var apiKey = 'YOUR_OPENWEATHER_API_KEY';`
   - Replace `YOUR_OPENWEATHER_API_KEY` with your actual key
   - Do the same in: `BuyerPortal2/Messages.php`
   - Save both files

**Example:**
```javascript
// Before:
var apiKey = 'YOUR_OPENWEATHER_API_KEY';

// After:
var apiKey = 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6';
```

---

### Step 3: Test Everything (1 minute)

1. **Test Login Pages**:
   - `http://localhost/agrocraft/auth/FarmerLogin.php` ✓
   - `http://localhost/agrocraft/auth/BuyerLogin.php` ✓

2. **Test Delete Product**:
   - Login as farmer → My Products → Click Delete button ✓

3. **Test Messaging**:
   - Login as farmer → Click "Messages" in navigation
   - Weather widget should show at top ✓

---

## 📋 Checklist

- [ ] Database table created (use `setup_database.php`)
- [ ] OpenWeatherMap account created
- [ ] API key added to `FarmerPortal/Messages.php`
- [ ] API key added to `BuyerPortal2/Messages.php`
- [ ] Tested login pages
- [ ] Tested delete product feature
- [ ] Tested messaging system

---

## 🆘 Common Issues

### "Table doesn't exist" Error
→ Run `setup_database.php` or create table manually in phpMyAdmin

### "Weather data unavailable"
→ Check API key is correct (no extra spaces)
→ Make sure you have internet connection
→ Verify API key is active in OpenWeatherMap account

### Messages not working
→ Verify database table exists
→ Check both users are logged in
→ Look at browser console (F12) for errors

---

## 📁 Important Files

- **Database Setup**: `setup_database.php` (run in browser)
- **SQL File**: `Database/messages_table.sql`
- **Farmer Messages**: `FarmerPortal/Messages.php`
- **Buyer Messages**: `BuyerPortal2/Messages.php`
- **Full Instructions**: `SETUP_INSTRUCTIONS.md`

---

## 🎯 What's New?

✅ Modern login pages (gradient designs)
✅ Professional registration forms
✅ Delete product feature for farmers
✅ Real-time messaging system
✅ Weather information in messages

---

**Need Help?** Check `SETUP_INSTRUCTIONS.md` for detailed step-by-step guide with screenshots.

