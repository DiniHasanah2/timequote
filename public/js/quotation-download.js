/**
 * Quotation Download Helper
 * This script adds CSV download functionality alongside existing PDF downloads
 * without modifying the blade files
 */

document.addEventListener('DOMContentLoaded', function() {
    // Find the existing PDF download button
    const pdfButton = document.querySelector('a[href*="versions.quotation.generate_pdf"]');
    
    if (pdfButton) {
        // Get the version ID from the PDF button URL
        const pdfUrl = pdfButton.getAttribute('href');
        const versionId = pdfUrl.match(/versions\/([a-f0-9-]+)\/generate-pdf/)?.[1];
        
        if (versionId) {
            // Create CSV download button
            const csvButton = document.createElement('a');
            csvButton.href = `/versions/${versionId}/generate-csv`;
            csvButton.className = 'btn btn-success';
            csvButton.innerHTML = '<i class="bi bi-download"></i> Download CSV';
            csvButton.style.marginLeft = '10px';
            
            // Insert after the PDF button
            pdfButton.parentNode.insertBefore(csvButton, pdfButton.nextSibling);
        }
    }
    
    // Alternative: Add both buttons in a dropdown format
    const downloadContainer = document.querySelector('.d-flex.gap-2.ms-auto');
    if (downloadContainer && !downloadContainer.querySelector('[data-csv-added]')) {
        const versionId = window.location.pathname.match(/versions\/([a-f0-9-]+)/)?.[1];
        
        if (versionId) {
            // Create CSV button
            const csvButton = document.createElement('a');
            csvButton.href = `/versions/${versionId}/generate-csv`;
            csvButton.className = 'btn btn-success';
            csvButton.innerHTML = '<i class="bi bi-download"></i> Download CSV';
            csvButton.setAttribute('data-csv-added', 'true');
            downloadContainer.appendChild(csvButton);
        }
    }
});

// Manual trigger functions
function downloadQuotationCSV(versionId) {
    if (versionId) {
        window.location.href = `/versions/${versionId}/generate-csv`;
    }
}

function downloadQuotationPDF(versionId) {
    if (versionId) {
        window.location.href = `/versions/${versionId}/generate-pdf`;
    }
}
