<?php
error_reporting(1);
include('../essential/backbone.php');
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

$username = $_COOKIE['user_name'];
$sessionID = $_COOKIE['sessionId'];
$idx = getUserID();


// AES decryption
$aes = new AES256;
$err = $_GET['err'];
$err = $aes->decrypt($err, "secretkey");

// Validate session and check if the user is an admin
$loggedIn = confirmSessionKey($username, $sessionID);
$isAdmin = checkIsUserAdmin($username, $sessionID);

if (!$loggedIn) {
    echo 'Please login';
    exit; // Stop further execution if the user is not logged in.
}

$postcode = $_GET['postcode'] ?? null;
if ($postcode) {
    $url = "https://api.postcodes.io/postcodes/$postcode";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['result'])) {
        $eastings = $data['result']['eastings'];
        $northings = $data['result']['northings'];
    } else {
        $eastings = 0;
		$northings = 0;
    }
}

$userType = $isAdmin ? "Admin" : "Standard";
$isAdmin = $isAdmin ? 1 : 0;

$distance1 = $_GET['distance'];

if ($distance > 30000)
	$distance = 30000;

$n1 = $northings - $distance;
$n2 = $northings + $distance;
$e1 = $eastings - $distance;
$e2 = $eastings + $distance;

try {
    
	$items = searchBowsers($e1, $e2, $n1, $n2);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
	$items = [];

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="/_next/static/css/ffefdee645895bec.css" crossorigin="" />
    <link rel="preload" as="image" href="/assets/arrow-right.svg" />
    <link rel="icon" href="/assets/favicon.ico" type="image/x-icon" sizes="16x16"/>
    <link rel="stylesheet" href="/assets/style1.css">
    <link rel="stylesheet" href="/assets/style1mobile.css" media="(max-width: 768px)">
    <script src="/_next/static/chunks/polyfills-c67a75d1b6f99dc8.js" crossorigin="" noModule=""></script>
    <title>Search Bowsers</title>
</head>
<body>
<?php
    if ($loggedIn) {
        echo '<a href="/create-bowser" class="login-btn">Add Bowser</a>';
        //echo '<a href="/my-items" class="login-btn">My Bowsers</a>';
    }
?>

<div class="top"></div>
<div class="text"><h1>TEST</h1></div>

<!-- Postcode Search Bar -->
<!-- Postcode Search Bar with Distance Slider -->
<form method="GET" action="" class="search-form">
    <input type="text" name="postcode" placeholder="Enter postcode" required 
        value="<?= isset($_GET['postcode']) ? htmlspecialchars($_GET['postcode']) : '' ?>">

    <div class="slider-container">
        <label for="distance" style="color: white;">
            Distance: <span id="distanceValue"><?= isset($_GET['distance']) ? (int)$_GET['distance'] : 15 ?></span> km
        </label>
        <input type="range" id="distance" name="distance" min="1" max="30" 
            value="<?= isset($_GET['distance']) ? (int)$_GET['distance'] : 15 ?>" 
            oninput="updateDistanceValue(this.value)">
    </div>

    <button type="submit">Search</button>
</form>


<div class="content">
    <div class="mainHeader">
        <div class="mainHeaderText">
            <div class="HeaderText">TEST</div>
            <div class="HeaderText textPurple">TEST</div>
        </div>
    </div>
    <div class="products">
        <?php foreach ($items as $item) { 
            $id = $item['id'];
            $ownerId = $item['ownerId'];
            $model = htmlspecialchars($item['model']);
            $manufacturer_details = htmlspecialchars($item['manufacturer_details']);
            $itemImageName = getItemImage($id);
            $ownerName = getUsernameById($ownerId);
            $avaliable = $item['active'] ?? 0;
			$postcode = $item['postcode'];
        ?>
            <div class="product">
                <img src="/create-bowser/uploads/<?php echo htmlspecialchars($itemImageName); ?>" alt="Product Image" class="productImage" onerror="this.onerror=null;this.src='/create-item/uploads/NOIMAGE.jpg';"/>
                <div class="productTitle"><?php echo $model; ?></div>
                <div class="productInfo">
                    <img src="/assets/arrow-right.svg" alt="SVG Image" style="font-size:35px"/>
                    <div>Postcode: <?php echo $postcode; ?></div>
                </div>
                <div class="productInfo">
                    <img src="/assets/arrow-right.svg" alt="SVG Image" style="font-size:35px"/>
                    <div>Details: <?php echo $manufacturer_details; ?></div>
                </div>
                <div class="productInfo">
                    <img src="/assets/arrow-right.svg" alt="SVG Image" style="font-size:35px"/>
                    <div>Owned By: <?php echo $ownerName; ?></div>
                </div>
                <?php if ($avaliable == 1) { ?>
                    <div onclick="location.href = '../item-details?itemId=<?php echo $id; ?>';" class="getAccessBtn">View</div>
                <?php } else { ?>
                    <div class="getAccessBtn">Unavailable</div>
                <?php } ?>
                <?php if ($isAdmin || $idx == $ownerId) { ?>
                    <div onclick="location.href = '../edit-item?itemId=<?php echo $id; ?>';" class="getAccessBtn">Edit</div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>
<script>
function deleteItem(id) {
    const urlEncodedData = new URLSearchParams();
    urlEncodedData.append("id", id);

    fetch("../php/deleteItem.php", {
        method: "POST",
        body: urlEncodedData,
        headers: {
            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
        } else {
            return response.text();
        }
    })
    .then(data => {
        console.log(data);
    })
    .catch(error => {
        console.error("Error during fetch:", error);
    });
}
</script>
<script>
    function updateDistanceValue(value) {
        document.getElementById("distanceValue").textContent = value;
    }
</script>

</body>
</html>
