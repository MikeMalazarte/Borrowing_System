<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engineering Borrowing System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f5f5f5;
            margin: 0;
        }
        .sidebar {
            width: 220px;
            min-width: 220px;
            min-height: 100vh;
            background: #fff;
            border-right: 1px solid #e9e9e9;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
        }
        .sidebar-brand {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #e9e9e9;
        }
        .sidebar-brand small {
            font-size: 11px;
            color: #888;
            display: block;
        }
        .sidebar-brand span {
            font-size: 14px;
            font-weight: 500;
            color: #1a1a1a;
        }
        .sidebar-nav {
            flex: 1;
            padding: 0.75rem 0;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 1rem;
            font-size: 13px;
            color: #666;
            text-decoration: none;
            border-left: 2px solid transparent;
            transition: all 0.15s;
        }
        .sidebar-nav a:hover {
            background: #f5f5f5;
            color: #1a1a1a;
        }
        .sidebar-nav a.active {
            background: #f5f5f5;
            color: #1a1a1a;
            border-left: 2px solid #1a1a1a;
            font-weight: 500;
        }
        .sidebar-nav a i {
            font-size: 14px;
            width: 16px;
        }
        .sidebar-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid #e9e9e9;
        }
        .sidebar-footer .user-name {
            font-size: 12px;
            font-weight: 500;
            color: #1a1a1a;
            margin: 0;
        }
        .sidebar-footer .user-code {
            font-size: 11px;
            color: #888;
            margin: 2px 0 6px;
        }
        .sidebar-footer a {
            font-size: 12px;
            color: #888;
            text-decoration: none;
        }
        .sidebar-footer a:hover {
            color: #1a1a1a;
        }
        .main-content {
            margin-left: 220px;
            padding: 1.5rem;
            min-height: 100vh;
        }
    </style>
</head>
<body>

<div class="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <small>Engineering Tools</small>
        <span>Borrowing System</span>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <a href="<?= base_url('Borrowing-System?meaction=STUDENT-DASHBOARD') ?>"
            class="<?= (isset($active_page) && $active_page == 'dashboard') ? 'active' : '' ?>">
            <i class="bi bi-grid"></i> Dashboard
        </a>
        <a href="<?= base_url('Borrowing-System?meaction=BROWSE-TOOLS') ?>"
            class="<?= (isset($active_page) && $active_page == 'browse') ? 'active' : '' ?>">
            <i class="bi bi-search"></i> Browse Tools
        </a>
        <a href="<?= base_url('Borrowing-System?meaction=MY-BORROWINGS') ?>"
            class="<?= (isset($active_page) && $active_page == 'borrowings') ? 'active' : '' ?>">
            <i class="bi bi-clipboard"></i> My Borrowings
        </a>
        <a href="<?= base_url('Borrowing-System?meaction=BORROW-HISTORY') ?>"
            class="<?= (isset($active_page) && $active_page == 'history') ? 'active' : '' ?>">
            <i class="bi bi-clock-history"></i> Borrow History
        </a>
        <a href="<?= base_url('Borrowing-System?meaction=MY-PROFILE') ?>"
            class="<?= (isset($active_page) && $active_page == 'profile') ? 'active' : '' ?>">
            <i class="bi bi-person"></i> My Profile
        </a>
    </nav>

    <!-- User info + logout -->
    <div class="sidebar-footer">
        <p class="user-name"><?= session()->get('full_name') ?></p>
        <p class="user-code"><?= session()->get('user_code') ?></p>
        <a href="#" id="btnLogout"><i class="bi bi-box-arrow-left"></i> Sign out</a>
    </div>

</div>

<!-- Main content starts here -->
<div class="main-content">