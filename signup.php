
<style>
.custom-auth-form {
    /* width: 90%; */
    /* max-width: 400px; */
    /* margin: 0 auto; */
    padding: 10px;
    /* border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #f9f9f9; */
}

.auth-tabs {
    display: flex;
    /* justify-content: space-between; */
    margin-bottom: 15px;
}

.auth-tabs button {
    width: 48%;
    padding: 10px;
    font-size: 16px;
    background-color: #f9f9f9;
    color: #333;
    border: 0px;
    border-bottom: 1px solid #0073aa;
    cursor: pointer;
    text-align: center;
}

.auth-tabs button.active {
    background-color: #0073aa;
    color: #fff;
    border-color: #0073aa;
}

.auth-tabs button:hover {
    background-color: #ddd;
}

.auth-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.auth-form input {
    width: 250px;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.auth-form button {
    width: 270px;
    padding: 10px;
    background-color: #0073aa;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.auth-form button:hover {
    background-color: #005177;
}

.hidden {
    display: none;
}

/* Responsive design for mobile */
@media (max-width: 600px) {
    .custom-auth-form {
        width: 100%;
    }
}
</style>

<div class="custom-auth-form">
    <div class="auth-tabs">
        <button id="toggle-login" class="active">Login</button>
        <button id="toggle-signup">Sign Up</button>
    </div>

    <form id="custom-login-form" class="auth-form" method="post">
        <!-- <h3>Login</h3> -->
        <label for="login-username">Twitter ID</label>
        <input type="text" id="login-username" name="login_username" placeholder="@" required>
        <label for="login-password">Password</label>
        <input type="password" id="login-password" name="login_password" required>
        <button type="submit" name="custom_login_submit">Login</button>
    </form>

    <form id="custom-signup-form" class="auth-form hidden" method="post">
        <!-- <h3>Sign Up</h3> -->
        <label for="signup-username">Twitter ID</label>
        <input type="text" id="signup-username" name="signup_username" placeholder="@" required>
        <label for="signup-password">Password</label>
        <input type="password" id="signup-password" name="signup_password" required>
        <button type="submit" name="custom_signup_submit">Sign Up</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Get references to the form elements and buttons
    const loginForm = document.getElementById('custom-login-form');
    const signupForm = document.getElementById('custom-signup-form');
    const toggleLogin = document.getElementById('toggle-login');
    const toggleSignup = document.getElementById('toggle-signup');

    // Initially show the login form and hide the signup form
    loginForm.classList.remove('hidden');
    signupForm.classList.add('hidden');

    // Event listener for toggling to the login form
    toggleLogin.addEventListener('click', () => {
        // Show the login form and hide the signup form
        loginForm.classList.remove('hidden');
        signupForm.classList.add('hidden');
        
        // Highlight the active button
        toggleLogin.classList.add('active');
        toggleSignup.classList.remove('active');
    });

    // Event listener for toggling to the signup form
    toggleSignup.addEventListener('click', () => {
        // Show the signup form and hide the login form
        signupForm.classList.remove('hidden');
        loginForm.classList.add('hidden');
        
        // Highlight the active button
        toggleSignup.classList.add('active');
        toggleLogin.classList.remove('active');
    });
});
</script>