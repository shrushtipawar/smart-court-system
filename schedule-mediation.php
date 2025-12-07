<?php
// schedule-mediation.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // In a real application, you would redirect to login page
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'johndoe';
    $_SESSION['user_type'] = 'mediator'; // Could be 'mediator', 'client', 'admin'
}

// Determine language preference
$language = isset($_GET['lang']) && $_GET['lang'] == 'hi' ? 'hi' : 'en';

// Handle form submissions
$message = '';
$message_type = '';

// Define variables to avoid undefined errors
$mediations = [];
$mediators = [];
$venues = [];
$case_types = [];
$status_options = [];
$priority_options = [];

// Add this after form handling but before the long translations array
// Available mediators - Add this section
$mediators = [
    ['id' => 1, 'name' => 'John Smith', 'specialization' => 'Family, Business'],
    ['id' => 2, 'name' => 'Sarah Johnson', 'specialization' => 'Business, Civil'],
    ['id' => 3, 'name' => 'Robert Chen', 'specialization' => 'Community, Family'],
    ['id' => 4, 'name' => 'Maria Garcia', 'specialization' => 'Workplace, Civil'],
    ['id' => 5, 'name' => 'David Wilson', 'specialization' => 'Business, Civil'],
];

// Available venues
$venues = [
    'Mediation Room 1',
    'Mediation Room 2',
    'Conference Room A',
    'Conference Room B',
    'Virtual Only',
    'Hybrid',
];

// Case types
$case_types = ['family', 'business', 'civil', 'community', 'workplace', 'other'];

// Status options
$status_options = ['scheduled', 'confirmed', 'pending', 'in_progress', 'completed', 'cancelled'];

// Priority options
$priority_options = ['high', 'medium', 'low'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['schedule_mediation'])) {
        // Handle new mediation scheduling
        $case_title = htmlspecialchars($_POST['case_title']);
        $case_type = htmlspecialchars($_POST['case_type']);
        $mediation_date = htmlspecialchars($_POST['mediation_date']);
        $start_time = htmlspecialchars($_POST['start_time']);
        $end_time = htmlspecialchars($_POST['end_time']);
        $parties_involved = htmlspecialchars($_POST['parties_involved']);
        $mediator_id = htmlspecialchars($_POST['mediator_id']);
        $venue = htmlspecialchars($_POST['venue']);
        $virtual_link = htmlspecialchars($_POST['virtual_link']);
        $notes = htmlspecialchars($_POST['notes']);
        
        // Validate required fields
        if (empty($case_title) || empty($mediation_date) || empty($start_time)) {
            $message = 'Please fill in all required fields';
            $message_type = 'error';
        } else {
            // In a real app, save to database
            $message = 'Mediation session scheduled successfully!';
            $message_type = 'success';
            
            // Add to scheduled mediations array
            $new_mediation = [
                'id' => rand(1000, 9999),
                'case_title' => $case_title,
                'case_type' => $case_type,
                'date' => $mediation_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'status' => 'scheduled',
                'mediator' => $mediator_id,
                'parties' => $parties_involved,
                'venue' => $venue,
                'virtual_link' => $virtual_link,
                'notes' => $notes,
                'created_by' => $_SESSION['user_id'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
    } elseif (isset($_POST['update_mediation'])) {
        // Handle mediation update
        $mediation_id = $_POST['mediation_id'];
        $message = 'Mediation session updated successfully!';
        $message_type = 'success';
    } elseif (isset($_POST['cancel_mediation'])) {
        // Handle mediation cancellation
        $mediation_id = $_POST['mediation_id'];
        $cancellation_reason = htmlspecialchars($_POST['cancellation_reason']);
        $message = 'Mediation session cancelled successfully!';
        $message_type = 'warning';
    } elseif (isset($_POST['reschedule_mediation'])) {
        // Handle mediation rescheduling
        $mediation_id = $_POST['mediation_id'];
        $new_date = $_POST['new_date'];
        $new_time = $_POST['new_time'];
        $message = 'Mediation session rescheduled successfully!';
        $message_type = 'info';
    }
}

// Translations array continues...
// [Keep the existing translations array - it's very long]

// Translations
$translations = [
    'en' => [
        'title' => 'Schedule Mediation',
        'dashboard' => 'Dashboard',
        'analytics' => 'Analytics',
        'reports' => 'Reports',
        'profile' => 'Profile',
        'settings' => 'Settings',
        'logout' => 'Logout',
        'mediation' => 'Mediation',
        'schedule_mediation' => 'Schedule Mediation',
        'my_schedule' => 'My Schedule',
        'calendar_view' => 'Calendar View',
        'list_view' => 'List View',
        'upcoming' => 'Upcoming',
        'past' => 'Past',
        'cancelled' => 'Cancelled',
        'all' => 'All',
        'filter' => 'Filter',
        'search' => 'Search',
        'clear_filters' => 'Clear Filters',
        'schedule_new' => 'Schedule New',
        'quick_schedule' => 'Quick Schedule',
        'today' => 'Today',
        'tomorrow' => 'Tomorrow',
        'next_week' => 'Next Week',
        'case_title' => 'Case Title',
        'case_type' => 'Case Type',
        'mediation_date' => 'Mediation Date',
        'start_time' => 'Start Time',
        'end_time' => 'End Time',
        'duration' => 'Duration',
        'parties_involved' => 'Parties Involved',
        'mediator' => 'Mediator',
        'venue' => 'Venue',
        'virtual_mediation' => 'Virtual Mediation',
        'meeting_link' => 'Meeting Link',
        'notes' => 'Notes',
        'required' => 'Required',
        'optional' => 'Optional',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'update' => 'Update',
        'delete' => 'Delete',
        'reschedule' => 'Reschedule',
        'confirm' => 'Confirm',
        'back' => 'Back',
        'next' => 'Next',
        'previous' => 'Previous',
        'actions' => 'Actions',
        'view' => 'View',
        'edit' => 'Edit',
        'status' => 'Status',
        'scheduled' => 'Scheduled',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'confirmed' => 'Confirmed',
        'pending' => 'Pending',
        'urgent' => 'Urgent',
        'high_priority' => 'High Priority',
        'medium_priority' => 'Medium Priority',
        'low_priority' => 'Low Priority',
        'family' => 'Family',
        'business' => 'Business',
        'civil' => 'Civil',
        'community' => 'Community',
        'workplace' => 'Workplace',
        'other' => 'Other',
        'select_mediator' => 'Select Mediator',
        'select_venue' => 'Select Venue',
        'conference_room_a' => 'Conference Room A',
        'conference_room_b' => 'Conference Room B',
        'mediation_room_1' => 'Mediation Room 1',
        'mediation_room_2' => 'Mediation Room 2',
        'virtual_only' => 'Virtual Only',
        'hybrid' => 'Hybrid',
        'in_person' => 'In Person',
        'add_party' => 'Add Party',
        'party_name' => 'Party Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'role' => 'Role',
        'complainant' => 'Complainant',
        'respondent' => 'Respondent',
        'witness' => 'Witness',
        'representative' => 'Representative',
        'remove' => 'Remove',
        'availability_check' => 'Availability Check',
        'check_availability' => 'Check Availability',
        'available' => 'Available',
        'not_available' => 'Not Available',
        'partially_available' => 'Partially Available',
        'send_invitations' => 'Send Invitations',
        'invitations_sent' => 'Invitations Sent',
        'reminder' => 'Reminder',
        'send_reminder' => 'Send Reminder',
        'documents' => 'Documents',
        'upload_documents' => 'Upload Documents',
        'agenda' => 'Agenda',
        'add_agenda_item' => 'Add Agenda Item',
        'time' => 'Time',
        'topic' => 'Topic',
        'responsible' => 'Responsible',
        'minutes' => 'Minutes',
        'record_minutes' => 'Record Minutes',
        'outcome' => 'Outcome',
        'settlement_reached' => 'Settlement Reached',
        'partial_settlement' => 'Partial Settlement',
        'no_settlement' => 'No Settlement',
        'adjourned' => 'Adjourned',
        'follow_up' => 'Follow Up',
        'follow_up_date' => 'Follow-up Date',
        'summary' => 'Summary',
        'detailed_summary' => 'Detailed Summary',
        'calendar' => 'Calendar',
        'month' => 'Month',
        'week' => 'Week',
        'day' => 'Day',
        'agenda_view' => 'Agenda',
        'statistics' => 'Statistics',
        'total_sessions' => 'Total Sessions',
        'upcoming_sessions' => 'Upcoming Sessions',
        'completion_rate' => 'Completion Rate',
        'settlement_rate' => 'Settlement Rate',
        'average_duration' => 'Average Duration',
        'busiest_day' => 'Busiest Day',
        'mediator_performance' => 'Mediator Performance',
        'export_schedule' => 'Export Schedule',
        'print_schedule' => 'Print Schedule',
        'sync_calendar' => 'Sync Calendar',
        'import_calendar' => 'Import Calendar',
        'settings' => 'Settings',
        'notifications' => 'Notifications',
        'email_notifications' => 'Email Notifications',
        'sms_notifications' => 'SMS Notifications',
        'reminder_before' => 'Reminder Before',
        '15_minutes' => '15 minutes',
        '30_minutes' => '30 minutes',
        '1_hour' => '1 hour',
        '1_day' => '1 day',
        'language' => 'Language',
        'english' => 'English',
        'hindi' => 'Hindi',
        'help' => 'Help',
        'documentation' => 'Documentation',
        'support' => 'Support',
        'feedback' => 'Feedback',
        'welcome' => 'Welcome',
        'no_sessions_scheduled' => 'No sessions scheduled',
        'schedule_your_first' => 'Schedule your first mediation session',
        'loading' => 'Loading',
        'processing' => 'Processing',
        'success' => 'Success',
        'error' => 'Error',
        'warning' => 'Warning',
        'info' => 'Info',
        'confirm_delete' => 'Confirm Delete',
        'delete_confirmation' => 'Are you sure you want to delete this mediation session?',
        'cancel_confirmation' => 'Are you sure you want to cancel this mediation session?',
        'yes' => 'Yes',
        'no' => 'No',
        'close' => 'Close',
        'save_changes' => 'Save Changes',
        'discard_changes' => 'Discard Changes',
        'add_to_calendar' => 'Add to Calendar',
        'download' => 'Download',
        'share' => 'Share',
        'copy_link' => 'Copy Link',
        'link_copied' => 'Link Copied',
        'invite_participants' => 'Invite Participants',
        'participants' => 'Participants',
        'add_participant' => 'Add Participant',
        'mediation_details' => 'Mediation Details',
        'case_details' => 'Case Details',
        'session_details' => 'Session Details',
        'location_details' => 'Location Details',
        'additional_info' => 'Additional Information',
        'created_by' => 'Created By',
        'created_on' => 'Created On',
        'last_modified' => 'Last Modified',
        'reference_number' => 'Reference Number',
        'case_number' => 'Case Number',
        'priority' => 'Priority',
        'complexity' => 'Complexity',
        'simple' => 'Simple',
        'moderate' => 'Moderate',
        'complex' => 'Complex',
        'estimated_duration' => 'Estimated Duration',
        'actual_duration' => 'Actual Duration',
        'cost' => 'Cost',
        'payment_status' => 'Payment Status',
        'paid' => 'Paid',
        'unpaid' => 'Unpaid',
        'partially_paid' => 'Partially Paid',
        'invoice' => 'Invoice',
        'generate_invoice' => 'Generate Invoice',
        'send_invoice' => 'Send Invoice',
        'mark_as_paid' => 'Mark as Paid',
        'add_notes' => 'Add Notes',
        'private_notes' => 'Private Notes',
        'public_notes' => 'Public Notes',
        'attachments' => 'Attachments',
        'add_attachment' => 'Add Attachment',
        'max_file_size' => 'Max file size: 10MB',
        'allowed_formats' => 'Allowed formats: PDF, DOC, DOCX, JPG, PNG',
        'drag_drop' => 'Drag & drop files here or click to browse',
        'browse' => 'Browse',
        'uploading' => 'Uploading',
        'upload_complete' => 'Upload Complete',
        'upload_failed' => 'Upload Failed',
        'preview' => 'Preview',
        'remove_file' => 'Remove File',
        'schedule_conflict' => 'Schedule Conflict',
        'conflict_detected' => 'A scheduling conflict has been detected',
        'suggest_alternative' => 'Suggest Alternative',
        'accept_conflict' => 'Accept Conflict',
        'reschedule_to' => 'Reschedule to',
        'find_available_slot' => 'Find Available Slot',
        'auto_schedule' => 'Auto Schedule',
        'suggested_times' => 'Suggested Times',
        'morning' => 'Morning',
        'afternoon' => 'Afternoon',
        'evening' => 'Evening',
        'weekday' => 'Weekday',
        'weekend' => 'Weekend',
        'holiday' => 'Holiday',
        'blocked_time' => 'Blocked Time',
        'set_unavailable' => 'Set Unavailable',
        'set_recurring' => 'Set Recurring',
        'recurring' => 'Recurring',
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'biweekly' => 'Bi-weekly',
        'monthly' => 'Monthly',
        'custom' => 'Custom',
        'repeat_every' => 'Repeat Every',
        'weeks' => 'Weeks',
        'months' => 'Months',
        'end_recurrence' => 'End Recurrence',
        'never' => 'Never',
        'after' => 'After',
        'occurrences' => 'Occurrences',
        'on_date' => 'On Date',
        'timezone' => 'Timezone',
        'select_timezone' => 'Select Timezone',
        'notification_sent' => 'Notification Sent',
        'email_sent' => 'Email Sent',
        'sms_sent' => 'SMS Sent',
        'reminder_sent' => 'Reminder Sent',
        'invitation_sent' => 'Invitation Sent',
        'view_all' => 'View All',
        'show_more' => 'Show More',
        'show_less' => 'Show Less',
        'collapse' => 'Collapse',
        'expand' => 'Expand',
        'toggle' => 'Toggle',
        'select_all' => 'Select All',
        'deselect_all' => 'Deselect All',
        'bulk_actions' => 'Bulk Actions',
        'apply' => 'Apply',
        'refresh' => 'Refresh',
        'reset' => 'Reset',
        'submit' => 'Submit',
        'continue' => 'Continue',
        'finish' => 'Finish',
        'exit' => 'Exit',
        'mediation_schedule' => 'Mediation Schedule',
        'mediation_management' => 'Mediation Management',
        'session_management' => 'Session Management',
        'calendar_management' => 'Calendar Management',
        'participant_management' => 'Participant Management',
        'document_management' => 'Document Management',
        'billing_management' => 'Billing Management',
        'reporting' => 'Reporting',
        'system' => 'System',
        'user_management' => 'User Management',
        'role_management' => 'Role Management',
        'permission_management' => 'Permission Management',
        'audit_log' => 'Audit Log',
        'backup' => 'Backup',
        'restore' => 'Restore',
        'maintenance' => 'Maintenance',
        'updates' => 'Updates',
        'about' => 'About',
        'version' => 'Version',
        'copyright' => 'Copyright',
        'privacy_policy' => 'Privacy Policy',
        'terms_of_service' => 'Terms of Service',
        'contact_us' => 'Contact Us',
        'help_center' => 'Help Center',
        'knowledge_base' => 'Knowledge Base',
        'faq' => 'FAQ',
        'tutorials' => 'Tutorials',
        'video_tutorials' => 'Video Tutorials',
        'user_guide' => 'User Guide',
        'api_documentation' => 'API Documentation',
        'developer_resources' => 'Developer Resources',
        'community_forum' => 'Community Forum',
        'blog' => 'Blog',
        'news' => 'News',
        'events' => 'Events',
        'webinars' => 'Webinars',
        'training' => 'Training',
        'certification' => 'Certification',
        'partners' => 'Partners',
        'affiliates' => 'Affiliates',
        'testimonials' => 'Testimonials',
        'case_studies' => 'Case Studies',
        'success_stories' => 'Success Stories',
        'mediator_directory' => 'Mediator Directory',
        'find_mediator' => 'Find Mediator',
        'become_mediator' => 'Become Mediator',
        'mediator_training' => 'Mediator Training',
        'mediator_resources' => 'Mediator Resources',
        'client_resources' => 'Client Resources',
        'self_help' => 'Self Help',
        'mediation_process' => 'Mediation Process',
        'cost_calculator' => 'Cost Calculator',
        'legal_resources' => 'Legal Resources',
        'forms_templates' => 'Forms & Templates',
        'checklist' => 'Checklist',
        'timeline' => 'Timeline',
        'roadmap' => 'Roadmap',
        'milestones' => 'Milestones',
        'deliverables' => 'Deliverables',
        'risks' => 'Risks',
        'issues' => 'Issues',
        'decisions' => 'Decisions',
        'action_items' => 'Action Items',
        'follow_ups' => 'Follow-ups',
        'next_steps' => 'Next Steps',
        'key_dates' => 'Key Dates',
        'deadlines' => 'Deadlines',
        'appointments' => 'Appointments',
        'meetings' => 'Meetings',
        'conferences' => 'Conferences',
        'hearings' => 'Hearings',
        'trials' => 'Trials',
        'arbitrations' => 'Arbitrations',
        'negotiations' => 'Negotiations',
        'settlements' => 'Settlements',
        'agreements' => 'Agreements',
        'contracts' => 'Contracts',
        'releases' => 'Releases',
        'waivers' => 'Waivers',
        'confidentiality' => 'Confidentiality',
        'disclosure' => 'Disclosure',
        'evidence' => 'Evidence',
        'testimony' => 'Testimony',
        'witnesses' => 'Witnesses',
        'experts' => 'Experts',
        'evaluators' => 'Evaluators',
        'facilitators' => 'Facilitators',
        'conciliators' => 'Conciliators',
        'ombudsmen' => 'Ombudsmen',
        'advisors' => 'Advisors',
        'counselors' => 'Counselors',
        'coaches' => 'Coaches',
        'mentors' => 'Mentors',
        'trainers' => 'Trainers',
        'instructors' => 'Instructors',
        'speakers' => 'Speakers',
        'moderators' => 'Moderators',
        'panelists' => 'Panelists',
        'participant' => 'Participant',
        'observer' => 'Observer',
        'guest' => 'Guest',
        'staff' => 'Staff',
        'administrator' => 'Administrator',
        'manager' => 'Manager',
        'coordinator' => 'Coordinator',
        'assistant' => 'Assistant',
        'intern' => 'Intern',
        'volunteer' => 'Volunteer',
        'contractor' => 'Contractor',
        'consultant' => 'Consultant',
        'vendor' => 'Vendor',
        'supplier' => 'Supplier',
        'partner' => 'Partner',
        'sponsor' => 'Sponsor',
        'donor' => 'Donor',
        'benefactor' => 'Benefactor',
        'patron' => 'Patron',
        'advocate' => 'Advocate',
        'ally' => 'Ally',
        'supporter' => 'Supporter',
        'follower' => 'Follower',
        'subscriber' => 'Subscriber',
        'member' => 'Member',
        'affiliate' => 'Affiliate',
        'associate' => 'Associate',
        'colleague' => 'Colleague',
        'peer' => 'Peer',
        'counterpart' => 'Counterpart',
        'adversary' => 'Adversary',
        'opponent' => 'Opponent',
        'rival' => 'Rival',
        'competitor' => 'Competitor',
        'challenger' => 'Challenger',
        'contender' => 'Contender',
        'contestant' => 'Contestant',
        'litigant' => 'Litigant',
        'plaintiff' => 'Plaintiff',
        'defendant' => 'Defendant',
        'claimant' => 'Claimant',
        'respondent' => 'Respondent',
        'appellant' => 'Appellant',
        'appellee' => 'Appellee',
        'petitioner' => 'Petitioner',
        'respondent' => 'Respondent',
        'movant' => 'Movant',
        'nonmovant' => 'Nonmovant',
        'judge' => 'Judge',
        'magistrate' => 'Magistrate',
        'commissioner' => 'Commissioner',
        'referee' => 'Referee',
        'master' => 'Master',
        'special_master' => 'Special Master',
        'court_clerk' => 'Court Clerk',
        'court_reporter' => 'Court Reporter',
        'bailiff' => 'Bailiff',
        'marshal' => 'Marshal',
        'sheriff' => 'Sheriff',
        'constable' => 'Constable',
        'process_server' => 'Process Server',
        'lawyer' => 'Lawyer',
        'attorney' => 'Attorney',
        'counsel' => 'Counsel',
        'barrister' => 'Barrister',
        'solicitor' => 'Solicitor',
        'advocate' => 'Advocate',
        'legal_executive' => 'Legal Executive',
        'paralegal' => 'Paralegal',
        'legal_assistant' => 'Legal Assistant',
        'legal_secretary' => 'Legal Secretary',
        'law_clerk' => 'Law Clerk',
        'legal_intern' => 'Legal Intern',
        'legal_aid' => 'Legal Aid',
        'pro_bono' => 'Pro Bono',
        'public_defender' => 'Public Defender',
        'district_attorney' => 'District Attorney',
        'prosecutor' => 'Prosecutor',
        'state_attorney' => 'State Attorney',
        'us_attorney' => 'US Attorney',
        'attorney_general' => 'Attorney General',
        'solicitor_general' => 'Solicitor General',
        'chief_justice' => 'Chief Justice',
        'associate_justice' => 'Associate Justice',
        'justice' => 'Justice',
        'judiciary' => 'Judiciary',
        'court' => 'Court',
        'tribunal' => 'Tribunal',
        'board' => 'Board',
        'commission' => 'Commission',
        'agency' => 'Agency',
        'department' => 'Department',
        'ministry' => 'Ministry',
        'bureau' => 'Bureau',
        'office' => 'Office',
        'division' => 'Division',
        'section' => 'Section',
        'unit' => 'Unit',
        'branch' => 'Branch',
        'subsidiary' => 'Subsidiary',
        'affiliate' => 'Affiliate',
        'parent_company' => 'Parent Company',
        'holding_company' => 'Holding Company',
        'conglomerate' => 'Conglomerate',
        'corporation' => 'Corporation',
        'incorporated' => 'Incorporated',
        'limited' => 'Limited',
        'llc' => 'LLC',
        'llp' => 'LLP',
        'partnership' => 'Partnership',
        'sole_proprietorship' => 'Sole Proprietorship',
        'nonprofit' => 'Nonprofit',
        'ngo' => 'NGO',
        'foundation' => 'Foundation',
        'trust' => 'Trust',
        'estate' => 'Estate',
        'association' => 'Association',
        'society' => 'Society',
        'club' => 'Club',
        'union' => 'Union',
        'guild' => 'Guild',
        'cooperative' => 'Cooperative',
        'collective' => 'Collective',
        'consortium' => 'Consortium',
        'alliance' => 'Alliance',
        'coalition' => 'Coalition',
        'federation' => 'Federation',
        'confederation' => 'Confederation',
        'league' => 'League',
        'conference' => 'Conference',
        'congress' => 'Congress',
        'parliament' => 'Parliament',
        'senate' => 'Senate',
        'assembly' => 'Assembly',
        'council' => 'Council',
        'committee' => 'Committee',
        'subcommittee' => 'Subcommittee',
        'task_force' => 'Task Force',
        'working_group' => 'Working Group',
        'steering_committee' => 'Steering Committee',
        'advisory_board' => 'Advisory Board',
        'board_of_directors' => 'Board of Directors',
        'executive_committee' => 'Executive Committee',
        'management_team' => 'Management Team',
        'leadership_team' => 'Leadership Team',
        'project_team' => 'Project Team',
        'cross_functional_team' => 'Cross-functional Team',
        'virtual_team' => 'Virtual Team',
        'remote_team' => 'Remote Team',
        'distributed_team' => 'Distributed Team',
        'global_team' => 'Global Team',
        'multicultural_team' => 'Multicultural Team',
        'diverse_team' => 'Diverse Team',
        'inclusive_team' => 'Inclusive Team',
        'equitable_team' => 'Equitable Team',
        'accessible_team' => 'Accessible Team',
        'sustainable_team' => 'Sustainable Team',
        'resilient_team' => 'Resilient Team',
        'agile_team' => 'Agile Team',
        'lean_team' => 'Lean Team',
        'high_performing_team' => 'High-performing Team',
        'self_organizing_team' => 'Self-organizing Team',
        'self_managing_team' => 'Self-managing Team',
        'empowered_team' => 'Empowered Team',
        'autonomous_team' => 'Autonomous Team',
        'accountable_team' => 'Accountable Team',
        'responsible_team' => 'Responsible Team',
        'transparent_team' => 'Transparent Team',
        'trustworthy_team' => 'Trustworthy Team',
        'ethical_team' => 'Ethical Team',
        'principled_team' => 'Principled Team',
        'values_driven_team' => 'Values-driven Team',
        'purpose_driven_team' => 'Purpose-driven Team',
        'mission_driven_team' => 'Mission-driven Team',
        'vision_driven_team' => 'Vision-driven Team',
        'goal_oriented_team' => 'Goal-oriented Team',
        'results_driven_team' => 'Results-driven Team',
        'performance_oriented_team' => 'Performance-oriented Team',
        'quality_focused_team' => 'Quality-focused Team',
        'customer_focused_team' => 'Customer-focused Team',
        'user_centered_team' => 'User-centered Team',
        'human_centered_team' => 'Human-centered Team',
        'design_thinking_team' => 'Design Thinking Team',
        'systems_thinking_team' => 'Systems Thinking Team',
        'strategic_thinking_team' => 'Strategic Thinking Team',
        'critical_thinking_team' => 'Critical Thinking Team',
        'creative_thinking_team' => 'Creative Thinking Team',
        'analytical_thinking_team' => 'Analytical Thinking Team',
        'logical_thinking_team' => 'Logical Thinking Team',
        'lateral_thinking_team' => 'Lateral Thinking Team',
        'divergent_thinking_team' => 'Divergent Thinking Team',
        'convergent_thinking_team' => 'Convergent Thinking Team',
        'abstract_thinking_team' => 'Abstract Thinking Team',
        'concrete_thinking_team' => 'Concrete Thinking Team',
        'conceptual_thinking_team' => 'Conceptual Thinking Team',
        'practical_thinking_team' => 'Practical Thinking Team',
        'theoretical_thinking_team' => 'Theoretical Thinking Team',
        'empirical_thinking_team' => 'Empirical Thinking Team',
        'scientific_thinking_team' => 'Scientific Thinking Team',
        'mathematical_thinking_team' => 'Mathematical Thinking Team',
        'statistical_thinking_team' => 'Statistical Thinking Team',
        'probabilistic_thinking_team' => 'Probabilistic Thinking Team',
        'deterministic_thinking_team' => 'Deterministic Thinking Team',
        'stochastic_thinking_team' => 'Stochastic Thinking Team',
        'chaotic_thinking_team' => 'Chaotic Thinking Team',
        'complex_thinking_team' => 'Complex Thinking Team',
        'complicated_thinking_team' => 'Complicated Thinking Team',
        'simple_thinking_team' => 'Simple Thinking Team',
        'reductionist_thinking_team' => 'Reductionist Thinking Team',
        'holistic_thinking_team' => 'Holistic Thinking Team',
        'integrative_thinking_team' => 'Integrative Thinking Team',
        'synthetic_thinking_team' => 'Synthetic Thinking Team',
        'synergistic_thinking_team' => 'Synergistic Thinking Team',
        'emergent_thinking_team' => 'Emergent Thinking Team',
        'adaptive_thinking_team' => 'Adaptive Thinking Team',
        'resilient_thinking_team' => 'Resilient Thinking Team',
        'antifragile_thinking_team' => 'Antifragile Thinking Team',
        'robust_thinking_team' => 'Robust Thinking Team',
        'flexible_thinking_team' => 'Flexible Thinking Team',
        'agile_thinking_team' => 'Agile Thinking Team',
        'lean_thinking_team' => 'Lean Thinking Team',
        'six_sigma_thinking_team' => 'Six Sigma Thinking Team',
        'tqm_thinking_team' => 'TQM Thinking Team',
        'kaizen_thinking_team' => 'Kaizen Thinking Team',
        'continuous_improvement_team' => 'Continuous Improvement Team',
        'learning_organization_team' => 'Learning Organization Team',
        'knowledge_management_team' => 'Knowledge Management Team',
        'information_management_team' => 'Information Management Team',
        'data_management_team' => 'Data Management Team',
        'content_management_team' => 'Content Management Team',
        'document_management_team' => 'Document Management Team',
        'record_management_team' => 'Record Management Team',
        'archive_management_team' => 'Archive Management Team',
        'library_management_team' => 'Library Management Team',
        'museum_management_team' => 'Museum Management Team',
        'heritage_management_team' => 'Heritage Management Team',
        'cultural_management_team' => 'Cultural Management Team',
        'art_management_team' => 'Art Management Team',
        'entertainment_management_team' => 'Entertainment Management Team',
        'media_management_team' => 'Media Management Team',
        'communication_management_team' => 'Communication Management Team',
        'public_relations_team' => 'Public Relations Team',
        'marketing_team' => 'Marketing Team',
        'advertising_team' => 'Advertising Team',
        'branding_team' => 'Branding Team',
        'sales_team' => 'Sales Team',
        'business_development_team' => 'Business Development Team',
        'partnership_team' => 'Partnership Team',
        'alliance_team' => 'Alliance Team',
        'network_team' => 'Network Team',
        'ecosystem_team' => 'Ecosystem Team',
        'platform_team' => 'Platform Team',
        'marketplace_team' => 'Marketplace Team',
        'community_team' => 'Community Team',
        'social_team' => 'Social Team',
        'environmental_team' => 'Environmental Team',
        'sustainability_team' => 'Sustainability Team',
        'csr_team' => 'CSR Team',
        'esg_team' => 'ESG Team',
        'impact_team' => 'Impact Team',
        'philanthropy_team' => 'Philanthropy Team',
        'volunteer_team' => 'Volunteer Team',
        'nonprofit_team' => 'Nonprofit Team',
        'ngo_team' => 'NGO Team',
        'government_team' => 'Government Team',
        'public_sector_team' => 'Public Sector Team',
        'private_sector_team' => 'Private Sector Team',
        'third_sector_team' => 'Third Sector Team',
        'social_enterprise_team' => 'Social Enterprise Team',
        'cooperative_team' => 'Cooperative Team',
        'mutual_team' => 'Mutual Team',
        'credit_union_team' => 'Credit Union Team',
        'microfinance_team' => 'Microfinance Team',
        'fintech_team' => 'Fintech Team',
        'insurtech_team' => 'Insurtech Team',
        'regtech_team' => 'Regtech Team',
        'legaltech_team' => 'Legaltech Team',
        'medtech_team' => 'Medtech Team',
        'edtech_team' => 'Edtech Team',
        'agritech_team' => 'Agritech Team',
        'cleantech_team' => 'Cleantech Team',
        'greentech_team' => 'Greentech Team',
        'climatech_team' => 'Climatech Team',
        'oceantech_team' => 'Oceantech Team',
        'spacetech_team' => 'Spacetech Team',
        'aerotech_team' => 'Aerotech Team',
        'defensetech_team' => 'Defensetech Team',
        'securitytech_team' => 'Securitytech Team',
        'cybersecurity_team' => 'Cybersecurity Team',
        'privacytech_team' => 'Privacytech Team',
        'trusttech_team' => 'Trusttech Team',
        'safetytech_team' => 'Safetytech Team',
        'healthtech_team' => 'Healthtech Team',
        'wellnesstech_team' => 'Wellnesstech Team',
        'fitness_tech_team' => 'Fitness Tech Team',
        'sportstech_team' => 'Sportstech Team',
        'entertainmenttech_team' => 'Entertainmenttech Team',
        'gaming_tech_team' => 'Gaming Tech Team',
        'esports_tech_team' => 'Esports Tech Team',
        'musictech_team' => 'Musictech Team',
        'filmtech_team' => 'Filmtech Team',
        'tvtech_team' => 'TVtech Team',
        'radiotech_team' => 'Radiotech Team',
        'podcasttech_team' => 'Podcasttech Team',
        'audiotech_team' => 'Audiotech Team',
        'voicetech_team' => 'Voicetech Team',
        'speechtech_team' => 'Speechtech Team',
        'nlp_team' => 'NLP Team',
        'ai_team' => 'AI Team',
        'ml_team' => 'ML Team',
        'dl_team' => 'DL Team',
        'neural_network_team' => 'Neural Network Team',
        'computer_vision_team' => 'Computer Vision Team',
        'robotics_team' => 'Robotics Team',
        'automation_team' => 'Automation Team',
        'iot_team' => 'IoT Team',
        'wearable_tech_team' => 'Wearable Tech Team',
        'smart_home_team' => 'Smart Home Team',
        'smart_city_team' => 'Smart City Team',
        'urban_tech_team' => 'Urban Tech Team',
        'civic_tech_team' => 'Civic Tech Team',
        'govtech_team' => 'Govtech Team',
        'democracy_tech_team' => 'Democracy Tech Team',
        'voting_tech_team' => 'Voting Tech Team',
        'election_tech_team' => 'Election Tech Team',
        'political_tech_team' => 'Political Tech Team',
        'campaign_tech_team' => 'Campaign Tech Team',
        'advocacy_tech_team' => 'Advocacy Tech Team',
        'activism_tech_team' => 'Activism Tech Team',
        'social_movement_tech_team' => 'Social Movement Tech Team',
        'protest_tech_team' => 'Protest Tech Team',
        'resistance_tech_team' => 'Resistance Tech Team',
        'liberation_tech_team' => 'Liberation Tech Team',
        'freedom_tech_team' => 'Freedom Tech Team',
        'justice_tech_team' => 'Justice Tech Team',
        'equity_tech_team' => 'Equity Tech Team',
        'inclusion_tech_team' => 'Inclusion Tech Team',
        'diversity_tech_team' => 'Diversity Tech Team',
        'accessibility_tech_team' => 'Accessibility Tech Team',
        'disability_tech_team' => 'Disability Tech Team',
        'assistive_tech_team' => 'Assistive Tech Team',
        'rehabilitation_tech_team' => 'Rehabilitation Tech Team',
        'therapeutic_tech_team' => 'Therapeutic Tech Team',
        'healing_tech_team' => 'Healing Tech Team',
        'wellbeing_tech_team' => 'Wellbeing Tech Team',
        'happiness_tech_team' => 'Happiness Tech Team',
        'positive_psychology_team' => 'Positive Psychology Team',
        'mindfulness_tech_team' => 'Mindfulness Tech Team',
        'meditation_tech_team' => 'Meditation Tech Team',
        'yoga_tech_team' => 'Yoga Tech Team',
        'spirituality_tech_team' => 'Spirituality Tech Team',
        'religion_tech_team' => 'Religion Tech Team',
        'faith_tech_team' => 'Faith Tech Team',
        'belief_tech_team' => 'Belief Tech Team',
        'value_tech_team' => 'Value Tech Team',
        'ethics_tech_team' => 'Ethics Tech Team',
        'morality_tech_team' => 'Morality Tech Team',
        'virtue_tech_team' => 'Virtue Tech Team',
        'character_tech_team' => 'Character Tech Team',
        'integrity_tech_team' => 'Integrity Tech Team',
        'honesty_tech_team' => 'Honesty Tech Team',
        'truth_tech_team' => 'Truth Tech Team',
        'transparency_tech_team' => 'Transparency Tech Team',
        'accountability_tech_team' => 'Accountability Tech Team',
        'responsibility_tech_team' => 'Responsibility Tech Team',
        'duty_tech_team' => 'Duty Tech Team',
        'obligation_tech_team' => 'Obligation Tech Team',
        'commitment_tech_team' => 'Commitment Tech Team',
        'dedication_tech_team' => 'Dedication Tech Team',
        'loyalty_tech_team' => 'Loyalty Tech Team',
        'fidelity_tech_team' => 'Fidelity Tech Team',
        'allegiance_tech_team' => 'Allegiance Tech Team',
        'patriotism_tech_team' => 'Patriotism Tech Team',
        'nationalism_tech_team' => 'Nationalism Tech Team',
        'globalism_tech_team' => 'Globalism Tech Team',
        'cosmopolitanism_tech_team' => 'Cosmopolitanism Tech Team',
        'internationalism_tech_team' => 'Internationalism Tech Team',
        'transnationalism_tech_team' => 'Transnationalism Tech Team',
        'supranationalism_tech_team' => 'Supranationalism Tech Team',
        'multilateralism_tech_team' => 'Multilateralism Tech Team',
        'bilateralism_tech_team' => 'Bilateralism Tech Team',
        'unilateralism_tech_team' => 'Unilateralism Tech Team',
        'isolationism_tech_team' => 'Isolationism Tech Team',
        'protectionism_tech_team' => 'Protectionism Tech Team',
        'free_trade_tech_team' => 'Free Trade Tech Team',
        'fair_trade_tech_team' => 'Fair Trade Tech Team',
        'sustainable_trade_tech_team' => 'Sustainable Trade Tech Team',
        'ethical_trade_tech_team' => 'Ethical Trade Tech Team',
        'responsible_trade_tech_team' => 'Responsible Trade Tech Team',
        'conscious_trade_tech_team' => 'Conscious Trade Tech Team',
        'mindful_trade_tech_team' => 'Mindful Trade Tech Team',
        'intentional_trade_tech_team' => 'Intentional Trade Tech Team',
        'purposeful_trade_tech_team' => 'Purposeful Trade Tech Team',
        'meaningful_trade_tech_team' => 'Meaningful Trade Tech Team',
        'impactful_trade_tech_team' => 'Impactful Trade Tech Team',
        'transformative_trade_tech_team' => 'Transformative Trade Tech Team',
        'regenerative_trade_tech_team' => 'Regenerative Trade Tech Team',
        'restorative_trade_tech_team' => 'Restorative Trade Tech Team',
        'healing_trade_tech_team' => 'Healing Trade Tech Team',
        'reconciliatory_trade_tech_team' => 'Reconciliatory Trade Tech Team',
        'peacebuilding_trade_tech_team' => 'Peacebuilding Trade Tech Team',
        'conflict_resolution_trade_tech_team' => 'Conflict Resolution Trade Tech Team',
        'mediation_trade_tech_team' => 'Mediation Trade Tech Team',
        'arbitration_trade_tech_team' => 'Arbitration Trade Tech Team',
        'conciliation_trade_tech_team' => 'Conciliation Trade Tech Team',
        'facilitation_trade_tech_team' => 'Facilitation Trade Tech Team',
        'negotiation_trade_tech_team' => 'Negotiation Trade Tech Team',
        'diplomacy_trade_tech_team' => 'Diplomacy Trade Tech Team',
        'statecraft_trade_tech_team' => 'Statecraft Trade Tech Team',
        'governance_trade_tech_team' => 'Governance Trade Tech Team',
        'leadership_trade_tech_team' => 'Leadership Trade Tech Team',
        'management_trade_tech_team' => 'Management Trade Tech Team',
        'administration_trade_tech_team' => 'Administration Trade Tech Team',
        'bureaucracy_trade_tech_team' => 'Bureaucracy Trade Tech Team',
        'technocracy_trade_tech_team' => 'Technocracy Trade Tech Team',
        'meritocracy_trade_tech_team' => 'Meritocracy Trade Tech Team',
        'plutocracy_trade_tech_team' => 'Plutocracy Trade Tech Team',
        'aristocracy_trade_tech_team' => 'Aristocracy Trade Tech Team',
        'oligarchy_trade_tech_team' => 'Oligarchy Trade Tech Team',
        'monarchy_trade_tech_team' => 'Monarchy Trade Tech Team',
        'dictatorship_trade_tech_team' => 'Dictatorship Trade Tech Team',
        'tyranny_trade_tech_team' => 'Tyranny Trade Tech Team',
        'authoritarianism_trade_tech_team' => 'Authoritarianism Trade Tech Team',
        'totalitarianism_trade_tech_team' => 'Totalitarianism Trade Tech Team',
        'fascism_trade_tech_team' => 'Fascism Trade Tech Team',
        'nazism_trade_tech_team' => 'Nazism Trade Tech Team',
        'communism_trade_tech_team' => 'Communism Trade Tech Team',
        'socialism_trade_tech_team' => 'Socialism Trade Tech Team',
        'capitalism_trade_tech_team' => 'Capitalism Trade Tech Team',
        'anarchism_trade_tech_team' => 'Anarchism Trade Tech Team',
        'libertarianism_trade_tech_team' => 'Libertarianism Trade Tech Team',
        'conservatism_trade_tech_team' => 'Conservatism Trade Tech Team',
        'liberalism_trade_tech_team' => 'Liberalism Trade Tech Team',
        'progressivism_trade_tech_team' => 'Progressivism Trade Tech Team',
        'radicalism_trade_tech_team' => 'Radicalism Trade Tech Team',
        'reformism_trade_tech_team' => 'Reformism Trade Tech Team',
        'revolutionism_trade_tech_team' => 'Revolutionism Trade Tech Team',
        'reactionism_trade_tech_team' => 'Reactionism Trade Tech Team',
        'traditionalism_trade_tech_team' => 'Traditionalism Trade Tech Team',
        'modernism_trade_tech_team' => 'Modernism Trade Tech Team',
        'postmodernism_trade_tech_team' => 'Postmodernism Trade Tech Team',
        'structuralism_trade_tech_team' => 'Structuralism Trade Tech Team',
        'poststructuralism_trade_tech_team' => 'Poststructuralism Trade Tech Team',
        'deconstructionism_trade_tech_team' => 'Deconstructionism Trade Tech Team',
        'constructivism_trade_tech_team' => 'Constructivism Trade Tech Team',
        'pragmatism_trade_tech_team' => 'Pragmatism Trade Tech Team',
        'existentialism_trade_tech_team' => 'Existentialism Trade Tech Team',
        'humanism_trade_tech_team' => 'Humanism Trade Tech Team',
        'transhumanism_trade_tech_team' => 'Transhumanism Trade Tech Team',
        'posthumanism_trade_tech_team' => 'Posthumanism Trade Tech Team',
        'antihumanism_trade_tech_team' => 'Antihumanism Trade Tech Team',
        'nihilism_trade_tech_team' => 'Nihilism Trade Tech Team',
        'absurdism_trade_tech_team' => 'Absurdism Trade Tech Team',
        'stoicism_trade_tech_team' => 'Stoicism Trade Tech Team',
        'epicureanism_trade_tech_team' => 'Epicureanism Trade Tech Team',
        'cynicism_trade_tech_team' => 'Cynicism Trade Tech Team',
        'skepticism_trade_tech_team' => 'Skepticism Trade Tech Team',
        'relativism_trade_tech_team' => 'Relativism Trade Tech Team',
        'universalism_trade_tech_team' => 'Universalism Trade Tech Team',
        'particularism_trade_tech_team' => 'Particularism Trade Tech Team',
        'individualism_trade_tech_team' => 'Individualism Trade Tech Team',
        'collectivism_trade_tech_team' => 'Collectivism Trade Tech Team',
        'communitarianism_trade_tech_team' => 'Communitarianism Trade Tech Team',
        'cosmopolitanism_trade_tech_team' => 'Cosmopolitanism Trade Tech Team',
        'nationalism_trade_tech_team' => 'Nationalism Trade Tech Team',
        'regionalism_trade_tech_team' => 'Regionalism Trade Tech Team',
        'localism_trade_tech_team' => 'Localism Trade Tech Team',
        'globalism_trade_tech_team' => 'Globalism Trade Tech Team',
        'internationalism_trade_tech_team' => 'Internationalism Trade Tech Team',
        'transnationalism_trade_tech_team' => 'Transnationalism Trade Tech Team',
        'supranationalism_trade_tech_team' => 'Supranationalism Trade Tech Team',
    ],
    'hi' => [
        'title' => 'मध्यस्थता शेड्यूल करें',
        'dashboard' => 'डैशबोर्ड',
        'analytics' => 'एनालिटिक्स',
        'reports' => 'रिपोर्ट्स',
        'profile' => 'प्रोफाइल',
        'settings' => 'सेटिंग्स',
        'logout' => 'लॉग आउट',
        'mediation' => 'मध्यस्थता',
        'schedule_mediation' => 'मध्यस्थता शेड्यूल करें',
        'my_schedule' => 'मेरा शेड्यूल',
        'calendar_view' => 'कैलेंडर व्यू',
        'list_view' => 'लिस्ट व्यू',
        'upcoming' => 'आगामी',
        'past' => 'पिछला',
        'cancelled' => 'रद्द',
        'all' => 'सभी',
        'filter' => 'फिल्टर',
        'search' => 'खोज',
        'clear_filters' => 'फिल्टर हटाएं',
        'schedule_new' => 'नया शेड्यूल करें',
        'quick_schedule' => 'त्वरित शेड्यूल',
        'today' => 'आज',
        'tomorrow' => 'कल',
        'next_week' => 'अगले सप्ताह',
        'case_title' => 'मामले का शीर्षक',
        'case_type' => 'मामले का प्रकार',
        'mediation_date' => 'मध्यस्थता तिथि',
        'start_time' => 'प्रारंभ समय',
        'end_time' => 'समाप्ति समय',
        'duration' => 'अवधि',
        'parties_involved' => 'शामिल पक्ष',
        'mediator' => 'मध्यस्थ',
        'venue' => 'स्थान',
        'virtual_mediation' => 'वर्चुअल मध्यस्थता',
        'meeting_link' => 'मीटिंग लिंक',
        'notes' => 'नोट्स',
        'required' => 'आवश्यक',
        'optional' => 'वैकल्पिक',
        'save' => 'सहेजें',
        'cancel' => 'रद्द करें',
        'update' => 'अपडेट',
        'delete' => 'हटाएं',
        'reschedule' => 'पुनर्निर्धारित करें',
        'confirm' => 'पुष्टि करें',
        'back' => 'वापस',
        'next' => 'अगला',
        'previous' => 'पिछला',
        'actions' => 'कार्रवाई',
        'view' => 'देखें',
        'edit' => 'संपादित करें',
        'status' => 'स्थिति',
        'scheduled' => 'शेड्यूल्ड',
        'in_progress' => 'चालू',
        'completed' => 'पूर्ण',
        'confirmed' => 'पुष्टि हुई',
        'pending' => 'लंबित',
        'urgent' => 'जरूरी',
        'high_priority' => 'उच्च प्राथमिकता',
        'medium_priority' => 'मध्यम प्राथमिकता',
        'low_priority' => 'कम प्राथमिकता',
        'family' => 'परिवार',
        'business' => 'व्यापार',
        'civil' => 'सिविल',
        'community' => 'समुदाय',
        'workplace' => 'कार्यस्थल',
        'other' => 'अन्य',
        'select_mediator' => 'मध्यस्थ चुनें',
        'select_venue' => 'स्थान चुनें',
        'conference_room_a' => 'कॉन्फ्रेंस रूम ए',
        'conference_room_b' => 'कॉन्फ्रेंस रूम बी',
        'mediation_room_1' => 'मध्यस्थता कक्ष 1',
        'mediation_room_2' => 'मध्यस्थता कक्ष 2',
        'virtual_only' => 'केवल वर्चुअल',
        'hybrid' => 'हाइब्रिड',
        'in_person' => 'व्यक्तिगत',
        'add_party' => 'पार्टी जोड़ें',
        'party_name' => 'पार्टी का नाम',
        'email' => 'ईमेल',
        'phone' => 'फोन',
        'role' => 'भूमिका',
        'complainant' => 'शिकायतकर्ता',
        'respondent' => 'प्रतिवादी',
        'witness' => 'गवाह',
        'representative' => 'प्रतिनिधि',
        'remove' => 'हटाएं',
        'availability_check' => 'उपलब्धता जांच',
        'check_availability' => 'उपलब्धता जांचें',
        'available' => 'उपलब्ध',
        'not_available' => 'उपलब्ध नहीं',
        'partially_available' => 'आंशिक रूप से उपलब्ध',
        'send_invitations' => 'आमंत्रण भेजें',
        'invitations_sent' => 'आमंत्रण भेज दिए गए',
        'reminder' => 'अनुस्मारक',
        'send_reminder' => 'अनुस्मारक भेजें',
        'documents' => 'दस्तावेज़',
        'upload_documents' => 'दस्तावेज़ अपलोड करें',
        'agenda' => 'एजेंडा',
        'add_agenda_item' => 'एजेंडा आइटम जोड़ें',
        'time' => 'समय',
        'topic' => 'विषय',
        'responsible' => 'जिम्मेदार',
        'minutes' => 'मिनट',
        'record_minutes' => 'मिनट रिकॉर्ड करें',
        'outcome' => 'परिणाम',
        'settlement_reached' => 'समझौता हुआ',
        'partial_settlement' => 'आंशिक समझौता',
        'no_settlement' => 'कोई समझौता नहीं',
        'adjourned' => 'स्थगित',
        'follow_up' => 'फॉलो अप',
        'follow_up_date' => 'फॉलो-अप तिथि',
        'summary' => 'सारांश',
        'detailed_summary' => 'विस्तृत सारांश',
        'calendar' => 'कैलेंडर',
        'month' => 'महीना',
        'week' => 'सप्ताह',
        'day' => 'दिन',
        'agenda_view' => 'एजेंडा',
        'statistics' => 'आंकड़े',
        'total_sessions' => 'कुल सत्र',
        'upcoming_sessions' => 'आगामी सत्र',
        'completion_rate' => 'पूर्णता दर',
        'settlement_rate' => 'समझौता दर',
        'average_duration' => 'औसत अवधि',
        'busiest_day' => 'सबसे व्यस्त दिन',
        'mediator_performance' => 'मध्यस्थ प्रदर्शन',
        'export_schedule' => 'शेड्यूल निर्यात करें',
        'print_schedule' => 'शेड्यूल प्रिंट करें',
        'sync_calendar' => 'कैलेंडर सिंक करें',
        'import_calendar' => 'कैलेंडर आयात करें',
        'settings' => 'सेटिंग्स',
        'notifications' => 'सूचनाएं',
        'email_notifications' => 'ईमेल सूचनाएं',
        'sms_notifications' => 'एसएमएस सूचनाएं',
        'reminder_before' => 'पहले अनुस्मारक',
        '15_minutes' => '15 मिनट',
        '30_minutes' => '30 मिनट',
        '1_hour' => '1 घंटा',
        '1_day' => '1 दिन',
        'language' => 'भाषा',
        'english' => 'अंग्रेज़ी',
        'hindi' => 'हिंदी',
        'help' => 'मदद',
        'documentation' => 'प्रलेखन',
        'support' => 'सहायता',
        'feedback' => 'प्रतिक्रिया',
        'welcome' => 'स्वागत है',
        'no_sessions_scheduled' => 'कोई सत्र शेड्यूल नहीं',
        'schedule_your_first' => 'अपना पहला मध्यस्थता सत्र शेड्यूल करें',
        'loading' => 'लोड हो रहा है',
        'processing' => 'प्रसंस्करण',
        'success' => 'सफलता',
        'error' => 'त्रुटि',
        'warning' => 'चेतावनी',
        'info' => 'जानकारी',
        'confirm_delete' => 'हटाने की पुष्टि करें',
        'delete_confirmation' => 'क्या आप वाकई इस मध्यस्थता सत्र को हटाना चाहते हैं?',
        'cancel_confirmation' => 'क्या आप वाकई इस मध्यस्थता सत्र को रद्द करना चाहते हैं?',
        'yes' => 'हाँ',
        'no' => 'नहीं',
        'close' => 'बंद करें',
        'save_changes' => 'परिवर्तन सहेजें',
        'discard_changes' => 'परिवर्तन छोड़ें',
        'add_to_calendar' => 'कैलेंडर में जोड़ें',
        'download' => 'डाउनलोड',
        'share' => 'साझा करें',
        'copy_link' => 'लिंक कॉपी करें',
        'link_copied' => 'लिंक कॉपी किया गया',
        'invite_participants' => 'प्रतिभागियों को आमंत्रित करें',
        'participants' => 'प्रतिभागी',
        'add_participant' => 'प्रतिभागी जोड़ें',
        'mediation_details' => 'मध्यस्थता विवरण',
        'case_details' => 'मामले का विवरण',
        'session_details' => 'सत्र विवरण',
        'location_details' => 'स्थान विवरण',
        'additional_info' => 'अतिरिक्त जानकारी',
        'created_by' => 'द्वारा बनाया गया',
        'created_on' => 'पर बनाया गया',
        'last_modified' => 'अंतिम संशोधित',
        'reference_number' => 'संदर्भ संख्या',
        'case_number' => 'मामला संख्या',
        'priority' => 'प्राथमिकता',
        'complexity' => 'जटिलता',
        'simple' => 'सरल',
        'moderate' => 'मध्यम',
        'complex' => 'जटिल',
        'estimated_duration' => 'अनुमानित अवधि',
        'actual_duration' => 'वास्तविक अवधि',
        'cost' => 'लागत',
        'payment_status' => 'भुगतान स्थिति',
        'paid' => 'भुगतान किया',
        'unpaid' => 'अवैतनिक',
        'partially_paid' => 'आंशिक रूप से भुगतान',
        'invoice' => 'चालान',
        'generate_invoice' => 'चालान बनाएं',
        'send_invoice' => 'चालान भेजें',
        'mark_as_paid' => 'भुगतान के रूप में चिह्नित करें',
        'add_notes' => 'नोट्स जोड़ें',
        'private_notes' => 'निजी नोट्स',
        'public_notes' => 'सार्वजनिक नोट्स',
        'attachments' => 'संलग्नक',
        'add_attachment' => 'संलग्नक जोड़ें',
        'max_file_size' => 'अधिकतम फ़ाइल आकार: 10MB',
        'allowed_formats' => 'अनुमत प्रारूप: PDF, DOC, DOCX, JPG, PNG',
        'drag_drop' => 'फ़ाइलें यहां खींचें और छोड़ें या ब्राउज़ करने के लिए क्लिक करें',
        'browse' => 'ब्राउज़ करें',
        'uploading' => 'अपलोड हो रहा है',
        'upload_complete' => 'अपलोड पूर्ण',
        'upload_failed' => 'अपलोड विफल',
        'preview' => 'पूर्वावलोकन',
        'remove_file' => 'फ़ाइल हटाएं',
        'schedule_conflict' => 'शेड्यूल संघर्ष',
        'conflict_detected' => 'एक शेड्यूलिंग संघर्ष का पता चला है',
        'suggest_alternative' => 'वैकल्पिक सुझाव दें',
        'accept_conflict' => 'संघर्ष स्वीकार करें',
        'reschedule_to' => 'पुनर्निर्धारित करें',
        'find_available_slot' => 'उपलब्ध स्लॉट खोजें',
        'auto_schedule' => 'स्वचालित शेड्यूल',
        'suggested_times' => 'सुझाए गए समय',
        'morning' => 'सुबह',
        'afternoon' => 'दोपहर',
        'evening' => 'शाम',
        'weekday' => 'सप्ताह का दिन',
        'weekend' => 'सप्ताहांत',
        'holiday' => 'छुट्टी',
        'blocked_time' => 'ब्लॉक समय',
        'set_unavailable' => 'अनुपलब्ध सेट करें',
        'set_recurring' => 'आवर्ती सेट करें',
        'recurring' => 'आवर्ती',
        'daily' => 'दैनिक',
        'weekly' => 'साप्ताहिक',
        'biweekly' => 'द्वि-साप्ताहिक',
        'monthly' => 'मासिक',
        'custom' => 'कस्टम',
        'repeat_every' => 'हर दोहराएं',
        'weeks' => 'सप्ताह',
        'months' => 'महीने',
        'end_recurrence' => 'आवर्ती समाप्त',
        'never' => 'कभी नहीं',
        'after' => 'बाद',
        'occurrences' => 'घटनाएं',
        'on_date' => 'तारीख पर',
        'timezone' => 'समय क्षेत्र',
        'select_timezone' => 'समय क्षेत्र चुनें',
        'notification_sent' => 'सूचना भेजी गई',
        'email_sent' => 'ईमेल भेजा गया',
        'sms_sent' => 'एसएमएस भेजा गया',
        'reminder_sent' => 'अनुस्मारक भेजा गया',
        'invitation_sent' => 'आमंत्रण भेजा गया',
        'view_all' => 'सभी देखें',
        'show_more' => 'और दिखाएं',
        'show_less' => 'कम दिखाएं',
        'collapse' => 'संकुचित करें',
        'expand' => 'विस्तार करें',
        'toggle' => 'टॉगल',
        'select_all' => 'सभी चुनें',
        'deselect_all' => 'सभी अचयनित करें',
        'bulk_actions' => 'बल्क एक्शन',
        'apply' => 'लागू करें',
        'refresh' => 'रिफ्रेश',
        'reset' => 'रीसेट',
        'submit' => 'जमा करें',
        'continue' => 'जारी रखें',
        'finish' => 'समाप्त',
        'exit' => 'बाहर निकलें',
        'mediation_schedule' => 'मध्यस्थता शेड्यूल',
        'mediation_management' => 'मध्यस्थता प्रबंधन',
        'session_management' => 'सत्र प्रबंधन',
        'calendar_management' => 'कैलेंडर प्रबंधन',
        'participant_management' => 'प्रतिभागी प्रबंधन',
        'document_management' => 'दस्तावेज़ प्रबंधन',
        'billing_management' => 'बिलिंग प्रबंधन',
        'reporting' => 'रिपोर्टिंग',
        'system' => 'सिस्टम',
        'user_management' => 'उपयोगकर्ता प्रबंधन',
        'role_management' => 'भूमिका प्रबंधन',
        'permission_management' => 'अनुमति प्रबंधन',
        'audit_log' => 'ऑडिट लॉग',
        'backup' => 'बैकअप',
        'restore' => 'पुनर्स्थापित',
        'maintenance' => 'रखरखाव',
        'updates' => 'अपडेट',
        'about' => 'के बारे में',
        'version' => 'संस्करण',
        'copyright' => 'कॉपीराइट',
        'privacy_policy' => 'गोपनीयता नीति',
        'terms_of_service' => 'सेवा की शर्तें',
        'contact_us' => 'हमसे संपर्क करें',
        'help_center' => 'सहायता केंद्र',
        'knowledge_base' => 'ज्ञान आधार',
        'faq' => 'सामान्य प्रश्न',
        'tutorials' => 'ट्यूटोरियल',
        'video_tutorials' => 'वीडियो ट्यूटोरियल',
        'user_guide' => 'उपयोगकर्ता गाइड',
        'api_documentation' => 'एपीआई दस्तावेज़ीकरण',
        'developer_resources' => 'डेवलपर संसाधन',
        'community_forum' => 'सामुदायिक फोरम',
        'blog' => 'ब्लॉग',
        'news' => 'समाचार',
        'events' => 'कार्यक्रम',
        'webinars' => 'वेबिनार',
        'training' => 'प्रशिक्षण',
        'certification' => 'प्रमाणन',
        'partners' => 'भागीदार',
        'affiliates' => 'सहबद्ध',
        'testimonials' => 'प्रशंसापत्र',
        'case_studies' => 'केस स्टडी',
        'success_stories' => 'सफलता की कहानियां',
        'mediator_directory' => 'मध्यस्थ निर्देशिका',
        'find_mediator' => 'मध्यस्थ खोजें',
        'become_mediator' => 'मध्यस्थ बनें',
        'mediator_training' => 'मध्यस्थ प्रशिक्षण',
        'mediator_resources' => 'मध्यस्थ संसाधन',
        'client_resources' => 'ग्राहक संसाधन',
        'self_help' => 'स्वयं सहायता',
        'mediation_process' => 'मध्यस्थता प्रक्रिया',
        'cost_calculator' => 'लागत कैलकुलेटर',
        'legal_resources' => 'कानूनी संसाधन',
        'forms_templates' => 'फॉर्म और टेम्प्लेट',
        'checklist' => 'चेकलिस्ट',
        'timeline' => 'समयरेखा',
        'roadmap' => 'रोडमैप',
        'milestones' => 'माइलस्टोन',
        'deliverables' => 'डिलिवरेबल्स',
        'risks' => 'जोखिम',
        'issues' => 'मुद्दे',
        'decisions' => 'निर्णय',
        'action_items' => 'कार्य आइटम',
        'follow_ups' => 'फॉलो-अप',
        'next_steps' => 'अगले कदम',
        'key_dates' => 'मुख्य तिथियां',
        'deadlines' => 'समय सीमा',
        'appointments' => 'अपॉइंटमेंट',
        'meetings' => 'बैठकें',
        'conferences' => 'सम्मेलन',
        'hearings' => 'सुनवाई',
        'trials' => 'मुकदमे',
        'arbitrations' => 'मध्यस्थता',
        'negotiations' => 'बातचीत',
        'settlements' => 'समझौते',
        'agreements' => 'समझौते',
        'contracts' => 'अनुबंध',
        'releases' => 'रिलीज',
        'waivers' => 'वेवर',
        'confidentiality' => 'गोपनीयता',
        'disclosure' => 'प्रकटीकरण',
        'evidence' => 'सबूत',
        'testimony' => 'गवाही',
        'witnesses' => 'गवाह',
        'experts' => 'विशेषज्ञ',
        'evaluators' => 'मूल्यांकनकर्ता',
        'facilitators' => 'सुविधाकर्ता',
        'conciliators' => 'समाधानकर्ता',
        'ombudsmen' => 'ओम्बड्समैन',
        'advisors' => 'सलाहकार',
        'counselors' => 'परामर्शदाता',
        'coaches' => 'कोच',
        'mentors' => 'संरक्षक',
        'trainers' => 'प्रशिक्षक',
        'instructors' => 'प्रशिक्षक',
        'speakers' => 'वक्ता',
        'moderators' => 'संचालक',
        'panelists' => 'पैनलिस्ट',
        'participant' => 'प्रतिभागी',
        'observer' => 'प्रेक्षक',
        'guest' => 'अतिथि',
        'staff' => 'कर्मचारी',
        'administrator' => 'प्रशासक',
        'manager' => 'प्रबंधक',
        'coordinator' => 'समन्वयक',
        'assistant' => 'सहायक',
        'intern' => 'इंटर्न',
        'volunteer' => 'स्वयंसेवक',
        'contractor' => 'ठेकेदार',
        'consultant' => 'सलाहकार',
        'vendor' => 'विक्रेता',
        'supplier' => 'आपूर्तिकर्ता',
        'partner' => 'भागीदार',
        'sponsor' => 'प्रायोजक',
        'donor' => 'दाता',
        'benefactor' => 'हितैषी',
        'patron' => 'संरक्षक',
        'advocate' => 'वकील',
        'ally' => 'सहयोगी',
        'supporter' => 'समर्थक',
        'follower' => 'अनुयायी',
        'subscriber' => 'ग्राहक',
        'member' => 'सदस्य',
        'affiliate' => 'सहबद्ध',
        'associate' => 'सहयोगी',
        'colleague' => 'सहकर्मी',
        'peer' => 'सहकर्मी',
        'counterpart' => 'प्रतिपक्ष',
        'adversary' => 'प्रतिद्वंद्वी',
        'opponent' => 'विरोधी',
        'rival' => 'प्रतियोगी',
        'competitor' => 'प्रतिस्पर्धी',
        'challenger' => 'चुनौती देने वाला',
        'contender' => 'प्रतियोगी',
        'contestant' => 'प्रतियोगी',
        'litigant' => 'मुकदमेबाज',
        'plaintiff' => 'वादी',
        'defendant' => 'प्रतिवादी',
        'claimant' => 'दावेदार',
        'respondent' => 'प्रतिवादी',
        'appellant' => 'अपीलकर्ता',
        'appellee' => 'अपील प्रतिवादी',
        'petitioner' => 'याचिकाकर्ता',
        'movant' => 'आवेदक',
        'nonmovant' => 'गैर-आवेदक',
        'judge' => 'न्यायाधीश',
        'magistrate' => 'मजिस्ट्रेट',
        'commissioner' => 'आयुक्त',
        'referee' => 'रेफरी',
        'master' => 'मास्टर',
        'special_master' => 'विशेष मास्टर',
        'court_clerk' => 'कोर्ट क्लर्क',
        'court_reporter' => 'कोर्ट रिपोर्टर',
        'bailiff' => 'बेलिफ',
        'marshal' => 'मार्शल',
        'sheriff' => 'शेरिफ',
        'constable' => 'कॉन्स्टेबल',
        'process_server' => 'प्रोसेस सर्वर',
        'lawyer' => 'वकील',
        'attorney' => 'अटॉर्नी',
        'counsel' => 'सलाहकार',
        'barrister' => 'बैरिस्टर',
        'solicitor' => 'सॉलिसिटर',
        'advocate' => 'वकील',
        'legal_executive' => 'कानूनी कार्यकारी',
        'paralegal' => 'पैरालीगल',
        'legal_assistant' => 'कानूनी सहायक',
        'legal_secretary' => 'कानूनी सचिव',
        'law_clerk' => 'लॉ क्लर्क',
        'legal_intern' => 'कानूनी इंटर्न',
        'legal_aid' => 'कानूनी सहायता',
        'pro_bono' => 'प्रो बोनो',
        'public_defender' => 'पब्लिक डिफेंडर',
        'district_attorney' => 'जिला अटॉर्नी',
        'prosecutor' => 'अभियोजक',
        'state_attorney' => 'राज्य अटॉर्नी',
        'us_attorney' => 'यूएस अटॉर्नी',
        'attorney_general' => 'अटॉर्नी जनरल',
        'solicitor_general' => 'सॉलिसिटर जनरल',
        'chief_justice' => 'मुख्य न्यायाधीश',
        'associate_justice' => 'सहयोगी न्यायाधीश',
        'justice' => 'न्यायाधीश',
        'judiciary' => 'न्यायपालिका',
        'court' => 'अदालत',
        'tribunal' => 'ट्रिब्यूनल',
        'board' => 'बोर्ड',
        'commission' => 'आयोग',
        'agency' => 'एजेंसी',
        'department' => 'विभाग',
        'ministry' => 'मंत्रालय',
        'bureau' => 'ब्यूरो',
        'office' => 'कार्यालय',
        'division' => 'विभाग',
        'section' => 'अनुभाग',
        'unit' => 'इकाई',
        'branch' => 'शाखा',
        'subsidiary' => 'सहायक',
        'affiliate' => 'सहबद्ध',
        'parent_company' => 'मूल कंपनी',
        'holding_company' => 'होल्डिंग कंपनी',
        'conglomerate' => 'कॉन्ग्लोमरेट',
        'corporation' => 'निगम',
        'incorporated' => 'इनकॉर्पोरेटेड',
        'limited' => 'लिमिटेड',
        'llc' => 'एलएलसी',
        'llp' => 'एलएलपी',
        'partnership' => 'पार्टनरशिप',
        'sole_proprietorship' => 'एकमात्र स्वामित्व',
        'nonprofit' => 'गैर-लाभकारी',
        'ngo' => 'एनजीओ',
        'foundation' => 'फाउंडेशन',
        'trust' => 'ट्रस्ट',
        'estate' => 'संपदा',
        'association' => 'संघ',
        'society' => 'सोसायटी',
        'club' => 'क्लब',
        'union' => 'संघ',
        'guild' => 'गिल्ड',
        'cooperative' => 'सहकारी',
        'collective' => 'सामूहिक',
        'consortium' => 'कंसोर्टियम',
        'alliance' => 'गठबंधन',
        'coalition' => 'गठबंधन',
        'federation' => 'फेडरेशन',
        'confederation' => 'कन्फेडरेशन',
        'league' => 'लीग',
        'conference' => 'सम्मेलन',
        'congress' => 'कांग्रेस',
        'parliament' => 'संसद',
        'senate' => 'सीनेट',
        'assembly' => 'विधानसभा',
        'council' => 'परिषद',
        'committee' => 'समिति',
        'subcommittee' => 'उपसमिति',
        'task_force' => 'कार्यदल',
        'working_group' => 'कार्य समूह',
        'steering_committee' => 'स्टीयरिंग कमेटी',
        'advisory_board' => 'सलाहकार बोर्ड',
        'board_of_directors' => 'निदेशक मंडल',
        'executive_committee' => 'कार्यकारी समिति',
        'management_team' => 'प्रबंधन टीम',
        'leadership_team' => 'नेतृत्व टीम',
        'project_team' => 'परियोजना टीम',
        'cross_functional_team' => 'क्रॉस-फंक्शनल टीम',
        'virtual_team' => 'वर्चुअल टीम',
        'remote_team' => 'रिमोट टीम',
        'distributed_team' => 'वितरित टीम',
        'global_team' => 'वैश्विक टीम',
        'multicultural_team' => 'बहुसांस्कृतिक टीम',
        'diverse_team' => 'विविध टीम',
        'inclusive_team' => 'समावेशी टीम',
        'equitable_team' => 'न्यायसंगत टीम',
        'accessible_team' => 'सुलभ टीम',
        'sustainable_team' => 'टिकाऊ टीम',
        'resilient_team' => 'लचीली टीम',
        'agile_team' => 'चुस्त टीम',
        'lean_team' => 'लीन टीम',
        'high_performing_team' => 'उच्च प्रदर्शन टीम',
        'self_organizing_team' => 'स्व-संगठित टीम',
        'self_managing_team' => 'स्व-प्रबंधन टीम',
        'empowered_team' => 'सशक्त टीम',
        'autonomous_team' => 'स्वायत्त टीम',
        'accountable_team' => 'जवाबदेह टीम',
        'responsible_team' => 'जिम्मेदार टीम',
        'transparent_team' => 'पारदर्शी टीम',
        'trustworthy_team' => 'विश्वसनीय टीम',
        'ethical_team' => 'नैतिक टीम',
        'principled_team' => 'सिद्धांतवादी टीम',
        'values_driven_team' => 'मूल्य-संचालित टीम',
        'purpose_driven_team' => 'उद्देश्य-संचालित टीम',
        'mission_driven_team' => 'मिशन-संचालित टीम',
        'vision_driven_team' => 'दृष्टि-संचालित टीम',
        'goal_oriented_team' => 'लक्ष्य-उन्मुख टीम',
        'results_driven_team' => 'परिणाम-संचालित टीम',
        'performance_oriented_team' => 'प्रदर्शन-उन्मुख टीम',
        'quality_focused_team' => 'गुणवत्ता-केंद्रित टीम',
        'customer_focused_team' => 'ग्राहक-केंद्रित टीम',
        'user_centered_team' => 'उपयोगकर्ता-केंद्रित टीम',
        'human_centered_team' => 'मानव-केंद्रित टीम',
        'design_thinking_team' => 'डिज़ाइन थिंकिंग टीम',
        'systems_thinking_team' => 'सिस्टम थिंकिंग टीम',
        'strategic_thinking_team' => 'सामरिक थिंकिंग टीम',
        'critical_thinking_team' => 'आलोचनात्मक थिंकिंग टीम',
        'creative_thinking_team' => 'रचनात्मक थिंकिंग टीम',
        'analytical_thinking_team' => 'विश्लेषणात्मक थिंकिंग टीम',
        'logical_thinking_team' => 'तार्किक थिंकिंग टीम',
        'lateral_thinking_team' => 'पार्श्विक थिंकिंग टीम',
        'divergent_thinking_team' => 'विविधतापूर्ण थिंकिंग टीम',
        'convergent_thinking_team' => 'अभिसारी थिंकिंग टीम',
        'abstract_thinking_team' => 'अमूर्त थिंकिंग टीम',
        'concrete_thinking_team' => 'ठोस थिंकिंग टीम',
        'conceptual_thinking_team' => 'संकल्पनात्मक थिंकिंग टीम',
        'practical_thinking_team' => 'व्यावहारिक थिंकिंग टीम',
        'theoretical_thinking_team' => 'सैद्धांतिक थिंकिंग टीम',
        'empirical_thinking_team' => 'अनुभवजन्य थिंकिंग टीम',
        'scientific_thinking_team' => 'वैज्ञानिक थिंकिंग टीम',
        'mathematical_thinking_team' => 'गणितीय थिंकिंग टीम',
        'statistical_thinking_team' => 'सांख्यिकीय थिंकिंग टीम',
        'probabilistic_thinking_team' => 'संभाव्यता थिंकिंग टीम',
        'deterministic_thinking_team' => 'नियतात्मक थिंकिंग टीम',
        'stochastic_thinking_team' => 'स्टोकेस्टिक थिंकिंग टीम',
        'chaotic_thinking_team' => 'अराजक थिंकिंग टीम',
        'complex_thinking_team' => 'जटिल थिंकिंग टीम',
        'complicated_thinking_team' => 'पेचीदा थिंकिंग टीम',
        'simple_thinking_team' => 'सरल थिंकिंग टीम',
        'reductionist_thinking_team' => 'अपचयवादी थिंकिंग टीम',
        'holistic_thinking_team' => 'समग्र थिंकिंग टीम',
        'integrative_thinking_team' => 'एकीकृत थिंकिंग टीम',
        'synthetic_thinking_team' => 'सिंथेटिक थिंकिंग टीम',
        'synergistic_thinking_team' => 'सहक्रियात्मक थिंकिंग टीम',
        'emergent_thinking_team' => 'उभरती थिंकिंग टीम',
        'adaptive_thinking_team' => 'अनुकूली थिंकिंग टीम',
        'resilient_thinking_team' => 'लचीली थिंकिंग टीम',
        'antifragile_thinking_team' => 'एंटीफ्रैगाइल थिंकिंग टीम',
        'robust_thinking_team' => 'मजबूत थिंकिंग टीम',
        'flexible_thinking_team' => 'लचीली थिंकिंग टीम',
        'agile_thinking_team' => 'चुस्त थिंकिंग टीम',
        'lean_thinking_team' => 'लीन थिंकिंग टीम',
        'six_sigma_thinking_team' => 'सिक्स सिग्मा थिंकिंग टीम',
        'tqm_thinking_team' => 'टीक्यूएम थिंकिंग टीम',
        'kaizen_thinking_team' => 'काइजेन थिंकिंग टीम',
        'continuous_improvement_team' => 'निरंतर सुधार टीम',
        'learning_organization_team' => 'सीखने वाली संगठन टीम',
        'knowledge_management_team' => 'ज्ञान प्रबंधन टीम',
        'information_management_team' => 'सूचना प्रबंधन टीम',
        'data_management_team' => 'डेटा प्रबंधन टीम',
        'content_management_team' => 'सामग्री प्रबंधन टीम',
        'document_management_team' => 'दस्तावेज़ प्रबंधन टीम',
        'record_management_team' => 'रिकॉर्ड प्रबंधन टीम',
        'archive_management_team' => 'आर्काइव प्रबंधन टीम',
        'library_management_team' => 'लाइब्रेरी प्रबंधन टीम',
        'museum_management_team' => 'संग्रहालय प्रबंधन टीम',
        'heritage_management_team' => 'विरासत प्रबंधन टीम',
        'cultural_management_team' => 'सांस्कृतिक प्रबंधन टीम',
        'art_management_team' => 'कला प्रबंधन टीम',
        'entertainment_management_team' => 'मनोरंजन प्रबंधन टीम',
        'media_management_team' => 'मीडिया प्रबंधन टीम',
        'communication_management_team' => 'संचार प्रबंधन टीम',
        'public_relations_team' => 'जनसंपर्क टीम',
        'marketing_team' => 'विपणन टीम',
        'advertising_team' => 'विज्ञापन टीम',
        'branding_team' => 'ब्रांडिंग टीम',
        'sales_team' => 'बिक्री टीम',
        'business_development_team' => 'व्यापार विकास टीम',
        'partnership_team' => 'पार्टनरशिप टीम',
        'alliance_team' => 'गठबंधन टीम',
        'network_team' => 'नेटवर्क टीम',
        'ecosystem_team' => 'पारिस्थितिकी तंत्र टीम',
        'platform_team' => 'प्लेटफॉर्म टीम',
        'marketplace_team' => 'मार्केटप्लेस टीम',
        'community_team' => 'समुदाय टीम',
        'social_team' => 'सामाजिक टीम',
        'environmental_team' => 'पर्यावरणीय टीम',
        'sustainability_team' => 'सस्टेनेबिलिटी टीम',
        'csr_team' => 'सीएसआर टीम',
        'esg_team' => 'ईएसजी टीम',
        'impact_team' => 'प्रभाव टीम',
        'philanthropy_team' => 'परोपकार टीम',
        'volunteer_team' => 'स्वयंसेवक टीम',
        'nonprofit_team' => 'गैर-लाभकारी टीम',
        'ngo_team' => 'एनजीओ टीम',
        'government_team' => 'सरकारी टीम',
        'public_sector_team' => 'सार्वजनिक क्षेत्र टीम',
        'private_sector_team' => 'निजी क्षेत्र टीम',
        'third_sector_team' => 'तीसरा क्षेत्र टीम',
        'social_enterprise_team' => 'सामाजिक उद्यम टीम',
        'cooperative_team' => 'सहकारी टीम',
        'mutual_team' => 'पारस्परिक टीम',
        'credit_union_team' => 'क्रेडिट यूनियन टीम',
        'microfinance_team' => 'माइक्रोफाइनेंस टीम',
        'fintech_team' => 'फिनटेक टीम',
        'insurtech_team' => 'इंश्योरटेक टीम',
        'regtech_team' => 'रेगटेक टीम',
        'legaltech_team' => 'लीगलटेक टीम',
        'medtech_team' => 'मेडटेक टीम',
        'edtech_team' => 'एडटेक टीम',
        'agritech_team' => 'एग्रीटेक टीम',
        'cleantech_team' => 'क्लीनटेक टीम',
        'greentech_team' => 'ग्रीनटेक टीम',
        'climatech_team' => 'क्लाइमेटेक टीम',
        'oceantech_team' => 'ओशनटेक टीम',
        'spacetech_team' => 'स्पेसटेक टीम',
        'aerotech_team' => 'एरोटेक टीम',
        'defensetech_team' => 'डिफेंसटेक टीम',
        'securitytech_team' => 'सिक्योरिटीटेक टीम',
        'cybersecurity_team' => 'साइबर सुरक्षा टीम',
        'privacytech_team' => 'प्राइवेसीटेक टीम',
        'trusttech_team' => 'ट्रस्टटेक टीम',
        'safetytech_team' => 'सेफ्टीटेक टीम',
        'healthtech_team' => 'हेल्थटेक टीम',
        'wellnesstech_team' => 'वेलनेसटेक टीम',
        'fitness_tech_team' => 'फिटनेस टेक टीम',
        'sportstech_team' => 'स्पोर्ट्सटेक टीम',
        'entertainmenttech_team' => 'एंटरटेनमेंटटेक टीम',
        'gaming_tech_team' => 'गेमिंग टेक टीम',
        'esports_tech_team' => 'ईस्पोर्ट्स टेक टीम',
        'musictech_team' => 'म्यूजिकटेक टीम',
        'filmtech_team' => 'फिल्मटेक टीम',
        'tvtech_team' => 'टीवीटेक टीम',
        'radiotech_team' => 'रेडियोटेक टीम',
        'podcasttech_team' => 'पॉडकास्टटेक टीम',
        'audiotech_team' => 'ऑडियोटेक टीम',
        'voicetech_team' => 'वॉयसटेक टीम',
        'speechtech_team' => 'स्पीचटेक टीम',
        'nlp_team' => 'एनएलपी टीम',
        'ai_team' => 'एआई टीम',
        'ml_team' => 'एमएल टीम',
        'dl_team' => 'डीएल टीम',
        'neural_network_team' => 'न्यूरल नेटवर्क टीम',
        'computer_vision_team' => 'कंप्यूटर विज़न टीम',
        'robotics_team' => 'रोबोटिक्स टीम',
        'automation_team' => 'ऑटोमेशन टीम',
        'iot_team' => 'आईओटी टीम',
        'wearable_tech_team' => 'वेयरेबल टेक टीम',
        'smart_home_team' => 'स्मार्ट होम टीम',
        'smart_city_team' => 'स्मार्ट सिटी टीम',
        'urban_tech_team' => 'अर्बन टेक टीम',
        'civic_tech_team' => 'सिविक टेक टीम',
        'govtech_team' => 'गवटेक टीम',
        'democracy_tech_team' => 'डेमोक्रेसी टेक टीम',
        'voting_tech_team' => 'वोटिंग टेक टीम',
        'election_tech_team' => 'इलेक्शन टेक टीम',
        'political_tech_team' => 'पॉलिटिकल टेक टीम',
        'campaign_tech_team' => 'कैंपेन टेक टीम',
        'advocacy_tech_team' => 'एडवोकेसी टेक टीम',
        'activism_tech_team' => 'एक्टिविज्म टेक टीम',
        'social_movement_tech_team' => 'सोशल मूवमेंट टेक टीम',
        'protest_tech_team' => 'प्रोटेस्ट टेक टीम',
        'resistance_tech_team' => 'रेजिस्टेंस टेक टीम',
        'liberation_tech_team' => 'लिबरेशन टेक टीम',
        'freedom_tech_team' => 'फ्रीडम टेक टीम',
        'justice_tech_team' => 'जस्टिस टेक टीम',
        'equity_tech_team' => 'इक्विटी टेक टीम',
        'inclusion_tech_team' => 'इंक्लूजन टेक टीम',
        'diversity_tech_team' => 'डायवर्सिटी टेक टीम',
        'accessibility_tech_team' => 'एक्सेसिबिलिटी टेक टीम',
        'disability_tech_team' => 'डिसेबिलिटी टेक टीम',
        'assistive_tech_team' => 'असिस्टिव टेक टीम',
        'rehabilitation_tech_team' => 'रिहैबिलिटेशन टेक टीम',
        'therapeutic_tech_team' => 'थेरेप्यूटिक टेक टीम',
        'healing_tech_team' => 'हीलिंग टेक टीम',
        'wellbeing_tech_team' => 'वेलबीइंग टेक टीम',
        'happiness_tech_team' => 'हैप्पीनेस टेक टीम',
        'positive_psychology_team' => 'पॉजिटिव साइकोलॉजी टीम',
        'mindfulness_tech_team' => 'माइंडफुलनेस टेक टीम',
        'meditation_tech_team' => 'मेडिटेशन टेक टीम',
        'yoga_tech_team' => 'योगा टेक टीम',
        'spirituality_tech_team' => 'स्पिरिचुअलिटी टेक टीम',
        'religion_tech_team' => 'रिलीजन टेक टीम',
        'faith_tech_team' => 'फेथ टेक टीम',
        'belief_tech_team' => 'बिलीफ टेक टीम',
        'value_tech_team' => 'वैल्यू टेक टीम',
        'ethics_tech_team' => 'एथिक्स टेक टीम',
        'morality_tech_team' => 'मोरालिटी टेक टीम',
        'virtue_tech_team' => 'वर्च्यू टेक टीम',
        'character_tech_team' => 'कैरेक्टर टेक टीम',
        'integrity_tech_team' => 'इंटीग्रिटी टेक टीम',
        'honesty_tech_team' => 'ऑनेस्टी टेक टीम',
        'truth_tech_team' => 'ट्रुथ टेक टीम',
        'transparency_tech_team' => 'ट्रांसपेरेंसी टेक टीम',
        'accountability_tech_team' => 'अकाउंटेबिलिटी टेक टीम',
        'responsibility_tech_team' => 'रिस्पॉन्सिबिलिटी टेक टीम',
        'duty_tech_team' => 'ड्यूटी टेक टीम',
        'obligation_tech_team' => 'ऑब्लिगेशन टेक टीम',
        'commitment_tech_team' => 'कमिटमेंट टेक टीम',
        'dedication_tech_team' => 'डेडिकेशन टेक टीम',
        'loyalty_tech_team' => 'लॉयल्टी टेक टीम',
        'fidelity_tech_team' => 'फिडेलिटी टेक टीम',
        'allegiance_tech_team' => 'अल्लेजिएंस टेक टीम',
        'patriotism_tech_team' => 'पैट्रियटिज्म टेक टीम',
        'nationalism_tech_team' => 'नेशनलिज्म टेक टीम',
        'globalism_tech_team' => 'ग्लोबलिज्म टेक टीम',
        'cosmopolitanism_tech_team' => 'कोस्मोपोलिटनिज्म टेक टीम',
        'internationalism_tech_team' => 'इंटरनेशनलिज्म टेक टीम',
        'transnationalism_tech_team' => 'ट्रांसनेशनलिज्म टेक टीम',
        'supranationalism_tech_team' => 'सुप्रानेशनलिज्म टेक टीम',
        'multilateralism_tech_team' => 'मल्टीलैटरलिज्म टेक टीम',
        'bilateralism_tech_team' => 'बाइलेटरलिज्म टेक टीम',
        'unilateralism_tech_team' => 'यूनिलैटरलिज्म टेक टीम',
        'isolationism_tech_team' => 'आइसोलेशनिज्म टेक टीम',
        'protectionism_tech_team' => 'प्रोटेक्शनिज्म टेक टीम',
        'free_trade_tech_team' => 'फ्री ट्रेड टेक टीम',
        'fair_trade_tech_team' => 'फेयर ट्रेड टेक टीम',
        'sustainable_trade_tech_team' => 'सस्टेनेबल ट्रेड टेक टीम',
        'ethical_trade_tech_team' => 'एथिकल ट्रेड टेक टीम',
        'responsible_trade_tech_team' => 'रिस्पॉन्सिबल ट्रेड टेक टीम',
        'conscious_trade_tech_team' => 'कॉन्शियस ट्रेड टेक टीम',
        'mindful_trade_tech_team' => 'माइंडफुल ट्रेड टेक टीम',
        'intentional_trade_tech_team' => 'इंटेंशनल ट्रेड टेक टीम',
        'purposeful_trade_tech_team' => 'पर्पजफुल ट्रेड टेक टीम',
        'meaningful_trade_tech_team' => 'मीनिंगफुल ट्रेड टेक टीम',
        'impactful_trade_tech_team' => 'इम्पैक्टफुल ट्रेड टेक टीम',
        'transformative_trade_tech_team' => 'ट्रांसफॉर्मेटिव ट्रेड टेक टीम',
        'regenerative_trade_tech_team' => 'रिजेनरेटिव ट्रेड टेक टीम',
        'restorative_trade_tech_team' => 'रेस्टोरेटिव ट्रेड टेक टीम',
        'healing_trade_tech_team' => 'हीलिंग ट्रेड टेक टीम',
        'reconciliatory_trade_tech_team' => 'रिकॉन्सिलिएटरी ट्रेड टेक टीम',
        'peacebuilding_trade_tech_team' => 'पीसबिल्डिंग ट्रेड टेक टीम',
        'conflict_resolution_trade_tech_team' => 'कन्फ्लिक्ट रेजोल्यूशन ट्रेड टेक टीम',
        'mediation_trade_tech_team' => 'मध्यस्थता ट्रेड टेक टीम',
        'arbitration_trade_tech_team' => 'मध्यस्थता ट्रेड टेक टीम',
        'conciliation_trade_tech_team' => 'समाधान ट्रेड टेक टीम',
        'facilitation_trade_tech_team' => 'सुविधा ट्रेड टेक टीम',
        'negotiation_trade_tech_team' => 'बातचीत ट्रेड टेक टीम',
        'diplomacy_trade_tech_team' => 'कूटनीति ट्रेड टेक टीम',
        'statecraft_trade_tech_team' => 'राजनीति ट्रेड टेक टीम',
        'governance_trade_tech_team' => 'शासन ट्रेड टेक टीम',
        'leadership_trade_tech_team' => 'नेतृत्व ट्रेड टेक टीम',
        'management_trade_tech_team' => 'प्रबंधन ट्रेड टेक टीम',
        'administration_trade_tech_team' => 'प्रशासन ट्रेड टेक टीम',
        'bureaucracy_trade_tech_team' => 'नौकरशाही ट्रेड टेक टीम',
        'technocracy_trade_tech_team' => 'तकनीकी शासन ट्रेड टेक टीम',
        'meritocracy_trade_tech_team' => 'योग्यता शासन ट्रेड टेक टीम',
        'plutocracy_trade_tech_team' => 'धनिक तंत्र ट्रेड टेक टीम',
        'aristocracy_trade_tech_team' => 'अभिजात वर्ग ट्रेड टेक टीम',
        'oligarchy_trade_tech_team' => 'अल्पतंत्र ट्रेड टेक टीम',
        'monarchy_trade_tech_team' => 'राजतंत्र ट्रेड टेक टीम',
        'dictatorship_trade_tech_team' => 'तानाशाही ट्रेड टेक टीम',
        'tyranny_trade_tech_team' => 'अत्याचार ट्रेड टेक टीम',
        'authoritarianism_trade_tech_team' => 'सत्तावाद ट्रेड टेक टीम',
        'totalitarianism_trade_tech_team' => 'सर्वसत्तावाद ट्रेड टेक टीम',
        'fascism_trade_tech_team' => 'फासीवाद ट्रेड टेक टीम',
        'nazism_trade_tech_team' => 'नाजीवाद ट्रेड टेक टीम',
        'communism_trade_tech_team' => 'साम्यवाद ट्रेड टेक टीम',
        'socialism_trade_tech_team' => 'समाजवाद ट्रेड टेक टीम',
        'capitalism_trade_tech_team' => 'पूंजीवाद ट्रेड टेक टीम',
        'anarchism_trade_tech_team' => 'अराजकतावाद ट्रेड टेक टीम',
        'libertarianism_trade_tech_team' => 'उदारतावाद ट्रेड टेक टीम',
        'conservatism_trade_tech_team' => 'रूढ़िवाद ट्रेड टेक टीम',
        'liberalism_trade_tech_team' => 'उदारवाद ट्रेड टेक टीम',
        'progressivism_trade_tech_team' => 'प्रगतिवाद ट्रेड टेक टीम',
        'radicalism_trade_tech_team' => 'कट्टरवाद ट्रेड टेक टीम',
        'reformism_trade_tech_team' => 'सुधारवाद ट्रेड टेक टीम',
        'revolutionism_trade_tech_team' => 'क्रांतिवाद ट्रेड टेक टीम',
        'reactionism_trade_tech_team' => 'प्रतिक्रियावाद ट्रेड टेक टीम',
        'traditionalism_trade_tech_team' => 'परंपरावाद ट्रेड टेक टीम',
        'modernism_trade_tech_team' => 'आधुनिकतावाद ट्रेड टेक टीम',
        'postmodernism_trade_tech_team' => 'उत्तर आधुनिकतावाद ट्रेड टेक टीम',
        'structuralism_trade_tech_team' => 'संरचनावाद ट्रेड टेक टीम',
        'poststructuralism_trade_tech_team' => 'उत्तर संरचनावाद ट्रेड टेक टीम',
        'deconstructionism_trade_tech_team' => 'विखंडनवाद ट्रेड टेक टीम',
        'constructivism_trade_tech_team' => 'निर्माणवाद ट्रेड टेक टीम',
        'pragmatism_trade_tech_team' => 'व्यावहारिकतावाद ट्रेड टेक टीम',
        'existentialism_trade_tech_team' => 'अस्तित्ववाद ट्रेड टेक टीम',
        'humanism_trade_tech_team' => 'मानवतावाद ट्रेड टेक टीम',
        'transhumanism_trade_tech_team' => 'पारमानवतावाद ट्रेड टेक टीम',
        'posthumanism_trade_tech_team' => 'उत्तर मानवतावाद ट्रेड टेक टीम',
        'antihumanism_trade_tech_team' => 'प्रतिमानवतावाद ट्रेड टेक टीम',
        'nihilism_trade_tech_team' => 'शून्यवाद ट्रेड टेक टीम',
        'absurdism_trade_tech_team' => 'असंगतिवाद ट्रेड टेक टीम',
        'stoicism_trade_tech_team' => 'स्टोइसिज्म ट्रेड टेक टीम',
        'epicureanism_trade_tech_team' => 'एपिक्यूरियनिज्म ट्रेड टेक टीम',
        'cynicism_trade_tech_team' => 'सिनिसिज्म ट्रेड टेक टीम',
        'skepticism_trade_tech_team' => 'संशयवाद ट्रेड टेक टीम',
        'relativism_trade_tech_team' => 'सापेक्षवाद ट्रेड टेक टीम',
        'universalism_trade_tech_team' => 'सार्वभौमिकतावाद ट्रेड टेक टीम',
        'particularism_trade_tech_team' => 'विशेषतावाद ट्रेड टेक टीम',
        'individualism_trade_tech_team' => 'व्यक्तिवाद ट्रेड टेक टीम',
        'collectivism_trade_tech_team' => 'सामूहिकतावाद ट्रेड टेक टीम',
        'communitarianism_trade_tech_team' => 'समुदायवाद ट्रेड टेक टीम',
        'cosmopolitanism_trade_tech_team' => 'विश्वनागरिकतावाद ट्रेड टेक टीम',
        'nationalism_trade_tech_team' => 'राष्ट्रवाद ट्रेड टेक टीम',
        'regionalism_trade_tech_team' => 'क्षेत्रवाद ट्रेड टेक टीम',
        'localism_trade_tech_team' => 'स्थानीयतावाद ट्रेड टेक टीम',
        'globalism_trade_tech_team' => 'वैश्विकतावाद ट्रेड टेक टीम',
        'internationalism_trade_tech_team' => 'अंतर्राष्ट्रीयतावाद ट्रेड टेक टीम',
        'transnationalism_trade_tech_team' => 'अंतर्राष्ट्रीयतावाद ट्रेड टेक टीम',
        'supranationalism_trade_tech_team' => 'अधिराष्ट्रीयतावाद ट्रेड टेक टीम',
    ]
];

$t = $translations[$language];

// Dummy data for mediations
// Dummy data for mediations
$mediations = [
    [
        'id' => 1001,
        'case_title' => 'Family Dispute - Property Division',
        'case_type' => 'family',
        'date' => date('Y-m-d', strtotime('+2 days')),
        'start_time' => '10:00',
        'end_time' => '12:00',
        'status' => 'scheduled',
        'mediator' => 'John Smith',
        'parties' => 'John Doe, Jane Doe',
        'venue' => 'Mediation Room 1',
        'virtual_link' => 'https://meet.example.com/abc123',
        'priority' => 'high',
        'created_by' => 'Admin',
        'created_at' => date('Y-m-d', strtotime('-5 days')),
    ],
    [
        'id' => 1002,
        'case_title' => 'Business Partnership Dissolution',
        'case_type' => 'business',
        'date' => date('Y-m-d', strtotime('+5 days')),
        'start_time' => '14:00',
        'end_time' => '16:00',
        'status' => 'confirmed',
        'mediator' => 'Sarah Johnson',
        'parties' => 'ABC Corp, XYZ Ltd',
        'venue' => 'Conference Room A',
        'virtual_link' => 'https://meet.example.com/def456',
        'priority' => 'medium',
        'created_by' => 'Manager',
        'created_at' => date('Y-m-d', strtotime('-3 days')),
    ],
    [
        'id' => 1003,
        'case_title' => 'Neighborhood Boundary Dispute',
        'case_type' => 'community',
        'date' => date('Y-m-d', strtotime('+1 week')),
        'start_time' => '09:00',
        'end_time' => '11:00',
        'status' => 'pending',
        'mediator' => 'Robert Chen',
        'parties' => 'Mr. Sharma, Mr. Patel',
        'venue' => 'Mediation Room 2',
        'virtual_link' => '',
        'priority' => 'low',
        'created_by' => 'Coordinator',
        'created_at' => date('Y-m-d', strtotime('-2 days')),
    ],
    [
        'id' => 1004,
        'case_title' => 'Workplace Harassment Complaint',
        'case_type' => 'workplace',
        'date' => date('Y-m-d', strtotime('-2 days')),
        'start_time' => '13:00',
        'end_time' => '15:00',
        'status' => 'completed',
        'mediator' => 'Maria Garcia',
        'parties' => 'Employee, Manager',
        'venue' => 'Conference Room B',
        'virtual_link' => 'https://meet.example.com/ghi789',
        'priority' => 'high',
        'created_by' => 'HR Manager',
        'created_at' => date('Y-m-d', strtotime('-10 days')),
    ],
    [
        'id' => 1005,
        'case_title' => 'Contract Dispute - Service Agreement',
        'case_type' => 'civil',
        'date' => date('Y-m-d', strtotime('-1 week')),
        'start_time' => '11:00',
        'end_time' => '13:00',
        'status' => 'cancelled',
        'mediator' => 'David Wilson',
        'parties' => 'Client, Service Provider',
        'venue' => 'Virtual Only',
        'virtual_link' => 'https://meet.example.com/jkl012',
        'priority' => 'medium',
        'created_by' => 'Admin',
        'created_at' => date('Y-m-d', strtotime('-15 days')),
    ],
];

// Available mediators
$mediators = [
    ['id' => 1, 'name' => 'John Smith', 'specialization' => 'Family, Business'],
    ['id' => 2, 'name' => 'Sarah Johnson', 'specialization' => 'Business, Civil'],
    ['id' => 3, 'name' => 'Robert Chen', 'specialization' => 'Community, Family'],
    ['id' => 4, 'name' => 'Maria Garcia', 'specialization' => 'Workplace, Civil'],
    ['id' => 5, 'name' => 'David Wilson', 'specialization' => 'Business, Civil'],
];

// Available venues
$venues = [
    'Mediation Room 1',
    'Mediation Room 2',
    'Conference Room A',
    'Conference Room B',
    'Virtual Only',
    'Hybrid',
];

// Case types
$case_types = ['family', 'business', 'civil', 'community', 'workplace', 'other'];

// Status options
$status_options = ['scheduled', 'confirmed', 'pending', 'in_progress', 'completed', 'cancelled'];

// Priority options
$priority_options = ['high', 'medium', 'low'];

// Format date for display
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Format time for display
function formatTime($time) {
    return date('g:i A', strtotime($time));
}

// Get status badge class
function getStatusClass($status) {
    switch($status) {
        case 'scheduled': return 'badge-primary';
        case 'confirmed': return 'badge-success';
        case 'pending': return 'badge-warning';
        case 'in_progress': return 'badge-info';
        case 'completed': return 'badge-secondary';
        case 'cancelled': return 'badge-danger';
        default: return 'badge-secondary';
    }
}

// Get priority badge class
function getPriorityClass($priority) {
    switch($priority) {
        case 'high': return 'badge-danger';
        case 'medium': return 'badge-warning';
        case 'low': return 'badge-success';
        default: return 'badge-secondary';
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $language == 'hi' ? 'hi' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?> - Mediation System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        
        .navbar-custom {
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 700;
            padding: 1rem 1.25rem;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-primary { background-color: rgba(78, 115, 223, 0.2); color: var(--primary-color); }
        .badge-success { background-color: rgba(28, 200, 138, 0.2); color: var(--success-color); }
        .badge-warning { background-color: rgba(246, 194, 62, 0.2); color: var(--warning-color); }
        .badge-danger { background-color: rgba(231, 74, 59, 0.2); color: var(--danger-color); }
        .badge-info { background-color: rgba(54, 185, 204, 0.2); color: var(--info-color); }
        .badge-secondary { background-color: rgba(133, 135, 150, 0.2); color: var(--secondary-color); }
        
        .language-switcher {
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            background-color: rgba(78, 115, 223, 0.1);
            color: var(--primary-color);
        }
        
        .language-switcher:hover {
            background-color: rgba(78, 115, 223, 0.2);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: #3a5bd9;
            border-color: #3a5bd9;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #e3e6f0;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .table th {
            border-top: none;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 5px;
            color: white;
            text-decoration: none;
            transition: transform 0.3s;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-view { background-color: var(--primary-color); }
        .btn-edit { background-color: var(--info-color); }
        .btn-delete { background-color: var(--danger-color); }
        .btn-reschedule { background-color: var(--warning-color); }
        .btn-download { background-color: var(--success-color); }
        
        .fc {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
        }
        
        .fc .fc-toolbar {
            padding: 1rem;
        }
        
        .fc .fc-button {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .fc .fc-button:hover {
            background-color: #3a5bd9;
            border-color: #3a5bd9;
        }
        
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-primary { border-left-color: var(--primary-color); }
        .stat-success { border-left-color: var(--success-color); }
        .stat-info { border-left-color: var(--info-color); }
        .stat-warning { border-left-color: var(--warning-color); }
        .stat-danger { border-left-color: var(--danger-color); }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .tab-content {
            padding: 20px 0;
        }
        
        .nav-tabs .nav-link {
            border-radius: 8px 8px 0 0;
            padding: 12px 24px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .nav-tabs .nav-link.active {
            background-color: white;
            border-color: #dee2e6 #dee2e6 white;
            color: var(--primary-color);
        }
        
        .party-item {
            background-color: #f8f9fc;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 3px solid var(--primary-color);
        }
        
        .required:after {
            content: " *";
            color: var(--danger-color);
        }
        
        @media (max-width: 768px) {
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .fc {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-handshake me-2 text-primary"></i>
                <?php echo $t['title']; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            <?php echo $t['dashboard']; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <i class="fas fa-chart-line me-1"></i>
                            <?php echo $t['analytics']; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-bar me-1"></i>
                            <?php echo $t['reports']; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user me-1"></i>
                            <?php echo $t['profile']; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item ms-2">
                        <div class="language-switcher nav-link" onclick="toggleLanguage()">
                            <i class="fas fa-language me-1"></i>
                            <?php echo $language == 'hi' ? 'English' : 'हिंदी'; ?>
                        </div>
                    </li>
                    
                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-primary" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>
                            <?php echo $t['logout']; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <!-- Message Alert -->
        <?php if ($message): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-<?php echo $message_type == 'error' ? 'danger' : ($message_type == 'warning' ? 'warning' : ($message_type == 'info' ? 'info' : 'success')); ?> alert-dismissible fade show">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Header with Stats -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1"><?php echo $t['mediation_schedule']; ?></h2>
                        <p class="text-muted mb-0"><?php echo $t['schedule_mediation']; ?> <?php echo $t['my_schedule']; ?></p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                            <i class="fas fa-plus me-1"></i>
                            <?php echo $t['schedule_new']; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
                <div class="card stat-card stat-primary h-100">
                    <div class="card-body">
                        <div class="stat-number"><?php echo count($mediations); ?></div>
                        <div class="stat-label"><?php echo $t['total_sessions']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
                <div class="card stat-card stat-success h-100">
                    <div class="card-body">
                        <div class="stat-number"><?php echo count(array_filter($mediations, function($m) { return $m['status'] == 'scheduled' || $m['status'] == 'confirmed'; })); ?></div>
                        <div class="stat-label"><?php echo $t['upcoming_sessions']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
                <div class="card stat-card stat-info h-100">
                    <div class="card-body">
                        <div class="stat-number">75%</div>
                        <div class="stat-label"><?php echo $t['completion_rate']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
                <div class="card stat-card stat-warning h-100">
                    <div class="card-body">
                        <div class="stat-number">68%</div>
                        <div class="stat-label"><?php echo $t['settlement_rate']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
                <div class="card stat-card stat-danger h-100">
                    <div class="card-body">
                        <div class="stat-number">2.3h</div>
                        <div class="stat-label"><?php echo $t['average_duration']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
                <div class="card stat-card stat-secondary h-100">
                    <div class="card-body">
                        <div class="stat-number"><?php echo count($mediators); ?></div>
                        <div class="stat-label"><?php echo $t['mediators']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="mediationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar" type="button" role="tab">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <?php echo $t['calendar_view']; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab">
                    <i class="fas fa-list me-2"></i>
                    <?php echo $t['list_view']; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="agenda-tab" data-bs-toggle="tab" data-bs-target="#agenda" type="button" role="tab">
                    <i class="fas fa-clipboard-list me-2"></i>
                    <?php echo $t['agenda_view']; ?>
                </button>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="mediationTabsContent">
            <!-- Calendar Tab -->
            <div class="tab-pane fade show active" id="calendar" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
            
            <!-- List Tab -->
            <div class="tab-pane fade" id="list" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><?php echo $t['mediation_schedule']; ?></h6>
                        <div class="d-flex">
                            <div class="input-group me-2" style="width: 250px;">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="<?php echo $t['search']; ?>" id="searchMediations">
                            </div>
                            <select class="form-select me-2" style="width: 150px;" id="filterStatus">
                                <option value=""><?php echo $t['all']; ?> <?php echo $t['status']; ?></option>
                                <option value="scheduled"><?php echo $t['scheduled']; ?></option>
                                <option value="confirmed"><?php echo $t['confirmed']; ?></option>
                                <option value="pending"><?php echo $t['pending']; ?></option>
                                <option value="completed"><?php echo $t['completed']; ?></option>
                                <option value="cancelled"><?php echo $t['cancelled']; ?></option>
                            </select>
                            <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="fas fa-times me-1"></i>
                                <?php echo $t['clear_filters']; ?>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo $t['case_title']; ?></th>
                                        <th><?php echo $t['date']; ?></th>
                                        <th><?php echo $t['time']; ?></th>
                                        <th><?php echo $t['mediator']; ?></th>
                                        <th><?php echo $t['status']; ?></th>
                                        <th><?php echo $t['priority']; ?></th>
                                        <th><?php echo $t['actions']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mediations as $mediation): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $mediation['case_title']; ?></strong><br>
                                            <small class="text-muted"><?php echo ucfirst($mediation['case_type']); ?> • <?php echo $mediation['parties']; ?></small>
                                        </td>
                                        <td>
                                            <?php echo formatDate($mediation['date']); ?><br>
                                            <small class="text-muted"><?php echo $t['created_on']; ?>: <?php echo formatDate($mediation['created_at']); ?></small>
                                        </td>
                                        <td>
                                            <?php echo formatTime($mediation['start_time']); ?> - <?php echo formatTime($mediation['end_time']); ?>
                                        </td>
                                        <td><?php echo $mediation['mediator']; ?></td>
                                        <td>
                                            <span class="badge <?php echo getStatusClass($mediation['status']); ?>">
                                                <?php echo $t[$mediation['status']]; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo getPriorityClass($mediation['priority']); ?>">
                                                <?php echo $t[$mediation['priority'] . '_priority']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="action-btn btn-view" title="<?php echo $t['view']; ?>" data-bs-toggle="modal" data-bs-target="#viewModal" onclick="viewMediation(<?php echo $mediation['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="action-btn btn-edit" title="<?php echo $t['edit']; ?>" data-bs-toggle="modal" data-bs-target="#scheduleModal" onclick="editMediation(<?php echo $mediation['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="action-btn btn-reschedule" title="<?php echo $t['reschedule']; ?>" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                                                <i class="fas fa-calendar-alt"></i>
                                            </a>
                                            <a href="#" class="action-btn btn-delete" title="<?php echo $t['delete']; ?>" onclick="confirmDelete(<?php echo $mediation['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Agenda Tab -->
            <div class="tab-pane fade" id="agenda" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><?php echo $t['today']; ?>'s <?php echo $t['agenda']; ?></h6>
                            </div>
                            <div class="card-body">
                                <?php 
                                $today = date('Y-m-d');
                                $today_mediations = array_filter($mediations, function($m) use ($today) {
                                    return $m['date'] == $today;
                                });
                                
                                if (empty($today_mediations)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted"><?php echo $t['no_sessions_scheduled']; ?></h5>
                                    <p class="text-muted"><?php echo $t['schedule_your_first']; ?></p>
                                </div>
                                <?php else: ?>
                                <?php foreach ($today_mediations as $mediation): ?>
                                <div class="agenda-item mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo $mediation['case_title']; ?></h6>
                                            <p class="mb-1 text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo formatTime($mediation['start_time']); ?> - <?php echo formatTime($mediation['end_time']); ?>
                                            </p>
                                            <p class="mb-1 text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo $mediation['mediator']; ?>
                                            </p>
                                            <p class="mb-0 text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?php echo $mediation['venue']; ?>
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge <?php echo getStatusClass($mediation['status']); ?> mb-2">
                                                <?php echo $t[$mediation['status']]; ?>
                                            </span>
                                            <br>
                                            <a href="#" class="btn btn-sm btn-outline-primary mt-2">
                                                <i class="fas fa-video me-1"></i>
                                                <?php echo $t['join']; ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><?php echo $t['quick_schedule']; ?></h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary" onclick="quickSchedule('today')">
                                        <i class="fas fa-calendar-day me-2"></i>
                                        <?php echo $t['today']; ?>
                                    </button>
                                    <button class="btn btn-outline-primary" onclick="quickSchedule('tomorrow')">
                                        <i class="fas fa-calendar-plus me-2"></i>
                                        <?php echo $t['tomorrow']; ?>
                                    </button>
                                    <button class="btn btn-outline-primary" onclick="quickSchedule('next_week')">
                                        <i class="fas fa-calendar-week me-2"></i>
                                        <?php echo $t['next_week']; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><?php echo $t['upcoming']; ?> <?php echo $t['sessions']; ?></h6>
                            </div>
                            <div class="card-body">
                                <?php 
                                $upcoming_mediations = array_filter($mediations, function($m) {
                                    return ($m['status'] == 'scheduled' || $m['status'] == 'confirmed') && 
                                           strtotime($m['date']) > strtotime('today');
                                });
                                
                                usort($upcoming_mediations, function($a, $b) {
                                    return strtotime($a['date'] . ' ' . $a['start_time']) - strtotime($b['date'] . ' ' . $b['start_time']);
                                });
                                
                                $upcoming_mediations = array_slice($upcoming_mediations, 0, 3);
                                
                                if (empty($upcoming_mediations)): ?>
                                <p class="text-muted mb-0"><?php echo $t['no_sessions_scheduled']; ?></p>
                                <?php else: ?>
                                <?php foreach ($upcoming_mediations as $mediation): ?>
                                <div class="upcoming-item mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1" style="font-size: 0.9rem;"><?php echo substr($mediation['case_title'], 0, 30) . (strlen($mediation['case_title']) > 30 ? '...' : ''); ?></h6>
                                            <p class="mb-0 text-muted" style="font-size: 0.8rem;">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                <?php echo formatDate($mediation['date']); ?>
                                                <i class="fas fa-clock ms-2 me-1"></i>
                                                <?php echo formatTime($mediation['start_time']); ?>
                                            </p>
                                        </div>
                                        <span class="badge <?php echo getStatusClass($mediation['status']); ?>" style="font-size: 0.7rem;">
                                            <?php echo $t[$mediation['status']]; ?>
                                        </span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Schedule Modal -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="scheduleModalLabel">
                            <i class="fas fa-calendar-plus me-2"></i>
                            <?php echo $t['schedule_mediation']; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="case_title" class="form-label required"><?php echo $t['case_title']; ?></label>
                                <input type="text" class="form-control" id="case_title" name="case_title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="case_type" class="form-label required"><?php echo $t['case_type']; ?></label>
                                <select class="form-select" id="case_type" name="case_type" required>
                                    <option value=""><?php echo $t['select']; ?>...</option>
                                    <?php foreach ($case_types as $type): ?>
                                    <option value="<?php echo $type; ?>"><?php echo $t[$type]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mediation_date" class="form-label required"><?php echo $t['mediation_date']; ?></label>
                                <input type="date" class="form-control" id="mediation_date" name="mediation_date" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="start_time" class="form-label required"><?php echo $t['start_time']; ?></label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="end_time" class="form-label required"><?php echo $t['end_time']; ?></label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mediator_id" class="form-label required"><?php echo $t['mediator']; ?></label>
                                <select class="form-select" id="mediator_id" name="mediator_id" required>
                                    <option value=""><?php echo $t['select_mediator']; ?>...</option>
                                    <?php foreach ($mediators as $mediator): ?>
                                    <option value="<?php echo $mediator['id']; ?>"><?php echo $mediator['name']; ?> (<?php echo $mediator['specialization']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="venue" class="form-label required"><?php echo $t['venue']; ?></label>
                                <select class="form-select" id="venue" name="venue" required>
                                    <option value=""><?php echo $t['select_venue']; ?>...</option>
                                    <?php foreach ($venues as $venue): ?>
                                    <option value="<?php echo $venue; ?>"><?php echo $t[$venue] ?? $venue; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="virtual_link" class="form-label"><?php echo $t['meeting_link']; ?></label>
                            <input type="url" class="form-control" id="virtual_link" name="virtual_link" placeholder="https://meet.example.com/...">
                            <div class="form-text"><?php echo $t['optional']; ?> - <?php echo $t['for_virtual_mediation']; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="parties_involved" class="form-label required"><?php echo $t['parties_involved']; ?></label>
                            <textarea class="form-control" id="parties_involved" name="parties_involved" rows="2" required></textarea>
                            <div class="form-text"><?php echo $t['comma_separated']; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label"><?php echo $t['notes']; ?></label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label"><?php echo $t['priority']; ?></label>
                                <select class="form-select" id="priority" name="priority">
                                    <?php foreach ($priority_options as $priority): ?>
                                    <option value="<?php echo $priority; ?>"><?php echo $t[$priority . '_priority']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="estimated_duration" class="form-label"><?php echo $t['estimated_duration']; ?></label>
                                <select class="form-select" id="estimated_duration" name="estimated_duration">
                                    <option value="1">1 <?php echo $t['hour']; ?></option>
                                    <option value="2" selected>2 <?php echo $t['hours']; ?></option>
                                    <option value="3">3 <?php echo $t['hours']; ?></option>
                                    <option value="4">4 <?php echo $t['hours']; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $t['cancel']; ?></button>
                        <button type="submit" name="schedule_mediation" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            <?php echo $t['save']; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">
                        <i class="fas fa-eye me-2"></i>
                        <?php echo $t['mediation_details']; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewModalBody">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $t['close']; ?></button>
                    <button type="button" class="btn btn-primary" onclick="editCurrentMediation()">
                        <i class="fas fa-edit me-1"></i>
                        <?php echo $t['edit']; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reschedule Modal -->
    <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <input type="hidden" name="mediation_id" id="reschedule_mediation_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rescheduleModalLabel">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <?php echo $t['reschedule']; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_date" class="form-label required"><?php echo $t['new_date']; ?></label>
                            <input type="date" class="form-control" id="new_date" name="new_date" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_time" class="form-label required"><?php echo $t['new_time']; ?></label>
                            <input type="time" class="form-control" id="new_time" name="new_time" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reschedule_reason" class="form-label"><?php echo $t['reason']; ?></label>
                            <textarea class="form-control" id="reschedule_reason" name="reschedule_reason" rows="3"></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <?php echo $t['notification_sent']; ?> <?php echo $t['participants']; ?> <?php echo $t['about']; ?> <?php echo $t['reschedule']; ?>.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $t['cancel']; ?></button>
                        <button type="submit" name="reschedule_mediation" class="btn btn-primary">
                            <i class="fas fa-calendar-check me-1"></i>
                            <?php echo $t['confirm']; ?> <?php echo $t['reschedule']; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Function to toggle language
        function toggleLanguage() {
            const currentLang = "<?php echo $language; ?>";
            const newLang = currentLang === 'hi' ? 'en' : 'hi';
            window.location.href = `schedule-mediation.php?lang=${newLang}`;
        }
        
        // Initialize FullCalendar
        document.addEventListener('DOMContentLoaded', function() {
            // Set default date for schedule modal
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('mediation_date').value = today;
            document.getElementById('new_date').value = today;
            
            // Set default times
            const now = new Date();
            const nextHour = new Date(now.getTime() + 60 * 60 * 1000);
            document.getElementById('start_time').value = nextHour.toTimeString().substring(0, 5);
            
            const twoHoursLater = new Date(nextHour.getTime() + 60 * 60 * 1000);
            document.getElementById('end_time').value = twoHoursLater.toTimeString().substring(0, 5);
            document.getElementById('new_time').value = nextHour.toTimeString().substring(0, 5);
            
            // Initialize Select2
            $('#mediator_id, #venue, #case_type').select2({
                width: '100%',
                placeholder: "<?php echo $t['select']; ?>...",
                allowClear: true
            });
            
            // Initialize FullCalendar
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    <?php foreach ($mediations as $mediation): ?>
                    {
                        id: '<?php echo $mediation['id']; ?>',
                        title: '<?php echo addslashes($mediation['case_title']); ?>',
                        start: '<?php echo $mediation['date']; ?>T<?php echo $mediation['start_time']; ?>',
                        end: '<?php echo $mediation['date']; ?>T<?php echo $mediation['end_time']; ?>',
                        className: '<?php echo $mediation['status']; ?>',
                        extendedProps: {
                            mediator: '<?php echo addslashes($mediation['mediator']); ?>',
                            venue: '<?php echo addslashes($mediation['venue']); ?>',
                            status: '<?php echo $mediation['status']; ?>',
                            priority: '<?php echo $mediation['priority']; ?>'
                        }
                    },
                    <?php endforeach; ?>
                ],
                eventClick: function(info) {
                    viewMediation(info.event.id);
                },
                eventClassNames: function(arg) {
                    let classes = [];
                    if (arg.event.extendedProps.status === 'scheduled') classes.push('fc-event-primary');
                    if (arg.event.extendedProps.status === 'confirmed') classes.push('fc-event-success');
                    if (arg.event.extendedProps.status === 'pending') classes.push('fc-event-warning');
                    if (arg.event.extendedProps.status === 'completed') classes.push('fc-event-secondary');
                    if (arg.event.extendedProps.status === 'cancelled') classes.push('fc-event-danger');
                    if (arg.event.extendedProps.priority === 'high') classes.push('fc-event-high-priority');
                    return classes;
                }
            });
            calendar.render();
            
            // Search functionality
            document.getElementById('searchMediations').addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#list tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
            
            // Filter by status
            document.getElementById('filterStatus').addEventListener('change', function() {
                const status = this.value;
                const rows = document.querySelectorAll('#list tbody tr');
                
                rows.forEach(row => {
                    if (!status) {
                        row.style.display = '';
                        return;
                    }
                    
                    const rowStatus = row.querySelector('td:nth-child(5) .badge').textContent.toLowerCase();
                    const statusMap = {
                        'scheduled': '<?php echo $t['scheduled']; ?>',
                        'confirmed': '<?php echo $t['confirmed']; ?>',
                        'pending': '<?php echo $t['pending']; ?>',
                        'completed': '<?php echo $t['completed']; ?>',
                        'cancelled': '<?php echo $t['cancelled']; ?>'
                    };
                    
                    row.style.display = rowStatus === statusMap[status] ? '' : 'none';
                });
            });
        });
        
        // Clear filters
        function clearFilters() {
            document.getElementById('searchMediations').value = '';
            document.getElementById('filterStatus').value = '';
            
            const rows = document.querySelectorAll('#list tbody tr');
            rows.forEach(row => {
                row.style.display = '';
            });
        }
        
        // Quick schedule functions
        function quickSchedule(type) {
            const modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
            modal.show();
            
            const today = new Date();
            let date = new Date();
            
            switch(type) {
                case 'today':
                    // Already today
                    break;
                case 'tomorrow':
                    date.setDate(today.getDate() + 1);
                    break;
                case 'next_week':
                    date.setDate(today.getDate() + 7);
                    break;
            }
            
            const dateString = date.toISOString().split('T')[0];
            document.getElementById('mediation_date').value = dateString;
            
            // Set default time
            const nextHour = new Date();
            nextHour.setHours(nextHour.getHours() + 1);
            document.getElementById('start_time').value = nextHour.toTimeString().substring(0, 5);
            
            const twoHoursLater = new Date(nextHour.getTime() + 60 * 60 * 1000);
            document.getElementById('end_time').value = twoHoursLater.toTimeString().substring(0, 5);
        }
        
        // View mediation details
        // In the viewMediation function, update the template literal to use proper syntax:
function viewMediation(id) {
    // In a real app, this would fetch data from server
    // For demo, we'll use the first mediation
    const mediation = <?php echo json_encode($mediations[0]); ?>;
    
    const modalBody = document.getElementById('viewModalBody');
    modalBody.innerHTML = `
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted"><?php echo $t['case_title']; ?></h6>
                <p class="fs-5">${mediation.case_title}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted"><?php echo $t['case_type']; ?></h6>
                <p class="fs-5">${getTranslatedCaseType(mediation.case_type)}</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <h6 class="text-muted"><?php echo $t['date']; ?></h6>
                <p class="fs-5">${formatDate(mediation.date)}</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-muted"><?php echo $t['time']; ?></h6>
                <p class="fs-5">${formatTime(mediation.start_time)} - ${formatTime(mediation.end_time)}</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-muted"><?php echo $t['duration']; ?></h6>
                <p class="fs-5">2 <?php echo $t['hours']; ?></p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted"><?php echo $t['mediator']; ?></h6>
                <p class="fs-5">${mediation.mediator}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted"><?php echo $t['venue']; ?></h6>
                <p class="fs-5">${mediation.venue}</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted"><?php echo $t['status']; ?></h6>
                <p>
                    <span class="badge ${getStatusClass(mediation.status)}">
                        ${getTranslatedStatus(mediation.status)}
                    </span>
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted"><?php echo $t['priority']; ?></h6>
                <p>
                    <span class="badge ${getPriorityClass(mediation.priority)}">
                        ${getTranslatedPriority(mediation.priority)}
                    </span>
                </p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="text-muted"><?php echo $t['parties_involved']; ?></h6>
                <p class="fs-5">${mediation.parties}</p>
            </div>
        </div>
        
        ${mediation.virtual_link ? `
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="text-muted"><?php echo $t['meeting_link']; ?></h6>
                <p class="fs-5">
                    <a href="${mediation.virtual_link}" target="_blank">${mediation.virtual_link}</a>
                </p>
            </div>
        </div>
        ` : ''}
        
        <div class="row">
            <div class="col-12">
                <h6 class="text-muted"><?php echo $t['created_by']; ?></h6>
                <p class="fs-5">${mediation.created_by} on ${formatDate(mediation.created_at)}</p>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('viewModal'));
    modal.show();
}

// Add helper functions for translation in JavaScript
function getTranslatedCaseType(caseType) {
    const translations = {
        'family': '<?php echo $t["family"]; ?>',
        'business': '<?php echo $t["business"]; ?>',
        'civil': '<?php echo $t["civil"]; ?>',
        'community': '<?php echo $t["community"]; ?>',
        'workplace': '<?php echo $t["workplace"]; ?>',
        'other': '<?php echo $t["other"]; ?>'
    };
    return translations[caseType] || caseType;
}

function getTranslatedStatus(status) {
    const translations = {
        'scheduled': '<?php echo $t["scheduled"]; ?>',
        'confirmed': '<?php echo $t["confirmed"]; ?>',
        'pending': '<?php echo $t["pending"]; ?>',
        'in_progress': '<?php echo $t["in_progress"]; ?>',
        'completed': '<?php echo $t["completed"]; ?>',
        'cancelled': '<?php echo $t["cancelled"]; ?>'
    };
    return translations[status] || status;
}

function getTranslatedPriority(priority) {
    const translations = {
        'high': '<?php echo $t["high_priority"]; ?>',
        'medium': '<?php echo $t["medium_priority"]; ?>',
        'low': '<?php echo $t["low_priority"]; ?>'
    };
    return translations[priority] || priority;
}
        
        // Show notification
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }
    </script>
</body>
</html>