# -*- coding: utf-8 -*-

NBSP = '\xa0'
ENDASH = '–'
RSQ = '’'

def dash(text):
    return NBSP + ENDASH + ' ' + text

replacements = {
3: [
    ('What is SB Document Tracking?', 'What is the Treasury Management System?'),
],
4: [
    ('What is SB Document Tracking', 'What is the Treasury Management System'),
    ('A Digital Solution for Modern LGU Document Management', 'A Digital Solution for Modern LGU Treasury Operations'),
    (NBSP + 'designed specifically for Local Government Units to manage official documents such as' + NBSP,
     NBSP + 'designed specifically for Municipal and City Treasurer' + RSQ + 's Offices to manage' + NBSP),
    ('ordinances and resolutions', 'collections, disbursements, and accountable forms'),
    (NBSP + 'with defined roles and permissions.',
     NBSP + 'with defined roles for Treasurers, Collectors, and Administrators.'),
    (NBSP + 'through automated audit trails.', NBSP + 'through automated activity logs and audit trails.'),
    ('Streamlines the entire document lifecycle' + ENDASH + 'from creation and review to approval and archiving.',
     'Streamlines the entire revenue cycle' + ENDASH + 'from collection and receipt issuance to bank deposit, reconciliation, and reporting.'),
    ('Document approvals and version control', 'Payment capture across cash, cheque, online, and money order'),
    ('Automated notifications and alerts', 'Serial-number and batch validation for accountable forms'),
    ('Advanced keyword-based search and filters', 'Bank deposit and cheque reconciliation'),
    ('Real-time reporting and monitoring dashboards', 'Real-time reporting and abstract generation'),
],
5: [
    ('Streamline document workflows', 'Streamline revenue collection'),
    (dash('Simplify the creation, management, and monitoring of ordinances, resolutions, and other official records.'),
     dash('Simplify the recording of Official Receipts, Real Property Tax, Cedulas, Marriage Certificates, and Burial Permits.')),
    (dash('Enable multiple users to work simultaneously with role-based access and permissions.'),
     dash('Enable multiple collectors and treasury staff to work simultaneously with role-based access.')),
    (dash('Implement complete audit trails and structured approval workflows.'),
     dash('Implement complete activity logs, cancel-request workflows, and serial-number controls.')),
    (dash('Automate notifications, reminders, and report generation to reduce manual tasks.'),
     dash('Automate payment capture, reconciliation, and report generation to reduce manual tallying.')),
    (dash('Use version control to track all document changes and preserve historical data.'),
     dash('Track every transaction, deposit, and cheque status from issuance to clearing.')),
    (dash('Provide advanced search and filtering tools for instant document access.'),
     dash('Provide search and filtering across collection logs, report logs, and activity records.')),
],
6: [
    ('Fully functional web-based document tracking system', 'Fully functional web-based Collection Management system across 6 accountable-form types'),
    ('Role-based access control (SB Secretary, Administrators, Members, etc.)', 'Role-based access control (Treasurer/Admin, Collector, Staff)'),
    ('Document creation, approval workflows, and version control', 'Official Receipts &amp; Accountable Forms tracking with batch and serial-number validation'),
    ('Automated notifications and email alerts', 'Banks Deposit &amp; Reconciliation (deposits, cheque clearing/bouncing, online payment confirmation)'),
    ('Advanced search and filtering', 'Cheque Management (issuance, cancellation, disbursement reporting)'),
    ('Report generation modules', 'Reporting &amp; Abstract generation (Treasurer' + RSQ + 's Monthly Report, CRAAF, Summary of Collections)'),
    ('Integration with external systems (e.g., national government portals, cloud storage services like Google Drive or OneDrive)',
     'Integration with external systems (e.g., national government e-payment portals, cloud storage services like Google Drive or OneDrive)'),
],
7: [
    ('Centralized overview with document statistics, pending approvals, recent activities, and notification summaries.',
     'Centralized overview with collection statistics, pending cancel requests, recent activity, and notification summaries.'),
    ('Document Management', 'Collection Management'),
    ('Create, edit, submit, review, approve, and archive ordinances/resolutions. Supports file attachments and version history.',
     'Record transactions across 6 form types' + ENDASH + 'Official Receipt, OR/RPT, Individual &amp; Corporation Cedula, Marriage Certificate, and Burial Permit' + ENDASH + 'capturing payment method and amount per transaction.'),
    ('Approval Workflow', 'Official Receipts &amp; Accountable Forms'),
    ('Configurable multi-level approval routing with email notifications at each stage.',
     'Add serial-numbered form batches, validate against duplicate or already-used serials, and track issued vs. remaining stock per form.'),
    ('Role-based access control' + ENDASH + 'assign permissions per user role (Admin, Secretary, Member, Staff).',
     'Role-based access control' + ENDASH + 'assign permissions per user role (Admin, Treasurer, Collector, Staff).'),
    ('Search and Filters', 'Banks Deposit &amp; Reconciliation'),
    ('Keyword search with filters by document type, status, date range, author, and department.',
     'Record bank deposits from pending cash, cheque, and money-order collections; confirm online payments; mark cheques cleared or bounced.'),
    ('Reports Module', 'Cheque Management'),
    ('Generate status reports, history logs, and performance summaries in printable/exportable formats.',
     'Issue and track disbursement cheques, cancel or reissue, and generate the Report of Checks Issued.'),
    ('Audit Trails', 'Reporting &amp; Abstract'),
    ('Complete activity log tracking every action (create, edit, view, approve, delete) with timestamps and user IDs.',
     'Generate the Treasurer' + RSQ + 's Monthly Report of Accountability, CRAAF, and Summary of Collections in styled, exportable formats.'),
    ('Notifications', 'Records'),
    ('Automated in-app and email alerts for pending tasks, approvals, and document updates.',
     'System-wide activity log searchable and filterable by module, with a separate Archive for completed or cancelled records.'),
],
8: [
    ('Conduct stakeholder interviews and workshops with LGU personnel.',
     'Conduct stakeholder interviews and workshops with Treasurer' + RSQ + 's Office personnel.'),
    ('Create UI/UX mockups and interactive prototypes.', 'Create UI/UX mockups matching official accountable-form layouts.'),
    ('Develop the dashboard, document modules, and user management.',
     'Develop the dashboard, Collection Management, and User Management modules.'),
    ('Implement search, reporting, security, and access control features.',
     'Implement Official Receipts &amp; Accountable Forms, Bank Deposit &amp; Reconciliation, and Cheque Management.'),
],
11: [
    ('Document Creation and Management (Ordinances/Resolutions)', 'Recording Collections (Official Receipt, OR/RPT, Cedula, Marriage, Burial)'),
    ('Uploading Supporting Documents and Attachments', 'Managing Official Receipts &amp; Accountable Forms (Batches and Serials)'),
    ('Searching, Filtering, and Retrieving Documents', 'Bank Deposit &amp; Reconciliation and Cheque Management'),
    ('SB Secretary', 'Municipal/City Treasurer'),
    ('Administrative Staff', 'Collectors and Treasury Staff'),
    ('Authorized SB Members', 'Accounting/Bookkeeping Staff'),
],
16: [
    ('"We are excited to help you modernize your document management processes."',
     '"We are excited to help you modernize your treasury and collections management processes."'),
],
}

for slide_num, pairs in replacements.items():
    path = 'unpacked/ppt/slides/slide%d.xml' % slide_num
    with open(path, encoding='utf-8') as f:
        content = f.read()
    for old, new in pairs:
        cnt = content.count(old)
        if cnt != 1:
            print('!!! slide%d: expected 1 occurrence, found %d: %r' % (slide_num, cnt, old))
        content = content.replace(old, new)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
print('done')
