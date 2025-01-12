<div id="profile-creation">
    <form id="profile-creation-form">
        <!-- Step 1: User Information -->
        <div class="form-step" id="step-1">
            <h2>Step 1: Fill Your Information</h2>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br>

            <label for="website">Website URL:</label>
            <input type="url" name="website" id="website"><br>

            <label for="twitter">Twitter URL:</label>
            <input type="url" name="twitter" id="twitter"><br>

            <label for="linkedin">LinkedIn URL:</label>
            <input type="url" name="linkedin" id="linkedin"><br>

            <label for="instagram">Instagram URL:</label>
            <input type="url" name="instagram" id="instagram"><br>

            <button type="button" id="next-step">Next</button>
        </div>

        <!-- Step 2: Password Setup -->
        <div class="form-step" id="step-2" style="display: none;">
            <h2>Step 2: Set Your Password</h2>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>

            <label for="confirm-password">Confirm Password:</label>
            <input type="password" name="confirm-password" id="confirm-password" required><br>

            <button type="button" id="previous-step">Back</button>
            <button type="submit">Create Profile</button>
        </div>
    </form>
    <div id="response-message"></div>
</div>

<style>
    #profile-creation {
        max-width: 400px;
        margin: 0 auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        font-family: Arial, sans-serif;
    }
    #profile-creation h2 {
        font-size: 20px;
        margin-bottom: 20px;
    }
    #profile-creation label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        font-size: 14px;
    }
    #profile-creation input {
        width: calc(100% - 20px);
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }
    #profile-creation button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        font-size: 14px;
        cursor: pointer;
        border-radius: 5px;
        margin-right: 10px;
    }
    #profile-creation button:hover {
        background-color: #0056b3;
    }
    #response-message {
        margin-top: 20px;
        font-size: 14px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const step1 = document.getElementById("step-1");
    const step2 = document.getElementById("step-2");
    const nextStepBtn = document.getElementById("next-step");
    const previousStepBtn = document.getElementById("previous-step");
    const form = document.getElementById("profile-creation-form");
    const responseMessage = document.getElementById("response-message");

    // Navigate to Step 2
    nextStepBtn.addEventListener("click", function () {
        step1.style.display = "none";
        step2.style.display = "block";
    });

    // Navigate to Step 1
    previousStepBtn.addEventListener("click", function () {
        step1.style.display = "block";
        step2.style.display = "none";
    });

    // Handle form submission
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        if (formData.get("password") !== formData.get("confirm-password")) {
            responseMessage.textContent = "Passwords do not match.";
            responseMessage.style.color = "red";
            return;
        }

        fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
            method: "POST",
            body: new URLSearchParams({
                action: "create_user_profile",
                email: formData.get("email"),
                website: formData.get("website"),
                twitter: formData.get("twitter"),
                linkedin: formData.get("linkedin"),
                instagram: formData.get("instagram"),
                password: formData.get("password"),
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // Redirect to profile page
                    const userId = data.data.user_id;
                    window.location.href = `user-profile.php?user_id=${userId}`;
                } else {
                    responseMessage.textContent = data.data || "An error occurred.";
                    responseMessage.style.color = "red";
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                responseMessage.textContent = "Failed to create profile.";
                responseMessage.style.color = "red";
            });
    });
});
</script>