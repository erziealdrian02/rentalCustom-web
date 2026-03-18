# START HERE - Getting Started Guide

Welcome! Follow these simple steps to get started with the Tool Rental Management Dashboard.

---

## 1️⃣ Open the Dashboard

**Option A: Using Browser**
```
1. Navigate to /dashboard-prototype/index.html
2. Click "Go to Login" button
3. Done! You're in.
```

**Option B: Direct Login**
```
Navigate directly to /dashboard-prototype/login.html
```

---

## 2️⃣ Login with Demo Credentials

```
Email:    admin@toolrental.com
Password: password123
```

Click "Sign In" button.

---

## 3️⃣ Explore the Dashboard

You're now in the main dashboard! You'll see:
- ✅ Dashboard overview with statistics
- ✅ Interactive charts and graphs
- ✅ Recent rentals and returns
- ✅ Sidebar navigation menu (on left)

---

## 4️⃣ Test the Responsive Features

### On Desktop (1024px+)
- **Click the ☰ (hamburger) icon** in the top-left header
- Watch the sidebar collapse from wide to narrow
- Click again to expand back to full width
- Your preference is saved! Reload the page and it remembers.

### On Mobile (< 1024px)
- **Click the ☰ (hamburger) icon** in the top-left header
- Sidebar slides in from the left
- Dark overlay appears behind it
- Click a menu item or the dark area to close
- Sidebar auto-closes when you navigate

### On Tablet (1024px)
- Sidebar always visible
- Can't collapse it (by design)
- Responsive layouts adapt automatically

---

## 5️⃣ Navigate Through Pages

Use the **sidebar menu** to explore:

**Master Data**
- Tools inventory
- Tool categories
- Warehouse locations
- Customer list
- Rental pricing
- User management

**Warehouse Stock**
- Stock overview by warehouse
- Stock movement history

**Rental Management**
- View all rentals
- Create new rental

**Shipping**
- Shipping list
- Create delivery notes

**Monitoring**
- Active rentals
- Track rental periods

**Returns**
- Return list
- Process returns

**Special Status**
- Lost tools
- Sold tools

**Reports**
- Rental reports
- Revenue reports
- Inventory reports

---

## 6️⃣ Try Interactive Features

### Add New Item
1. Go to any Master Data page (e.g., "Tools")
2. Click the **"Add New" button**
3. Fill in the form
4. Click "Save"
5. Item appears in the table!

### Edit Item
1. In any table, click the **"Edit" button** (pencil icon)
2. Modify the form
3. Click "Update"
4. Changes saved!

### Delete Item
1. In any table, click the **"Delete" button** (trash icon)
2. Confirm deletion
3. Item removed from table

### Create Rental
1. Go to **"Rental Transactions" → "Create Rental"**
2. Select customer and tool
3. Set rental dates
4. Price calculates automatically
5. Click "Create Rental"
6. Invoice number generated!

---

## 7️⃣ Understanding the Layout

### Header (Top)
```
[☰] Dashboard     [Search]              🔔  👤
^    ^            ^                      ^   ^
|    |            |                      |   |
|    Page Title   Search Box             |   User Avatar
Sidebar Toggle    (hidden on mobile)     Notification Notifications

On Mobile: Only ☰, title, and icons visible
On Desktop: Everything visible
```

### Sidebar (Left)
```
[Logo] ToolRental Pro
│
├─ Dashboard (with icon)
│
├─ MASTER DATA (section label)
│  ├─ Tools
│  ├─ Categories
│  ├─ Warehouses
│  ├─ Customers
│  ├─ Pricing
│  └─ Users
│
├─ WAREHOUSE STOCK
│  ├─ Stock Overview
│  └─ Stock Movement
│
├─ RENTAL TRANSACTIONS
│  ├─ Rental List
│  └─ Create Rental
│
├─ SHIPPING
│  ├─ Shipping List
│  └─ Create Delivery
│
├─ RENTAL MONITORING
│  └─ Active Rentals
│
├─ RETURNS
│  ├─ Return Tools
│  └─ Return Form
│
├─ SPECIAL STATUS
│  ├─ Lost Tools
│  └─ Sold Tools
│
├─ REPORTS
│  ├─ Rental Report
│  ├─ Revenue Report
│  └─ Inventory Report
│
└─ [Logout Button]

On Desktop: Can collapse to show icons only
On Mobile: Hidden, click ☰ to show
On Tablet: Always visible (can't collapse)
```

### Main Content Area
```
[Title / Breadcrumb]

[Cards with Statistics]      [Charts]

[Tables with Data]

Takes full width when sidebar is hidden
Adjusts automatically on all screen sizes
```

---

## 8️⃣ Responsive Design Features

### What Changes at Different Screen Sizes

| Size | Sidebar | Layout | View |
|------|---------|--------|------|
| 📱 Mobile | Hidden | 1 column | Hamburger menu |
| 📱 Tablet | Visible | 2-3 columns | Always on |
| 💻 Desktop | Visible/Collapsible | 3-5 columns | Toggle button |

---

## 9️⃣ Keyboard Shortcuts

While browsing:
- **F12** - Open browser DevTools (for debugging)
- **Ctrl+Shift+M** - Toggle responsive design mode
- **Escape** - Close modals/menus (when implemented)
- **Ctrl+F** - Search on page

---

## 🔟 Tips & Tricks

### ✅ Test Responsive Design
```
1. Press F12 to open DevTools
2. Click the device icon (top-left of DevTools)
3. Select different devices:
   - iPhone SE (375px)
   - iPad (768px)
   - Desktop (1366px)
   - Custom size
4. See layout change instantly!
```

### ✅ Save Your Preferences
```
Sidebar collapse preference is automatically saved.
If you collapsed the sidebar on desktop:
- Reload the page
- Sidebar stays collapsed!
Uses browser's localStorage (same as cookies)
```

### ✅ Clear Browser Data
```
If something looks wrong:
1. Press F12 to open DevTools
2. Right-click page → Inspect
3. Hard refresh: Ctrl+F5 (or Cmd+Shift+R on Mac)
4. Or: Ctrl+Shift+Delete to clear cache

This fixes 90% of issues!
```

### ✅ Check for Errors
```
1. Press F12 to open DevTools
2. Click "Console" tab
3. Look for red error messages
4. These help diagnose issues
```

---

## ❓ Frequently Asked Questions

### Q: The sidebar won't collapse on my mobile phone
**A:** That's correct! Sidebar collapse only works on desktop (1024px+). On mobile, use the hamburger menu instead.

### Q: How do I go back to login page?
**A:** Click "Logout" button at bottom of sidebar.

### Q: Where's my data saved?
**A:** Uses dummy/mock data in JavaScript. Reloading the page resets everything. This is a prototype - not connected to a database.

### Q: Can I change the colors?
**A:** Yes! Edit `js/layout.js` or dashboard pages to change TailwindCSS color classes (bg-blue-600, etc.).

### Q: Why does the sidebar look different on my phone?
**A:** Because it's responsive! The design adapts to your phone's screen size for better readability.

### Q: How do I test on different devices?
**A:** Use browser DevTools responsive mode (F12 → Device toggle) or test on real devices.

### Q: Is this a real system?
**A:** It's a prototype/demo. Uses fake data for demonstration. Perfect for learning and prototyping!

---

## 📚 Need More Information?

### Quick Reference (5 minutes)
- See `QUICK-START.md`

### Visual Demonstrations (10 minutes)
- See `VISUAL-GUIDE.md`

### Complete Technical Guide (30 minutes)
- See `RESPONSIVE-DESIGN.md`

### What Changed (10 minutes)
- See `IMPROVEMENTS.md`

### Full Documentation Index
- See `DOCUMENTATION.md`

---

## 🚀 You're Ready!

That's it! You now understand:
- ✅ How to log in
- ✅ How to navigate
- ✅ How responsive design works
- ✅ How to test on different devices
- ✅ Where to find more help

**Start exploring and have fun!**

---

## 📞 Still Need Help?

1. **Check Sidebar:** Is there a menu item for what you're looking for?
2. **Read Docs:** See `QUICK-START.md` for common questions
3. **Inspect:** Press F12 and look at styles/elements
4. **Clear Cache:** Hard refresh with Ctrl+F5
5. **Different Browser:** Try Chrome, Firefox, Safari

---

## Quick Links

| Want to... | See this file |
|---|---|
| Get started quickly | You're reading it! |
| Quick reference | `QUICK-START.md` |
| Visual examples | `VISUAL-GUIDE.md` |
| Technical details | `RESPONSIVE-DESIGN.md` |
| See what changed | `IMPROVEMENTS.md` |
| Full documentation | `DOCUMENTATION.md` |
| Original system info | `README.md` |

---

**Let's go!** 🎉

Click on any sidebar menu item to explore the dashboard.

Enjoy! 🚀
