# Setup Instructions for AgroCraft Updates

## Step 1: Database Setup - Create Messages Table

### Option A: Using phpMyAdmin (Recommended for beginners)

1. **Open phpMyAdmin**:
   - Go to `http://localhost/phpmyadmin` in your browser
   - Login with your MySQL credentials (usually username: `root`, password: empty)

2. **Select Your Database**:
   - Click on your database name in the left sidebar (likely `impulse101` or `agrocraft`)
   - If you're not sure, check `Includes/db.php` to see which database is being used

3. **Import the SQL File**:
   - Click on the **"SQL"** tab at the top
   - Open the file `Database/messages_table.sql` in a text editor
   - Copy all the SQL code from that file
   - Paste it into the SQL text area in phpMyAdmin
   - Click the **"Go"** button

   OR

   - Click on the **"Import"** tab
   - Click **"Choose File"** and select `Database/messages_table.sql`
   - Click **"Go"**

4. **Verify the Table was Created**:
   - Look in the left sidebar under your database
   - You should see a new table called `messages`
   - Click on it to verify it has the correct columns:
     - message_id
     - sender_id
     - sender_type
     - receiver_id
     - receiver_type
     - message_text
     - timestamp
     - is_read

### Option B: Using MySQL Command Line

1. **Open Command Prompt/Terminal**:
   - Navigate to your project directory:
     ```bash
     cd C:\xampp\htdocs\agrocraft
     ```

2. **Login to MySQL**:
   ```bash
     mysql -u root -p
     ```
   - Enter your MySQL password (press Enter if no password)

3. **Select Your Database**:
   ```sql
     USE impulse101;
     ```
   (Replace `impulse101` with your actual database name)

4. **Run the SQL File**:
   ```sql
     source Database/messages_table.sql;
     ```

5. **Verify**:
   ```sql
     SHOW TABLES;
     DESCRIBE messages;
     ```

---

## Step 2: OpenWeatherMap API Setup

### Getting Your Free API Key

1. **Sign Up for OpenWeatherMap**:
   - Go to: https://openweathermap.org/api
   - Click **"Sign Up"** or **"Sign In"** if you already have an account
   - Fill in the registration form:
     - Username
     - Email
     - Password
     - Confirm you're not a robot
   - Click **"Create Account"**

2. **Verify Your Email**:
   - Check your email inbox
   - Click the verification link sent by OpenWeatherMap
   - This may take a few minutes

3. **Get Your API Key**:
   - After logging in, go to: https://home.openweathermap.org/api_keys
   - You'll see your **API key** (a long string of letters and numbers)
   - **Copy this key** - you'll need it in the next step
   - Note: The free tier allows 60 calls per minute, which is plenty for this project

### Configuring the API Key in Your Project

1. **Open Farmer Messages File**:
   - Navigate to: `FarmerPortal/Messages.php`
   - Open it in your code editor

2. **Find the API Key Line**:
   - Look for this line (around line 200-210):
     ```javascript
     var apiKey = 'YOUR_OPENWEATHER_API_KEY';
     ```

3. **Replace with Your Key**:
   - Replace `YOUR_OPENWEATHER_API_KEY` with your actual API key
   - It should look like:
     ```javascript
     var apiKey = 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6';
     ```
   - Make sure to keep the quotes around it!

4. **Do the Same for Buyer Messages**:
   - Open: `BuyerPortal2/Messages.php`
   - Find the same line and replace the API key
   - Save the file

### Testing the Weather Feature

1. **Login as a Farmer or Buyer**
2. **Navigate to Messages**:
   - For Farmers: Click "Messages" in the navigation
   - For Buyers: Go to `BuyerPortal2/Messages.php`
3. **Check the Weather Widget**:
   - You should see weather information displayed
   - If you see "Weather data unavailable", check:
     - API key is correctly set
     - You have internet connection
     - The location in your profile is valid

---

## Step 3: Testing All Features

### Test Login Pages

1. **Farmer Login**:
   - Go to: `http://localhost/agrocraft/auth/FarmerLogin.php`
   - You should see the new modern gradient design
   - Try logging in with existing credentials

2. **Buyer Login**:
   - Go to: `http://localhost/agrocraft/auth/BuyerLogin.php`
   - You should see the pink gradient design
   - Try logging in with existing credentials

### Test Registration Pages

1. **Farmer Registration**:
   - Go to: `http://localhost/agrocraft/auth/FarmerRegister.php`
   - You should see the new professional layout
   - Try creating a test account (or just view the design)

2. **Buyer Registration**:
   - Go to: `http://localhost/agrocraft/auth/BuyerRegistration.php`
   - You should see the new professional layout

### Test Delete Product Feature

1. **Login as a Farmer**
2. **Go to My Products**:
   - Navigate to: `FarmerPortal/MyProducts.php`
3. **Test Delete**:
   - You should see a red "Delete" button on each product
   - Click it and confirm
   - The product should be removed

### Test Messaging System

1. **Login as a Farmer**
2. **Go to Messages**:
   - Click "Messages" in the navigation
   - You should see the messaging interface
3. **Start a Conversation**:
   - If you have existing messages, click on a conversation
   - If not, you'll need to send a message from a buyer first
4. **Check Weather Widget**:
   - The weather information should appear at the top
   - It should show temperature, conditions, and humidity

5. **Test from Buyer Side**:
   - Login as a buyer
   - Go to a product detail page
   - Click "CHAT HERE" button
   - This should open the messaging interface with that farmer

---

## Troubleshooting

### Database Issues

**Problem**: "Table 'messages' doesn't exist"
- **Solution**: Make sure you ran the SQL file correctly
- Check that you selected the correct database
- Verify the table exists in phpMyAdmin

**Problem**: "Access denied" when running SQL
- **Solution**: Make sure you're logged in with the correct MySQL credentials
- Check `Includes/db.php` for the correct database name

### Weather API Issues

**Problem**: "Weather data unavailable"
- **Solutions**:
  1. Check that your API key is correctly set (no extra spaces)
  2. Verify your API key is active (login to OpenWeatherMap and check)
  3. Make sure you have internet connection
  4. Check browser console (F12) for JavaScript errors
  5. The location might not be found - try updating your profile with a major city name

**Problem**: "Invalid API key"
- **Solution**: 
  - Make sure you copied the entire API key
  - Check for any extra spaces or characters
  - Verify the key is active in your OpenWeatherMap account

### Messaging Issues

**Problem**: Messages not showing
- **Solution**: 
  - Make sure the messages table was created
  - Check that both users are logged in
  - Verify sender_id and receiver_id are correct

**Problem**: Can't send messages
- **Solution**:
  - Check browser console for errors (F12)
  - Verify database connection in `Includes/db.php`
  - Make sure the form is submitting correctly

### General Issues

**Problem**: Pages not loading or showing errors
- **Solution**:
  - Check that all files were saved correctly
  - Verify PHP error reporting is enabled
  - Check Apache/XAMPP is running
  - Look at error logs in XAMPP control panel

---

## Quick Reference

### File Locations

- **Database SQL**: `Database/messages_table.sql`
- **Farmer Messages**: `FarmerPortal/Messages.php`
- **Buyer Messages**: `BuyerPortal2/Messages.php`
- **Database Config**: `Includes/db.php`

### Important URLs

- **phpMyAdmin**: `http://localhost/phpmyadmin`
- **OpenWeatherMap API Keys**: `https://home.openweathermap.org/api_keys`
- **OpenWeatherMap Sign Up**: `https://openweathermap.org/api`

### Database Name

Check `Includes/db.php` to find your database name. It's usually:
- `impulse101`
- `agrocraft`
- Or your custom database name

---

## Next Steps After Setup

1. ✅ Database table created
2. ✅ API key configured
3. ✅ Test all features
4. ✅ Customize weather location (if needed)
5. ✅ Add more features as needed

## Need Help?

If you encounter any issues:
1. Check the error messages in your browser console (F12)
2. Check PHP error logs in XAMPP
3. Verify all files are in the correct locations
4. Make sure all dependencies are installed

---

**Note**: The weather API has a free tier that allows 60 calls per minute. For production use with many users, you may need to upgrade to a paid plan.

