<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - GAD VPA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #8e2de2, #4a00e0);
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
            padding: 2rem;
            max-width: 450px;
            width: 100%;
            background: white;
        }

        .logo {
            width: 160px;
            display: block;
            margin: 0 auto 1.5rem auto;
        }

        h4 {
            color: #4a00e0;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .btn-register {
            background: linear-gradient(135deg, #8e2de2, #4a00e0);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .login-link {
            text-align: center;
        }

        .login-link a {
            color: #ffb700;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .role-radio {
            margin-right: 15px;
        }
    </style>
</head>
<body>

<div class="card">
    <img src="GAD LOGO.jpg" alt="GAD Logo" class="logo">

    <h4>Create Account</h4>

    <form action="register.php" method="POST">
        <!-- First Name -->
        <input type="text" name="first_name" class="form-control mb-3" placeholder="First Name" required>

        <!-- Last Name -->
        <input type="text" name="last_name" class="form-control mb-3" placeholder="Last Name" required>

        <!-- Username -->
        <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>

        <!-- Email -->
        <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>

        <!-- Phone -->
        <input type="text" name="phone" class="form-control mb-3" placeholder="Phone Number" required>

        <!-- Role selection -->
        <div class="mb-3">
            <label class="form-label">Role</label>
            <div>
                <input type="radio" name="role" id="roleUser" value="user" checked class="role-radio" onchange="toggleCourseYear()">
                <label for="roleUser">Student/User</label>

                <input type="radio" name="role" id="roleAdmin" value="admin" class="role-radio" onchange="toggleCourseYear()">
                <label for="roleAdmin">Admin</label>
            </div>
        </div>

        <!-- Student Number -->
        <div class="mb-3" id="studentNumberDiv">
            <input type="text" name="student_number" id="studentNumberInput" class="form-control" placeholder="Student Number (e.g. 2023-00123)" required>
        </div>

        <!-- Course & Year -->
        <div class="mb-3" id="courseDiv">
            <input type="text" name="course_year" id="courseInput" class="form-control" placeholder="Course & Year (e.g. BSIT 3rd Year)" required>
        </div>

        <!-- Password -->
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

        <!-- Confirm Password -->
        <input type="password" name="confirm_password" class="form-control mb-4" placeholder="Confirm Password" required>

        <button type="submit" class="btn btn-register w-100 mb-3">Register</button>
    </form>

    <div class="login-link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<script>
function toggleCourseYear() {
    const isAdmin = document.getElementById('roleAdmin').checked;

    const courseDiv = document.getElementById('courseDiv');
    const courseInput = document.getElementById('courseInput');
    const studentDiv = document.getElementById('studentNumberDiv');
    const studentInput = document.getElementById('studentNumberInput');

    if (isAdmin) {
        courseDiv.style.display = 'none';
        courseInput.value = '';
        courseInput.removeAttribute('required');

        studentDiv.style.display = 'none';
        studentInput.value = '';
        studentInput.removeAttribute('required');
    } else {
        courseDiv.style.display = 'block';
        courseInput.setAttribute('required', 'required');

        studentDiv.style.display = 'block';
        studentInput.setAttribute('required', 'required');
    }
}

// Initialize on page load
window.onload = toggleCourseYear;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>