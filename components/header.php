<?php
// unset($_SESSION['error']);
// unset($_SESSION['success']);

$user = isset($_SESSION["user"]) ? $_SESSION["user"] : null;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gray1: '#333333',
                        gray4: '#BDBDBD',
                        gray5: '#E0E0E0',
                        blue100: '#2F80ED',
                        iris100: '#5D5FEF',
                        red: "#EB5757"
                    },
                    boxShadow: {
                        bg: "10px 10px 120px 0px rgba(0, 0, 0, 0.05)"
                    }
                }
            }
        }
    </script>
    <title>Job Hunt</title>
    <link rel="stylesheet" href="/assets/css/index.css">
    <script src="/assets/js/index.js"></script>
</head>

<body>
    <nav class="flex justify-between items-center py-4 md:py-8 px-6 md:px-12 shadow-bg">
        <a href="/">
            <svg width="50" height="49" viewBox="0 0 50 49" fill="none" xmlns="http://www.w3.org/2000/svg"
                class="w-6 md:w-8 h-auto">
                <path
                    d="M43.75 8.88672H35.4492V6.73828C35.4492 4.98047 34.847 3.49935 33.6426 2.29492C32.4382 1.09049 30.957 0.488281 29.1992 0.488281H20.8008C19.043 0.488281 17.5618 1.09049 16.3574 2.29492C15.153 3.49935 14.5508 4.98047 14.5508 6.73828V8.88672H6.25C4.49219 8.88672 3.01107 9.47266 1.80664 10.6445C0.602214 11.8164 0 13.3138 0 15.1367V23.4375C0 24.8047 0.683594 25.4883 2.05078 25.4883V42.1875C2.05078 43.2943 2.47396 44.2708 3.32031 45.1172C4.16667 45.9635 5.14323 46.3867 6.25 46.3867H43.75C44.8568 46.3867 45.8333 45.9635 46.6797 45.1172C47.526 44.2708 47.9492 43.2943 47.9492 42.1875V25.4883C49.3164 25.4883 50 24.8047 50 23.4375V15.1367C50 13.3138 49.3978 11.8164 48.1934 10.6445C46.9889 9.47266 45.5078 8.88672 43.75 8.88672ZM18.75 6.73828C18.75 6.15234 18.9453 5.66406 19.3359 5.27344C19.7266 4.88281 20.2148 4.6875 20.8008 4.6875H29.1992C29.7852 4.6875 30.2734 4.88281 30.6641 5.27344C31.0547 5.66406 31.25 6.15234 31.25 6.73828V8.88672H18.75V6.73828ZM6.25 42.1875V25.4883H18.75V31.7383C18.75 32.9102 19.1569 33.903 19.9707 34.7168C20.7845 35.5306 21.7773 35.9375 22.9492 35.9375H27.0508C28.2227 35.9375 29.2155 35.5306 30.0293 34.7168C30.8431 33.903 31.25 32.9102 31.25 31.7383V25.4883H43.75V42.1875H6.25ZM22.9492 31.7383V25.4883H27.0508V31.7383H22.9492ZM45.8008 21.3867H4.19922V15.1367C4.19922 13.7044 4.88281 12.9883 6.25 12.9883H43.75C45.1172 12.9883 45.8008 13.7044 45.8008 15.1367V21.3867Z"
                    fill="url(#paint0_linear_2_71)" />
                <defs>
                    <linearGradient id="paint0_linear_2_71" x1="-24" y1="-22.5" x2="53.5" y2="55"
                        gradientUnits="userSpaceOnUse">
                        <stop stop-color="#5D5FEF" />
                        <stop offset="0.9999" stop-color="#EF5DA8" />
                        <stop offset="1" stop-color="#333333" stop-opacity="0" />
                    </linearGradient>
                </defs>
            </svg>
        </a>

        <ul class="items-center justify-center gap-14 font-medium hidden md:flex">
            <?php if (!isset($user)) { ?>
                <li><a href="/signup.php" class="hover:text-blue100 focus:text-blue100">Sign up</a></li>
                <li><a href="/" class="hover:text-blue100 focus:text-blue100">Login</a></li>
            <?php } ?>
            <li><a href="/marketplace.php" class="hover:text-blue100 focus:text-blue100">Marketplace</a></li>
            <!-- show only when logged in -->
            <?php if (isset($user)) { ?>
                <li><a href="/applications.php" class="hover:text-blue100 focus:text-blue100">Applications</a></li>
                <li><a href="/my-jobs.php" class="hover:text-blue100 focus:text-blue100">My jobs</a></li>
                <form action="/logout.php" method="post">
                    <input type="submit" value="Logout"
                        class="hover:text-blue100 focus:text-blue100 cursor-pointer">
                </form>

            <?php } ?>
        </ul>


        <button id="menu-toggle" class="md:hidden">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 8H18M6 12H18M6 16H18" stroke="black" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="stroke-gray1" />
            </svg>
        </button>

        <ul id="menu" class="hidden md:hidden flex-col justify-evenly fixed inset-0 bg-white p-6">
            <button id="close-menu" class="absolute top-10 right-6">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="black" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="stroke-gray1" />
                </svg>
            </button>
            <?php if (!isset($user)) { ?>
                <li><a href="/signup.php" class="text-2xl font-semibold hover:text-blue100 focus:text-blue100">Sign up</a>
                </li>
                <li><a href="/" class="text-2xl font-semibold hover:text-blue100 focus:text-blue100">Login</a></li>
            <?php } ?>
            <li><a href="/marketplace.php"
                    class="text-2xl font-semibold hover:text-blue100 focus:text-blue100">Marketplace</a></li>
            <!-- show only when logged in -->
            <?php if (isset($user)) { ?>
                <li><a href="/my-jobs.php" class="text-2xl font-semibold hover:text-blue100 focus:text-blue100">My jobs</a>
                </li>
                <li><a href="/applications.php"
                        class="text-2xl font-semibold hover:text-blue100 focus:text-blue100">Applications</a></li>
                <form action="logout.php" method="post">
                    <input type="submit" value="Logout"
                        class="text-2xl font-semibold hover:text-blue100 focus:text-blue100">
                </form>
            <?php } ?>
        </ul>
    </nav>