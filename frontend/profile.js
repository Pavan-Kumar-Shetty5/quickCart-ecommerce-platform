document.addEventListener("DOMContentLoaded", function() {
    // 1. Load User Identity (Username/Email) on Page Load
    loadUserIdentity();

    // 2. Handle Update Button Click
    const updateBtn = document.getElementById("updateBtn");
    if (updateBtn) {
        updateBtn.addEventListener("click", updateProfileData);
    }

    // 3. Handle Logout Button Click
    const logoutBtn = document.getElementById("logoutBtn");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", function() {
            if (confirm("Are you sure you want to log out of QuickCart?")) {
                window.location.href = "../backend/logout.php";
            }
        });
    }
});

/**
 * Fetches data from the 'users' table to display account details
 */
async function loadUserIdentity() {
    try {
        const response = await fetch('../backend/update-profile.php');
        const data = await response.json();

        if (data.status === "success") {
            // Account info (Read-only)
            document.getElementById('username').value = data.username;
            document.getElementById('email').value = data.email;

            // Shipping info (Editable - now stays filled!)
            if (data.phone) document.getElementById('phone').value = data.phone;
            if (data.address) document.getElementById('address').value = data.address;
            if (data.pincode) document.getElementById('pincode').value = data.pincode;
            
        }
    } catch (error) {
        console.error("Error loading profile:", error);
    }
}

/**
 * Sends Shipping Info to 'orders' table and Password to 'users' table
 */
async function updateProfileData() {
    // Gather all data from the form
    const payload = {
        phone: document.getElementById('phone').value,
        address: document.getElementById('address').value,
        pincode: document.getElementById('pincode').value,
       
    };

    // Basic Validation
    if (!payload.phone || !payload.address || !payload.pincode) {
        alert("Please fill in all shipping details.");
        return;
    }

    try {
        const response = await fetch('../backend/update-profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        // Handle the response from the 404 or PHP error pages
        if (!response.ok) {
            throw new Error(`Server responded with status ${response.status}`);
        }

        const result = await response.json();

        if (result.status === "success") {
            alert("Profile Updated Successfully");
            // Clear password field for security after successful change
            
        } else {
            alert("Error: " + result.message);
        }
    } catch (error) {
        console.error("Fetch Error (Update):", error);
        alert("Communication error: Ensure your PHP files are in the 'backend' folder.");
    }
}