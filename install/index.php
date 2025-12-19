<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Al-Khair</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .installer-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }

        .installer-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }

        .installer-header h1 {
            font-size: 32px;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .installer-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .installer-body {
            padding: 40px;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: #666;
            position: relative;
            z-index: 1;
        }

        .step.active {
            background: #667eea;
            color: #fff;
        }

        .step.completed {
            background: #10b981;
            color: #fff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #666;
            margin-right: 10px;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #3b82f6;
        }

        .requirement-list {
            list-style: none;
        }

        .requirement-list li {
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .requirement-list li:last-child {
            border-bottom: none;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-ok {
            background: #d1fae5;
            color: #065f46;
        }

        .status-fail {
            background: #fee2e2;
            color: #991b1b;
        }

        .progress-bar {
            height: 6px;
            background: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 13px;
        }

        .hidden {
            display: none;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }

        @media (max-width: 640px) {
            .installer-body {
                padding: 20px;
            }

            .installer-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1>Al-Khair Installation</h1>
            <p>Setup your donation management system in minutes</p>
        </div>

        <div class="installer-body">
            <div class="step-indicator">
                <div class="step active" id="step-1">1</div>
                <div class="step" id="step-2">2</div>
                <div class="step" id="step-3">3</div>
                <div class="step" id="step-4">4</div>
            </div>

            <div class="progress-bar">
                <div class="progress-fill" id="progress" style="width: 25%"></div>
            </div>

            <!-- Step 1: Requirements Check -->
            <div id="step-content-1" class="step-content">
                <h2 style="margin-bottom: 20px; color: #333;">System Requirements</h2>
                <ul class="requirement-list" id="requirements-list">
                    <li>
                        <span>Checking PHP Version...</span>
                        <span class="loading"></span>
                    </li>
                </ul>
                <div class="button-group">
                    <button class="btn btn-primary" onclick="checkRequirements()">Check Requirements</button>
                </div>
            </div>

            <!-- Step 2: Database Configuration -->
            <div id="step-content-2" class="step-content hidden">
                <h2 style="margin-bottom: 20px; color: #333;">Database Configuration</h2>
                <form id="database-form">
                    <div class="form-group">
                        <label for="db_host">Database Host</label>
                        <input type="text" id="db_host" name="db_host" value="localhost" required>
                        <div class="help-text">Usually "localhost" for shared hosting</div>
                    </div>
                    <div class="form-group">
                        <label for="db_name">Database Name</label>
                        <input type="text" id="db_name" name="db_name" required>
                    </div>
                    <div class="form-group">
                        <label for="db_user">Database Username</label>
                        <input type="text" id="db_user" name="db_user" required>
                    </div>
                    <div class="form-group">
                        <label for="db_pass">Database Password</label>
                        <input type="password" id="db_pass" name="db_pass">
                    </div>
                    <div id="db-message"></div>
                    <div class="button-group">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(1)">Back</button>
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Test & Continue</button>
                    </div>
                </form>
            </div>

            <!-- Step 3: Admin Account -->
            <div id="step-content-3" class="step-content hidden">
                <h2 style="margin-bottom: 20px; color: #333;">Create Admin Account</h2>
                <form id="admin-form">
                    <div class="form-group">
                        <label for="admin_name">Full Name</label>
                        <input type="text" id="admin_name" name="admin_name" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_username">Username</label>
                        <input type="text" id="admin_username" name="admin_username" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_email">Email Address</label>
                        <input type="email" id="admin_email" name="admin_email" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_password">Password</label>
                        <input type="password" id="admin_password" name="admin_password" required minlength="8">
                        <div class="help-text">At least 8 characters</div>
                    </div>
                    <div class="form-group">
                        <label for="admin_password_confirm">Confirm Password</label>
                        <input type="password" id="admin_password_confirm" name="admin_password_confirm" required>
                    </div>
                    <div id="admin-message"></div>
                    <div class="button-group">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(2)">Back</button>
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Continue</button>
                    </div>
                </form>
            </div>

            <!-- Step 4: Organization Settings -->
            <div id="step-content-4" class="step-content hidden">
                <h2 style="margin-bottom: 20px; color: #333;">Organization Information</h2>
                <form id="organization-form">
                    <div class="form-group">
                        <label for="org_name">Organization Name</label>
                        <input type="text" id="org_name" name="org_name" value="Al-Khair Foundation" required>
                    </div>
                    <div class="form-group">
                        <label for="org_address">Address</label>
                        <textarea id="org_address" name="org_address"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="org_phone">Phone Number</label>
                        <input type="text" id="org_phone" name="org_phone">
                    </div>
                    <div class="form-group">
                        <label for="org_email">Email Address</label>
                        <input type="email" id="org_email" name="org_email">
                    </div>
                    <div id="org-message"></div>
                    <div class="button-group">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(3)">Back</button>
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Complete Installation</button>
                    </div>
                </form>
            </div>

            <div class="footer-text">
                Developed by <strong>Tansiq Labs</strong>
            </div>
        </div>
    </div>

    <script>
        let installData = {
            database: {},
            admin: {},
            organization: {}
        };

        function goToStep(step) {
            // Hide all steps
            for (let i = 1; i <= 4; i++) {
                document.getElementById('step-content-' + i).classList.add('hidden');
                document.getElementById('step-' + i).classList.remove('active', 'completed');
            }

            // Show current step
            document.getElementById('step-content-' + step).classList.remove('hidden');
            document.getElementById('step-' + step).classList.add('active');

            // Mark previous steps as completed
            for (let i = 1; i < step; i++) {
                document.getElementById('step-' + i).classList.add('completed');
            }

            // Update progress bar
            document.getElementById('progress').style.width = (step * 25) + '%';
        }

        function showMessage(elementId, message, type) {
            const element = document.getElementById(elementId);
            element.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        }

        function checkRequirements() {
            fetch('install_process.php?action=check_requirements')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('requirements-list');
                    list.innerHTML = '';

                    let allPassed = true;
                    data.requirements.forEach(req => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <span>${req.name}</span>
                            <span class="status-badge status-${req.status ? 'ok' : 'fail'}">
                                ${req.status ? 'OK' : 'FAIL'}
                            </span>
                        `;
                        list.appendChild(li);
                        if (!req.status) allPassed = false;
                    });

                    const buttonGroup = list.parentElement.querySelector('.button-group');
                    if (allPassed) {
                        buttonGroup.innerHTML = '<button class="btn btn-primary" onclick="goToStep(2)">Continue to Database Setup</button>';
                    } else {
                        buttonGroup.innerHTML = '<div class="alert alert-error">Please fix the requirements above before continuing.</div>';
                    }
                });
        }

        // Database form
        document.getElementById('database-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'test_database');

            fetch('install_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Store database credentials without the action field
                    installData.database = {
                        db_host: formData.get('db_host'),
                        db_name: formData.get('db_name'),
                        db_user: formData.get('db_user'),
                        db_pass: formData.get('db_pass')
                    };
                    showMessage('db-message', data.message, 'success');
                    setTimeout(() => goToStep(3), 1500);
                } else {
                    showMessage('db-message', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('db-message', 'Connection failed. Please check your credentials.', 'error');
            });
        });

        // Admin form
        document.getElementById('admin-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const password = document.getElementById('admin_password').value;
            const confirmPassword = document.getElementById('admin_password_confirm').value;

            if (password !== confirmPassword) {
                showMessage('admin-message', 'Passwords do not match!', 'error');
                return;
            }

            if (password.length < 8) {
                showMessage('admin-message', 'Password must be at least 8 characters long!', 'error');
                return;
            }

            installData.admin = {
                admin_name: document.getElementById('admin_name').value,
                admin_username: document.getElementById('admin_username').value,
                admin_email: document.getElementById('admin_email').value,
                admin_password: password
            };
            
            showMessage('admin-message', 'Admin account validated!', 'success');
            setTimeout(() => goToStep(4), 800);
        });

        // Organization form
        document.getElementById('organization-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            installData.organization = {
                org_name: document.getElementById('org_name').value,
                org_address: document.getElementById('org_address').value,
                org_phone: document.getElementById('org_phone').value,
                org_email: document.getElementById('org_email').value
            };

            // Combine all data
            const completeData = new FormData();
            completeData.append('action', 'complete_installation');
            
            Object.keys(installData.database).forEach(key => {
                completeData.append(key, installData.database[key]);
            });
            Object.keys(installData.admin).forEach(key => {
                completeData.append(key, installData.admin[key]);
            });
            Object.keys(installData.organization).forEach(key => {
                completeData.append(key, installData.organization[key]);
            });

            showMessage('org-message', '<div class="loading" style="margin: 20px auto;"></div> Installing...', 'info');

            fetch('install_process.php', {
                method: 'POST',
                body: completeData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('org-message', data.message + ' Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = '../index.php';
                    }, 2000);
                } else {
                    showMessage('org-message', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Installation error:', error);
                showMessage('org-message', 'Installation failed. Please check console for details.', 'error');
            });
        });

        // Auto-check requirements on load
        window.addEventListener('load', checkRequirements);
    </script>
</body>
</html>
