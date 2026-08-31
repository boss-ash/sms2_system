# Research Progress Module - Student Pages Implementation Guide

This guide provides the complete implementation for the remaining student pages. All pages follow the same patterns established in `my-research.php`.

## Completed
- ✅ `my-research.php` - Research overview page

## Remaining Pages to Implement

### 1. research-plan.php
**Purpose:** Display and manage research plan details

**Key Features:**
- Display plan start date, target completion date
- Show current stage
- List all milestones with details
- Allow editing milestone notes (researcher notes field only)

**Structure:**
```php
// Get research plan using rpGetOrCreateResearchPlan()
// Display glass-panel with plan details
// Show milestones table with edit modal for notes
// Use Bootstrap modals for editing
```

### 2. milestones.php
**Purpose:** Detailed milestone management

**Key Features:**
- Card-based milestone display (one card per milestone)
- Show progress bar, status, dates, notes
- Display adviser remarks if present
- Button to submit progress update for each milestone

**Structure:**
```php
// Fetch all milestones
// Display each in glass-panel
// Add "Submit Progress Update" button → links to progress-updates.php?milestone_id=X
```

### 3. progress-updates.php
**Purpose:** Submit progress updates with duplicate prevention

**Key Features:**
- Form to select milestone
- Progress percentage slider (0-100)
- Status dropdown (In Progress, Submitted for Review)
- Text fields: Accomplishments, Problems/Blockers, Next Steps
- **DUPLICATE PREVENTION:**
  - Generate token on page load via API
  - Disable submit button onclick
  - Show loading state
  - Check for HTTP 409 response
  - Show error if duplicate detected

**Critical JavaScript:**
```javascript
let submitToken = null;

// Generate token on page load
fetch('/modules/crad/api/research-progress.php?action=generate_token')
    .then(r => r.json())
    .then(data => {
        submitToken = data.token;
    });

// Submit handler
const submitBtn = document.getElementById('submitProgressBtn');
const submitForm = document.getElementById('progressUpdateForm');

submitForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!submitToken) {
        alert('Please wait, initializing...');
        return;
    }
    
    // DUPLICATE PREVENTION: Disable button immediately
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
    
    const formData = new FormData(submitForm);
    const data = {
        action: 'submit_progress',
        milestone_id: formData.get('milestone_id'),
        new_progress: formData.get('new_progress'),
        milestone_status: formData.get('milestone_status'),
        update_title: formData.get('update_title'),
        accomplishments: formData.get('accomplishments'),
        problems_blockers: formData.get('problems_blockers'),
        next_planned_activity: formData.get('next_planned_activity'),
        submission_token: submitToken
    };
    
    try {
        const response = await fetch('/modules/crad/api/research-progress.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.status === 409) {
            // DUPLICATE DETECTED
            alert('Duplicate submission detected. This update was already submitted.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Submit Progress Update';
            return;
        }
        
        if (result.success) {
            // Success - regenerate token for next submission
            submitToken = null;
            alert('Progress update submitted successfully!');
            window.location.href = 'my-research.php';
        } else {
            alert(result.message || 'Failed to submit progress update');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Submit Progress Update';
        }
    } catch (error) {
        console.error(error);
        alert('Network error. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Submit Progress Update';
    }
});
```

### 4. adviser-feedback.php
**Purpose:** View all adviser feedback

**Key Features:**
- Timeline-style display of feedback
- Filter by milestone
- Filter by feedback type
- Show feedback text, milestone, date, adviser name

**Structure:**
```php
// Fetch feedback using API: get_adviser_feedback
// Display in timeline format
// Use badges for feedback types (Comment, Revision Request, Approved)
```

## UI Patterns to Follow

### Glass Panel Structure
```php
<div class="glass-panel mb-4">
    <div class="glass-panel-body">
        <div class="glass-panel-head">
            <div>
                <h5 class="glass-panel-title">Title</h5>
                <p class="glass-panel-sub">Subtitle</p>
            </div>
            <span class="glass-chip">Badge</span>
        </div>
        <!-- Content -->
    </div>
</div>
```

### Status Colors
- Not Started: `#94a3b8` (gray)
- In Progress: `#f59e0b` (orange)
- Submitted for Review: `#3b82f6` (blue)
- Revision Requested: `#ef4444` (red)
- Approved: `#10b981` (green)
- Completed: `#059669` (dark green)

### Icons
- Not Started: `fa-circle`
- In Progress: `fa-clock`
- Submitted: `fa-paper-plane`
- Revision: `fa-redo`
- Approved: `fa-check-circle`
- Completed: `fa-check-double`

## Duplicate Prevention Checklist

For ALL forms that submit data:
1. ✅ Generate submission token on page load
2. ✅ Include token in POST data
3. ✅ Disable submit button immediately onclick
4. ✅ Show loading state
5. ✅ Check for HTTP 409 (conflict) response
6. ✅ Display appropriate error message
7. ✅ Re-enable button only on error
8. ✅ Regenerate token after successful submission

## API Integration

All pages use:
- `/modules/crad/api/research-progress.php`
- GET actions: no token needed
- POST actions: require submission_token

## Testing

Test each page for:
1. ✅ Normal data display
2. ✅ Form submission works
3. ✅ Double-click button doesn't create duplicate
4. ✅ Refresh page doesn't re-submit
5. ✅ Multiple tabs don't cause duplicates
6. ✅ Network error handled gracefully

## Quick Implementation Command

To create all remaining pages quickly, copy the patterns from `my-research.php` and adjust:
- API calls
- Form fields
- Display logic

All pages should be placed in:
`f:\xampp\htdocs\SMS2_system\modules\student-portal\pages\`
