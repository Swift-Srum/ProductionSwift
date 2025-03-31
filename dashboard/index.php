<?php
error_reporting(1);
include('../essential/backbone.php');
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
$username = $_COOKIE['user_name'];
$sessionID = $_COOKIE['sessionId'];

$aes = new AES256;
$err = $_GET['err'];
$err = $aes->decrypt($err, "secretkey");;

$loggedIn = confirmSessionKey($username, $sessionID);
$isAdmin = checkIsUserAdmin($username, $sessionID);

$idx = getUserID();

$userType = "";

if ($isAdmin == true)
	$userType = "Admin";
else
{
	$userType = "Standard";
	$isAdmin = 0;
}

if ($loggedIn != true)
	die();

$bowsers = getAllBowsers();
$ownBowsers = getAllBowsersOwned($idx);

$totalBowsers = count($bowsers);
$ownBowsersCount = count($ownBowsers);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="/assets/style3.css"> 
	<link rel="stylesheet" href="/assets/style3mobile.css" media="(max-width: 768px)">
    <meta charSet="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="preload" as="image" href="./fortnite.jpg"/>
    <link rel="preload" as="image" href="./assets/arrow-right.svg"/>
    <link rel="preload" as="image" href="./apex.jpg"/>
    <link rel="preload" as="image" href="./cod.jpg"/>
    <link rel="stylesheet" href="/_next/static/css/ffefdee645895bec.css" crossorigin="" data-precedence="next"/>
    <link rel="preload" as="script" fetchPriority="low" href="/_next/static/chunks/webpack-7836209105978213.js" crossorigin=""/>
    <script src="/_next/static/chunks/fd9d1056-c7082c319cc53ced.js" async="" crossorigin=""></script>
    <script src="/_next/static/chunks/69-d05e895b1e27a732.js" async="" crossorigin=""></script>
    <script src="/_next/static/chunks/main-app-d79b338b1a974716.js" async="" crossorigin=""></script>

	<style>
	html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: auto; /* Ensure scrolling is enabled */
        }
	.login-btn {
            position: absolute;
            top: 30px;
            right: 20px;
            background-color: #6a0dad; /* Purple button background */
            color: #ffffff; /* White text color */
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px; /* Add border-radius for a softer button appearance */
            text-decoration: none; /* Remove underline from link */
        }
		</style>
    <meta name="description" content="Find bowsers near you.">
	<meta name="keywords" content="Bowsers, water bowsers">

    <link rel="icon" href="/assets/favicon.ico" type="image/x-icon" sizes="16x16"/>

    <script src="/_next/static/chunks/polyfills-c67a75d1b6f99dc8.js" crossorigin="" noModule=""></script>
	
</head>
<body class="__className_aaf875">

<?php
if($loggedIn != true)
	echo '<a href="/login" class="login-btn">Login</a>';
else
	echo '<a href="/login/logout.php?session=' . $sessionID . '" class="login-btn">Logout</a>';
?>
<div>
    <div class="top"></div>
    <div class="text"><h1>Welcome to the dashboard, <?php echo $username?>. Access Type: <?php echo $userType ?></h1></div>
    <div class="content">
        <div class="mainHeader">
            <div class="mainHeaderText">
                <div class="HeaderText"Bowsers Prototype</div>
                <div class="HeaderText textPurple"></div>
            </div>
        </div>
        <div class="products">
            <div class="product">
                <img src="/assets/back.jpg" alt="Product Image" class="productImage"/>
                <div class="productTitle">View Local Bowsers</div>
                <div class="productInfo">

                </div>
                <div class="productInfo">
                    <img src="/assets/arrow-right.svg" alt="SVG Image" style="font-size:15px"/>
                    <div>Status: Active</div>
                </div>
                <div class="productInfo">
                    <img src="/assets/arrow-right.svg" alt="SVG Image" style="font-size:15px"/>
                    <div>Total Avaliable Bowsers: <?php echo $totalBowsers ?></div>
                </div>
                <div onclick="location.href = 'lol';" class="getAccessBtn">View Now</div>
        </div>
		<?php
    if ($userType == "Admin") {
        echo '
        <div class="product">
            <img src="/assets/back.jpg" alt="Product Image" class="productImage"/>
            <div class="productTitle">View Your Bowsers (Admin)</div>
            <div class="productInfo"></div>
            <div class="productInfo">
                <img src="/assets/arrow-right.svg" alt="SVG Image" style="font-size:15px"/>
                <div>Your Bowsers: ' . $ownBowsersCount . '</div>
            </div>
            <div onclick="location.href = \'lol\';" class="getAccessBtn">View Now</div>
        </div>
        <div class="product">
            <img src="/assets/back.jpg" alt="Product Image" class="productImage"/>
            <div class="productTitle">Add Bowser To Database (Admin)</div>
            <div class="productInfo"></div>
            <div class="productInfo">
                <img src="/assets/arrow-right.svg" alt="SVG Image" style="font-size:15px"/>
                <div>Your Bowsers: ' . $ownBowsersCount . '</div>
            </div>
            <div onclick="location.href = \'lol\';" class="getAccessBtn">Add Now</div>
        </div>
        ';
    }
?>

    </div>
</div>