# Weather Page Access Guide

## Correct URLs to Access Weather Pages

### For Farmers:
```
http://localhost/agrocraft/FarmerPortal/Weather.php
```

**OR** navigate through the interface:
1. Login as Farmer: `http://localhost/agrocraft/auth/FarmerLogin.php`
2. After login, you'll be on the farmer homepage
3. Click "Weather" in the navigation bar

### For Buyers:
```
http://localhost/agrocraft/BuyerPortal2/Weather.php
```

**OR** navigate through the interface:
1. Login as Buyer: `http://localhost/agrocraft/auth/BuyerLogin.php`
2. After login, you'll be on the buyer homepage
3. Click "Settings" dropdown → "Weather"
   OR
   Click the mobile menu → "Weather"

## Common "Not Found" Errors

### Error: "The requested URL was not found on this server"

**Possible Causes:**

1. **Wrong URL Path**
   - ❌ Wrong: `http://localhost/Weather.php`
   - ❌ Wrong: `http://localhost/agrocraft/Weather.php`
   - ✅ Correct: `http://localhost/agrocraft/FarmerPortal/Weather.php`
   - ✅ Correct: `http://localhost/agrocraft/BuyerPortal2/Weather.php`

2. **Not Logged In**
   - Weather pages require login
   - If not logged in, you'll be redirected to login page
   - Make sure you're logged in first

3. **File Location Issue**
   - Files should be in:
     - `FarmerPortal/Weather.php`
     - `BuyerPortal2/Weather.php`
   - Verify files exist in these locations

4. **Apache/XAMPP Not Running**
   - Make sure XAMPP Apache is running
   - Check XAMPP Control Panel

## Quick Test

1. **Test Farmer Weather:**
   - Go to: `http://localhost/agrocraft/FarmerPortal/farmerHomepage.php`
   - Make sure you're logged in
   - Click "Weather" link in navigation

2. **Test Buyer Weather:**
   - Go to: `http://localhost/agrocraft/BuyerPortal2/bhome.php`
   - Make sure you're logged in
   - Click "Settings" → "Weather"

## File Structure

```
agrocraft/
├── FarmerPortal/
│   ├── Weather.php          ← Farmer weather page
│   ├── Messages.php
│   └── farmerHomepage.php
├── BuyerPortal2/
│   ├── Weather.php          ← Buyer weather page
│   ├── Messages.php
│   └── bhome.php
└── auth/
    ├── FarmerLogin.php
    └── BuyerLogin.php
```

## If Still Getting Errors

1. **Check File Permissions:**
   - Make sure files are readable
   - Check file extensions (.php not .txt)

2. **Check Apache Error Log:**
   - XAMPP → Apache → Logs → error.log
   - Look for specific error messages

3. **Verify Database Connection:**
   - Weather pages need database access
   - Check `Includes/db.php` is correct

4. **Check PHP Errors:**
   - Enable error reporting in PHP
   - Look for syntax errors

## Direct Access Test

If you want to test the files directly:

1. **Farmer Weather:**
   ```
   http://localhost/agrocraft/FarmerPortal/Weather.php
   ```
   (Will redirect to login if not logged in)

2. **Buyer Weather:**
   ```
   http://localhost/agrocraft/BuyerPortal2/Weather.php
   ```
   (Will redirect to login if not logged in)

## Navigation Links

The weather pages are accessible via:
- **Farmer**: Navigation bar → "Weather" link
- **Buyer**: Settings dropdown → "Weather" OR Mobile menu → "Weather"

Make sure you're accessing from the correct portal (FarmerPortal or BuyerPortal2)!

