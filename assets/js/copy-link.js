document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        const buttons = document.querySelectorAll(".button-copylink");

        if (buttons.length > 0) {
            buttons.forEach(function (button) {
                button.addEventListener("click", function (event) {
                    event.preventDefault(); // Prevent the default behavior

                    const copyText = button.getAttribute("data-link");
                    const nonce = button.getAttribute("data-nonce"); // Get the nonce from the button

                    // Send nonce to the server for validation before copying
                    validateNonce(nonce).then(function (isValid) {
                        if (isValid) {
                            // If nonce is valid, copy the link to clipboard
                            navigator.clipboard.writeText(copyText).then(function () {
                                alert('Link copied to clipboard!');
                            }).catch(function (err) {
                                console.error("Failed to copy text: ", err);
                            });
                        } else {
                            alert('Invalid nonce. Action cannot be completed.');
                        }
                    }).catch(function (err) {
                        console.error("Error validating nonce: ", err);
                    });
                });

                // Optional: Add keydown event for accessibility (Enter or Space key)
                button.addEventListener("keydown", function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        const copyText = button.getAttribute("data-link");
                        const nonce = button.getAttribute("data-nonce"); // Get the nonce from the button

                        // Send nonce to the server for validation before copying
                        validateNonce(nonce).then(function (isValid) {
                            if (isValid) {
                                // If nonce is valid, copy the link to clipboard
                                navigator.clipboard.writeText(copyText).then(function () {
                                    alert('Link copied to clipboard!');
                                }).catch(function (err) {
                                    console.error("Failed to copy text: ", err);
                                });
                            } else {
                                alert('Invalid nonce. Action cannot be completed.');
                            }
                        }).catch(function (err) {
                            console.error("Error validating nonce: ", err);
                        });
                    }
                });
            });
        }
    }, 2000); // Delay to ensure elements are fully loaded

    // Function to validate the nonce via an AJAX request
    function validateNonce(nonce) {
        return new Promise(function (resolve, reject) {
            const data = {
                action: 'afcl_validate_nonce', // The action to call the WP AJAX handler
                nonce: nonce,
            };

            // Make AJAX request to validate nonce
            fetch(afcl_nonce_data.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data),
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    resolve(true); // Valid nonce
                } else {
                    resolve(false); // Invalid nonce
                }
            })
            .catch(err => {
                reject(err); // Reject promise if there's an error with AJAX
            });
        });
    }
});
