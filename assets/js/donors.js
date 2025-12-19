// Donors Management JavaScript

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Donor';
    document.getElementById('donorForm').reset();
    document.getElementById('donor_id').value = '';
    document.getElementById('is_active').checked = true;
    document.getElementById('formMessage').innerHTML = '';
    document.getElementById('donorModal').classList.add('show');
}

function closeModal() {
    document.getElementById('donorModal').classList.remove('show');
}

function viewDonor(id) {
    fetch(`../api/donors.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showDonorDetails(data.donor);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load donor details');
        });
}

function showDonorDetails(donor) {
    const donationsHTML = donor.donations.length > 0 
        ? donor.donations.map(d => `
            <tr>
                <td>${formatDate(d.donation_date)}</td>
                <td>${d.project_title || 'General'}</td>
                <td><strong>${formatCurrency(d.amount)}</strong></td>
            </tr>
        `).join('')
        : '<tr><td colspan="3" style="text-align:center; color:#999;">No donations yet</td></tr>';

    const html = `
        <div class="modal show" id="viewModal">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h3>Donor Details</h3>
                    <button class="modal-close" onclick="document.getElementById('viewModal').remove()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="donor-details-grid">
                        <div class="detail-section">
                            <h4>Personal Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Donor Code:</span>
                                <span class="detail-value badge badge-secondary">${donor.donor_code}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Full Name:</span>
                                <span class="detail-value">${donor.full_name}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">${donor.email || '-'}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value">${donor.phone || '-'}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">WhatsApp:</span>
                                <span class="detail-value">${donor.whatsapp || '-'}</span>
                            </div>
                        </div>
                        <div class="detail-section">
                            <h4>Address Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Address:</span>
                                <span class="detail-value">${donor.address || '-'}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">City:</span>
                                <span class="detail-value">${donor.city || '-'}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">District:</span>
                                <span class="detail-value">${donor.district || '-'}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">
                                    <span class="badge ${donor.is_active == 1 ? 'badge-success' : 'badge-danger'}">
                                        ${donor.is_active == 1 ? 'Active' : 'Inactive'}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section mt-20">
                        <h4>Donation Summary</h4>
                        <div class="stats-mini-grid">
                            <div class="mini-stat">
                                <div class="mini-stat-value">${formatCurrency(donor.total_donated)}</div>
                                <div class="mini-stat-label">Total Donated</div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat-value">${donor.donations.length}</div>
                                <div class="mini-stat-label">Total Donations</div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-section mt-20">
                        <h4>Donation History</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Project</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${donationsHTML}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="document.getElementById('viewModal').remove()">Close</button>
                    <button class="btn btn-primary" onclick="document.getElementById('viewModal').remove(); editDonor(${donor.id})">Edit Donor</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', html);
}

function editDonor(id) {
    fetch(`../api/donors.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const donor = data.donor;
                document.getElementById('modalTitle').textContent = 'Edit Donor';
                document.getElementById('donor_id').value = donor.id;
                document.getElementById('full_name').value = donor.full_name;
                document.getElementById('email').value = donor.email || '';
                document.getElementById('phone').value = donor.phone || '';
                document.getElementById('whatsapp').value = donor.whatsapp || '';
                document.getElementById('address').value = donor.address || '';
                document.getElementById('city').value = donor.city || '';
                document.getElementById('district').value = donor.district || '';
                document.getElementById('postal_code').value = donor.postal_code || '';
                document.getElementById('notes').value = donor.notes || '';
                document.getElementById('is_active').checked = donor.is_active == 1;
                document.getElementById('formMessage').innerHTML = '';
                document.getElementById('donorModal').classList.add('show');
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load donor details');
        });
}

function deleteDonor(id, name) {
    if (!confirm(`Are you sure you want to delete donor "${name}"?\n\nThis action cannot be undone.`)) {
        return;
    }

    fetch('../api/donors.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to delete donor');
    });
}

// Form submission
document.getElementById('donorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const donorId = formData.get('donor_id');
    formData.append('action', donorId ? 'update' : 'create');

    fetch('../api/donors.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            document.getElementById('formMessage').innerHTML = 
                `<div class="alert alert-error">${data.message}</div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('formMessage').innerHTML = 
            '<div class="alert alert-error">Failed to save donor</div>';
    });
});

// Close modal on outside click
document.getElementById('donorModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Helper functions
function formatCurrency(amount) {
    return '৳ ' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB');
}
