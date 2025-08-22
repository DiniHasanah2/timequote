# CSV Download Guide for Quotation

## Overview
This guide explains how to use the new CSV download functionality alongside the existing PDF downloads.

## Files Created
1. **QuotationCsvController.php** - New controller for CSV generation
2. **quotation-download.js** - JavaScript for adding CSV download buttons
3. **Updated routes/web.php** - Added new CSV route

## Usage

### Method 1: Automatic Button Addition
The JavaScript file will automatically add a CSV download button next to the existing PDF button when you include the script in your layout.

### Method 2: Manual Usage
You can manually trigger downloads using these URLs:

- **PDF Download**: `/versions/{version-id}/generate-pdf`
- **CSV Download**: `/versions/{version-id}/generate-csv`

### Method 3: Direct Links
Add these links to your page:

```html
<!-- PDF Download -->
<a href="{{ route('versions.quotation.generate_pdf', $version->id) }}" class="btn btn-pink">
    <i class="bi bi-download"></i> Generate PDF
</a>

<!-- CSV Download -->
<a href="{{ route('versions.quotation.generate_csv', $version->id) }}" class="btn btn-success">
    <i class="bi bi-download"></i> Download CSV
</a>
```

## CSV File Contents
The CSV file includes:
- Customer information
- Project details
- Contract duration
- All service categories with pricing
- Detailed breakdown of services
- Total calculations including taxes

## Testing
To test the CSV functionality:
1. Navigate to any quotation page
2. Click the new "Download CSV" button
3. The CSV file will download automatically with all quotation data

## Requirements
- Laravel framework
- League CSV package (already installed via composer)
- No blade file modifications needed
