# Quick Start Guide - Responsive Dashboard

## Getting Started

### 1. Access the Dashboard
```
Open: /dashboard-prototype/index.html
OR
Open: /dashboard-prototype/login.html (directly to login)
```

### 2. Demo Credentials
```
Email:    admin@toolrental.com
Password: password123
```

### 3. What's New - Responsive Features

#### Desktop (1024px+)
- Click **☰ (hamburger icon)** in header to collapse sidebar
- Sidebar shrinks from 256px to 80px
- Content area expands automatically
- Preference saved in browser

#### Tablet (768px - 1024px)
- Sidebar always visible
- Responsive grid layouts
- Touch-friendly buttons

#### Mobile (< 768px)
- Click **☰** to open sidebar (slides from left)
- Dark overlay behind sidebar
- Click overlay or link to close sidebar
- Full-width content when sidebar closed

---

## File Structure

```
dashboard-prototype/
├── index.html                      ← Start here!
├── login.html                      ← Login page
├── dashboard.html                  ← Dashboard
├── RESPONSIVE-DESIGN.md            ← Full documentation
├── QUICK-START.md                  ← This file
├── README.md                       ← Original docs
│
├── js/
│   ├── layout.js                   ← Responsive sidebar logic
│   ├── app.js                      ← Main app module
│   ├── dummy-data.js               ← Mock data
│   ├── tables.js                   ← Table utilities
│   ├── forms.js                    ← Form utilities
│   └── charts.js                   ← Chart utilities
│
├── master/
│   ├── tools.html
│   ├── categories.html
│   ├── warehouses.html
│   ├── customers.html
│   ├── pricing.html
│   └── users.html
│
├── stock/
│   ├── stock-overview.html
│   └── stock-movement.html
│
├── rentals/
│   ├── rentals.html
│   └── create-rental.html
│
├── shipping/
│   ├── shipping-list.html
│   └── create-shipping.html
│
├── monitoring/
│   └── active-rentals.html
│
├── returns/
│   ├── returns.html
│   └── return-form.html
│
├── special/
│   ├── lost-tools.html
│   └── sold-tools.html
│
└── reports/
    ├── rental-report.html
    ├── revenue-report.html
    └── inventory-report.html
```

---

## Key Responsive Features

### 1. Collapsible Sidebar (Desktop Only)
```
Before Click        After Click
┌────────┐         ┌──┐
│ Tools  │         │🔧│
│ Cat... │   →     │📦│
│ Stock  │         │📋│
└────────┘         └──┘
256px wide         80px wide
```

### 2. Mobile Sidebar (Mobile Only)
```
Before Click        After Click
┌──────────────┐   ┌──┐┌────────┐
│ ☰ Dashboard  │   │☰││ Tools  │
│              │   ││ │ Cat... │
│ Content...   │ → ││ │ Stock  │
│              │   │└─└────────┘
└──────────────┘   └────────────┘
                   Overlay visible
```

### 3. Responsive Grids
```
Mobile (1 column)    Tablet (2 col)       Desktop (3+ col)
┌──────────┐        ┌─────────┬────────┐  ┌──┬──┬──┬──┐
│ Card 1   │        │ Card 1  │ Card 2 │  │C1│C2│C3│C4│
├──────────┤        ├─────────┼────────┤  ├──┼──┼──┼──┤
│ Card 2   │        │ Card 3  │ Card 4 │  │..............│
├──────────┤        ├─────────┼────────┤  └──────────────┘
│ Card 3   │        │ Card 5  │ Card 6 │
└──────────┘        └─────────┴────────┘
```

---

## Testing on Different Devices

### Resize Browser Window
1. Open dashboard in browser
2. Press **F12** (or Ctrl+Shift+I)
3. Click device toggle (top left of DevTools)
4. Select different devices to test

### Test Scenarios

| Device | Size | Test |
|--------|------|------|
| iPhone | 375px | Click ☰, sidebar slides in |
| iPad | 768px | Sidebar visible, grid 2-col |
| Laptop | 1366px | Sidebar visible, can collapse |
| Desktop 4K | 2560px | Full features, optimized spacing |

---

## Browser Support

✅ Works on:
- Chrome/Edge 88+
- Firefox 87+
- Safari 14+
- Mobile Safari (iOS)
- Chrome Mobile (Android)

---

## Common Tasks

### Collapse Sidebar (Desktop)
1. Click **☰** in header
2. Sidebar shrinks to 80px
3. Hover icons for tooltips
4. Click again to expand

### Open Sidebar (Mobile)
1. Click **☰** in header
2. Sidebar slides in from left
3. Click any menu item to navigate
4. Sidebar auto-closes

### Restore Preferences
```
If sidebar looks wrong:
1. Press F12 to open DevTools
2. Go to Application → LocalStorage
3. Find sidebarCollapsed key
4. Delete it and refresh
```

### Test Mobile View
```
Method 1: Browser DevTools
- F12 → Click device icon → Select phone

Method 2: Direct Resize
- Resize browser window to < 768px width
- Observe hamburger menu appears

Method 3: Online Tools
- Use responsive.is or similar tool
- Test with actual mobile device
```

---

## Troubleshooting

### Sidebar Not Collapsing?
- Check window width > 1024px
- Clear browser cache (Ctrl+Shift+Delete)
- Check console for JavaScript errors (F12)

### Mobile Menu Not Working?
- Check viewport meta tag is present
- Test on actual device (not just DevTools)
- Ensure JavaScript enabled
- Try different browser

### Layout Looking Wrong?
- Hard refresh page (Ctrl+F5)
- Clear localStorage
- Check zoom level is 100%
- Verify screen orientation

### Charts Not Showing?
- Wait 2-3 seconds for Chart.js to load
- Check internet connection
- Open console (F12) for errors
- Reload page

---

## Development Tips

### Modify Responsive Breakpoints
Edit `js/layout.js`, section `responsiveStyles`:
```css
@media (max-width: 768px) {
  /* Change 768 to different value */
}
```

### Customize Sidebar Width
Edit `js/layout.js`:
```css
#sidebar { width: 256px; }           /* Full width */
#sidebar.collapsed { width: 80px; }  /* Collapsed width */
```

### Add New Page
1. Create new HTML file in appropriate folder
2. Include script tags:
   ```html
   <script src="../js/dummy-data.js"></script>
   <script src="../js/app.js"></script>
   <script src="../js/layout.js"></script>
   ```
3. Add link to sidebar in `js/layout.js`
4. Content goes in `#page-content` div

---

## What's Next?

- [ ] Explore all 30+ pages
- [ ] Test on different devices
- [ ] Try collapse/expand sidebar
- [ ] Create sample data (edit CRUD modals)
- [ ] Generate reports
- [ ] Check responsive design at different widths

---

## Support

For detailed information, see **RESPONSIVE-DESIGN.md**

Questions? Check the documentation files:
- `README.md` - Full system documentation
- `RESPONSIVE-DESIGN.md` - Detailed responsive guide
- `QUICK-START.md` - This quick reference

Enjoy exploring the responsive dashboard! 🚀
