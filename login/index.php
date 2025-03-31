<?php
error_reporting(1);
include('../essential/backbone.php');
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
$aes = new AES256;
$err = $_GET['err'];
$err = $aes->decrypt($err, "secretkey");
?>

<!DOCTYPE html>   
<html>   
<head>  
<meta name="viewport" content="width=device-width, initial-scale=1">  
<title> Login Page </title>  
<link rel="stylesheet" href="/assets/style2.css">  
</head>    
<body>    
    <center> <h1> Login Form </h1> </center>   
    <form>  
        <div class="container">   
            <label>Username : </label>   
            <input type="text" id="username" placeholder="Enter Username" name="username" required>  
            <label>Password : </label>   
            <input type="password" id="password" placeholder="Enter Password" name="password" required>  
            <button type="button" onclick="login();">Login</button>   
			<a href="/register"><button type="button" class="cancelbtn"> Register</button></a> 
			<a href="/"><button type="button" class="cancelbtn"> Cancel</button></a> 
        </div>  	
    </form>   
	<center> <h1> <?php echo $err ?> </h1> </center> 


  <script>
  function login() {
  const usernameInput = document.getElementById("username");
  const usernameValue = usernameInput.value;

  const passwordInput = document.getElementById("password");
  const passwordValue = passwordInput.value;

  // Create URL-encoded string
  const urlEncodedData = new URLSearchParams();
  urlEncodedData.append("userID", usernameValue);
  urlEncodedData.append("password", passwordValue);

  fetch("./login.php", {
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
</html>  