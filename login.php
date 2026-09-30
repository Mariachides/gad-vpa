<?php
session_start();
include 'db.php';

// LOGIN CHECK
if(isset($_POST['username']) && isset($_POST['password'])){

    $username=$_POST['username'];
    $password=$_POST['password'];

    $stmt=$conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s",$username);
    $stmt->execute();

    $result=$stmt->get_result();
    $user=$result->fetch_assoc();

    if($user && password_verify($password,$user['password'])){

        $_SESSION['username']=$user['username'];
        $_SESSION['role']=$user['role'];

        if($user['role']=="admin"){

            header("Location: admin_landing.php");

        }else{

            header("Location: index.php");

        }

        exit();

    }else{

        $error="Invalid username or password!";

    }

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Login - GAD VPA</title>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width,initial-scale=1">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700;900&display=swap"
rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{

height:100vh;

overflow:hidden;

display:flex;

justify-content:center;

align-items:center;

font-family:'Poppins',sans-serif;

background:

radial-gradient(
circle at 20% 20%,
rgba(160,0,255,.35),
transparent 30%
),

radial-gradient(
circle at 80% 20%,
rgba(80,0,255,.35),
transparent 30%
),

radial-gradient(
circle at 50% 90%,
rgba(90,0,255,.35),
transparent 40%
),

linear-gradient(
-45deg,
#12001f,
#240046,
#3a0ca3,
#4a00e0
);

background-size:400% 400%;

animation:bgmove 12s infinite ease;

}

body::before,
body::after{

content:"";

position:absolute;

width:600px;

height:600px;

border-radius:50%;

filter:blur(150px);

z-index:-1;

}

body::before{

background:#8e2de2;

top:-150px;

left:-150px;

animation:float1 12s infinite;

}

body::after{

background:#6a11cb;

bottom:-150px;

right:-150px;

animation:float2 14s infinite;

}

@keyframes bgmove{

0%{
background-position:0% 50%;
}

50%{
background-position:100% 50%;
}

100%{
background-position:0% 50%;
}

}

@keyframes float1{

0%,100%{
transform:translate(0);
}

50%{
transform:translate(120px,60px);
}

}

@keyframes float2{

0%,100%{
transform:translate(0);
}

50%{
transform:translate(-120px,-80px);
}

}

.login-container{

width:900px;

max-width:95%;

display:flex;

overflow:hidden;

border-radius:30px;

background:
rgba(255,255,255,.08);

backdrop-filter:
blur(20px);

border:
1px solid rgba(255,255,255,.15);

box-shadow:

0 0 60px rgba(142,45,226,.35),

0 25px 80px rgba(0,0,0,.5);

}

.login-left{

width:50%;

padding:50px;

background:
rgba(255,255,255,.93);

display:flex;

flex-direction:column;

justify-content:center;

align-items:center;

}

.logo{

width:170px;

margin-bottom:20px;

filter:

drop-shadow(
0 0 15px #8e2de2
)

drop-shadow(
0 0 30px #6a11cb
);

animation:glow 2s infinite alternate;

}

@keyframes glow{

from{
transform:scale(1);
}

to{
transform:scale(1.04);
}

}

.title{

font-size:42px;

font-weight:900;

letter-spacing:8px;

margin-bottom:30px;

text-transform:uppercase;

background:

linear-gradient(
90deg,
#6a11cb,
#8e2de2,
#b44cff
);

-webkit-background-clip:text;

-webkit-text-fill-color:transparent;

text-shadow:
0 0 18px rgba(142,45,226,.3);

animation:titleMove 3s infinite;

}

.title span{

font-style:italic;

letter-spacing:10px;

}

@keyframes titleMove{

0%{
transform:translateY(0);
}

50%{
transform:translateY(-4px);
}

100%{
transform:translateY(0);
}

}

.form-control{

padding:12px;

border-radius:12px;

}

.btn-login{

background:

linear-gradient(
135deg,
#8e2de2,
#4a00e0
);

border:none;

color:white;

font-weight:700;

border-radius:12px;

transition:.3s;

}

.btn-login:hover{

transform:translateY(-3px);

box-shadow:
0 0 30px rgba(142,45,226,.6);

}

/* FORGOT PASSWORD LINK */
.forgot-link{

font-size:14px;

color:#6a11cb;

text-decoration:none;

font-weight:500;

transition:.3s;

}

.forgot-link:hover{

color:#8e2de2;

text-decoration:underline;

}

.login-right{

width:50%;

padding:60px;

display:flex;

flex-direction:column;

justify-content:center;

align-items:center;

text-align:center;

color:white;

background:

linear-gradient(
135deg,
rgba(142,45,226,.85),
rgba(74,0,224,.9)
);

}

.login-right h1{

font-size:42px;

font-weight:900;

}

.login-right p{

margin-top:15px;

opacity:.9;

}

.error{

color:red;

margin-bottom:15px;

}

.spinner-border{

margin-left:10px;

}

@media(max-width:768px){

body{
padding:20px;
}

.login-container{
flex-direction:column;
}

.login-left,
.login-right{
width:100%;
}

.title{
font-size:34px;
}

}

</style>

</head>

<body>

<div class="login-container">

<div class="login-left">

<img
src="Images/gad logo.jpg"
class="logo">

<h3 class="title">

GAD <span>VPA</span>

</h3>

<?php

if(isset($error)){

echo "<div class='error'>$error</div>";

}

?>

<form
method="POST"
id="loginForm"
style="width:100%;max-width:320px;">

<input
type="text"
name="username"
class="form-control mb-3"
placeholder="Username"
required>

<input
type="password"
name="password"
class="form-control mb-2"
placeholder="Password"
required>

<!-- FORGOT PASSWORD -->
<div class="text-end mb-3">

<a
href="forgot_password.php"
class="forgot-link">

Forgot Password?

</a>

</div>

<button
type="submit"
id="loginBtn"
class="btn btn-login w-100 mb-3">

<span id="btnText">

Login

</span>

<span
id="spinner"
class="spinner-border spinner-border-sm d-none">

</span>

</button>

<a
href="signup.php"
class="btn btn-outline-warning w-100">

Sign Up

</a>

</form>

</div>

<div class="login-right">

<h1>

WELCOME BACK

</h1>

<p>

Welcome to the GAD VPA System.<br>

Login to continue.

</p>

</div>

</div>

<script>

document
.getElementById("loginForm")

.addEventListener(

"submit",

function(e){

e.preventDefault();

let btn=
document.getElementById("loginBtn");

let text=
document.getElementById("btnText");

let spin=
document.getElementById("spinner");

btn.disabled=true;

text.innerHTML=
"Logging in...";

spin.classList.remove(
"d-none"
);

setTimeout(()=>{

this.submit();

},1200);

}

);

</script>

</body>

</html>