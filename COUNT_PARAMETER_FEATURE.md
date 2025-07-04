# Count Parameter Feature Documentation

## Overview
This document describes the implementation of the count parameter feature for the questionnaire category creation form. This feature allows the "Tambah Kategori" buttons to pass a GET count parameter that reflects the current count for the selected type, and the create form uses this parameter to pre-fill the order field.

## Implementation Details

### 1. Updated "Tambah Kategori" Buttons in show.blade.php

The following buttons now include a `count` parameter in their URLs:

#### Alumni Category Buttons:
- **General Add Alumni Category Button**: Uses `count={{ $alumniCategories->count() + $bothCategories->count() }}`
- **Alumni Tab Add Category Button**: Uses `count={{ $alumniCategories->count() + $bothCategories->count() }}`
- **Empty State Alumni Button**: Uses `count=0`

#### Company Category Buttons:
- **General Add Company Category Button**: Uses `count={{ $companyCategories->count() + $bothCategories->count() }}`
- **Company Tab Add Category Button**: Uses `count={{ $companyCategories->count() + $bothCategories->count() }}`
- **Empty State Company Button**: Uses `count=0`

#### Both Category Buttons:
- **General Add Both Category Button**: Uses `count={{ $bothCategories->count() }}`
- **Empty State Both Button**: Uses `count=0`

### 2. Updated Form Default Value in create.blade.php

The order input field now uses the count parameter if available:

```php
value="{{ old('order', isset($_GET['count']) ? $_GET['count'] + 1 : $categories->count() + 1) }}"
```

This ensures that:
- If a `count` parameter is provided via GET, it uses `count + 1` as the default order
- If no `count` parameter is provided, it falls back to the global count of categories + 1
- The `old()` function takes precedence for form validation errors

### 3. Updated JavaScript Logic

The JavaScript `updateOrderValue()` function now checks for the URL count parameter:

```javascript
// Get count from URL parameter if available
const urlParams = new URLSearchParams(window.location.search);
const urlCount = urlParams.get('count');

function updateOrderValue() {
    const selectedForType = forTypeSelect.value;
    let count = 0;
    
    // If URL count parameter is provided, use it instead of calculating
    if (urlCount !== null) {
        count = parseInt(urlCount);
    } else {
        // Use global ordering - count all existing categories regardless of type
        count = categoriesData.length;
    }
    
    // Update the order input
    const orderInput = document.getElementById('order');
    orderInput.value = count + 1;
    
    // Update preview
    document.getElementById('preview-order').textContent = 'Urutan: ' + (count + 1);
}
```

### 4. Updated Template Functions

The `fillTemplate()` function also uses the count parameter:

```javascript
function fillTemplate(name, order, forType) {
    // ... existing code ...
    
    // Calculate correct order based on URL count parameter if available, otherwise use global count
    const urlParams = new URLSearchParams(window.location.search);
    const urlCount = urlParams.get('count');
    let count = 0;
    
    if (urlCount !== null) {
        count = parseInt(urlCount);
    } else {
        const categoriesData = JSON.parse(document.getElementById('categories-data').value);
        count = categoriesData.length;
    }
    
    document.getElementById('order').value = count + 1;
    
    // ... rest of the function ...
}
```

## How It Works

1. **User clicks "Tambah Kategori Alumni" button**: The URL includes `?for_type=alumni&count=X` where X is the current count of alumni + both categories
2. **User clicks "Tambah Kategori Perusahaan" button**: The URL includes `?for_type=company&count=Y` where Y is the current count of company + both categories
3. **User clicks "Kategori Umum" button**: The URL includes `?for_type=both&count=Z` where Z is the current count of both categories
4. **Form loads**: The order field is pre-filled with `count + 1` from the URL parameter
5. **JavaScript runs**: The `updateOrderValue()` function respects the URL count parameter and doesn't override it unless the user changes the type

## Benefits

- **Context-aware ordering**: The order field reflects the actual count for the selected category type
- **Consistent user experience**: Users see the correct "next" order number based on their selection
- **No manual calculation**: Users don't need to count existing categories manually
- **Fallback support**: If no count parameter is provided, the system falls back to global counting

## Example URLs

- Alumni category: `/admin/questionnaire/category/create/1?for_type=alumni&count=3`
- Company category: `/admin/questionnaire/category/create/1?for_type=company&count=2`
- Both category: `/admin/questionnaire/category/create/1?for_type=both&count=1`

## Files Modified

1. `/resources/views/admin/questionnaire/show.blade.php` - Added count parameters to all "Tambah Kategori" buttons
2. `/resources/views/admin/questionnaire/category/create.blade.php` - Updated default value logic and JavaScript to use count parameter
