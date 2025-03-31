<?php
// Set error reporting level to report all errors except E_NOTICE
error_reporting(1);

// Include necessary files
include('../essential/backbone.php');

// Set HTTP headers for security measures
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
$origin = $_SERVER['HTTP_REFERER'];
if($origin != "https://s4308324-ctxxxx.uogs.co.uk/view-bikes/" && $origin != "https://s4308324-ctxxxx.uogs.co.uk/view-all-bikes/")
	$origin = "../";

// Retrieve username and session ID from cookies
$username = $_COOKIE['user_name'];
$sessionID = $_COOKIE['sessionId'];

// Check if user is logged in and if they are an admin
$loggedIn = confirmSessionKey($username, $sessionID);
$isAdmin = checkIsUserAdmin($username, $sessionID);

$userType = "";

// Retrieve bike ID from URL parameter
$itemId =  $_GET['itemId'];

// Retrieve bike details based on user ID and bike ID
$itemInfo = getItemDetails($itemId);

// Get bike image name
$itemImageName = getItemImage($itemId);

// Initialize AES encryption object and decrypt error message from URL parameter
$aes = new AES256;
$err = $_GET['err'];
$err = $aes->decrypt($err, "secretkey");

// Loop through bike details array and assign values to variables
foreach ($itemInfo as $item) {
	$id = $item['id'];
    $name = $item['name'];
	$price = $item['price'];
	$details = $item['details'];
	$buyLink = $item['buyLink'];
	$avaliable = $item['avaliable'];
	
	if($avaliable == 0)
		$avaliable = "no";
	else
		$avaliable = "yes";
}

?>
<!DOCTYPE html>   
<html>   
<head>  
<meta name="viewport" content="width=device-width, initial-scale=1">  
<title> Edit Item </title>  
<link rel="stylesheet" href="/assets/style_create.css">  

</head>    
<body>    
  <center><h1>Edit Item</h1></center>   
  <form>  
    <div class="container">   
	  <label>Name (e.g. PvP Kit):</label>   
      <input type="text" id="name" placeholder="Enter Item Name" name="name" value="<?php echo $name; ?>" required>
      <label>Price:</label>   
      <input type="text" id="price" placeholder="Enter Price" name="price" value=<?php echo $price ?> required>  
      <label>Details:</label>   
      <textarea id="details" name="details" placeholder="Enter Details" required rows="10" cols="45"><?php echo $details ?></textarea>
	  <label>Buy Link (Can use shoppy.gg):</label>   
      <input type="text" id="buyLink" placeholder="Enter Buy Link" name="buyLink" value=<?php echo $buyLink ?> required>  
	  <br>
      <button type="button" onclick="submitForm(<?php echo $id ?>);">Update Item</button>
      <a href="../"><button type="button" class="cancelbtn">Cancel</button></a>
	  <?php echo "<button onclick=\"deleteItem($id)\" type=\"button\" class=\"cancelbtn\">Delete</button>"; ?>
	  <?php 
	  if ($avaliable == "no")
        echo "<button onclick=\"updateItemStatus($id)\" type=\"button\" class=\"cancelbtn\">Enable</button>";
      else
        echo "<button onclick=\"updateItemStatus($id)\" type=\"button\" class=\"cancelbtn\">Disable</button>";		  ?>
    </div>   
  </form>     
  <center><h1><?php echo $err ?></h1></center>

  <script>
    function handleFileSelect() {
        const fileInput = document.getElementById('fileToUpload');
        const file = fileInput.files[0];

        if (file) {
            uploadFile(file);
        }
    }

    function uploadFile(file) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();

        formData.append('fileToUpload', file);

        xhr.upload.addEventListener('progress', function (event) {
            if (event.lengthComputable) {
                const percentComplete = (event.loaded / event.total) * 100;
                document.getElementById('uploadProgress').value = percentComplete;
            }
        });

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    document.getElementById('uploadStatus').innerHTML = xhr.responseText;
                } else {
                    document.getElementById('uploadStatus').innerHTML = 'Error uploading file.';
                }
            }
        };

        xhr.open('POST', 'upload.php', true);
        xhr.send(formData);
    }
	
	  function updateItemStatus(id) {

  const status = "<?php echo $reportStatus ?>";


  // Create URL-encoded string
  const urlEncodedData = new URLSearchParams();
  urlEncodedData.append("avaliable", "<?php echo $avaliable ?>");
  urlEncodedData.append("itemId", id);

  fetch("../php/updateItemStatus.php", {
    method: "POST",
    body: urlEncodedData,
    headers: {
      "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
    },
  })
    .then(response => {
      // Check if the response is a redirect
      if (response.redirected) {
        // If redirected, get the new location
        const redirectLocation = response.url;

        // Redirect the user to the specified location
        window.location.href = redirectLocation;
      } else {
        // Handle other aspects of the response if needed
        if (response.ok) {
          return response.text(); // or response.json() if expecting JSON
        } else {
          throw new Error(`Failed with status: ${response.status}`);
        }
      }
    })
    .then(data => {
      // Handle the response data if needed
      console.log(data);
    })
    .catch(error => {
      // Handle fetch errors
      console.error("Error during fetch:", error);
    });
}

    function submitForm(itemId) {
		const nameInput = document.getElementById("name");
        const nameValue = nameInput.value;
		
        const priceInput = document.getElementById("price");
        const priceValue = priceInput.value;

        const detailsInput = document.getElementById("details");
        const detailsValue = detailsInput.value;
		
		const buyLinkInput = document.getElementById("buyLink");
        const buyLinkValue = buyLinkInput.value;



        // Create URL-encoded string
        const urlEncodedData = new URLSearchParams();
		urlEncodedData.append("id", itemId);
		urlEncodedData.append("name", nameValue);
        urlEncodedData.append("price", priceValue);
        urlEncodedData.append("details", detailsValue);
		urlEncodedData.append("buyLink", buyLinkValue);

        fetch("./edit.php", {
            method: "POST",
            body: urlEncodedData,
            headers: {
                "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
            },
        })
        .then(response => {
            if (response.redirected) {
                const redirectLocation = response.url;
                window.location.href = redirectLocation;
            } else {
                if (response.ok) {
                    return response.text();
				
                } else {
                    throw new Error(`Failed with status: ${response.status}`);
                }
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
function deleteItem(id) {


  // Create URL-encoded string
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
      // Check if the response is a redirect
      if (response.redirected) {
        // If redirected, get the new location
        const redirectLocation = response.url;

        // Redirect the user to the specified location
        window.location.href = redirectLocation;
      } else {
        // Handle other aspects of the response if needed
        if (response.ok) {
          return response.text(); // or response.json() if expecting JSON
        } else {
          throw new Error(`Failed with status: ${response.status}`);
        }
      }
    })
    .then(data => {
      // Handle the response data if needed
      console.log(data);
    })
    .catch(error => {
      // Handle fetch errors
      console.error("Error during fetch:", error);
    });
}

</script>

</body>


</script>     
</html>  