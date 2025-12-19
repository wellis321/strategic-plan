<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(to right, #2563eb, #4f46e5);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .field {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .field:last-child {
            border-bottom: none;
        }
        .field-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            display: block;
        }
        .field-value {
            color: #6b7280;
            margin-top: 5px;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        .button:hover {
            background: #1d4ed8;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Organisation Registration Request</h1>
    </div>
    <div class="content">
        <p>A new organisation has requested registration for Simple Strategic Plans.</p>
        
        <div class="field">
            <span class="field-label">Organisation Name:</span>
            <div class="field-value"><?= h($organization_name) ?></div>
        </div>
        
        <div class="field">
            <span class="field-label">Email Domain:</span>
            <div class="field-value"><?= h($domain) ?></div>
        </div>
        
        <div class="field">
            <span class="field-label">Contact Name:</span>
            <div class="field-value"><?= h($contact_name) ?></div>
        </div>
        
        <div class="field">
            <span class="field-label">Contact Email:</span>
            <div class="field-value"><?= h($contact_email) ?></div>
        </div>
        
        <div class="field">
            <span class="field-label">Estimated Number of Users:</span>
            <div class="field-value"><?= h($estimated_seats) ?></div>
        </div>
        
        <?php if (!empty($additional_info)): ?>
        <div class="field">
            <span class="field-label">Additional Information:</span>
            <div class="field-value"><?= nl2br(h($additional_info)) ?></div>
        </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="<?= h($admin_url) ?>" class="button">Create Organisation</a>
        </div>
        
        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
            Click the button above to go directly to the organisation creation page, or visit:<br>
            <a href="<?= h($admin_url) ?>" style="color: #2563eb; word-break: break-all;"><?= h($admin_url) ?></a>
        </p>
    </div>
    <div class="footer">
        <p>This is an automated email from Simple Strategic Plans.</p>
        <p>This request was submitted through the organisation registration request form.</p>
    </div>
</body>
</html>

